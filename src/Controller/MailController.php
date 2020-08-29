<?php
namespace Controller;

use CustomException\EmptyWebsiteException;
use CustomException\CaptchaException;

use Service\ReCaptcha;

class MailController
{
    private $requestMethod = null;

    public function __construct($requestMethod)
    {
        $this->requestMethod = $requestMethod;
    }

    public function processRequest()
    {
        $response = array();

        switch ($this->requestMethod)
        {
            case 'POST':
                try {
                    $result = $this->process();
                    echo json_encode(array(
                        'result' => $result
                    ));
                    header('HTTP/1.1 200 OK');
                } catch(EmptyWebsiteException | CaptchaException $e) {
                    echo $e->getErrorResponse();
                    header('HTTP/1.1 400 Bad Request');
                } catch(Exception $e) {
                    echo json_encode(array(
                        "error-code" => 500,
                        "error-message" => "An internal error occurred"
                    ));
                    header('HTTP/1.1 500 Internal Server Error');
                }
            break;
            default:
                header('HTTP/1.1 404 Not Found');
        }
    }

    private function process()
    {
        $website = isset($_POST["site"]) ? $_POST["site"] : '';
        $sanitizedWebsite = filter_var($website,  FILTER_SANITIZE_URL);
        if ($sanitizedWebsite === '' || $sanitizedWebsite === null)
        {
            throw new EmptyWebsiteException();
        }

        $captcha = isset($_POST["captcha"]) ? $_POST["captcha"] : '';
        $sanitizedCaptcha = filter_var($captcha,  FILTER_SANITIZE_STRING);
        if ($sanitizedCaptcha === '' || $sanitizedCaptcha === null)
        {
            throw new CaptchaException();
        }

        $reCaptcha = new ReCaptcha($captcha);
        $reCaptchaResult = $reCaptcha->verifyCaptcha();
        if (!json_decode($reCaptchaResult, true)["success"])
        {
            throw new CaptchaException();
        }


    }
}