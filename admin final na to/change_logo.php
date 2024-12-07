<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Define upload directories
    $marketplace_upload_dir = __DIR__ . '/../MarketplaceV3.6/uploads/';
    $admin_upload_dir = __DIR__ . '/uploads/';
    $login_module_upload_dir = __DIR__ . '/../login_module/uploads/';

    // Ensure all upload directories exist
    if (!is_dir($marketplace_upload_dir)) {
        mkdir($marketplace_upload_dir, 0777, true);
    }
    if (!is_dir($admin_upload_dir)) {
        mkdir($admin_upload_dir, 0777, true);
    }
    if (!is_dir($login_module_upload_dir)) {
        mkdir($login_module_upload_dir, 0777, true);
    }

    // Sanitize the file name
    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $_FILES['logo']['name']);
    $marketplace_upload_file = $marketplace_upload_dir . basename($filename);
    $admin_upload_file = $admin_upload_dir . basename($filename);
    $login_module_upload_file =  $login_module_upload_dir . basename($filename);

    $upload_ok = 1;

    // Check if the file is an image
    $check = getimagesize($_FILES['logo']['tmp_name']);
    if ($check === false) {
        $error_message = "File is not an image.";
        $upload_ok = 0;
    }

    // Allow certain file formats
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $image_file_type = strtolower(pathinfo($marketplace_upload_file, PATHINFO_EXTENSION));
    if (!in_array($image_file_type, $allowed_types)) {
        $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $upload_ok = 0;
    }

    if ($upload_ok === 0) {
        $error_message = $error_message ?? "Sorry, your file was not uploaded.";
    } else {
        // Move the uploaded file to the first location
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $marketplace_upload_file)) {
            // Copy the file to the second and third locations (admin and login_module)
            if (!copy($marketplace_upload_file, $admin_upload_file)) {
                $error_message = "File was uploaded to marketplace/uploads, but failed to copy to admin/uploads.";
            } else if (!copy($marketplace_upload_file, $login_module_upload_file)) {
                $error_message = "File was uploaded to marketplace/uploads, but failed to copy to login_module/uploads.";
            } else {
                $success_message = "The file " . htmlspecialchars(basename($filename)) . " has been uploaded to all directories.";

                // Save the relative path to logo_path.txt in all locations
                $relative_path = 'uploads/' . $filename;
                file_put_contents(__DIR__ . '/../MarketplaceV3.6/logo_path.txt', $relative_path); // marketplace
                file_put_contents(__DIR__ . '/logo_path.txt', $relative_path); // admin
                file_put_contents(__DIR__ . '/../login_module/logo_path.txt', $relative_path); // login_module
            }
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }

            //activity log ni josh mojica(nakikita ka nya, dapat masipag ka)

$activityType = "Update Logo";
$insert_sql = "INSERT INTO activity_log (user_name, activity_type, date_time) VALUES (?, ?, NOW())";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("ss", $userEmail, $activityType);
$insert_stmt->execute();
$insert_stmt->close();



        
        // Redirect back to the change_logo page after the upload
        header('Location: main.php?page=change_logo');
        exit();
    }
}
?>
  <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #712798;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #555;
        }

        input[type="file"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #712798;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: purple;
        }

        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
  <div class="container">
        <h1>Change Logo</h1>
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="logo">Select new logo:</label>
            <input type="file" name="logo" id="logo" required>
            <button type="submit">Upload</button>
        </form>
    </div>