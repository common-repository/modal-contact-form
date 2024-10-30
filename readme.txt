=== Modal Contact Form ===
Contributors: lucbianco
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UGNC8G9TBNJAJ
Tags: contact form, modal 
Requires at least: 3.1
Tested up to: 4.6
Stable tag: 1.8.1
License: GPLv2 or later

Provide a shortcode which will be replaced by either a button able to open a modal contact form, or by a classic contact form inside a page


== Description ==

Modal Contact Form is a plugin which allows to insert a shortcode where you want to provide a contact button on your site.
Clicking on this button then opens a modal contact form with following features:

* Slider captcha validation (Send button is visible only after drag the slider).
* Modal window is CSS only
* Form fields are: Name, Email (optional), Phone number(optional), Message, Checkbox to receive a copy (if Email option is enabled)
* Optional field is file attachment (extensions allowed are jpg, jpeg, jpe, pdf, zip, png, txt, doc, docx, xls, xlsx, xla, xlt and xlw). Chosen rule : In case attached file extension is not allowed, mail is still sent but without file attached
* Autoreply feature to add a specific message to your copy message when you are out of office
* Languages translation ready for frontend and backend (English/French provided)
* One default style CSS provided (lb-modal-contact-form.css) which can be turned off in admin interface to use your own 

Note that it is also possible to disable modal window and just have contact form inside a page or post.  
  
== Installation ==

1. Make sure you are using WordPress 3.1 or later and that your server is running PHP 5.2.4 or later (same requirement as WordPress itself)
2. Download the plugin
3. Extract all the files.
4. Upload everything (keeping the directory structure) to the '/wp-content/plugins/' directory.
5. Activate the plugin through the 'Plugins' menu in WordPress.
6. Go to the Contact Modal Form settings menu and fill your email address to can receive emails
7. Select form fields you want to display (phone and email are optional)
8. Insert a shortcode [insert-modal-contact-form] where you want to have your contact button
9. In case you want or need to create a customized CSS, name it as lb-modal-contact-form-custom.css and place it into '/plugins/lb-modal-contact-form' folder 
10. In case you don't need modal window but just need a contact form, you can disable modal window (select "off" in this case)

== Screenshots ==

1. Contact button created by inserting shortcode 
2. Modal Contact Form window is displayed after clicking contact button (modal display "on") 
3. Settings options

== Frequently Asked Questions == 

Not yet

== Upgrade Notice ==

New features as optional fields or possibility to disable modal window have been added. 

== Changelog ==

= Version 1.8.1 =

* Removed non used files

= Version 1.8 =

* Updated CSS to be compatible with default WordPress 4.6 theme : Twenty Sixteen

= Version 1.7 =

* Add autoreply feature

= Version 1.6.1 =

* Avoid multiple emails sending in case of several shortcodes in same page

= Version 1.6 =

* Customer email and phone are now optional form fields
* Keep users parameters after plugin update

= Version 1.5.1 =

* Fixed an issue which causes new options added where not filled at a plugin update

= Version 1.5 =

* Added an option to disable modal window. In this case contact form will be embedded in page or post.

= Version 1.2 =

* Plugin settings backend is now ready for translation 
* Added optional attachment file feature 
* fixed issue : settings was reset at deactivation

= Version 1.1.3 =

* Updated way to sanitize form inputs to correctly display quotes

= Version 1.1.2 =

* Fixed translation issue

= Version 1.1.1 =

* Fixed versioning issue

= Version 1.1 =

* Added field for phone number
* After submit the form is closed
* Fixed minor issue in default CSS (removed text decoration on link of contact button) 

= Version 1.0 =

* First stable version

