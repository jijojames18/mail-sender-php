<?php
namespace Controller;

use CustomException\EmptyWebsiteException;
use CustomException\CaptchaException;
use CustomException\DocumentNotFoundException;

use Service\ReCaptcha;
use Service\Firebase;
use Service\Email;

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
                    header('HTTP/1.1 200 OK');
                } catch(EmptyWebsiteException | CaptchaException | DocumentNotFoundException $e) {
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
        $data = json_decode(file_get_contents('php://input'), true);
        $website = isset($data["site"]) ? htmlentities($data["site"]) : '';
        $sanitizedWebsite = filter_var($website,  FILTER_SANITIZE_URL);
        if ($sanitizedWebsite === '' || $sanitizedWebsite === null)
        {
            throw new EmptyWebsiteException();
        }

        $captcha = isset($data["captcha"]) ? htmlentities($data["captcha"]) : '';
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

        $firebase = new Firebase($sanitizedWebsite);
        $websiteDetails = $firebase->getDocument();
        if (!isset($websiteDetails["email"]) || !isset($websiteDetails["mail-template"]))
        {
            throw new DocumentNotFoundException();
        }

        $email = $websiteDetails["email"];
        $mailTemplate = $websiteDetails["mail-template"];

        $formData = isset($data["formData"]) ? $data["formData"] : array();
        $customSubject = "";

        foreach ($formData as $key => $value)
        {
            $formData[$key] = htmlentities(filter_var($formData[$key], FILTER_UNSAFE_RAW));
            if ($key === "subject")
            {
                $customSubject = $formData[$key];
            }
            $mailTemplate = str_replace('${' . $key . '}', $formData[$key], $mailTemplate);
        }

        $subject = isset($customSubject) ? $customSubject : "Contact form";

        $headers = 'From: ' . EMAIL_FROM_ADDRESS_NAME  . ' <' . EMAIL_FROM_ADDRESS . '>' . PHP_EOL .
			'Reply-To: ' . EMAIL_FROM_ADDRESS_NAME  . ' <' . EMAIL_FROM_ADDRESS . '>' . PHP_EOL .
            'X-Mailer: PHP/' . phpversion();

        $mailService = new Email();
        $mailService->sendEmail($email, $subject, $mailTemplate, $headers);
    }
}