<?php
session_start();

// Set inactivity timeout (30 seconds)
$timeout = 30;

// Check for inactivity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    // If the session has expired, log out the user
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Redirect if not authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: index.php');
    exit();
}

// Directory to store files
$media_dir = 'media/';

// Create directory if not exists
if (!is_dir($media_dir)) {
    mkdir($media_dir, 0755, true);
}

// Get list of files from directory
$media_files = array_diff(scandir($media_dir), array('.', '..'));

// Handle file upload
$upload_status = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $file_name = basename($file['name']);
        $file_tmp = $file['tmp_name'];
        $file_error = $file['error'];

        if ($file_error === UPLOAD_ERR_OK) {
            if (move_uploaded_file($file_tmp, $media_dir . $file_name)) {
                $upload_status = "File successfully uploaded.";
                $_SESSION['upload_status'] = $upload_status; // Save status in session
                // Redirect with a query string to avoid redirection to index.php
                header('Location: ' . $_SERVER['PHP_SELF'] . '?status=' . urlencode($upload_status));
                exit();
            } else {
                $upload_status = "Failed to move the file to the media directory.";
            }
        } else {
            $upload_status = "Error occurred during file upload. Error code: " . $file_error;
        }
    }

    if (isset($_POST['delete_file']) && isset($_POST['password'])) {
        $password = $_POST['password'];
        $correct_password = '123'; // Correct password

        if ($password === $correct_password) {
            $file_to_delete = basename($_POST['delete_file']);
            $file_path = $media_dir . $file_to_delete;

            if (file_exists($file_path)) {
                if (unlink($file_path)) {
                    $upload_status = "File successfully deleted.";
                    $_SESSION['upload_status'] = $upload_status; // Save status in session
                    // Redirect with a query string to avoid redirection to index.php
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?status=' . urlencode($upload_status));
                    exit();
                } else {
                    $upload_status = "Failed to delete the file.";
                }
            } else {
                $upload_status = "File does not exist.";
            }
        } else {
            $upload_status = "Incorrect password.";
        }
    }
}

// Get status message from query parameter
$status_message = isset($_GET['status']) ? $_GET['status'] : '';

// Handle file search
$search_query = isset($_POST['search_query']) ? strtolower(trim($_POST['search_query'])) : '';
if ($search_query) {
    $media_files = array_filter($media_files, function($file) use ($search_query) {
        return strpos(strtolower($file), $search_query) !== false;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protected Area</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #ffffff; /* White background */
            color: #333; /* Dark text for contrast */
            border-bottom: 3px solid #007bff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Light shadow for depth */
        }
        .header img {
            height: 50px;
            margin-right: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        .navbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 10px;
            background-color: #ffffff;
            border-bottom: 2px solid #007bff;
        }
        .navbar a {
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .navbar a:hover {
            background-color: #007bff;
            color: #fff;
        }
        .content {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }
        .status-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .upload-form, .search-form {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .upload-form input[type="file"], 
        .search-form input[type="text"] {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            margin-right: 10px;
            font-size: 16px;
        }
        .upload-form button, 
        .search-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .upload-form button:hover, 
        .search-form button:hover {
            background-color: #0056b3;
        }
        .media-container {
            border: 1px solid #ced4da;
            padding: 15px;
            border-radius: 5px;
            background-color: #fff;
            margin-bottom: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .media-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .media-link {
            color: #007bff;
            text-decoration: none;
            margin-right: 10px;
            transition: color 0.3s;
        }
        .media-link:hover {
            color: #0056b3;
        }
        .delete-btn {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .confirm-dialog {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ced4da;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        .confirm-dialog p {
            margin: 0 0 15px;
            font-size: 16px;
        }
        .confirm-dialog input {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            margin-bottom: 15px;
            width: 100%;
            box-sizing: border-box;
            font-size: 16px;
        }
        .confirm-dialog button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .confirm-dialog #confirm-delete-btn {
            background-color: #e74c3c;
            color: #fff;
        }
        .confirm-dialog #confirm-delete-btn:hover {
            background-color: #c0392b;
        }
        .confirm-dialog #cancel-delete-btn {
            background-color: #ccc;
        }
        .confirm-dialog #cancel-delete-btn:hover {
            background-color: #999;
        }
    </style>
    <script>
        // JavaScript to handle inactivity
        let timeout;
        function resetTimer() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                window.location.href = 'logout.php'; // Redirect to logout page after inactivity
            }, 30000); // 30 seconds
        }

        document.onload = resetTimer;
        document.onmousemove = resetTimer;
        document.onkeypress = resetTimer;
        document.onclick = resetTimer;
        document.onscroll = resetTimer;
    </script>
</head>
<body>
    <div class="header">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3c/Malaysian_Pathfinder_Badge.svg/800px-Malaysian_Pathfinder_Badge.svg.png" alt="Malaysian Pathfinder Badge">
        <h1>(R&D) SEDANG DIJALANKAN OLEH PEGAWAI</h1>
    </div>
    <div class="navbar">
        <div class="navbar-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="content">
        <p> Maklumat Sulit.</p>

        <!-- File upload form -->
        <form class="upload-form" method="post" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <button type="submit">Upload File</button>
        </form>

        <!-- File search form -->
        <form class="search-form" method="post">
            <input type="text" name="search_query" placeholder="Search files..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>

        <?php if ($status_message): ?>
            <div class="status-message"><?php echo htmlspecialchars($status_message); ?></div>
        <?php endif; ?>

        <?php if (!empty($media_files)): ?>
            <?php foreach ($media_files as $file): ?>
                <?php $file_path = $media_dir . $file; ?>
                <div class="media-container">
                    <div class="media-title"><?php echo htmlspecialchars($file); ?></div>
                    <a class="media-link" href="<?php echo htmlspecialchars($file_path); ?>" target="_blank">View</a>
                    <a class="media-link" href="<?php echo htmlspecialchars($file_path); ?>" download>Download</a>
                    <!-- Form to delete file with password -->
                    <button class="delete-btn" data-file="<?php echo htmlspecialchars($file); ?>">Delete File</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No files found in the directory.</p>
        <?php endif; ?>

    </div>

    <!-- Confirmation dialog for file deletion -->
    <div id="confirm-dialog" class="confirm-dialog">
        <p>Enter password to confirm file deletion:</p>
        <input type="password" id="password" placeholder="Password">
        <button id="confirm-delete-btn">Delete</button>
        <button id="cancel-delete-btn" class="cancel-btn">Cancel</button>
    </div>

    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', () => {
                const file = button.getAttribute('data-file');
                document.getElementById('confirm-dialog').style.display = 'block';
                document.getElementById('confirm-delete-btn').onclick = () => {
                    const password = document.getElementById('password').value;
                    if (password === '123') { // Check password
                        document.body.insertAdjacentHTML('beforeend', `
                            <form id="delete-form" method="post" style="display:none;">
                                <input type="hidden" name="delete_file" value="${file}">
                                <input type="hidden" name="password" value="${password}">
                            </form>
                        `);
                        document.getElementById('delete-form').submit();
                    } else {
                        alert('Incorrect password.');
                    }
                };
                document.getElementById('cancel-delete-btn').onclick = () => {
                    document.getElementById('confirm-dialog').style.display = 'none';
                };
            });
        });
    </script>
</body>
</html>
