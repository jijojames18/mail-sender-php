<?php
namespace CustomException;

use \Exception as Exception;

class CaptchaException extends Exception {
    const ERROR_CODE = 102;
    const ERROR_MESSAGE = "Captcha verification failed";

    public function getErrorResponse() {
        return json_encode(array(
            "error-code" => self::ERROR_CODE,
            "error-message" => self::ERROR_MESSAGE
        ));
    }
}