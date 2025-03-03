<?php
// Start session
session_start();

// Initialize variables to store success message and redirection status
$successMessage = '';
$redirectToLanding = false;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Replace with your database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "urbanlink";

    // Retrieve the data from the form
    $reportingUserID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
    $reportingUserName = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $reportingUserPhone = isset($_SESSION['user_phone']) ? $_SESSION['user_phone'] : '';
    $reportedUserID = $_POST['reported_user_id'];
    $reportedUserName = $_POST['reported_user_name'];
    $reportedUserPhone = $_POST['reported_user_phone'];
    $reportedUserLoc = isset($_POST['reported_user_loc']) ? $_POST['reported_user_loc'] : '';
    $reportReason = isset($_POST['report_reason']) ? $_POST['report_reason'] : '';
    $reportType = isset($_POST['report_type']) ? $_POST['report_type'] : '';
    $reportDate = date("Y-m-d H:i:s");

    // Generate a unique ID
    $uniqueID = "urid" . uniqid();


    // Check if all required fields are filled
    if (empty($reportedUserID) || empty($reportedUserName) || empty($reportedUserPhone) || empty($reportReason) || empty($reportType)) {
        // Display the alert message only when the form is submitted and required fields are empty
        if (isset($_POST['submit'])) {
            echo '<script>alert("Please fill all the required fields before submitting the report.");</script>';
        }
    } else {
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare and execute the SQL statement to insert the report data
        $stmt = $conn->prepare("INSERT INTO reported_users (user_report_id,reporting_user_id, reporting_user_name, reporting_user_phone, reported_user_id, reported_user_name, reported_user_phone, reported_user_loc, report_reason, report_type, report_date) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $uniqueID, $reportingUserID, $reportingUserName, $reportingUserPhone, $reportedUserID, $reportedUserName, $reportedUserPhone, $reportedUserLoc, $reportReason, $reportType, $reportDate);

        if ($stmt->execute()) {
            // Report submitted successfully
            $successMessage = 'Report submitted successfully. 🚩You will be redirected to your Home Page.🚩';
            // Empty the form fields after successful submission
            $reportedUserID = $reportedUserName = $reportedUserPhone = $reportedUserLoc = $reportReason = $reportType = '';
            // Set the redirection flag to true
            $redirectToLanding = true;
        } else {
            // Handle the error, e.g., display an error message or log it
            echo "Error: " . $conn->error;
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    }
}
?>


<!-- Rest of the HTML remains unchanged -->


<!DOCTYPE html>
<html>
<title>Report User</title>
<link rel="icon" href="images/urbanlink-logo.png" type="image/icon type">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
    body {
        font-family: Arial, sans-serif;
        background-image: url("./images/danger_report.jpg");
        background-size: cover;
        background-position: 0 -130px;
        background-repeat: no-repeat;
        margin: 0;
        padding: 0;
        display: block;
    }

    .go-back {
        padding: 10px;
        text-align: center;
        background-color: orange;
        cursor: pointer;
        color: white;
        text-decoration: none;
    }

    .go-back:hover {
        background-color: #0088cc;
        transition: 0.2s linear;
    }

    .back-button {
        position: relative;
        top: 30px;
        margin-left: 10px;
    }

    .dimmed-red-background {
        background-color: #ffcccc;
    }

    .form-container {
        width: 800px;
        margin: 20px auto;
        background-color: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(10px);
        padding: 20px;
        border-radius: 10px;
        height: 550px;
        position: relative;
        top: 50px;
        border-left: 2px solid #333;
        border-bottom: 2px solid #333;
    }

    .form-container h1 {
        text-align: center;
        color: #333;
    }

    .form-container label {
        display: block;
        font-weight: bold;
        margin-top: 10px;
    }

    .form-container input,
    .form-container textarea,
    .form-container select {
        width: 90%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .form-container select {
        margin-bottom: 15px;
    }

    .form-container textarea {
        resize: vertical;
        height: 100px;
    }

    .form-container input[type="submit"] {
        background-color: #3090C7;
        color: #fff;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        padding: 10px 20px;
        width: 50%;
        position: relative;
        left: 30%;
        top: 50px;
    }

    .form-container input[type="submit"]:hover {
        background-color: #e57373;
    }

    /* Media queries */
    @media (max-width: 1200px) {
        .form-container {
            max-width: 500px;
        }
    }

    @media (max-width: 800px) {
        .form-container {
            max-width: 400px;
        }
    }

    @media (max-width: 600px) {
        .form-container {
            max-width: 90%;
        }
    }

    @media (max-width: 400px) {
        .form-container {
            max-width: 100%;
            padding: 10px;
        }
    }

    /* Form layout */
    .form-container .left-fields {
        width: 45%;
        float: left;
    }

    .form-container .right-fields {
        width: 45%;
        float: right;
    }

    /* Clear floats */
    .form-container:after {
        content: "";
        display: table;
        clear: both;
    }
</style>

<head>
    <!-- Head content remains unchanged -->
</head>

<body>

    <div class="form-container">
        <h1>Report User</h1>
        <form method="post">
            <div class="left-fields">
                <!-- Autofilled fields -->
                <label for="reporting_user_id">Reporting User ID:</label>
                <input type="text" name="reporting_user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>" readonly>

                <label for="reporting_user_name">Reporting User Name:</label>
                <input type="text" name="reporting_user_name" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>" readonly>

                <!-- <label for="reporting_user_phone">Reporting User Phone:</label>
                <input type="text" name="reporting_user_phone"> -->

                <label for="reporting_user_phone">Reporting User Phone:</label>
                <input type="text" name="reporting_user_phone" value="<?php echo isset($_SESSION['user_phone']) ? $_SESSION['user_phone'] : ''; ?>" required>

                <label for="reported_user_id">Reported User ID:</label>
                <input type="text" class="dimmed-red-background" name="reported_user_id" value="<?php echo $reportedUserID; ?>" required>

                <label for="reported_user_name">Reported User Name:</label>
                <input type="text" class="dimmed-red-background" name="reported_user_name" value="<?php echo $reportedUserName; ?>" required>
            </div>

            <div class="right-fields">
                <label for="reported_user_phone">Reported User Phone:</label>
                <input type="text" class="dimmed-red-background" name="reported_user_phone" value="<?php echo $reportedUserPhone; ?>" required>

                <label for="reported_user_loc">Reported User Location:</label>
                <select name="reported_user_loc" class="dimmed-red-background" required>
                    <option value="" disabled selected>Select an option</option>
                    <option value="Gobichettipalayam">Gobichettipalayam</option>
                    <option value="Sathyamangalam">Sathyamangalam</option>
                    <!-- Add other location options as needed -->
                </select>

                <label for="report_reason">Report Reason:</label>
                <textarea name="report_reason" rows="4" cols="50" required></textarea>

                <label for="report_type">Report Type:</label>
                <select name="report_type" class="dimmed-red-background" required>
                    <option value="" disabled selected>Select Report Type</option>
                    <option value="Spam">Spam</option>
                    <option value="Abuse">Abuse</option>
                    <option value="Inappropriate Content">Inappropriate Content</option>
                    <option value="Fraud">Fraud</option>
                    <option value="Impersonation">Impersonation</option>
                    <option value="Privacy Violation">Privacy Violation</option>
                    <option value="Fake Account">Fake Account</option>
                    <option value="Cyber Bullying">Cyber Bullying</option>
                    <option value="Phishing">Phishing</option>
                    <option value="Identity Theft">Identity Theft</option>
                    <option value="Child Exploitation">Child Exploitation</option>
                    <option value="Stalking">Stalking</option>
                    <option value="Online Harassment">Online Harassment</option>
                    <option value="Scam">Scam</option>
                    <option value="Malware">Malware</option>
                    <option value="Hacking">Hacking</option>
                    <option value="Fraudulent Activity">Fraudulent Activity</option>
                    <option value="Hate Crime">Hate Crime</option>
                    <option value="Violence">Violence</option>
                    <option value="False Information">False Information</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <input type="submit" name="submit" value="Submit Report">
        </form>
    </div>

    <!-- Display the success message if the form is submitted successfully -->
    <?php
    if (!empty($successMessage)) {
        echo '<script>alert("' . $successMessage . '");</script>';
    }
    ?>

    <script>
        // JavaScript code to redirect after successful submission
        <?php
        if ($redirectToLanding) {
            echo 'setTimeout(function() {
window.location.href = "./public/public_user_landing.php";
            }, 3000);';
        }
        ?>
    </script>
</body>

</html>