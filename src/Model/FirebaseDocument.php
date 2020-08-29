<?php
namespace Model;

class FirebaseDocument
{
    const EMAIL_KEY = 'email';
    const MAIL_TEMPLATE_KEY = 'mail-template';

    private $email = null;
    private $mailTemplate = null;

    public function __construct($result)
    {
        $this->email = isset($result[self::EMAIL_KEY]) ? $result[self::EMAIL_KEY] : null;
        $this->mailTemplate = isset($result[self::MAIL_TEMPLATE_KEY]) ? $result[self::MAIL_TEMPLATE_KEY] : null;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getMailTemplate()
    {
        return $this->mailTemplate;
    }
}
