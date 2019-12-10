<?php
namespace HazzelForms;

  /*
  *   This is a mail template for senidng hazzelform data to webmasters / responsible persons...
  *
  *   Requires:
  *   - a defined array $formFields which is the return value of the function getFields() called in HazzelForm Class
  *   - this template must be included within mailer class ($this = Mailer class)
  *
  *   Returns:
  *   - HTML Email content
  *   - $attachements array with filepaths to add as attachements
  */

  // vars
  $attachements = array();

?>
<!doctype html>
<html>
<body>
  <h3>Neue Anfrage:</h3>
  <table rules="all" style="border-color: #aaa;" cellpadding="10">

    <?php foreach ($formFields as $field):
      if($field instanceof Captcha || $field->getName() == 'csrf_token'){
        // do not send captcha response & csrf-token
        continue;
      } elseif($field instanceof FileUpload){
        // add all files as attachements
        foreach($field->getValue() as $fileData){
          array_push($attachements, $fileData);
        } unset($fileData);
        continue;
      }
    ?>
      <tr><td><strong><?php echo ucfirst($field->getName()); ?></strong></td><td><?php echo $field->getValue(); ?></td></tr>
    <?php
    endforeach;
    unset($field);
    ?>

  </table>
  <p><i>Dieses E-Mail wurde über ein Kontaktformular auf <u><?php echo $_SERVER['HTTP_HOST']; ?></u> versendet.</i></p>
</body>
</html>
