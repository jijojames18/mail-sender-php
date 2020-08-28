<?php
namespace Controller;

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
                $result = 123;
                echo json_encode(array(
                    'result' => $result
                ));
                header('HTTP/1.1 200 OK');
            break;
            default:
                header('HTTP/1.1 404 Not Found');
        }
    }
}