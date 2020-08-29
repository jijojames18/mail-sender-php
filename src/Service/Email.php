<?php
namespace Service;

class Email
{
    public function sendEmail($toAddress, $subject, $body, $headers)
    {
        mail($toAddress, $subject, $body, $headers);
    }
}