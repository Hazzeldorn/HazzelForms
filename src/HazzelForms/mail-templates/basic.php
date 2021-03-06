<?php

namespace HazzelForms;

/*
  *   This is a mail template for senidng hazzelform data to webmasters / responsible persons...
  *   It is included within mailer class ($this = Mailer class)
  *
  *   Requires:
  *   - a defined array $fields which contains all field objects
  *
  *   Returns:
  *   - HTML Email content
  */

?>
<!doctype html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
  <title><?php echo $this->subject; ?></title>
</head>
<body>
  <h3><?php echo $this->subject; ?></h3>
  <table rules="all" style="border-color: #aaa;" cellpadding="10">

    <?php foreach ($fields as $field) :
        ?>
      <tr><td><strong><?php echo $field->getName(); ?></strong></td><td><?php echo nl2br($field->getValue()); ?></td></tr>
        <?php
    endforeach;
    unset($field);
    ?>

  </table>
  <p><i>This E-Mail was sent via a contact form on <u><?php echo $_SERVER['HTTP_HOST']; ?></u>.</i></p>
</body>
</html>
