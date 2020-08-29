<?php
namespace CustomException;

use \Exception as Exception;

class DocumentNotFoundException extends Exception {
    const ERROR_CODE = 103;
    const ERROR_MESSAGE = "Website url is not registered with the service";

    public function getErrorResponse() {
        return json_encode(array(
            "error-code" => self::ERROR_CODE,
            "error-message" => self::ERROR_MESSAGE
        ));
    }
}