<?php

/**
 * This is HazzelForms by @hazzeldorn
 * The library is inspired by NibbleForms from NibbleDevelopment
 */

namespace HazzelForms;

use Exception;
use HazzelForms\Tools as Tools;
use HazzelForms\LegacyMailer as LegacyMailer;
use HazzelForms\Language as Language;
use PHPMailer\PHPMailer\PHPMailer;

class HazzelForm
{
    protected $action;
    protected $method;
    protected $novalidate;
    protected $autocomplete;
    protected $formName;
    protected $stealthmode;
    protected $gridClass;
    protected $submitCaption;
    protected $lang;
    protected $fields;
    protected $error;
    protected $isSubmitted = false;
    protected $valid = false;
    protected $mailer = null;

    protected static $formNr = 0;

    public function __construct($args = [])
    {
        // assign variables
        $this->action = $args['action'] ?? $_SERVER['REQUEST_URI'];
        $this->method = $args['method'] ?? 'post';
        $this->novalidate = $args['novalidate'] ?? false;
        $this->autocomplete = $args['autocomplete'] ?? true;
        $this->stealthmode = $args['stealthmode'] ?? false;
        $this->gridClass = $args['gridclass'] ?? 'grid-wrap';
        $this->submitCaption = $args['submitcaption'] ?? 'SEND';
        $this->fields = new \stdClass();

        // generate unique form name if not set via constructor
        self::$formNr++;
        if (isset($args['formname'])) {
            $this->formName = Tools::slugify($args['formname'], '_');
        } else {
            $this->formName = 'hazzelform_' . self::$formNr;
        }

        // load language files
        if (isset($args['lang'])) {
            $this->lang = new Language($args['lang']);
        } else {
            $this->lang = new Language();
        }

        // create CSRF token
        $this->setToken();

        // add hidden field for formName
        $this->addField('form_name', 'hidden')->setValue($this->formName);
    }


    /**
     * Add a field to the form instance
     *
     * @param string $fieldName
     * @param string $type
     * @param array  $args
     */
    public function addField($fieldName, $type = 'text', $args = [])
    {

        switch ($type) {
            case 'text':
                $this->fields->$fieldName = new Field\Text\Text($fieldName, $this->formName, $args);
                break;
            case 'textarea':
                $this->fields->$fieldName = new Field\Text\Textarea($fieldName, $this->formName, $args);
                break;
            case 'email':
                $this->fields->$fieldName = new Field\Text\Email($fieldName, $this->formName, $args);
                break;
            case 'url':
                $this->fields->$fieldName = new Field\Text\Url($fieldName, $this->formName, $args);
                break;
            case 'tel':
                $this->fields->$fieldName = new Field\Text\Tel($fieldName, $this->formName, $args);
                break;
            case 'password':
                $this->fields->$fieldName = new Field\Text\Password($fieldName, $this->formName, $args);
                break;
            case 'number':
                $this->fields->$fieldName = new Field\Text\Number\Number($fieldName, $this->formName, $args);
                break;
            case 'range':
                $this->fields->$fieldName = new Field\Text\Number\Range($fieldName, $this->formName, $args);
                break;
            case 'date':
                $this->fields->$fieldName = new Field\Text\Number\Date($fieldName, $this->formName, $args);
                break;
            case 'time':
                $this->fields->$fieldName = new Field\Text\Number\Time($fieldName, $this->formName, $args);
                break;
            case 'radio':
                $this->fields->$fieldName = new Field\Options\Radio($fieldName, $this->formName, $args);
                break;
            case 'checkbox':
                $this->fields->$fieldName = new Field\Options\Checkbox($fieldName, $this->formName, $args);
                break;
            case 'dropdown':
                $this->fields->$fieldName = new Field\Options\Dropdown($fieldName, $this->formName, $args);
                break;
            case 'file':
                $this->fields->$fieldName = new Field\File\File($fieldName, $this->formName, $args);
                break;
            case 'hidden':
                $this->fields->$fieldName = new Field\Text\Hidden($fieldName, $this->formName, $args);
                break;
            case 'recaptcha-v2':
                $this->fields->$fieldName = new Field\Captcha\RecaptchaV2($fieldName, $this->formName, $args);
                break;
            case 'recaptcha-v3':
                $this->fields->$fieldName = new Field\Captcha\RecaptchaV3($fieldName, $this->formName, $args);
                break;
            case 'math-captcha':
                $this->fields->$fieldName = new Field\Captcha\MathCaptcha($fieldName, $this->formName, $args);
                break;
            case 'honeypot':
                $this->fields->$fieldName = new Field\Captcha\HoneyPot($fieldName, $this->formName, $args);
                break;
            default:
                return;
        }

        return $this->fields->$fieldName;
    }


    public function renderAll()
    {
        $this->openForm();

        if (!$this->stealthmode) {
            $this->openGrid();
        }

        // render all the fields
        foreach ($this->fields as $fieldName => $fieldObject) {
            if (!$fieldObject instanceof Field\Text\Hidden) {
                // hidden fields will be rendered together with the submit button but without opening and closing html
                $this->renderField($fieldName);
            }
        }

        if (!$this->stealthmode) {
            $this->renderSubmitErrors();
            $this->renderSubmit($this->submitCaption);
            $this->closeGrid();
        }

        $this->closeForm();
    }


    /**
     * prints out field wrapper, label, input tag and errors all together
     *
     * @param $fieldName
     */
    public function renderField($fieldName)
    {
        if (!$this->fieldExists($fieldName)) {
            // API NOTICE
            die("<b>renderField('{$fieldName}')</b> not possible. Field <b>{$fieldName}</b> does not exist.");
        }

        $this->openField($fieldName);
        $this->renderLabel($fieldName);
        $this->renderInput($fieldName);
        if (!$this->stealthmode) {
            $this->renderError($fieldName);
        }
        $this->closeField($fieldName);
    }

    /**
     * prints label
     *
     * @param $fieldName
     */
    public function renderLabel($fieldName)
    {
        if (!$this->fieldExists($fieldName)) {
            // API NOTICE
            die("<b>renderLabel('{$fieldName}')</b> not possible. Field <b>{$fieldName}</b> does not exist.");
        }
        echo $this->fields->$fieldName->returnLabel();
    }

    /**
     * prints the input field
     *
     * @param $fieldName
     */
    public function renderInput($fieldName)
    {
        if (!$this->fieldExists($fieldName)) {
            // API NOTICE
            die("<b>renderInput('{$fieldName}')</b> not possible. Field <b>{$fieldName}</b> does not exist.");
        }
        echo $this->fields->$fieldName->returnField();
    }

    /**
     * print message
     *
     * @param $fieldName
     */
    public function renderError($fieldName)
    {
        if (!$this->fieldExists($fieldName)) {
            // API NOTICE
            die("<b>renderError('{$fieldName}')</b> not possible. Field <b>{$fieldName}</b> does not exist.");
        }
        echo $this->fields->$fieldName->returnError($this->lang);
    }

    /**
     * create and print the submit button and hidden fields
     *
     * @param $caption
     */
    public function renderSubmit($caption = "SEND")
    {
        echo '<div class="field-wrap submit-wrap">';
        echo sprintf('<input type="submit" value="%1$s" name="%2$s[submit]">', $caption, $this->formName);
        echo '</div>';
    }

    /**
     * print message
     *
     * @param string $fieldName
     */
    public function renderSubmitErrors()
    {
        if (!empty($this->error)) {
            echo sprintf('<p class="error-msg">%1$s</p>', $this->lang->getMessage('submit', $this->error));
        }
    }


    // render hidden fields
    public function renderHidden()
    {
        foreach ($this->fields as $fieldName => $fieldObject) {
            if ($fieldObject instanceof Field\Text\Hidden) {
                $this->renderInput($fieldName);
            }
        }
    }

    /**
     * prints html before field
     *
     * @param $fieldName
     */
    public function openField($fieldName)
    {
        if (!$this->fieldExists($fieldName)) {
            // API NOTICE
            die("<b>fieldExists('{$fieldName}')</b> not possible. Field <b>{$fieldName}</b> does not exist.");
        }
        echo $this->fields->$fieldName->getFieldWrapBefore();
    }


    /**
     * prints html after field
     *
     * @param $fieldName
     */
    public function closeField($fieldName)
    {
        if (!$this->fieldExists($fieldName)) {
            // API NOTICE
            die("<b>closeField('{$fieldName}')</b> not possible. Field <b>{$fieldName}</b> does not exist.");
        }
        echo $this->fields->$fieldName->getFieldWrapAfter();
    }


    /**
     * Prints the HTML string for opening a form with the correct enctype, action and method
     */
    public function openForm()
    {
        $classList = '';
        $attributes = '';

        // build classlist
        if ($this->isSubmitted && $this->valid == false) {
            $classList .= ' has-error';
        } elseif ($this->isSubmitted == false) {
            $classList .= ' untouched';
        } elseif ($this->isSubmitted && $this->valid) {
            $classList .= ' is-valid submitted';
        }

        // build attribute string
        if ($this->autocomplete == false) {
            $attributes .= ' autocomplete="off"';
        }
        if ($this->novalidate == true) {
            $attributes .= ' novalidate';
        }

        // echo sprintf('<form action="%1$s" method="%2$s" name="%3$s" id="%3$s" class="%4$s" %5$s>', $this->action, $this->method, $this->formName, $classList, $attributes);
        echo sprintf('<form enctype="multipart/form-data" action="%1$s" method="%2$s" name="%3$s" id="%3$s" class="%4$s" %5$s>', $this->action, $this->method, $this->formName, $classList, $attributes);
    }

    /**
     * Prints form closing tag
     */
    public function closeForm()
    {
        $this->renderHidden();
        echo "</form>";
    }

    /**
     * Prints the HTML string for opening the grid system
     */
    public function openGrid()
    {
        echo '<div class="' . $this->gridClass . '">';
    }

    /**
     * Prints grid system closing tag
     */
    public function closeGrid()
    {
        echo '</div>';
    }


    /**
     * Validate all the fields
     */
    public function validate()
    {
        $request  = strtoupper($this->method) == 'POST' ? $_POST : $_GET;

        if (isset($request[$this->formName])) {
            $formData = $request[$this->formName];
            $this->isSubmitted = true;

            foreach ($this->fields as $field) {
                if (isset($formData[$field->getSlug()])) {
                    $field->setValue($formData[$field->getSlug()], 'REQUEST');

                    // skip captcha fields since they are validated later
                    if ($field instanceof Field\Captcha\Captcha) {
                        continue;
                    } elseif ($field->validate() == false) {
                        // validate all the other fields
                        $this->error = $this->error ?? 'invalid_fields';
                    }
                } elseif ($field instanceof Field\Options\Options) {
                    // it's actually possible that no data is sent to server when using option fields
                    if ($field->validate() == false) {
                        $this->error = $this->error ?? 'invalid_fields';
                    }
                } elseif ($field instanceof Field\File\File) {
                    // different handling for files
                    if ($field->validate() == false) {
                        $this->error = $this->error ?? 'upload_error';
                    }
                } else {
                    // form data does not contain all fields (required field might have been removed from DOM by malicious user)
                    $this->error = $this->error ?? 'transmission_error';
                }
            }
            unset($field);

            // validate CSRF Token if a session is running
            if (isset($formData['csrf_token'])) {
                if (session_id() != '' && isset($_SESSION["hazzelforms"][$this->formName]["csrf_token"])) {
                    if ($_SESSION["hazzelforms"][$this->formName]["csrf_token"] != $formData['csrf_token']) {
                        $this->error = $this->error ?? 'csrf_error';
                    }
                } else {
                    // token is sent within form but a session does not exist
                    $this->error = $this->error ?? 'session_error';
                }
            }

            // validate captcha if everything else is ok
            // this is done after all other fields have been validated to prevent unnecessary / duplicated requests to captcha provider
            if (empty($this->error)) {
                foreach ($this->fields as $field) {
                    if ($field instanceof Field\Captcha\Captcha) {
                        if (isset($formData[$field->getSlug()])) {
                            $field->setValue($formData[$field->getSlug()], 'REQUEST');
                        }

                        if ($field->validate() == false) {
                            $this->error = $this->error ?? 'invalid_captcha';
                        }
                    }
                }
            }

            // if no error has been set, the form is valid
            if (empty($this->error)) {
                $this->valid = true;
            }
        }

        return $this->valid;
    }


    /**
     * Creates a new CSRF token if a session exists
     */
    private function setToken()
    {
        if (session_id() != '' && isset($_SESSION)) {
            // if any session is active
            if (!isset($_SESSION["hazzelforms"][$this->formName]["csrf_token"])) {
                // if csrf_token is not defined in current session
                $_SESSION["hazzelforms"][$this->formName]["csrf_token"] = bin2hex(random_bytes(32));
            }
            $this->addField("csrf_token", "hidden");
            $this->getField("csrf_token")->setValue($_SESSION["hazzelforms"][$this->formName]["csrf_token"]);
        }
    }


    /**
     * Prepares and sends the HazzelForm data to specific mail address
     *
     * @param string $to         mail address of receiver
     * @param string $from       mail address of sender (optional)
     * @param string $replyTo    reply to mail address (optional)
     * @param string $senderName name of sender (optional)
     * @param string $subject    optional mail subject (optional)
     * @param string|TemplateLoader $template   mail template (optional)
     *
     * @throws Exception
     */
    public function sendMail($to, $from, $replyTo = '', $senderName = 'HazzelForms', $subject = 'New HazzelForms Message', $template = 'basic')
    {

        if ($this->mailer != null) {
            // use PHPMailer instance and override default settings
            $this->mailer->setFrom($from, $senderName);
            $this->mailer->addAddress($to);
            if (!empty($replyTo)) {
                $this->mailer->addReplyTo($replyTo);
            }

            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Subject = $subject;
            $this->mailer->AltBody = $subject;

            // prepare mail content
            [$fields, $attachements] = LegacyMailer::filterFieldsAndAttachements($this->getFields());

            if (is_string($template)) {
                $templateLoader = new TemplateLoader(__DIR__ . '/mail-templates/' . $template . '.php');
                $this->mailer->Body = $templateLoader->loadTemplate($subject, $fields);
            } else {
                $this->mailer->Body = $template->loadTemplate($subject, $fields);
            }

            // add attachements
            if (!empty($attachements)) {
                foreach ($attachements as $fileData) {
                    $this->mailer->addAttachment($fileData['dir'], $fileData['name']);
                };
            }

            // exit($this->mailer->Body);
            $this->mailer->send();

            // reset mailer before next mail
            $this->mailer->clearAllRecipients();
            $this->mailer->clearReplyTos();
            $this->mailer->clearAttachments();
        } else {
            // send mail
            $mail = new LegacyMailer($to, $from, $replyTo, $senderName, $subject, $template, $this->lang);
            $mail->prepareContent($this->getFields());
            $mail->send();
        }
    }


    /**
     * Set Mailer instance
     * @param PHPMailer $mailer  PHP mailer instance
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }


    /**
     * Resets all field values
     */
    public function clear()
    {
        foreach ($this->getFields() as $field) {
            $field->clear();
        }
        unset($field);
    }



    /**
     * If form is valid, this returns an array with all the field names and its values
     */
    public function getFieldValues()
    {
        $fieldValues = [];
        if ($this->valid != false) {
            // if all fields are valid
            foreach ($this->getFields() as $field) {
                $fieldValues[$field->getName()] = $field->getValue();
            }
            unset($field);
            return $fieldValues;
        } else {
            return false;
        }
    }

    /**
     * If form is valid, this returns an array with all the field names, its values and its types
     */
    public function getFieldValuesAndTypes()
    {
        $fieldValues = [];
        if ($this->valid != false) {
            // if all fields are valid
            foreach ($this->getFields() as $field) {
                $fieldValues[$field->getName()] = [$field->getValue(), $field->getType()];
            }
            unset($field);
            return $fieldValues;
        } else {
            return false;
        }
    }

    /**
     * Checks if a field exists
     */
    public function fieldExists($fieldName)
    {
        if (!isset($this->fields->$fieldName)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Getters and Setters
     */
    public function getFormName()
    {
        return $this->formName;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getField($fieldName)
    {
        return $this->fields->$fieldName;
    }

    public function isValid()
    {
        return $this->valid;
    }

    public function hasError()
    {
        return !empty($this->error);
    }

    public function setLanguage($lang)
    {
        $this->lang = new Language($lang);
    }
}
