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
    const REQUEST_SITE_KEY = 'site';
    const REQUEST_CAPTCHA_KEY = 'captcha';
    const REQUEST_FORM_DATA_KEY = 'formData';

    const EMAIL_KEY = 'email';
    const SUBJECT_KEY = 'subject';

    const DEFAULT_SUBJECT = 'Contact form';

    private $requestMethod = null;

    public function __construct($requestMethod)
    {
        $this->requestMethod = $requestMethod;
    }

    public function processRequest()
    {
        $response = array();

        switch ($this->requestMethod) {
            case 'POST':
                try {
                    $result = $this->process();
                    header('HTTP/1.1 200 OK');
                } catch (EmptyWebsiteException | CaptchaException | DocumentNotFoundException $e) {
                    header('HTTP/1.1 400 Bad Request');
                    echo $e->getErrorResponse();
                } catch (Exception $e) {
                    header('HTTP/1.1 500 Internal Server Error');
                    echo json_encode(array(
                        "error-code" => 500,
                        "error-message" => "An internal error occurred"
                    ));
                }
            break;
            default:
                header('HTTP/1.1 404 Not Found');
        }
    }

    private function process()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $website = isset($data[self::REQUEST_SITE_KEY]) ? htmlentities($data[self::REQUEST_SITE_KEY]) : '';
        $sanitizedWebsite = filter_var($website, FILTER_SANITIZE_URL);
        if ($sanitizedWebsite === '' || $sanitizedWebsite === null) {
            throw new EmptyWebsiteException();
        }

        $captcha = isset($data[self::REQUEST_CAPTCHA_KEY]) ? htmlentities($data[self::REQUEST_CAPTCHA_KEY]) : '';
        $sanitizedCaptcha = filter_var($captcha, FILTER_SANITIZE_STRING);
        if ($sanitizedCaptcha === '' || $sanitizedCaptcha === null) {
            throw new CaptchaException();
        }

        $reCaptcha = new ReCaptcha($captcha);
        $reCaptchaResult = $reCaptcha->verifyCaptcha();
        if (!$reCaptchaResult->isSuccess()) {
            throw new CaptchaException();
        }

        $firebase = new Firebase($sanitizedWebsite);
        $website = $firebase->getDocument();
        if ($website->getEmail() === null || $website->getMailTemplate() === null) {
            throw new DocumentNotFoundException();
        }

        $email = $website->getEmail();
        $mailTemplate = $website->getMailTemplate();

        $formData = isset($data[self::REQUEST_FORM_DATA_KEY]) ? $data[self::REQUEST_FORM_DATA_KEY] : array();
        $customSubject = null;
        $customEmail = null;

        foreach ($formData as $key => $value) {
            $formData[$key] = htmlentities(filter_var($formData[$key], FILTER_UNSAFE_RAW));
            if ($key === self::SUBJECT_KEY) {
                $customSubject = $formData[$key];
            }
            if ($key === self::EMAIL_KEY) {
                $customEmail = htmlentities(filter_var($formData[$key], FILTER_SANITIZE_EMAIL));
            }

            $mailTemplate = str_replace('${' . $key . '}', $formData[$key], $mailTemplate);
        }

        $subject = isset($customSubject) ? $customSubject : self::DEFAULT_SUBJECT;
        $fromAddress = isset($customEmail) ? $customEmail : EMAIL_FROM_ADDRESS;

        $headers = 'From: ' . EMAIL_FROM_ADDRESS_NAME  . ' <' . $fromAddress . '>' . PHP_EOL .
            'Reply-To: ' . EMAIL_FROM_ADDRESS_NAME  . ' <' . $fromAddress . '>' . PHP_EOL .
            'X-Mailer: PHP/' . phpversion();

        $mailService = new Email();
        $mailService->sendEmail($email, $subject, $mailTemplate, $headers);
    }
}
