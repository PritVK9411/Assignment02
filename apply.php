<?php
// ============================================================
//  apply.php — PHP version of the apply form for The Buddies™
//  Posts to process_eoi.php for full server-side validation.
//
//  This form uses HTML5 client-side validation (required,
//  pattern, type="email" etc.) as a first line of defence and
//  for user experience. process_eoi.php does NOT rely on this —
//  it independently re-validates and sanitises every field
//  server-side, since client-side validation can always be
//  bypassed (JS disabled, direct POST, edited HTML, etc.).
//
//  Fields added beyond the original apply.html to match the
//  `eoi` table schema: Job Reference, Date of Birth, Gender,
//  and full Address (street, suburb, state, postcode).
// ============================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Non-Profit Job Application">
    <meta name="keywords" content="Nonprofit, Jobs, Volunteer, Application">
    <meta name="author" content="Your Name">
    <title>Apply - The Buddies™</title>
    <link href="styles.css" rel="stylesheet">
</head>

<body>

<?php include("includes/header.inc"); ?>
<?php include("includes/nav.inc"); ?>

<main>

<form action="process_eoi.php" method="post" enctype="multipart/form-data">

    <!-- Job Reference -->
    <fieldset>
        <legend>Job Reference</legend>

        <label for="jobref">Job Reference Number (exactly 5 alphanumeric characters):</label>
        <input type="text" id="jobref" name="jobref" required maxlength="5" minlength="5"
            pattern="[A-Za-z0-9]{5}" placeholder="e.g. IT101"
            title="Exactly 5 alphanumeric characters"><br><br>
    </fieldset>

    <!-- Personal Information -->
    <fieldset>
        <legend>Personal Information</legend>

        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname" required
            pattern="[A-Za-z\s\-']+" title="Letters, spaces, hyphens, and apostrophes only"><br><br>

        <label for="dob">Date of Birth (dd/mm/yyyy):</label>
        <input type="text" id="dob" name="dob" required
            pattern="(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[0-2])/[0-9]{4}"
            placeholder="dd/mm/yyyy" title="Date in dd/mm/yyyy format"><br><br>

        <fieldset>
            <legend>Gender</legend>
            <label><input type="radio" name="gender" value="male" required> Male</label>
            <label><input type="radio" name="gender" value="female"> Female</label>
            <label><input type="radio" name="gender" value="nonbinary"> Non-binary</label>
            <label><input type="radio" name="gender" value="prefer-not"> Prefer not to say</label>
        </fieldset><br>

        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" required placeholder="jane@example.com"><br><br>

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" required
            pattern="[0-9]{8,12}" placeholder="0412345678"
            title="8 to 12 digits, numbers only"><br><br>
    </fieldset>

    <!-- Address -->
    <fieldset>
        <legend>Address</legend>

        <label for="street">Street Address (max 40 characters):</label>
        <input type="text" id="street" name="street" required maxlength="40"
            placeholder="123 Volunteer Lane"><br><br>

        <label for="suburb">Suburb / Town (max 40 characters):</label>
        <input type="text" id="suburb" name="suburb" required maxlength="40"
            placeholder="Melbourne"><br><br>

        <label for="state">State:</label>
        <select id="state" name="state" required>
            <option value="">-- Select State --</option>
            <option value="VIC">VIC</option>
            <option value="NSW">NSW</option>
            <option value="QLD">QLD</option>
            <option value="NT">NT</option>
            <option value="WA">WA</option>
            <option value="SA">SA</option>
            <option value="TAS">TAS</option>
            <option value="ACT">ACT</option>
        </select><br><br>

        <label for="postcode">Postcode (exactly 4 digits):</label>
        <input type="text" id="postcode" name="postcode" required
            pattern="[0-9]{4}" maxlength="4" minlength="4"
            placeholder="3000" title="Exactly 4 digits"><br><br>
    </fieldset>

    <!-- Position -->
    <fieldset>
        <legend>Position Applied For</legend>

        <label for="position">Select Position:</label>
        <select id="position" name="position" required>
            <option value="">-- Please Select --</option>
            <option value="IT101">IT101 — IT Helpdesk Volunteer</option>
            <option value="FR202">FR202 — Fundraising Officer</option>
            <option value="SM303">SM303 — Social Media Manager</option>
        </select><br><br>

        <label>Position Type:</label><br>
        <label><input type="radio" name="type" value="volunteer" required> Volunteer</label>
        <label><input type="radio" name="type" value="part-time"> Part-Time</label>
        <label><input type="radio" name="type" value="full-time"> Full-Time</label>
    </fieldset>

    <!-- Motivation -->
    <fieldset>
        <legend>Motivation</legend>

        <label for="motivation">Why do you want to join our organization? (min 20 characters)</label><br>
        <textarea id="motivation" name="motivation" rows="4" cols="40" required minlength="20"></textarea>
    </fieldset>

    <!-- Skills -->
    <fieldset>
        <legend>Skills</legend>

        <label><input type="checkbox" name="skills[]" value="communication"> Communication</label>
        <label><input type="checkbox" name="skills[]" value="teamwork"> Teamwork</label>
        <label><input type="checkbox" name="skills[]" value="leadership"> Leadership</label>
        <label><input type="checkbox" name="skills[]" value="it-support"> IT Support</label>
        <label><input type="checkbox" name="skills[]" value="marketing"> Marketing</label>
        <label><input type="checkbox" name="skills[]" value="other"> Other skills…</label><br><br>

        <label for="otherskills">Other Skills (describe if selected above):</label><br>
        <textarea id="otherskills" name="otherskills" rows="3" cols="40"
            placeholder="Describe any other skills you have…"></textarea>
    </fieldset>

    <!-- Experience -->
    <fieldset>
        <legend>Experience</legend>

        <label for="experience">Years of Experience:</label>
        <input type="number" id="experience" name="experience" required min="0" max="70"><br><br>

        <label for="description">Describe Your Experience (optional):</label><br>
        <textarea id="description" name="description" rows="4" cols="40"
          placeholder="Briefly describe your experience (optional)"></textarea>
    </fieldset>

    <!-- Availability -->
    <fieldset>
        <legend>Availability</legend>

        <label for="date">Available Start Date:</label>
        <input type="date" id="date" name="date" required><br><br>

        <label for="time">Preferred Interview Time:</label>
        <input type="time" id="time" name="time" required><br><br>

        <label>Days Available:</label><br>
        <label><input type="checkbox" name="days[]" value="monday"> Monday</label>
        <label><input type="checkbox" name="days[]" value="tuesday"> Tuesday</label>
        <label><input type="checkbox" name="days[]" value="wednesday"> Wednesday</label>
        <label><input type="checkbox" name="days[]" value="thursday"> Thursday</label>
        <label><input type="checkbox" name="days[]" value="friday"> Friday</label>
        <label><input type="checkbox" name="days[]" value="weekend"> Weekend</label><br><br>

        <label for="hours">Hours per week you can commit:</label>
        <input type="number" id="hours" name="hours" required min="1" max="80">
    </fieldset>

    <!-- Documents -->
    <fieldset>
        <legend>Upload Documents</legend>

        <label for="cv">Upload CV:</label>
        <input type="file" id="cv" name="cv" required accept=".pdf,.doc,.docx"><br><br>

        <label for="coverletter">Upload Cover Letter:</label>
        <input type="file" id="coverletter" name="coverletter" required accept=".pdf,.doc,.docx">
    </fieldset>

    <!-- Professional Links -->
    <fieldset>
        <legend>Professional Links</legend>

        <label for="linkedin">LinkedIn Profile (optional):</label>
        <input type="url" id="linkedin" name="linkedin"
               placeholder="https://linkedin.com/in/username (optional)"><br><br>

        <label for="github">GitHub Profile (optional):</label>
        <input type="url" id="github" name="github"
               placeholder="https://github.com/username (optional)"><br><br>

        <label for="portfolio">Portfolio Website (optional):</label>
        <input type="url" id="portfolio" name="portfolio"
               placeholder="https://yourportfolio.com (optional)">
    </fieldset>

    <!-- References -->
    <fieldset>
        <legend>References</legend>

        <label for="refname">Reference Name (optional):</label>
        <input type="text" id="refname" name="refname"
               placeholder="Enter reference name (optional)"><br><br>

        <label for="refcontact">Reference Contact (optional):</label>
        <input type="text" id="refcontact" name="refcontact"
               placeholder="Email or phone (optional)">
    </fieldset>

    <!-- Referral -->
    <fieldset>
        <legend>How Did You Hear About Us?</legend>

        <label for="referral">Select an option:</label>
        <select id="referral" name="referral">
            <option value="">-- Select --</option>
            <option value="website">Website</option>
            <option value="social">Social Media</option>
            <option value="friend">Friend / Referral</option>
            <option value="event">Event</option>
        </select>
    </fieldset>

    <!-- Contact Preference -->
    <fieldset>
        <legend>Preferred Contact Method</legend>

        <label><input type="radio" name="contact" value="email" required> Email</label>
        <label><input type="radio" name="contact" value="phone"> Phone</label>
    </fieldset>

    <!-- Declaration -->
    <fieldset>
        <legend>Declaration</legend>

        <label>
            <input type="checkbox" name="consent" value="1" required>
            I confirm that the information provided is accurate.
        </label><br>

        <label>
            <input type="checkbox" name="privacy" value="1" required>
            I agree to the organization's data privacy policy.
        </label><br>

        <label>
            <input type="checkbox" name="background" value="1">
            I consent to background verification if required.
        </label>
    </fieldset>

    <br>
    <input type="submit" value="Apply">
    <input type="reset" value="Reset Form">

</form>

</main>

<?php include("includes/footer.inc"); ?>

</body>
</html>
