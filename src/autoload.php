<?php
require 'vendor/autoload.php';

require 'Controller/MailController.php';

require 'Service/ReCaptcha.php';
require 'Service/Firebase.php';

require 'CustomException/EmptyWebsiteException.php';
require 'CustomException/CaptchaException.php';
require 'CustomException/DocumentNotFoundException.php';