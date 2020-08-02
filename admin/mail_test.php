<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/php/secret_config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function send_email($to_email, $subject, $message){
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = $GLOBALS['EMAIL_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $GLOBALS['EMAIL_USER'];
        $mail->Password   = $GLOBALS['EMAIL_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        //Recipients
        $mail->setFrom($GLOBALS['EMAIL_FROM'], $GLOBALS['EMAIL_FROM_NAME']);
        $mail->addAddress($to_email);
        $mail->addReplyTo($GLOBALS['EMAIL_FROM'], $GLOBALS['EMAIL_FROM_NAME']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

send_email("gregzzmail@gmail.com", "Test email", "This is a test email sent with PHP.");

?>
