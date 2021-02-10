<?php

namespace HazzelForms;

use HazzelForms\Field\File\File as File;
use HazzelForms\Field\Captcha\Captcha as Captcha;

class Mailer
{

    protected $to;
    protected $from;
    protected $replyTo;
    protected $senderName;
    protected $subject;
    protected $headers = [];
    protected $message = '';
    protected $mimeBoundary;
    protected $returnPath;
    protected $template;
    protected $lang;
    protected const EOL = "\r\n";   // end of line

    public function __construct($to, $from, $replyTo, $senderName, $subject, $template, $lang)
    {
        $this->to = $to;
        $this->from = $from;
        $this->replyTo = $replyTo;
        $this->senderName = $senderName;
        $this->subject = $subject;
        $this->template = $template;
        $this->lang = $lang;

        // define multipart content boundary
        $semiRand = md5(time());
        $this->mimeBoundary = "==MultipartBoundary_{$semiRand}==";

        // define headers
        $this->headers[] = "From: $senderName" . " <" . $from . ">";
        $this->headers[] = "Reply-To: " . $replyTo;
        $this->headers[] = "MIME-Version: 1.0";
        $this->headers[] = "Content-Type: multipart/mixed; boundary=\"{$this->mimeBoundary}\"";
        $this->returnPath = "-f" . $from;
    }


    public function prepareContent($formFields)
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

        // prepare mail content
        ob_start();
        include "mail-templates/{$this->template}.php";
        $htmlContent = ob_get_contents();
        ob_end_clean();

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
        $this->template = $template;
    }
}
