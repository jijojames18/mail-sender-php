<?php
namespace Model;

class Captcha
{
    const SUCCESS_KEY = 'success';
    const ERROR_CODE_KEY = 'error-codes';

    private $success = null;
    private $errorCodes = array();

    public function __construct($result)
    {
        $this->success = $result[self::SUCCESS_KEY];
        $this->errorCodes = $result[self::ERROR_CODE_KEY];
    }

    public function isSuccess()
    {
        return $this->success === true;
    }
}
