<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload_dir = 'uploads/';
    $upload_file = $upload_dir . basename($_FILES['logo']['name']);
    $upload_ok = 1;

    // Check if file is an image
    $check = getimagesize($_FILES['logo']['tmp_name']);
    if ($check === false) {
        echo "File is not an image.";
        $upload_ok = 0;
    }

    // Allow certain file formats
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $image_file_type = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION));
    if (!in_array($image_file_type, $allowed_types)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $upload_ok = 0;
    }

    // Check if $upload_ok is set to 0 by an error
    if ($upload_ok === 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Try to upload file
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES['logo']['name'])) . " has been uploaded.";
            
            // Save new logo path in a config file or database
            file_put_contents('logo_path.txt', $upload_file);

        } else {
            echo "Sorry, there was an error uploading your file.";
        }
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