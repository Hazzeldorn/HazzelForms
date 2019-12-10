<?php

namespace HazzelForms;

const EOL = "\r\n";   // end of line

class Mailer {

    protected $to,
              $from,
              $replyTo,
              $senderName,
              $subject,
              $headers = array(),
              $message = '',
              $mimeBoundary,
              $returnPath,
              $template;


    public function __construct($to, $from, $replyTo, $senderName, $subject, $template = 'basic') {
      $this->to = $to;
      $this->from = $from;
      $this->replyTo = $replyTo;
      $this->senderName = $senderName;
      $this->subject = $subject;
      $this->template = $template;

      // define multipart content boundary
      $semiRand = md5(time());
      $this->mimeBoundary = "==MultipartBoundary_{$semiRand}==";

      // define headers
      $this->headers[] = "From: $senderName"." <".$from.">";
      $this->headers[] = "Reply-To: ". $replyTo;
      $this->headers[] = "MIME-Version: 1.0";
      $this->headers[] = "Content-Type: multipart/mixed; boundary=\"{$this->mimeBoundary}\"";
      $this->returnPath = "-f" . $from;
    }


    public function prepareContent($formFields) {
      // prepare mail content
      ob_start();
        require("mail-templates/{$this->template}.php");
        $htmlContent = ob_get_contents();
      ob_end_clean();

      // content
      $this->message .= "--{$this->mimeBoundary}" . EOL
        . 'Content-Type: text/html; charset="UTF-8";' . EOL
        . 'Content-Transfer-Encoding: 8bit;' . EOL
        . EOL // empty line needed !
        . $htmlContent . EOL;

      // add attachements
      if( isset($attachements) && !empty($attachements) ){
        foreach ($attachements as $fileData){
          $this->addAttachement($fileData);
        } unset($fileData);
      }
    }

    /**
    *     This function adds attachements to an Email
    *     @param array $fileData like array(filepath, filename)
    */
    public function addAttachement($fileData) {

      $file = $fileData['dir'];

      if(is_file($file)){

          // load file data
          $basename    = basename($fileData['name']);
          $filesize    = filesize($file);
          $fileHandler = fopen($file,"rb");
          $fileData    = fread($fileHandler, $filesize);

          // close and delete file
          fclose($fileHandler);
          unlink($file);

          $this->message .= EOL // empty line in advance
          . "--{$this->mimeBoundary}" . EOL
          . "Content-Type: application/octet-stream; name=\"{$basename}\";" . EOL
          . "Content-Description: {$basename};" . EOL
          . "Content-Disposition: attachment; filename=\"{$basename}\"; size=\"{$filesize}\";" . EOL
          . "Content-Transfer-Encoding: base64" . EOL
          . EOL // empty line needed !
          . chunk_split(base64_encode($fileData)) . EOL;
      }
    }


    public function send() {
      // send using the default php mail function
      mail(
        $this->to,
        $this->subject,
        $this->message,
        implode(EOL, $this->headers),
        $this->returnPath
      ) or die("Das Formular konnte nicht gesendet werden. Bitte Internetverbindung überprüfen und erneut versuchen.");
    }


    // GETTERS AND SETTERS
    public function setTemplate($template) {
        $this->template = $template;
    }

}
