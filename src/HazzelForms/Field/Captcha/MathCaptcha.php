<?php

namespace HazzelForms\Field\Captcha;

use HazzelForms\Field\Field as Field;

class MathCaptcha extends Captcha {
  protected $fieldType = 'math-captcha';
  protected $num1;
  protected $num2;
  protected $operator;
  protected $answer;
  protected $secret;
  protected $useImage;
  protected $fontPath;
  protected $fontSize;
  protected $color;
  protected $ttl;

  public function __construct($formName, $fieldName, $args = []) {
    parent::__construct($formName, $fieldName, $args);

    $min            = $args['min'] ?? 1;  // default to 1  if not specified
    $max            = $args['max'] ?? 25; // default to 25 if not specified
    $this->secret   = $args['secret'];
    $this->ttl      =  isset($args['ttl']) && is_int($args['ttl']) ? $args['ttl'] : 10; // default to 10 minutes if not specified 
    $this->useImage = ($args['use_image'] && extension_loaded('gd')) ?? false;
    $this->fontPath = $args['font_path'] ?? realpath(__DIR__ . '/../../assets/font/OpenSans-Regular.ttf');
    $this->fontSize = $args['font_size'] ?? 24;
    $this->color    = $args['color'] ?? [0, 0, 0];

    // Generate two random numbers and a random operator
    $this->num1 = rand($min, $max);
    $this->num2 = rand($min, $max);
    $this->operator = rand(0, 1) ? '+' : '-';

    if ($this->operator == '-' && $this->num2 > $this->num1) {
      // Swap numbers to ensure a non-negative result
      [$this->num1, $this->num2] = [$this->num2, $this->num1];
    }

    $this->answer = $this->operator == '+' ? ($this->num1 + $this->num2) : ($this->num1 - $this->num2);
  }

  // override label method to allow for custom label
  public function returnLabel() {
    // Get the challenge string (either image or text representation)
    $challengeString = sprintf('%d %s %d', $this->num1, $this->operator, $this->num2);
    $challenge = $this->useImage ? $this->generateImage($challengeString) : $challengeString;

    // If the label contains the placeholder <challenge>, replace it
    if (strpos($this->label, '<challenge>') !== false) {
      $replacedLabel = str_replace('<challenge>', $challenge, $this->label);
      return sprintf('<label for="%1$s-%2$s"><span class="label">%3$s</span></label>', $this->formName, $this->fieldSlug, $replacedLabel);
    }

    // If no placeholder is found or if label is not set, revert to the default behavior
    return parent::returnLabel();
  }

  public function returnField() {
    $currentTimestamp = date('Y-m-d H:i'); // Capture current timestamp

    return sprintf(
      '<input type="text" name="%1$s[%2$s]" id="%1$s-%2$s" class="%4$s" />
             <input type="hidden" name="%1$s--math-verify" value="%3$s" />',
      $this->formName,
      $this->fieldSlug,
      md5($this->answer . $this->secret . $currentTimestamp),
      $this->classlist
    );
  }

  // Build error message as html
  public function returnError($lang) {
    if (!empty($this->error)) {
      return sprintf('<span class="error-msg">%1$s</span>', $lang->getMessage('submit', 'invalid_captcha'));
    }
  }

  // Build error placeholder
  public function returnErrorPlaceholder() {
    return sprintf('<span class="error-msg" id="error--%1$s-%2$s"></span>', $this->formName, $this->fieldSlug);
  }

  // Get error message
  public function getErrorMessage($lang) {
    if (!empty($this->error)) {
      return $lang->getMessage('submit', 'invalid_captcha');
    }
  }

  protected function generateImage($text) {
    $textBoundingBox = imagettfbbox($this->fontSize, 0, $this->fontPath, $text);

    // Determine the width and height of the text box
    $textWidth = $textBoundingBox[2] - $textBoundingBox[0];
    $textHeight = $textBoundingBox[1] - $textBoundingBox[7];

    // Add some padding around the text
    $padding = 2;
    $imageWidth = $textWidth + ($padding * 2);
    $imageHeight = $textHeight + ($padding * 2);

    // Create a true color image with transparency
    $image = imagecreatetruecolor($imageWidth, $imageHeight);

    // Enable alpha blending and save the alpha channel
    imagesavealpha($image, true);
    $backgroundColor = imagecolorallocatealpha($image, 0, 0, 0, 127);
    imagefill($image, 0, 0, $backgroundColor);

    // Allocate the color for the text (white)
    $textColor = imagecolorallocate($image, $this->color[0], $this->color[1], $this->color[2]);

    // Align the text at the bottom of the image
    imagettftext($image, $this->fontSize, 0, $padding, $imageHeight - $padding, $textColor, $this->fontPath, $text);

    ob_start();
    imagepng($image);
    $data = ob_get_contents();
    ob_end_clean();

    imagedestroy($image);

    return '<span class="img-puzzle"><img src="data:image/png;base64,' . base64_encode($data) . '" alt="Math puzzle" /></span>';
  }


  public function validate() {
    $oneValid = false;
    $storedAnswer = $_POST[$this->formName . '--math-verify'] ?? '';

    // loop over possible timestamps from last 10 minutes
    for ($i = 0; $i < $this->ttl; $i++) {
      $timestamp = date('Y-m-d H:i', strtotime('-' . $i . ' minutes')); // Capture current timestamp

      // check if the answer is correct for any of the timestamps
      if (md5($this->fieldValue . $this->secret . $timestamp) === $storedAnswer) {
        $oneValid = true;
        break;
      }
    }

    if ($oneValid === false || $storedAnswer === '') {
      $this->error = 'invalid';
    } else {
      $this->fieldValue = 'ok';
    }

    $this->validated = true;
    return $this->isValid();
  }

  public function setValue($value) {
    $this->fieldValue = $value;
  }
}
