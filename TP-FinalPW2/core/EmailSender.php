<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class EmailSender {

    function send($email, $body){
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'preguntastico.app@gmail.com';
            $mail->Password = 'xslyjwsvtwpchbyq';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('preguntastico.app@gmail.com', 'Preguntastico');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Verifica tu cuenta en Preguntastico';
            $mail->Body = $body;

            $mail->send();
        } catch (Exception $e) {
            error_log("Error enviando correo: {$mail->ErrorInfo}");
        }
    }
}
