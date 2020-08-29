<?php
namespace Service;

class ReCaptcha
{
    const CAPTCHA_VERIFY_ENDPOINT = "https://www.google.com/recaptcha/api/siteverify";

    private $captcha = null;

    public function __construct($captcha)
    {
        $this->captcha = $captcha;
    }

    public function verifyCaptcha()
    {
        $ch = $this->initCurl();
        return curl_exec($ch);
    }

    private function initCurl()
    {
        $fields = [
            'secret '  => RECAPTCHA_SITE_KEY,
            'response' => $this->captcha,
        ];
        $fields_string = http_build_query($fields);

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, self::CAPTCHA_VERIFY_ENDPOINT);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        return $ch;
    }
}