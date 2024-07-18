<?php

namespace HazzelForms;

use HazzelForms\Field\File\File as File;
use HazzelForms\Field\Captcha\Captcha as Captcha;

class LegacyMailer
{
    protected $to;
    protected $subject;
    protected $headers = [];
    protected $message = '';
    protected $mimeBoundary;
    protected $returnPath;
    protected $templateLoader;
    protected $lang;
    protected const EOL = "\r\n";   // end of line

    public function __construct($to, $from, $replyTo, $senderName, $subject, $template, $lang)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->lang = $lang;

        // set template
        $this->setTemplate($template);

        // define multipart content boundary
        $semiRand = md5(time());
        $this->mimeBoundary = "==MultipartBoundary_{$semiRand}==";

        // define headers
        $this->headers[] = "From: $senderName" . " <" . $from . ">";
        if(!empty($replyTo)){
            $this->headers[] = "Reply-To: " . $replyTo;
        }
        $this->headers[] = "MIME-Version: 1.0";
        $this->headers[] = "Content-Type: multipart/mixed; boundary=\"{$this->mimeBoundary}\"";
        $this->returnPath = "-f" . $from;
    }


    public function prepareContent($formFields)
    {
        [$fields, $attachements] = self::filterFieldsAndAttachements($formFields);

        // prepare mail content
        $htmlContent = $this->templateLoader->loadTemplate($this->subject, $fields);

        // content
        $this->message .= "--{$this->mimeBoundary}" . self::EOL
        . 'Content-Type: text/html; charset="UTF-8";' . self::EOL
        . 'Content-Transfer-Encoding: 8bit;' . self::EOL
        . self::EOL // empty line needed !
        . $htmlContent . self::EOL;

        // add attachements
        if (!empty($attachements)) {
            foreach ($attachements as $fileData) {
                $this->addAttachement($fileData);
            } unset($fileData);
        }
    }

    public static function filterFieldsAndAttachements($formFields)
    {
        $attachements = $fields = [];

        // loop through all fields and clean formFields array
        foreach ($formFields as $field) {
            if ($field instanceof File) {
                // add all files as attachements but remove from formFields
                foreach ($field->getValue() as $fileData) {
                    array_push($attachements, $fileData);
                } unset($fileData);
            } elseif (! $field instanceof Captcha && $field->getName() != 'csrf_token' && $field->getName() != 'form_name') {
                // build beautiful array for templates
                array_push($fields, $field);
            }
        }

        return [$fields, $attachements];
    }

    /**
     *     This function adds attachements to an Email
     *
     * @param array $fileData like array(filepath, filename)
     */
    public function addAttachement($fileData)
    {

        $file = $fileData['dir'];

        if (is_file($file)) {
            // load file data
            $basename    = basename($fileData['name']);
            $filesize    = filesize($file);
            $fileHandler = fopen($file, "rb");
            $fileData    = fread($fileHandler, $filesize);

            // close file -> php will automatically delete it after execution
            fclose($fileHandler);

            $this->message .= self::EOL // empty line in advance
            . "--{$this->mimeBoundary}" . self::EOL
            . "Content-Type: application/octet-stream; name=\"{$basename}\";" . self::EOL
            . "Content-Description: {$basename};" . self::EOL
            . "Content-Disposition: attachment; filename=\"{$basename}\"; size=\"{$filesize}\";" . self::EOL
            . "Content-Transfer-Encoding: base64" . self::EOL
            . self::EOL // empty line needed !
            . chunk_split(base64_encode($fileData)) . self::EOL;
        }
    }


    public function send()
    {
        // send using the default php mail function
        mail(
            $this->to,
            $this->subject,
            $this->message,
            implode(self::EOL, $this->headers),
            $this->returnPath
        ) or die($this->lang->getMessage('defaults', 'mailer_error'));
    }


    // GETTERS AND SETTERS
    public function setTemplate($template)
    {
        // set template
        if (is_string($template)) {
            $this->templateLoader = new TemplateLoader(__DIR__ . '/mail-templates/' . $template . '.php');
        } else {
            $this->templateLoader = $template;
        }
    }
}
