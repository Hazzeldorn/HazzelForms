<?php

/**
 * This is HazzelForms by Hazzeldorn
 * The library is based on FlexBoxGrid and inspired by NibbleForms 2 from NibbleDevelopment
*/

namespace HazzelForms;

class HazzelForm {

    protected $action,
              $method,
              $novalidate,
              $autocomplete,
              $stealthmode,
              $formName,
              $lang,
              $fields,
              $error,
              $isSubmitted = false,
              $valid = false;

    protected static $formNr = 0;

    public function __construct( $args = array() ){

        // load subclasses and tools
        $this->flexLoader();

        // assign variables
        $this->action                   = (isset($args['action']))        ? $args['action']          : $_SERVER['REQUEST_URI'];
        $this->method                   = (isset($args['method']))        ? $args['method']          : 'post';
        $this->novalidate               = (isset($args['novalidate']))    ? $args['novalidate']      : false;
        $this->autocomplete             = (isset($args['autocomplete']))  ? $args['autocomplete']    : true;
        $this->stealthmode              = (isset($args['stealthmode']))   ? $args['stealthmode']     : false;
        $this->fields                   = new \stdClass();

        // generate unique form name if not set via constructor
        self::$formNr++;
        if(isset($args['formname'])) {
          $this->formName = Tools::slugify($args['formname'], '_');
        } else {
          $this->formName = 'hazzelform_' . self::$formNr;
        }

        // load language files
        if(isset($args['lang'])) {
          $this->lang = new Language($args['lang']);
        } else {
          $this->lang = new Language();
        }

        // create CSRF token
        $this->setToken();
    }


    /**
     * Loading form classes
     * (i have no idea how autoloaders work ;-))
     */
     public static function flexLoader() {
        require_once('Tools.php');
        require_once('Mailer.php');
        require_once('Language.php');

        require_once('Field/Field.php');
        require_once('Field/Text/Text.php');
        require_once('Field/Text/Textarea.php');
        require_once('Field/Text/Email.php');
        require_once('Field/Text/Url.php');
        require_once('Field/Text/Tel.php');
        require_once('Field/Text/Password.php');
        require_once('Field/Text/Hidden.php');
        require_once('Field/Text/Number/Number.php');
        require_once('Field/Text/Number/Range.php');
        require_once('Field/Text/Number/Date.php');
        require_once('Field/Text/Number/Time.php');
        require_once('Field/Options/Options.php');
        require_once('Field/Options/Radio.php');
        require_once('Field/Options/Checkbox.php');
        require_once('Field/Options/Dropdown.php');
        require_once('Field/File/FileUpload.php');
        require_once('Field/Captcha/Captcha.php');
        require_once('Field/Captcha/RecaptchaV2.php');
        require_once('Field/Captcha/RecaptchaV3.php');
     }


    /**
     * Add a field to the form instance
     *
     * @param string  $fieldName
     * @param string  $type
     * @param array   $args
     */
    public function addField($fieldName, $type = 'text', $args = array())  {

        switch($type) {
          case 'text':         $this->fields->$fieldName = new Text($fieldName, $this->formName, $args);        break;
          case 'textarea':     $this->fields->$fieldName = new Textarea($fieldName, $this->formName, $args);    break;
          case 'email':        $this->fields->$fieldName = new Email($fieldName, $this->formName, $args);       break;
          case 'url':          $this->fields->$fieldName = new Url($fieldName, $this->formName, $args);         break;
          case 'tel':          $this->fields->$fieldName = new Tel($fieldName, $this->formName, $args);         break;
          case 'password':     $this->fields->$fieldName = new Password($fieldName, $this->formName, $args);    break;
          case 'number':       $this->fields->$fieldName = new Number($fieldName, $this->formName, $args);      break;
          case 'range':        $this->fields->$fieldName = new Range($fieldName, $this->formName, $args);       break;
          case 'date':         $this->fields->$fieldName = new Date($fieldName, $this->formName, $args);        break;
          case 'time':         $this->fields->$fieldName = new Time($fieldName, $this->formName, $args);        break;
          case 'radio':        $this->fields->$fieldName = new Radio($fieldName, $this->formName, $args);       break;
          case 'checkbox':     $this->fields->$fieldName = new Checkbox($fieldName, $this->formName, $args);    break;
          case 'dropdown':     $this->fields->$fieldName = new Dropdown($fieldName, $this->formName, $args);    break;
          case 'file':         $this->fields->$fieldName = new FileUpload($fieldName, $this->formName, $args);  break;
          case 'hidden':       $this->fields->$fieldName = new Hidden($fieldName, $this->formName, $args);      break;
          case 'recaptcha-v2': $this->fields->$fieldName = new RecaptchaV2($fieldName, $this->formName, $args); break;
          case 'recaptcha-v3': $this->fields->$fieldName = new RecaptchaV3($fieldName, $this->formName, $args); break;
          default: return;
        }

        return $this->fields->$fieldName;
    }


    public function renderAll()   {
        $this->openForm();

        if(!$this->stealthmode){
          $this->openGrid();
        }

        // render all the fields
        foreach ($this->fields as $fieldName => $fieldObject) {
          if(!$fieldObject instanceof Hidden){
            // hidden fields will be rendered together with the submit button but without opening and closing html
            $this->renderField($fieldName);
          }
        }

        if(!$this->stealthmode){
          $this->renderSubmitErrors();
          $this->renderSubmit();

          $this->closeGrid();
        }

        $this->closeForm();
    }


    /**
    * prints out field wrapper, label, input tag and errors all together
    * @param $fieldName
    */
    public function renderField($fieldName) {
      if(!$this->fieldExists($fieldName)){
        // API NOTICE
        die("<b>renderField('{$fieldName}')</b> not possible. Field <b>{$fieldName}</b> does not exist.");
      }

      $this->openField($fieldName);
        $this->renderLabel($fieldName);
        $this->renderInput($fieldName);
        if(!$this->stealthmode){
          $this->renderError($fieldName);
        }
      $this->closeField($fieldName);
    }

    /**
    * prints label
    * @param $fieldName
    */
    public function renderLabel($fieldName) {
      if(!$this->fieldExists($fieldName)){
        // API NOTICE
        die("<b>renderLabel('{$fieldName}')</b> not possible. Field <b>{$fieldName}</b> does not exist.");
      }
      echo $this->fields->$fieldName->returnLabel();
    }

    /**
    * prints the input field
    * @param $fieldName
    */
    public function renderInput($fieldName) {
        if(!$this->fieldExists($fieldName)){
          // API NOTICE
          die("<b>renderInput('{$fieldName}')</b> not possible. Field <b>{$fieldName}</b> does not exist.");
        }
        echo $this->fields->$fieldName->returnField();
    }

    /**
    * print message
    * @param $fieldName
    */
    public function renderError($fieldName) {
      if(!$this->fieldExists($fieldName)){
        // API NOTICE
        die("<b>renderError('{$fieldName}')</b> not possible. Field <b>{$fieldName}</b> does not exist.");
      }
      echo $this->fields->$fieldName->returnError($this->lang);
    }

    /**
    * create and print the submit button and hidden fields
    * @param $caption
    */
    public function renderSubmit($caption = "Senden") {
      echo '<div class="field-wrap submit-wrap">';
        echo sprintf('<input type="submit" value="%1$s" name="%2$s[submit]">', $caption, $this->formName);
      echo '</div>';
    }

    /**
    * print message
    * @param string $fieldName
    */
    public function renderSubmitErrors() {
      if(!empty($this->error)){
        echo sprintf('<p class="error-msg">%1$s</p>', $this->lang->getMessage('submit', $this->error));
      }
    }


    // render hidden fields
    public function renderHidden(){
      foreach($this->fields as $fieldName => $fieldObject){
        if($fieldObject instanceof Hidden){
          $this->renderInput($fieldName);
        }
      }
    }

    /**
    * prints html before field
    * @param $fieldName
    */
    public function openField($fieldName) {
      if(!$this->fieldExists($fieldName)){
        // API NOTICE
        die("<b>fieldExists('{$fieldName}')</b> not possible. Field <b>{$fieldName}</b> does not exist.");
      }
      echo $this->fields->$fieldName->getFieldWrapBefore();
    }


    /**
    * prints html after field
    * @param $fieldName
    */
    public function closeField($fieldName) {
      if(!$this->fieldExists($fieldName)){
        // API NOTICE
        die("<b>closeField('{$fieldName}')</b> not possible. Field <b>{$fieldName}</b> does not exist.");
      }
      echo $this->fields->$fieldName->getFieldWrapAfter();
    }


    /**
     * Prints the HTML string for opening a form with the correct enctype, action and method
     */
    public function openForm()  {
        $classList = '';
        $attributes = '';

        // build classlist
        if($this->isSubmitted && $this->valid == false){
          $classList .= ' has-errors';
        }
        elseif($this->isSubmitted == false){
          $classList .= ' untouched';
        }

        // build attribute string
        if($this->autocomplete == false){
          $attributes .= ' autocomplete="off"';
        }
        if($this->novalidate == true){
          $attributes .= ' novalidate';
        }

        // echo sprintf('<form action="%1$s" method="%2$s" name="%3$s" id="%3$s" class="%4$s" %5$s>', $this->action, $this->method, $this->formName, $classList, $attributes);
        echo sprintf('<form enctype="multipart/form-data" action="%1$s" method="%2$s" name="%3$s" id="%3$s" class="%4$s" %5$s>', $this->action, $this->method, $this->formName, $classList, $attributes);
    }

    /**
     * Prints form closing tag
     */
    public function closeForm()  {
        $this->renderHidden();
        echo "</form>";
    }

    /**
     * Prints the HTML string for opening the grid system
     */
    public function openGrid()  {
        echo '<div class="grid-wrap">';
    }

    /**
     * Prints grid system closing tag
     */
    public function closeGrid()  {
        echo '</div>';
    }


    /**
    * Validate all the fields
    */
    public function validate()  {
        $request  = strtoupper($this->method) == 'POST' ? $_POST : $_GET;

        if (isset($request[$this->formName])) {
            $formData = $request[$this->formName];
            $this->isSubmitted = true;

            foreach ($this->fields as $field) {
                if(isset($formData[$field->getSlug()])){
                  $field->setValue($formData[$field->getSlug()]);
                  if($field->validate() == false){
                    $this->error = 'invalid_fields';
                  }
                }
                elseif($field instanceof Options){
                  // it's actually possible that no data is sent to server when using option fields
                  if($field->validate() == false){
                    $this->error = 'invalid_fields';
                  }
                }
                elseif($field instanceof Captcha){
                  // different handling for captchas
                  if($field->validate() == false){
                    $this->error = 'invalid_captcha';
                  }
                }
                elseif($field instanceof FileUpload){
                  // different handling for files
                  if($field->validate() == false){
                    $this->error = 'upload_error';
                  }
                }
                else {
                  // form data does not contain all fields (required field might have been removed from DOM by malicious user)
                  $this->error = 'transmission_error';
                }
            } unset($field);

            // validate CSRF Token if a session is running
            if(isset($formData['csrf_token'])){
              if(session_id() != '' && isset($_SESSION["hazzelforms"][$this->formName]["csrf_token"])){
                if($_SESSION["hazzelforms"][$this->formName]["csrf_token"] != $formData['csrf_token']){
                  $this->error = 'csrf_error';
                }
              }
              else {
                // token is sent within form but a session does not exist
                $this->error = 'session_error';
              }
            }

            if(empty($this->error)){
              $this->valid = true;
            }
        }

        return $this->valid;
    }


    /**
     * Creates a new CSRF token if a session exists
     */
    private function setToken()  {
        if(session_id() != '' && isset($_SESSION)){
          // if any session is active
          if(!isset($_SESSION["hazzelforms"][$this->formName]["csrf_token"])) {
            // if csrf_token is not defined in current session
             $_SESSION["hazzelforms"][$this->formName]["csrf_token"] = bin2hex(random_bytes(32));
          }
          $this->addField("csrf_token", "hidden");
          $this->getField("csrf_token")->setValue($_SESSION["hazzelforms"][$this->formName]["csrf_token"]);
        }
    }


    /**
     * Prepares and sends the HazzelForm data to specific mail address
     * @param string $to           mail address of receiver
     * @param string $from         mail address of sender (optional)
     * @param string $replyTo      reply to mail address (optional)
     * @param string $senderName   name of sender (optional)
     * @param string $subject      optional mail subject (optional)
     * @param string $template     mail template (optional)
     */
    public function sendMail($to, $from = '', $replyTo = '', $senderName = 'HazzelForms', $subject = 'New HazzelForms Message', $template = 'basic') {
      if(empty($from)){
        $from = 'noreply@'.$_SERVER['HTTP_HOST'];
      }
      if(empty($replyTo)){
        $replyTo = 'noreply@'.$_SERVER['HTTP_HOST'];
      }

      // send mail
      $mail = new Mailer($to, $from, $replyTo, $senderName, $subject, $template, $this->lang);
      $mail->prepareContent($this->getFields());
      $mail->send();
    }

    /**
    * If form is valid, this returns an array with all the field names and its values
    */
    public function getFieldValues(){
      $fieldValues = array();
      if($this->valid != false){
        // if all fields are valid
        foreach ($this->getFields() as $field) {
          $fieldValues[$field->getName()] = $field->getValue();
        } unset($field);
        return $fieldValues;
      } else {
        return false;
      }
    }

    /**
    * Checks if a field exists
    */
    public function fieldExists($fieldName){
      if( !isset($this->fields->$fieldName) ){
        return false;
      } else{
        return true;
      }
    }

    /**
    * Getters and Setters
    */
    public function getFormName(){
      return $this->formName;
    }

    public function getFields(){
      return $this->fields;
    }

    public function getField($fieldName){
      return $this->fields->$fieldName;
    }

    public function setLanguage($lang){
      $this->lang = new Language($lang);
    }

}
