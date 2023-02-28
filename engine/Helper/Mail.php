<?php

namespace Engine\Helper;

use Engine\Core\Config\Config;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{

    /**
     * @param string $email
     * @param string $message
     * @param string $subject
     * @param array $smtp
     * @return bool
     * @throws Exception
     * @throws \Exception
     */
    public static function send(string $email, string $message, string $subject, array $smtp = [
        'Host' => '',
        'Port' => null,
        'Username' => '',
        'Password' => ''
    ]): bool
    {
        $mail = new PHPmailer();
        $mail->IsSMTP();
        $mail->IsHTML(true);
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = [
            'ssl' => Config::item('ssl', 'smtp')
        ];

        if (isset($smtp['Host']) && !empty($smtp['Host']) && isset($smtp['Port']) && !empty($smtp['Port'])) {
            $mail->Host = $smtp['Host'];
            $mail->Port = $smtp['Port'];
        } else {
            $mail->Host = Config::item('host', 'smtp');
            $mail->Port = Config::item('port', 'smtp');
        }

        if (isset($smtp['Username']) && !empty($smtp['Username']) && isset($smtp['Password']) && !empty($smtp['Password'])) {
            $username = $smtp['Host'];
            $mail->Password = $smtp['Port'];
        } else {
            $username = Config::item('username', 'smtp');
            $mail->Password = Config::item('password', 'smtp');;
        }

        $mail->Username = $username;
        $mail->setFrom($username, Config::item('baseUrl'));
        $mail->Subject = $subject;
        $mail->addAddress($email);
        $body = "<p>$message</p>";
        $mail->msgHTML($body);
        
        if ($mail->send()) {
            return true;
        }
        return false;
    }
}