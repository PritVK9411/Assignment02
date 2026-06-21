<?php
// ============================================================
//  apply.php — PHP version of the apply form for The Buddies™
//  Posts to process_eoi.php for full server-side validation.
//  All HTML5 client-side validation attributes are intentionally
//  omitted/disabled — validation happens entirely server-side.
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

<header>
    <h1>Join Our Non Profit Team</h1>
    <p>Make an impact. Apply today.</p>

    <!-- Navigation -->
    <nav>
        <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="jobs.html">Jobs</a></li>
            <li><a href="apply.html">Apply</a></li>
            <li><a href="about.html">About</a></li>
        </ul>
    </nav>
</header>

<!-- novalidate disables all HTML5 client-side validation; all validation is done server-side in process_eoi.php -->
<form action="process_eoi.php" method="post" enctype="multipart/form-data" novalidate>

    <!-- Job Reference -->
    <fieldset>
        <legend>Job Reference</legend>

        <label for="jobref">Job Reference Number (exactly 5 alphanumeric characters):</label>
        <input type="text" id="jobref" name="jobref" maxlength="5" placeholder="e.g. IT101"><br><br>
    </fieldset>

    <!-- Personal Information -->
    <fieldset>
        <legend>Personal Information</legend>

        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname"><br><br>

        <label for="dob">Date of Birth (dd/mm/yyyy):</label>
        <input type="text" id="dob" name="dob" placeholder="dd/mm/yyyy"><br><br>

        <fieldset>
            <legend>Gender</legend>
            <label><input type="radio" name="gender" value="male"> Male</label>
            <label><input type="radio" name="gender" value="female"> Female</label>
            <label><input type="radio" name="gender" value="nonbinary"> Non-binary</label>
            <label><input type="radio" name="gender" value="prefer-not"> Prefer not to say</label>
        </fieldset><br>

        <label for="email">Email Address:</label>
        <input type="text" id="email" name="email"><br><br>

        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone"><br><br>
    </fieldset>

    <!-- Address -->
    <fieldset>
        <legend>Address</legend>

        <label for="street">Street Address (max 40 characters):</label>
        <input type="text" id="street" name="street" maxlength="40" placeholder="123 Volunteer Lane"><br><br>

        <label for="suburb">Suburb / Town (max 40 characters):</label>
        <input type="text" id="suburb" name="suburb" maxlength="40" placeholder="Melbourne"><br><br>

        <label for="state">State:</label>
        <select id="state" name="state">
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
        <input type="text" id="postcode" name="postcode" maxlength="4" placeholder="3000"><br><br>
    </fieldset>

    <!-- Position -->
    <fieldset>
        <legend>Position Applied For</legend>

        <label for="position">Select Position:</label>
        <select id="position" name="position">
            <option value="">-- Please Select --</option>
            <option value="IT101">IT101 — IT Helpdesk Volunteer</option>
            <option value="FR202">FR202 — Fundraising Officer</option>
            <option value="SM303">SM303 — Social Media Manager</option>
        </select><br><br>

        <label>Position Type:</label><br>
        <label><input type="radio" name="type" value="volunteer"> Volunteer</label>
        <label><input type="radio" name="type" value="part-time"> Part-Time</label>
        <label><input type="radio" name="type" value="full-time"> Full-Time</label>
    </fieldset>

    <!-- Motivation -->
    <fieldset>
        <legend>Motivation</legend>

        <label for="motivation">Why do you want to join our organization?</label><br>
        <textarea id="motivation" name="motivation" rows="4" cols="40"></textarea>
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
        <input type="text" id="experience" name="experience"><br><br>

        <label for="description">Describe Your Experience (optional):</label><br>
        <textarea id="description" name="description" rows="4" cols="40"
          placeholder="Briefly describe your experience (optional)"></textarea>
    </fieldset>

    <!-- Availability -->
    <fieldset>
        <legend>Availability</legend>

        <label for="date">Available Start Date:</label>
        <input type="text" id="date" name="date" placeholder="YYYY-MM-DD"><br><br>

        <label for="time">Preferred Interview Time:</label>
        <input type="text" id="time" name="time" placeholder="HH:MM"><br><br>

        <label>Days Available:</label><br>
        <label><input type="checkbox" name="days[]" value="monday"> Monday</label>
        <label><input type="checkbox" name="days[]" value="tuesday"> Tuesday</label>
        <label><input type="checkbox" name="days[]" value="wednesday"> Wednesday</label>
        <label><input type="checkbox" name="days[]" value="thursday"> Thursday</label>
        <label><input type="checkbox" name="days[]" value="friday"> Friday</label>
        <label><input type="checkbox" name="days[]" value="weekend"> Weekend</label><br><br>

        <label for="hours">Hours per week you can commit:</label>
        <input type="text" id="hours" name="hours">
    </fieldset>

    <!-- Documents -->
    <fieldset>
        <legend>Upload Documents</legend>

        <label for="cv">Upload CV:</label>
        <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx"><br><br>

        <label for="coverletter">Upload Cover Letter:</label>
        <input type="file" id="coverletter" name="coverletter" accept=".pdf,.doc,.docx">
    </fieldset>

    <!-- Professional Links -->
    <fieldset>
        <legend>Professional Links</legend>

        <label for="linkedin">LinkedIn Profile (optional):</label>
        <input type="text" id="linkedin" name="linkedin"
               placeholder="https://linkedin.com/in/username (optional)"><br><br>

        <label for="github">GitHub Profile (optional):</label>
        <input type="text" id="github" name="github"
               placeholder="https://github.com/username (optional)"><br><br>

        <label for="portfolio">Portfolio Website (optional):</label>
        <input type="text" id="portfolio" name="portfolio"
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

        <label><input type="radio" name="contact" value="email"> Email</label>
        <label><input type="radio" name="contact" value="phone"> Phone</label>
    </fieldset>

    <!-- Declaration -->
    <fieldset>
        <legend>Declaration</legend>

        <label>
            <input type="checkbox" name="consent" value="1">
            I confirm that the information provided is accurate.
        </label><br>

        <label>
            <input type="checkbox" name="privacy" value="1">
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

</body>
</html>