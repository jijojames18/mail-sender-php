<?php
namespace CustomException;

use \Exception as Exception;

class EmptyWebsiteException extends Exception {
    const ERROR_CODE = 101;
    const ERROR_MESSAGE = "Website url is not present in request";

    public function getErrorResponse() {
        return json_encode(array(
            "error-code" => self::ERROR_CODE,
            "error-message" => self::ERROR_MESSAGE
        ));
    }
}