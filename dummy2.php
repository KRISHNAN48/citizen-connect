<!DOCTYPE html>
<html>

<head>
    <title>Update NGO Post</title>
    <link rel="icon" href="../images/urbanlink-logo.png" type="image/icon type">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url("../images/5337080_2776819.jpg");
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
        }

        .container {
            width: 600px;
            margin: 50px auto;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 10px;
            position: relative;
            top: 60px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333333;
            margin-bottom: 20px;
        }

        .success-message {
            color: #008000;
            margin-bottom: 10px;
        }

        .error-message {
            color: #FF0000;
            margin-bottom: 10px;
        }

        form {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #cccccc;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #0088cc;
            color: #ffffff;
            border: none;
            cursor: pointer;
            border-radius: 3px;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #006699;
        }

        /*  600 to 900 */
        @media only screen and (min-width:601px) and (max-width: 900px) {
            .container {
                /* margin: 20px; */
                width: 80%;
                height: 800px;
            }

            form {
                max-width: 100%;
            }
        }

        @media only screen and (max-width: 600px) {
            .container {
                position: relative;
                top: 500px;
                width: 800px;
                height: 800px;
            }

            form {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['ngo_username']) || !isset($_SESSION['ngo_id'])) {
        header('Location: ngo_login.php');
        exit();
    }

    // Include database configuration
    require_once 'ngop_config.php';

    // Initialize variables
    $post_id = $_GET['post_id'] ?? null;
    $username = '';
    $location = '';
    $phone = '';
    $description = '';

    // Fetch post details from the database
    if (!empty($post_id)) {
        $query = "SELECT * FROM ngo_post_details WHERE ngo_post_id = :post_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        // Assign values to variables
        $username = $post['ngo_user_name'];
        $location = $post['ngo_org_loc'];
        $phone = $post['ngo_user_phone'];
        $description = $post['ngo_post_desc'];
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update'])) {
            // Retrieve updated values from the form
            $newUsername = $_POST['username'];
            $newLocation = $_POST['location'];
            $newPhone = $_POST['phone'];
            $newDescription = $_POST['description'];

            // Update the post in the database
            $query = "UPDATE ngo_post_details SET ngo_user_name = :username, ngo_org_loc = :location, ngo_user_phone = :phone, ngo_post_desc = :description WHERE ngo_post_id = :post_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $newUsername);
            $stmt->bindParam(':location', $newLocation);
            $stmt->bindParam(':phone', $newPhone);
            $stmt->bindParam(':description', $newDescription);
            $stmt->bindParam(':post_id', $post_id);
            $stmt->execute();

            // Redirect to the previous page with a success message
            $_SESSION['success_message'] = "Post updated successfully.";
            header("Location: ngo_post_manage.php");
            exit();
        }
    }
    ?>

    <div class="container">
        <h2>Update NGO Post</h2>

        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="success-message"><?php echo $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="error-message"><?php echo $_SESSION['error_message']; ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?php echo $location; ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo $phone; ?>" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo $description; ?></textarea>
            </div>
            <div class="form-group">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <input type="submit" name="update" value="Update">
            </div>
        </form>
    </div>
    <script>
        function validatePhoneNumber(input) {
            // Remove non-numeric characters
            input.value = input.value.replace(/\D/g, '');

            // Limit the length to 10 characters
            if (input.value.length > 10) {
                input.value = input.value.slice(0, 10);
            }
        }
    </script>

</body>

</html>