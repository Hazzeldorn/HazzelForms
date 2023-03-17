<?php
/*
  *   @var $subject
  *   @var $fields
  *
  *   This is a mail template for sending HazzelForm data to webmasters / responsible persons...
  *
  *   Requires:
  *   - a defined variable $subject which contains the subject of the mail
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
  <title><?php echo $subject; ?></title>
</head>
<body>
  <h3><?php echo $subject; ?></h3>
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
