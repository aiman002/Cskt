<?php
// Start the session
session_start();

// Check if form is submitted
if (isset($_POST['password'])) {
    // Define the correct password
    $correct_password = 'F016';  // Replace with your actual password

    // Validate the submitted password
    if ($_POST['password'] === $correct_password) {
        $_SESSION['authenticated'] = true;  // Set session variable
        header('Location: protected.php');  // Redirect to protected page
        exit();  // Ensure no further code is executed
    } else {
        $error = "Kata laluan salah!";  // Error message for incorrect password
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk</title>
    <style>
        body {
            font-family: 'San Francisco', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            position: relative;
            overflow: hidden; /* Ensure watermark is contained within the viewport */
        }
        .container {
            width: 100%;
            max-width: 400px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }
        .container:hover {
            transform: scale(1.02);
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.15);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 400;
        }
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            padding: 12px 20px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
        }
        button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .error {
            color: #e74c3c;
            margin-top: 15px;
            font-size: 14px;
        }
        .dark-mode body {
            background: #333;
            color: #fff;
        }
        .dark-mode .container {
            background-color: #444;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
        }
        .dark-mode h2 {
            color: #fff;
        }
        .dark-mode input[type="password"] {
            background-color: #555;
            border: 1px solid #666;
            color: #fff;
        }
        .dark-mode button {
            background-color: #007bff;
        }
        .dark-mode button:hover {
            background-color: #0056b3;
        }
        .toggle-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .toggle-btn:hover {
            background-color: #0056b3;
        }
        .logo {
            width: 100px; /* Adjust the width as needed */
            margin-bottom: 20px;
        }
        .watermark {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 20px;
            color: rgba(255, 255, 255, 0.5);
            pointer-events: none;
            user-select: none;
            font-weight: bold;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body>

    <button class="toggle-btn" id="toggleMode">Dark Mode</button>

    <div class="container">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b6/Pathfinder_Company%2C_10th_Para_Bde.svg/220px-Pathfinder_Company%2C_10th_Para_Bde.svg.png" alt="Logo" class="logo">
        <h2>Masukkan Kod Ahli </h2>
        <form method="post" action="index.php">
            <input type="password" name="password" placeholder="Kod Ahli" required>
            <button type="submit">Log Masuk</button>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        </form>
    </div>

    <!-- Watermark -->
    <div class="watermark">"Laman web ini sedang dibangunkan."</div>

    <script>
        const toggleButton = document.getElementById('toggleMode');
        const body = document.body;

        // Check if dark mode is already enabled
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            toggleButton.textContent = 'Light Mode';
        }

        toggleButton.addEventListener('click', () => {
            if (body.classList.contains('dark-mode')) {
                body.classList.remove('dark-mode');
                localStorage.setItem('darkMode', 'disabled');
                toggleButton.textContent = 'Dark Mode';
            } else {
                body.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'enabled');
                toggleButton.textContent = 'Light Mode';
            }
        });
    </script>

</body>
</html>
