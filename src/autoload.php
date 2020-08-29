<?php
require 'vendor/autoload.php';

require 'Controller/MailController.php';

require 'Service/ReCaptcha.php';
require 'Service/Firebase.php';
require 'Service/Email.php';

require 'Model/Captcha.php';
require 'Model/FirebaseDocument.php';

require 'CustomException/EmptyWebsiteException.php';
require 'CustomException/CaptchaException.php';
require 'CustomException/DocumentNotFoundException.php';
