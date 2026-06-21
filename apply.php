<?php
// ============================================================
//  process_eoi.php — Validates, sanitises, and inserts EOI
//  applications into the `eoi` table for The Buddies™.
//
//  - Blocks direct URL access (GET requests / missing fields).
//  - All inputs trimmed, slashes stripped, HTML-escaped.
//  - Validation is 100% server-side (apply.php has novalidate).
//  - Uses PDO via settings.php, matching jobs.php conventions.
// ============================================================

require 'settings.php'; // provides $pdo (PDO connection)

session_start();

// ---------- Helpers ----------

function sanitise(string $value): string {
    if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    $value = trim($value);
    $value = stripslashes($value); // strip slashes regardless of magic quotes state
    $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $value;
}

function sanitiseArray(array $arr, array $allowed): array {
    $clean = [];
    foreach ($arr as $item) {
        $item = sanitise((string)$item);
        if (in_array($item, $allowed, true)) {
            $clean[] = $item;
        }
    }
    return $clean;
}

function error(string $msg): void {
    echo '<p class="error">&#10007; ' . htmlspecialchars($msg) . '</p>';
}

function success(string $msg): void {
    echo '<p class="success">&#10003; ' . htmlspecialchars($msg) . '</p>';
}

// ============================================================
//  1. BLOCK DIRECT URL ACCESS
//  Only accept POST requests that include the sentinel fields
//  this form always sends. Anything else gets redirected away.
// ============================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || !isset($_POST['jobref'], $_POST['fullname'], $_POST['email'], $_POST['consent'])) {
    header('Location: apply.php');
    exit;
}

$errors = [];

// ============================================================
//  2. COLLECT + SANITISE ALL FIELDS
// ============================================================

$scalarFields = [
    'jobref', 'fullname', 'dob', 'gender', 'email', 'phone',
    'street', 'suburb', 'state', 'postcode', 'otherskills', 'position',
    'type', 'motivation', 'experience', 'description', 'date', 'time',
    'hours', 'linkedin', 'github', 'portfolio', 'refname', 'refcontact',
    'referral', 'contact',
];

$f = [];
foreach ($scalarFields as $field) {
    $f[$field] = isset($_POST[$field]) ? sanitise((string)$_POST[$field]) : '';
}

$allowedSkills = ['communication', 'teamwork', 'leadership', 'it-support', 'marketing', 'other'];
$allowedDays   = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'weekend'];

$f['skills'] = isset($_POST['skills']) && is_array($_POST['skills'])
    ? sanitiseArray($_POST['skills'], $allowedSkills) : [];
$f['days']   = isset($_POST['days']) && is_array($_POST['days'])
    ? sanitiseArray($_POST['days'], $allowedDays) : [];

$f['consent']    = isset($_POST['consent'])    ? 1 : 0;
$f['privacy']    = isset($_POST['privacy'])    ? 1 : 0;
$f['background'] = isset($_POST['background']) ? 1 : 0;

// ============================================================
//  3. SERVER-SIDE VALIDATION
// ============================================================

// --- Job Reference ---
$validRefs = ['IT101', 'FR202', 'SM303'];
$jobrefUC  = strtoupper($f['jobref']);
if (!preg_match('/^[A-Z0-9]{5}$/', $jobrefUC)) {
    $errors[] = 'Job reference must be exactly 5 alphanumeric characters (e.g. IT101).';
} elseif (!in_array($jobrefUC, $validRefs, true)) {
    $errors[] = 'Invalid job reference. Valid codes: IT101, FR202, SM303.';
} else {
    $f['jobref'] = $jobrefUC;
}

// --- Full Name -> first_name / last_name ---
// Split on the first space: everything before is the first name,
// everything after (which may contain further spaces) is the last name.
$first_name = '';
$last_name  = '';

if ($f['fullname'] === '') {
    $errors[] = 'Full name is required.';
} elseif (!preg_match("/^[A-Za-z\s\-']+$/", $f['fullname'])) {
    $errors[] = 'Full name may only contain letters, spaces, hyphens, and apostrophes.';
} else {
    $trimmedName = trim(preg_replace('/\s+/', ' ', $f['fullname']));
    if (strpos($trimmedName, ' ') === false) {
        $errors[] = 'Please enter both a first and last name.';
    } else {
        $parts      = explode(' ', $trimmedName, 2);
        $first_name = $parts[0];
        $last_name  = $parts[1];

        if (mb_strlen($first_name) > 20) {
            $errors[] = 'First name must not exceed 20 characters.';
        }
        if (mb_strlen($last_name) > 20) {
            $errors[] = 'Last name must not exceed 20 characters.';
        }
    }
}

// --- Date of Birth: dd/mm/yyyy, real date, applicant >= 15 ---
$dob_mysql = '';
if ($f['dob'] === '') {
    $errors[] = 'Date of birth is required.';
} elseif (!preg_match('/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/([0-9]{4})$/', $f['dob'], $m)) {
    $errors[] = 'Date of birth must be in dd/mm/yyyy format.';
} else {
    [$dd, $mm, $yyyy] = [(int)$m[1], (int)$m[2], (int)$m[3]];
    if (!checkdate($mm, $dd, $yyyy)) {
        $errors[] = 'Date of birth is not a valid calendar date.';
    } else {
        $birthDate = new DateTime("$yyyy-$mm-$dd");
        $today     = new DateTime();
        $age       = (int)$today->diff($birthDate)->y;
        if ($age < 15) {
            $errors[] = 'Applicant must be at least 15 years old.';
        } elseif ($age > 120) {
            $errors[] = 'Please enter a valid date of birth.';
        } else {
            $dob_mysql = sprintf('%04d-%02d-%02d', $yyyy, $mm, $dd);
        }
    }
}

// --- Gender ---
$allowedGenders = ['male', 'female', 'nonbinary', 'prefer-not'];
if (!in_array($f['gender'], $allowedGenders, true)) {
    $errors[] = 'Please select a gender option.';
}

// --- Email ---
if ($f['email'] === '') {
    $errors[] = 'Email address is required.';
} elseif (!filter_var(html_entity_decode($f['email']), FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

// --- Phone ---
if ($f['phone'] === '') {
    $errors[] = 'Phone number is required.';
} elseif (!preg_match('/^[0-9]{8,12}$/', $f['phone'])) {
    $errors[] = 'Phone number must be 8–12 digits, numbers only.';
}

// --- Address ---
if ($f['street'] === '' || mb_strlen(html_entity_decode($f['street'])) > 40) {
    $errors[] = 'Street address is required (max 40 characters).';
}
if ($f['suburb'] === '' || mb_strlen(html_entity_decode($f['suburb'])) > 40) {
    $errors[] = 'Suburb / town is required (max 40 characters).';
}
$allowedStates = ['VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT'];
if (!in_array($f['state'], $allowedStates, true)) {
    $errors[] = 'Please select a valid state.';
}
if (!preg_match('/^[0-9]{4}$/', $f['postcode'])) {
    $errors[] = 'Postcode must be exactly 4 digits.';
}

// --- Position ---
$allowedPositions = ['IT101', 'FR202', 'SM303'];
if (!in_array($f['position'], $allowedPositions, true)) {
    $errors[] = 'Please select a valid position.';
} elseif ($jobrefUC !== '' && $f['position'] !== $jobrefUC) {
    $errors[] = 'Position selected does not match the job reference number.';
}

$allowedTypes = ['volunteer', 'part-time', 'full-time'];
if (!in_array($f['type'], $allowedTypes, true)) {
    $errors[] = 'Please select a position type.';
}

// --- Motivation ---
if (mb_strlen($f['motivation']) < 20) {
    $errors[] = 'Please tell us why you want to join (at least 20 characters).';
}

// --- Experience ---
if ($f['experience'] === '' || !ctype_digit($f['experience'])
    || (int)$f['experience'] < 0 || (int)$f['experience'] > 70) {
    $errors[] = 'Years of experience must be a whole number between 0 and 70.';
}

// --- Available Start Date (today or future) ---
if ($f['date'] === '') {
    $errors[] = 'Available start date is required.';
} elseif (!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/', $f['date'])) {
    $errors[] = 'Available start date must be in YYYY-MM-DD format.';
} else {
    $startDate = DateTime::createFromFormat('Y-m-d', $f['date']);
    $today     = new DateTime('today');
    if (!$startDate || $startDate < $today) {
        $errors[] = 'Available start date cannot be in the past.';
    }
}

// --- Preferred Interview Time ---
if ($f['time'] === '' || !preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $f['time'])) {
    $errors[] = 'Interview time must be in HH:MM (24-hour) format.';
}

// --- Hours per week ---
if ($f['hours'] === '' || !ctype_digit($f['hours'])
    || (int)$f['hours'] < 1 || (int)$f['hours'] > 80) {
    $errors[] = 'Hours per week must be a whole number between 1 and 80.';
}

// --- File uploads (CV and Cover Letter required) ---
$allowed_mime = ['application/pdf', 'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$allowed_ext  = ['pdf', 'doc', 'docx'];
$max_size     = 5 * 1024 * 1024; // 5 MB

function validateUpload(array $file, string $label): ?string {
    global $allowed_mime, $allowed_ext, $max_size;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $codes = [
            UPLOAD_ERR_INI_SIZE   => 'exceeds server upload limit',
            UPLOAD_ERR_FORM_SIZE  => 'exceeds form size limit',
            UPLOAD_ERR_PARTIAL    => 'was only partially uploaded',
            UPLOAD_ERR_NO_FILE    => 'was not uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'failed to write to disk',
            UPLOAD_ERR_EXTENSION  => 'blocked by a PHP extension',
        ];
        $reason = $codes[$file['error']] ?? 'unknown error';
        return "$label upload failed: $reason.";
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext, true)) {
        return "$label must be a .pdf, .doc, or .docx file.";
    }
    if ($file['size'] > $max_size) {
        return "$label must be smaller than 5 MB.";
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowed_mime, true)) {
        return "$label has an unrecognised file type ($mime).";
    }
    return null;
}

$cv_error = validateUpload($_FILES['cv']         ?? ['error' => UPLOAD_ERR_NO_FILE], 'CV');
$cl_error = validateUpload($_FILES['coverletter'] ?? ['error' => UPLOAD_ERR_NO_FILE], 'Cover Letter');
if ($cv_error) $errors[] = $cv_error;
if ($cl_error) $errors[] = $cl_error;

// --- Professional links (optional, validate format if given) ---
foreach (['linkedin' => 'LinkedIn', 'github' => 'GitHub', 'portfolio' => 'Portfolio'] as $key => $label) {
    $raw = html_entity_decode($f[$key]);
    if ($raw !== '' && !filter_var($raw, FILTER_VALIDATE_URL)) {
        $errors[] = "$label URL is not a valid web address.";
    }
}

// --- References (if one filled, both should be) ---
if (($f['refname'] !== '' && $f['refcontact'] === '') || ($f['refname'] === '' && $f['refcontact'] !== '')) {
    $errors[] = 'Please provide both a reference name and contact, or leave both blank.';
}

// --- Referral source ---
$allowedReferral = ['', 'website', 'social', 'friend', 'event'];
if (!in_array($f['referral'], $allowedReferral, true)) {
    $errors[] = 'Invalid referral source selected.';
}

// --- Preferred contact method ---
if (!in_array($f['contact'], ['email', 'phone'], true)) {
    $errors[] = 'Please select a preferred contact method.';
}

// --- Declaration ---
if (!$f['consent']) {
    $errors[] = 'You must confirm that the information provided is accurate.';
}
if (!$f['privacy']) {
    $errors[] = "You must agree to the organisation's data privacy policy.";
}

// ============================================================
//  4. SAVE FILES + INSERT (only if validation passed)
// ============================================================

$cv_saved   = false;
$cl_saved   = false;
$db_saved   = false;
$eoiNumber  = null;
$upload_dir = __DIR__ . '/uploads/';
$cv_dest    = '';
$cl_dest    = '';

if (empty($errors)) {
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $safe_name = preg_replace('/[^A-Za-z0-9_-]/', '_', $first_name . '_' . $last_name);
    $timestamp = date('Ymd_His');

    $cv_ext = strtolower(pathinfo($_FILES['cv']['name'],          PATHINFO_EXTENSION));
    $cl_ext = strtolower(pathinfo($_FILES['coverletter']['name'], PATHINFO_EXTENSION));

    $cv_dest = $upload_dir . "cv_{$safe_name}_{$timestamp}.{$cv_ext}";
    $cl_dest = $upload_dir . "cl_{$safe_name}_{$timestamp}.{$cl_ext}";

    if (!move_uploaded_file($_FILES['cv']['tmp_name'], $cv_dest)) {
        $errors[] = 'Failed to save CV. Please try again.';
    } else {
        $cv_saved = true;
    }

    if (!move_uploaded_file($_FILES['coverletter']['tmp_name'], $cl_dest)) {
        $errors[] = 'Failed to save cover letter. Please try again.';
    } else {
        $cl_saved = true;
    }
}

if (empty($errors) && $cv_saved && $cl_saved) {

    $skillsStr = implode(',', $f['skills']); // SET columns: comma-separated, no spaces
    $daysStr   = implode(',', $f['days']);

    $cv_path_rel = 'uploads/' . basename($cv_dest);
    $cl_path_rel = 'uploads/' . basename($cl_dest);

    $referral_value     = $f['referral']    !== '' ? $f['referral']    : null;
    $otherskills_value  = $f['otherskills'] !== '' ? $f['otherskills'] : null;
    $description_value  = $f['description'] !== '' ? $f['description'] : null;
    $linkedin_value     = $f['linkedin']    !== '' ? $f['linkedin']    : null;
    $github_value       = $f['github']      !== '' ? $f['github']      : null;
    $portfolio_value    = $f['portfolio']   !== '' ? $f['portfolio']   : null;
    $refname_value      = $f['refname']     !== '' ? $f['refname']     : null;
    $refcontact_value   = $f['refcontact']  !== '' ? $f['refcontact']  : null;

    try {
        $sql = "INSERT INTO eoi
            (job_reference, first_name, last_name, date_of_birth, gender, email, phone,
             street, suburb, state, postcode, skills, other_skills,
             position, position_type, motivation, years_experience, experience_desc,
             available_from, interview_time, days_available, hours_per_week,
             cv_path, cover_letter_path, linkedin_url, github_url, portfolio_url,
             ref_name, ref_contact, referral_source, preferred_contact,
             consent_accurate, consent_privacy, consent_background)
            VALUES
            (:job_reference, :first_name, :last_name, :date_of_birth, :gender, :email, :phone,
             :street, :suburb, :state, :postcode, :skills, :other_skills,
             :position, :position_type, :motivation, :years_experience, :experience_desc,
             :available_from, :interview_time, :days_available, :hours_per_week,
             :cv_path, :cover_letter_path, :linkedin_url, :github_url, :portfolio_url,
             :ref_name, :ref_contact, :referral_source, :preferred_contact,
             :consent_accurate, :consent_privacy, :consent_background)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':job_reference'      => $f['jobref'],
            ':first_name'         => $first_name,
            ':last_name'          => $last_name,
            ':date_of_birth'      => $dob_mysql,
            ':gender'             => $f['gender'],
            ':email'              => $f['email'],
            ':phone'              => $f['phone'],
            ':street'             => $f['street'],
            ':suburb'             => $f['suburb'],
            ':state'              => $f['state'],
            ':postcode'           => $f['postcode'],
            ':skills'             => $skillsStr,
            ':other_skills'       => $otherskills_value,
            ':position'           => $f['position'],
            ':position_type'      => $f['type'],
            ':motivation'         => $f['motivation'],
            ':years_experience'   => (int)$f['experience'],
            ':experience_desc'    => $description_value,
            ':available_from'     => $f['date'],
            ':interview_time'     => $f['time'],
            ':days_available'     => $daysStr,
            ':hours_per_week'     => (int)$f['hours'],
            ':cv_path'            => $cv_path_rel,
            ':cover_letter_path'  => $cl_path_rel,
            ':linkedin_url'       => $linkedin_value,
            ':github_url'         => $github_value,
            ':portfolio_url'      => $portfolio_value,
            ':ref_name'           => $refname_value,
            ':ref_contact'        => $refcontact_value,
            ':referral_source'    => $referral_value,
            ':preferred_contact'  => $f['contact'],
            ':consent_accurate'   => $f['consent'],
            ':consent_privacy'    => $f['privacy'],
            ':consent_background' => $f['background'],
        ]);

        $eoiNumber = $pdo->lastInsertId();
        $db_saved  = true;

    } catch (PDOException $e) {
        // Never expose raw DB errors to the user
        $errors[] = 'Your application could not be saved to the database. Please try again later.';
    }
}

// ============================================================
//  5. OUTPUT
// ============================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Application Result — The Buddies™</title>
  <link href="styles.css" rel="stylesheet">
</head>
<body>

<?php include("includes/header.inc"); ?>
<?php include("includes/nav.inc"); ?>

<main>

<?php if (!empty($errors)): ?>

  <section>
    <h2>&#10007; Please fix the following errors</h2>
    <?php foreach ($errors as $e): error($e); endforeach; ?>
    <a href="javascript:history.back()">&#8592; Go back and correct the form</a>
  </section>

<?php elseif ($db_saved): ?>

  <section>
    <?php success('Your application has been submitted successfully!'); ?>
    <p>Your EOI number is:</p>
    <h2><?= str_pad($eoiNumber, 6, '0', STR_PAD_LEFT) ?></h2>
    <p><small>Please keep this number for your records.</small></p>
  </section>

  <section>
    <h2>Application Summary</h2>
    <table>
      <tr><th>EOI Number</th>     <td><?= str_pad($eoiNumber, 6, '0', STR_PAD_LEFT) ?></td></tr>
      <tr><th>Job Reference</th>  <td><?= htmlspecialchars($f['jobref']) ?></td></tr>
      <tr><th>Position</th>       <td><?= htmlspecialchars($f['position']) ?></td></tr>
      <tr><th>Position Type</th>  <td><?= htmlspecialchars(ucfirst($f['type'])) ?></td></tr>
      <tr><th>Name</th>           <td><?= htmlspecialchars($first_name . ' ' . $last_name) ?></td></tr>
      <tr><th>Email</th>          <td><?= htmlspecialchars($f['email']) ?></td></tr>
      <tr><th>Phone</th>          <td><?= htmlspecialchars($f['phone']) ?></td></tr>
      <tr><th>Available From</th> <td><?= htmlspecialchars($f['date']) ?></td></tr>
      <tr><th>Hours / Week</th>   <td><?= htmlspecialchars($f['hours']) ?></td></tr>
      <tr><th>Skills</th>
          <td><?= htmlspecialchars(implode(', ', $f['skills']) ?: 'None specified') ?></td></tr>
      <tr><th>Status</th>         <td>New</td></tr>
    </table>
    <p>We will be in touch via <strong><?= htmlspecialchars($f['contact']) ?></strong> shortly.</p>
  </section>

  <a href="index.html">&#8592; Return to Home</a>

<?php endif; ?>

</main>

<?php include("includes/footer.inc"); ?>

</body>
</html>