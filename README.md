# HazzelForms
### Create, validate and mail PHP forms in minutes!

Please visit the documentation if you want to use HazzelForms:<br/>
https://hazzeldorn.github.io/HazzelForms/
<br/><br/><br/><br/>


### Upcoming

- [ ] Prevent multiple submissions
- [ ] File-Upload status callbacks

### Changelog
===== V1.3.0 | 2023-03-16 ===== <br/>
* Feature added to send emails with SMTP using PHPMailer<br/>
* Improved template loader for emails<br/>
* Some code changes in existing mail function to ensure backwards compatibility.<br/>

===== V1.2.14 | 2022-06-03 ===== <br/>
* Classlist added to field-wrap
* Real label tag added around labels (instead of just using span elements)

===== V1.2.13 | 2021-06-23 ===== <br/>
* Bugfix: <code>$form->getFieldValues</code> function was broken for uploaded files

===== V1.2.12 | 2021-04-04 ===== <br/>
* Field value encoding improved (html entity decoding added)

===== V1.2.11 | 2021-04-03 ===== <br/>
* Character escaping improved (prevent multiple escapes)

===== V1.2.10 | 2021-02-17 ===== <br/>
* New config <em>submitcaption</em> to set submit button caption when using the <code>renderAll()</code> function
* New config <em>gridclass</em> to define the grid class when using the <code>renderAll()</code> function

===== V1.2.9 | 2021-02-10 ===== <br/>
* Namespace bugfix

===== V1.2.8 | 2020-12-27 ===== <br/>
* Refactoring to adhere to PSR-12
* Github action workflow file added

===== V1.2.7 | 2020-07-22 ===== <br/>
* Function added to check for errors
* Function added to clear fields
* Lazy validation added to URL fields

===== V1.2.6 | 2020-04-14 ===== <br/>
* Added a new language string for single checkboxes (useful for terms and conditions)
* Now allowing key / value pairs for option fields
* Removed the function that deletes uploaded files while sending emails (sending an attachement to multiple recipents was impossible)

===== V1.2.5 | 2020-04-13 ===== <br/>
Minor bugfix: Readopted stealthmode

===== V1.2.4 | 2020-04-07 ===== <br/>
* CSP compatibility added to HoneyPot fields
* Some code refactoring
* Translation file added: german (informal)
* Docs updated

===== V1.2.3 | 2020-03-24 ===== <br/>
Added the nl2br() function to the mail template to preserve line breaks

===== V1.2.2 | 2020-02-26 ===== <br/>
Bugfix: File upload did not accept 'required' => false

===== V1.2.1 | 2020-01-29 ===== <br/>
Bugfix: Number fields did not throw an error message when not required

===== V1.2.0 |  2020-01-13 ===== <br/>
Honeypot feature added.

===== V1.1.3 |  2019-12-19 ===== <br/>
Bugfix: File-Upload did not work when no other form fields existed.

===== V1.1.2 |  2019-12-12 ===== <br/>
Minor improvements regarding HTML structure of option fields.

===== V1.1 |  2019-12-11 ===== <br/>
First stable + tested release containing major improvements.

===== V1.0 |  2019-12-10 ===== <br/>
Initial version
