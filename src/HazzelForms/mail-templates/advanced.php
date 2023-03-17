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
<body>

  <table  class="mailing-body" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td class="container">

        <!-- START CENTERED WHITE CONTAINER -->
        <table class="main" border="0" cellpadding="0" cellspacing="0">

          <tr>
            <td class="wrapper">
              <table border="0" cellpadding="0" cellspacing="0">
                  Hello <?= $vars['firstname'] ?? '' ?><br><br>

                  You can insert your own variables in this template<br><br>

                  Best Regards<br>
                  HazzelForms<br><br>
                <div class="logo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 250 100" width="250" height="100">
                        <rect width="250" height="100" fill="#cccccc"></rect>
                        <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="monospace" font-size="26px" fill="#333333">Your Logo</text>
                    </svg>
                </div>
              </table>
            </td>
          </tr>
        </table>
        <!-- END CENTERED WHITE CONTAINER -->


        <!-- START FOOTER -->
        <table border="0" cellpadding="0" cellspacing="0" class="footer">
          <tr>
            <td class="content-block powered-by">
              <span>Powered by XXXXXXXX</span>
            </td>
          </tr>
        </table>
        <!-- END FOOTER -->

      </td>
    </tr>
  </table>


  <style>
  /* Presets */
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    border: 0;
  }

  body {
    background-color: #f6f6f6;
  }

  body, p, span {
    font-family: sans-serif;
    font-size: 1em;
    line-height: 1.5em;
  }

  p {
    margin-bottom: 1.5em;
  }
  br {
    line-height: 1.5em;
  }

  .button-lg {
    display: inline-block;
    color: #eeeeee !important;
    border: 0px;
    background: #0d4c1d;
    cursor: pointer;
    text-decoration: none;
    font-size: 1.15em;
    font-weight: bold;
    margin-bottom: 1em;
    padding: 12px 25px;
  }
  .button-lg:hover {
    border: 0px;
  }

  table {
    border-collapse: separate;
    mso-table-lspace: 0pt;
    mso-table-rspace: 0pt;
    width: 100%;
    padding: 10px;
  }

  /* Brand */
  .logo {
      width: 100%;
      max-width: 150px;
  }

  /* Sections */
  table.main {
    background: #ffffff;
    border-radius: 3px;
  }
  td.container {
    vertical-align: top;
    display: block;
    margin: 0 auto;
    padding: 10px;
    max-width: 600px;
    width: 100%;
  }

  table.footer {
    clear: both;
    margin-top: 10px;
    text-align: center;
    width: 100%;
  }
  table.footer .powered-by {
    font-family: sans-serif;
    vertical-align: top;
    padding-bottom: 10px;
    padding-top: 10px;
    font-size: 0.75em;
    color: #999999;
    width: 100%;
    text-align: center;
  }

  /* Mobile */
  @media only screen and (max-width: 650px) {
    table[class=mailing-body] .wrapper,
    table[class=mailing-body] .article {
      padding: 10px !important;
    }
    table[class=mailing-body] .content {
      padding: 0 !important;
    }
    table[class=mailing-body] .container {
      padding: 0 !important;
      width: 100% !important;
    }
  }
  </style>

</body>
</html>
