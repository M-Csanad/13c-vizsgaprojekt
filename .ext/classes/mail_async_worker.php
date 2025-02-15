<?php
include_once 'mail.php';

$recipient = json_decode(base64_decode($argv[1]), true);
$mail = json_decode(base64_decode($argv[2]), true);

$mailer = new Mail();
$mailer->sendTo($recipient, $mail, false);