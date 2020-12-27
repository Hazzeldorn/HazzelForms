<?php

namespace HazzelForms\Field\Captcha;

use HazzelForms\Field\Field as Field;

class RecaptchaV2 extends Captcha
{

    protected $siteKey = '';
    protected $secretKey = '';
    protected $fieldType = 'recaptcha-v2';

    public function __construct($formName, $fieldName, $args = [])
    {
        parent::__construct($formName, $fieldName, $args);

        $this->siteKey   = $args['sitekey'] ?? '';
        $this->secretKey = $args['secretkey'] ?? '';
    }

    public function returnField()
    {
        return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>'
               . sprintf('<div class="g-recaptcha" data-sitekey="%1$s"></div>', $this->siteKey);
    }

    public function validate()
    {
        // get captcha response from google
        if (isset($_POST['g-recaptcha-response'])) {
            $reCaptchaResponse = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $this->secretKey . "&response=" . $_POST['g-recaptcha-response']), true);

            if ($reCaptchaResponse['success'] != 1) {
                // CAPTCHA INCORRECT
                $this->error = 'invalid';
            } else {
                // CAPTCHA CORRECT
                $this->fieldValue = 'ok';
            }
        } else {
            $this->error = 'invalid';
        }

        $this->validated = true;
        return $this->isValid();
    }
}
