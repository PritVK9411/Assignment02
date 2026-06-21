<?php
// ============================================================
//  apply.php — Form processor for The Buddiesss job application
// ============================================================

// ---------- Helpers ----------

function clean(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function error(string $msg): void {
    echo '<p class="error">&#10007; ' . htmlspecialchars($msg) . '</p>';
}

function success(string $msg): void {
    echo '<p class="success">&#10003; ' . htmlspecialchars($msg) . '</p>';
}

// ---------- Only accept POST ----------

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: apply.html');
    exit;
}

$errors = [];

// ============================================================
//  1. JOB REFERENCE
// ============================================================

$jobref = clean($_POST['jobref'] ?? '');
$valid_refs = ['IT101', 'FR202', 'SM303'];

if (!preg_match('/^[A-Za-z0-9]{5}$/', $jobref)) {
    $errors[] = 'Job reference must be exactly 5 alphanumeric characters.';
} elseif (!in_array(strtoupper($jobref), $valid_refs)) {
    $errors[] = 'Job reference "' . $jobref . '" is not a valid listing. Valid refs: IT101, FR202, SM303.';
} else {
    $jobref = strtoupper($jobref);
}

// ============================================================
//  2. PERSONAL INFORMATION
// ============================================================

$firstname = clean($_POST['firstname'] ?? '');
$lastname  = clean($_POST['lastname']  ?? '');
$dob       = clean($_POST['dob']       ?? '');
$gender    = clean($_POST['gender']    ?? '');
$email     = clean($_POST['email']     ?? '');
$phone     = clean($_POST['phone']     ?? '');

if (!preg_match('/^[A-Za-z]{1,20}$/', $firstname)) {
    $errors[] = 'First name must be 1–20 letters only.';
}

if (!preg_match('/^[A-Za-z]{1,20}$/', $lastname)) {
    $errors[] = 'Last name must be 1–20 letters only.';
}

// Validate dob format dd/mm/yyyy and check it is a real date
if (!preg_match('/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/[0-9]{4}$/', $dob)) {
    $errors[] = 'Date of birth must be in dd/mm/yyyy format.';
} else {
    [$d, $m, $y] = explode('/', $dob);
    if (!checkdate((int)$m, (int)$d, (int)$y)) {
        $errors[] = 'Date of birth is not a valid calendar date.';
    } else {
        // Must be at least 16 years old
        $birthDate = new DateTime("$y-$m-$d");
        $today     = new DateTime();
        $age       = $today->diff($birthDate)->y;
        if ($age < 16) {
            $errors[] = 'Applicants must be at least 16 years old.';
        }
    }
}

$allowed_genders = ['male', 'female', 'nonbinary', 'prefer-not'];
if (!in_array($gender, $allowed_genders)) {
    $errors[] = 'Please select a valid gender option.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (!preg_match('/^[0-9]{8,12}$/', $phone)) {
    $errors[] = 'Phone number must be 8–12 digits only.';
}

// ============================================================
//  3. ADDRESS
// ============================================================

$street   = clean($_POST['street']   ?? '');
$suburb   = clean($_POST['suburb']   ?? '');
$state    = clean($_POST['state']    ?? '');
$postcode = clean($_POST['postcode'] ?? '');

$allowed_states = ['VIC','NSW','QLD','NT','WA','SA','TAS','ACT'];

if (strlen($street) < 1 || strlen($street) > 40) {
    $errors[] = 'Street address is required (max 40 characters).';
}

if (strlen($suburb) < 1 || strlen($suburb) > 40) {
    $errors[] = 'Suburb/Town is required (max 40 characters).';
}

if (!in_array($state, $allowed_states)) {
    $errors[] = 'Please select a valid Australian state.';
}

if (!preg_match('/^[0-9]{4}$/', $postcode)) {
    $errors[] = 'Postcode must be exactly 4 digits.';
}

// ============================================================
//  4. SKILLS
// ============================================================

$allowed_skills = ['communication','teamwork','leadership','it-support','marketing','other'];
$skills = [];
if (!empty($_POST['skills']) && is_array($_POST['skills'])) {
    foreach ($_POST['skills'] as $s) {
        if (in_array($s, $allowed_skills)) {
            $skills[] = $s;
        }
    }
}
$otherskills = clean($_POST['otherskills'] ?? '');

// If "other" is ticked, a description is expected (soft warning only)
if (in_array('other', $skills) && $otherskills === '') {
    $errors[] = 'Please describe your other skills in the text box.';
}

// ============================================================
//  5. POSITION
// ============================================================

$position = clean($_POST['position'] ?? '');
$type     = clean($_POST['type']     ?? '');

if (!in_array($position, $valid_refs)) {
    $errors[] = 'Please select a valid position.';
}

// Cross-check job ref and position match
if (!empty($jobref) && !empty($position) && $jobref !== strtoupper($position)) {
    $errors[] = 'Job reference and selected position do not match.';
}

$allowed_types = ['volunteer','part-time','full-time'];
if (!in_array($type, $allowed_types)) {
    $errors[] = 'Please select a position type (Volunteer, Part-Time, or Full-Time).';
}

// ============================================================
//  6. MOTIVATION
// ============================================================

$motivation = clean($_POST['motivation'] ?? '');
if (strlen($motivation) < 20) {
    $errors[] = 'Please tell us why you want to join (at least 20 characters).';
}

// ============================================================
//  7. EXPERIENCE
// ============================================================

$experience  = $_POST['experience'] ?? '';
$description = clean($_POST['description'] ?? '');

if (!is_numeric($experience) || (int)$experience < 0 || (int)$experience > 70) {
    $errors[] = 'Years of experience must be a number between 0 and 70.';
} else {
    $experience = (int)$experience;
}

// ============================================================
//  8. AVAILABILITY
// ============================================================

$date  = clean($_POST['date']  ?? '');
$time  = clean($_POST['time']  ?? '');
$hours = $_POST['hours'] ?? '';

$allowed_days = ['monday','tuesday','wednesday','thursday','friday','weekend'];
$days = [];
if (!empty($_POST['days']) && is_array($_POST['days'])) {
    foreach ($_POST['days'] as $day) {
        if (in_array($day, $allowed_days)) {
            $days[] = $day;
        }
    }
}

// Validate available start date (must not be in the past)
if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $errors[] = 'Please enter a valid available start date.';
} else {
    $startDate = new DateTime($date);
    $today     = new DateTime('today');
    if ($startDate < $today) {
        $errors[] = 'Available start date cannot be in the past.';
    }
}

if (empty($time) || !preg_match('/^\d{2}:\d{2}$/', $time)) {
    $errors[] = 'Please enter a valid preferred interview time.';
}

if (!is_numeric($hours) || (int)$hours < 1 || (int)$hours > 80) {
    $errors[] = 'Hours per week must be between 1 and 80.';
} else {
    $hours = (int)$hours;
}

// ============================================================
//  9. FILE UPLOADS — CV and Cover Letter
// ============================================================

$allowed_mime  = ['application/pdf','application/msword',
                  'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$allowed_ext   = ['pdf','doc','docx'];
$max_size      = 5 * 1024 * 1024; // 5 MB

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
    if (!in_array($ext, $allowed_ext)) {
        return "$label must be a .pdf, .doc, or .docx file.";
    }

    if ($file['size'] > $max_size) {
        return "$label must be smaller than 5 MB.";
    }

    // MIME check
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowed_mime)) {
        return "$label has an unrecognised file type ($mime).";
    }

    return null; // no error
}

$cv_error = validateUpload($_FILES['cv']          ?? ['error' => UPLOAD_ERR_NO_FILE], 'CV');
$cl_error = validateUpload($_FILES['coverletter']  ?? ['error' => UPLOAD_ERR_NO_FILE], 'Cover Letter');
if ($cv_error) $errors[] = $cv_error;
if ($cl_error) $errors[] = $cl_error;

// ============================================================
//  10. PROFESSIONAL LINKS (optional — validate format if given)
// ============================================================

$linkedin  = clean($_POST['linkedin']  ?? '');
$github    = clean($_POST['github']    ?? '');
$portfolio = clean($_POST['portfolio'] ?? '');

foreach (['LinkedIn' => $linkedin, 'GitHub' => $github, 'Portfolio' => $portfolio] as $label => $url) {
    if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
        $errors[] = "$label URL is not a valid web address.";
    }
}

// ============================================================
//  11. REFERENCES (optional)
// ============================================================

$refname    = clean($_POST['refname']    ?? '');
$refcontact = clean($_POST['refcontact'] ?? '');

// If one is filled, both should be
if (($refname !== '' && $refcontact === '') || ($refname === '' && $refcontact !== '')) {
    $errors[] = 'Please provide both a reference name and their contact details, or leave both blank.';
}

// ============================================================
//  12. REFERRAL & CONTACT PREFERENCE
// ============================================================

$referral = clean($_POST['referral'] ?? '');
$contact  = clean($_POST['contact']  ?? '');

$allowed_referrals = ['','website','social','friend','event'];
if (!in_array($referral, $allowed_referrals)) {
    $errors[] = 'Invalid referral source selected.';
}

if (!in_array($contact, ['email','phone'])) {
    $errors[] = 'Please select a preferred contact method (Email or Phone).';
}

// ============================================================
//  13. DECLARATION CHECKBOXES
// ============================================================

$consent    = isset($_POST['consent']);
$privacy    = isset($_POST['privacy']);
$background = isset($_POST['background']);

if (!$consent) {
    $errors[] = 'You must confirm that the information provided is accurate and complete.';
}
if (!$privacy) {
    $errors[] = 'You must agree to the organisation\'s data privacy policy.';
}

// ============================================================
//  SAVE FILES (only if no errors so far)
// ============================================================

$cv_saved = false;
$cl_saved = false;
$upload_dir = __DIR__ . '/uploads/';

if (empty($errors)) {
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $safe_name = preg_replace('/[^A-Za-z0-9_-]/', '_', $firstname . '_' . $lastname);
    $timestamp = date('Ymd_His');

    $cv_ext  = strtolower(pathinfo($_FILES['cv']['name'],         PATHINFO_EXTENSION));
    $cl_ext  = strtolower(pathinfo($_FILES['coverletter']['name'], PATHINFO_EXTENSION));

    $cv_dest = $upload_dir . "cv_{$safe_name}_{$timestamp}.{$cv_ext}";
    $cl_dest = $upload_dir . "cl_{$safe_name}_{$timestamp}.{$cl_ext}";

    if (!move_uploaded_file($_FILES['cv']['tmp_name'],          $cv_dest)) {
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

// ============================================================
//  OUTPUT
// ============================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Application Result — The Buddiesss</title>
  <link href="apply.css" rel="stylesheet">
  <style>
    body       { font-family: sans-serif; max-width: 760px; margin: 2rem auto; padding: 0 1rem; }
    .error     { color: #c0392b; background: #fdecea; padding: .5rem .8rem; border-left: 4px solid #c0392b; margin:.4rem 0; border-radius:3px; }
    .success   { color: #1e8449; background: #eafaf1; padding: .5rem .8rem; border-left: 4px solid #1e8449; margin:.4rem 0; border-radius:3px; }
    table      { border-collapse: collapse; width: 100%; margin-top: 1rem; }
    td, th     { border: 1px solid #ccc; padding: .45rem .7rem; text-align: left; }
    th         { background: #f0f0f0; width: 35%; }
    h1         { color: #2c3e50; }
    h2         { color: #2c3e50; margin-top: 2rem; }
    .back-link { display: inline-block; margin-top: 1.5rem; }
  </style>
</head>
<body>

<header>
  <h1>The Buddiesss — Application Result</h1>
  <nav>
    <ul style="list-style:none;padding:0;display:flex;gap:1rem;">
      <li><a href="index.html">Home</a></li>
      <li><a href="jobs.html">Jobs</a></li>
      <li><a href="apply.html">Apply</a></li>
      <li><a href="about.html">About</a></li>
    </ul>
  </nav>
</header>

<?php if (!empty($errors)): ?>

  <h2>&#10007; Please fix the following errors</h2>
  <?php foreach ($errors as $e): error($e); endforeach; ?>
  <a class="back-link" href="javascript:history.back()">&#8592; Go back and correct the form</a>

<?php else: ?>

  <?php success('Your application has been submitted successfully! We will be in touch soon.'); ?>

  <h2>Application Summary</h2>

  <table>
    <tr><th>Job Reference</th>        <td><?= $jobref ?></td></tr>
    <tr><th>Position</th>             <td><?= $position ?></td></tr>
    <tr><th>Position Type</th>        <td><?= ucfirst($type) ?></td></tr>
  </table>

  <h2>Personal Details</h2>
  <table>
    <tr><th>Full Name</th>            <td><?= $firstname . ' ' . $lastname ?></td></tr>
    <tr><th>Date of Birth</th>        <td><?= $dob ?></td></tr>
    <tr><th>Gender</th>               <td><?= ucfirst(str_replace('-', ' ', $gender)) ?></td></tr>
    <tr><th>Email</th>                <td><?= $email ?></td></tr>
    <tr><th>Phone</th>                <td><?= $phone ?></td></tr>
  </table>

  <h2>Address</h2>
  <table>
    <tr><th>Street</th>               <td><?= $street ?></td></tr>
    <tr><th>Suburb / Town</th>        <td><?= $suburb ?></td></tr>
    <tr><th>State</th>                <td><?= $state ?></td></tr>
    <tr><th>Postcode</th>             <td><?= $postcode ?></td></tr>
  </table>

  <h2>Skills &amp; Experience</h2>
  <table>
    <tr><th>Skills</th>               <td><?= !empty($skills) ? implode(', ', array_map('ucfirst', $skills)) : 'None selected' ?></td></tr>
    <?php if ($otherskills !== ''): ?>
    <tr><th>Other Skills</th>         <td><?= $otherskills ?></td></tr>
    <?php endif; ?>
    <tr><th>Years of Experience</th>  <td><?= $experience ?></td></tr>
    <?php if ($description !== ''): ?>
    <tr><th>Experience Description</th><td><?= nl2br($description) ?></td></tr>
    <?php endif; ?>
  </table>

  <h2>Motivation</h2>
  <table>
    <tr><td><?= nl2br($motivation) ?></td></tr>
  </table>

  <h2>Availability</h2>
  <table>
    <tr><th>Start Date</th>           <td><?= htmlspecialchars($date) ?></td></tr>
    <tr><th>Preferred Interview Time</th><td><?= htmlspecialchars($time) ?></td></tr>
    <tr><th>Days Available</th>       <td><?= !empty($days) ? implode(', ', array_map('ucfirst', $days)) : 'None selected' ?></td></tr>
    <tr><th>Hours per Week</th>       <td><?= $hours ?></td></tr>
  </table>

  <h2>Documents</h2>
  <table>
    <tr><th>CV</th>                   <td><?= $cv_saved ? '&#10003; Uploaded successfully' : '&#10007; Not saved' ?></td></tr>
    <tr><th>Cover Letter</th>         <td><?= $cl_saved ? '&#10003; Uploaded successfully' : '&#10007; Not saved' ?></td></tr>
  </table>

  <?php if ($linkedin || $github || $portfolio): ?>
  <h2>Professional Links</h2>
  <table>
    <?php if ($linkedin):  ?><tr><th>LinkedIn</th>  <td><a href="<?= $linkedin  ?>"><?= $linkedin  ?></a></td></tr><?php endif; ?>
    <?php if ($github):    ?><tr><th>GitHub</th>    <td><a href="<?= $github    ?>"><?= $github    ?></a></td></tr><?php endif; ?>
    <?php if ($portfolio): ?><tr><th>Portfolio</th> <td><a href="<?= $portfolio ?>"><?= $portfolio ?></a></td></tr><?php endif; ?>
  </table>
  <?php endif; ?>

  <?php if ($refname !== ''): ?>
  <h2>Reference</h2>
  <table>
    <tr><th>Name</th>    <td><?= $refname ?></td></tr>
    <tr><th>Contact</th> <td><?= $refcontact ?></td></tr>
  </table>
  <?php endif; ?>

  <h2>Other Details</h2>
  <table>
    <tr><th>How You Heard About Us</th>  <td><?= $referral !== '' ? ucfirst($referral) : 'Not specified' ?></td></tr>
    <tr><th>Preferred Contact</th>       <td><?= ucfirst($contact) ?></td></tr>
    <tr><th>Background Check Consent</th><td><?= $background ? 'Yes' : 'No' ?></td></tr>
  </table>

  <a class="back-link" href="index.html">&#8592; Return to Home</a>

<?php endif; ?>

<footer style="margin-top:3rem;border-top:1px solid #ccc;padding-top:1rem;color:#666;">
  <p>© 2026 The Buddiesss Team. All rights reserved.</p>
</footer>

</body>
</html>