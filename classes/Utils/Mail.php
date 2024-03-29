<?php

namespace Utils;

use PHPMailer;
use phpmailerException;
use RuntimeException;

/**
 * Class Mail
 *
 * Simple wrapper to PHPMailer
 */
class Mail
{
    /**
     * @var array
     */
    private static $_instances = array();

    /**
     * Get a instance
     *
     * @param string $addressPrefix
     * @return PHPMailer
     * @throws phpmailerException, RuntimeException
     */
    public static function getInstance($addressPrefix) {
        if (!isset(self::$_instances[$addressPrefix])) {
            // setup mail
            $dotenv = new \Dotenv\Dotenv(dirname(dirname(__DIR__)));
            $dotenv->load();
            $dotenv->required(array('MAIL_HOST', 'MAIL_USER', 'MAIL_PASS', "${addressPrefix}_TO"))->notEmpty();

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USER'];
            $mail->Password = $_ENV['MAIL_PASS'];
            $mail->SMTPSecure = isset($_ENV['MAIL_SECURE']) ? $_ENV['MAIL_SECURE'] : 'ssl';
            $mail->Port = isset($_ENV['MAIL_PORT']) ? $_ENV['MAIL_PORT'] : 465;

            // setup from and to
            $mail->setFrom($_ENV['MAIL_USER'], isset($_ENV['MAIL_USER_DISPLAY']) ? $_ENV['MAIL_USER_DISPLAY'] : '');
            $recipients = explode(',', $_ENV["${addressPrefix}_TO"]);
            if (count($recipients) === 0) {
                throw new RuntimeException("No recipient defined in ${addressPrefix}_TO!");
            }
            foreach ($recipients as $recipient) {
                $mail->addAddress($recipient);
            }
            if (count($mail->getToAddresses()) === 0) {
                throw new RuntimeException('No one to send mail to!');
            }

            // set CCs
            $ccRecipients = isset($_ENV["${addressPrefix}_CC"]) ? explode(',', $_ENV["${addressPrefix}_CC"]) : array();
            foreach ($ccRecipients as $recipient) {
                $mail->addCC($recipient);
            }

            // set BCCs
            $bccRecipients = isset($_ENV["${addressPrefix}_BCC"]) ? explode(',', $_ENV["${addressPrefix}_BCC"]) : array();
            foreach ($bccRecipients as $recipient) {
                $mail->addBCC($recipient);
            }

            self::$_instances[$addressPrefix] = $mail;
        }

        return self::$_instances[$addressPrefix];
    }
}
