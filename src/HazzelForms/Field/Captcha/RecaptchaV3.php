<?php

namespace HazzelForms\Field\Captcha;

use HazzelForms\Field\Field as Field;

class RecaptchaV3 extends Captcha
{
    protected $siteKey = '';
    protected $secretKey = '';
    protected $minScore = 0.4;
    protected $fieldType = 'recaptcha-v3';

    public function __construct($formName, $fieldName, $args = [])
    {
        parent::__construct($formName, $fieldName, $args);

        $this->siteKey   = $args['sitekey'] ?? '';
        $this->secretKey = $args['secretkey'] ?? '';
        $this->minScore  = $args['min_score'] ?? 0.4;
        $this->label     = false;
    }

    public function returnField()
    {
        return '<script src="https://www.google.com/recaptcha/api.js?render=' . $this->siteKey . '"></script>'
            . sprintf(
                '<input type="hidden" name="%1$s[%2$s]" id="%1$s-%2$s" value="" />
                <script>
                document.addEventListener("DOMContentLoaded", () => {
                    const tokenField = document.getElementById("%1$s-%2$s");
                    const formElem = tokenField.closest("form");
                    const submitButton = formElem.querySelector("button[type=submit]") || formElem.querySelector("input[type=submit]");

                    grecaptcha.ready(() => {
                        submitButton.addEventListener("click", (event) => {
                            event.preventDefault();
                            grecaptcha.execute("' . $this->siteKey . '").then((token) => {
                                tokenField.value = token;
                                formElem.requestSubmit(submitButton);
                            });
                        });
                    });
                });
                </script>',
                $this->formName,
                $this->fieldSlug
            );
    }

    public function validate()
    {
        // get captcha response from google
        $reCaptchaResponse = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $this->secretKey . "&response=" . $this->fieldValue), true);

        if (empty($reCaptchaResponse) || $reCaptchaResponse['success'] != 1 || $reCaptchaResponse['score'] < $this->minScore) {
            // CAPTCHA INCORRECT
            $this->error = 'invalid';
        } else {
            // CAPTCHA CORRECT
            $this->fieldValue = 'ok';
        }

        $this->validated = true;
        return $this->isValid();
    }

    public function setValue($value, $origin = 'MANUAL')
    {
        $this->fieldValue = $value;
    }
}
