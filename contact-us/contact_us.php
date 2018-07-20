<<<<<<< HEAD
<?PHP
/*
=======
<?PHP
/*
>>>>>>> 2f93be75c8412ab716c86f9f0781c7f9fec0bf01
Simfatic Forms Main Form processor script

This script does all the server side processing. 
(Displaying the form, processing form submissions,
displaying errors, making CAPTCHA image, and so on.) 

All pages (including the form page) are displayed using 
templates in the 'templ' sub folder. 

The overall structure is that of a list of modules. Depending on the 
arguments (POST/GET) passed to the script, the modules process in sequence. 

Please note that just appending  a header and footer to this script won't work.
To embed the form, use the 'Copy & Paste' code in the 'Take the Code' page. 
To extend the functionality, see 'Extension Modules' in the help.
<<<<<<< HEAD

*/

@ini_set("display_errors", 1);//the error handler is added later in FormProc
error_reporting(E_ALL);

require_once(dirname(__FILE__)."/includes/Contact_Us-lib.php");
$formproc_obj =  new SFM_FormProcessor('Contact_Us');
$formproc_obj->initTimeZone('default');
$formproc_obj->setFormID('383d16d2-ae8a-499c-8e64-ff86182483f2');
$formproc_obj->setFormKey('14876fbb-88ef-4e19-937c-8fc8b68f8a9a');
$formproc_obj->setLocale('en-US','M/d/yyyy');
$formproc_obj->setEmailFormatHTML(true);
$formproc_obj->EnableLogging(false);
$formproc_obj->SetDebugMode(false);
$formproc_obj->setIsInstalled(true);
$formproc_obj->SetPrintPreviewPage(sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_print_preview_file.txt"));
$formproc_obj->SetSingleBoxErrorDisplay(true);
$formproc_obj->setFormPage(0,sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_form_page_0.txt"));
$formproc_obj->AddElementInfo('Name','text','');
$formproc_obj->AddElementInfo('Name2','text','');
$formproc_obj->AddElementInfo('Email','email','');
$formproc_obj->AddElementInfo('Name3','text','');
$formproc_obj->AddElementInfo('Name4','text','');
$formproc_obj->AddElementInfo('Message','multiline','');
$formproc_obj->setIsInstalled(true);
$formproc_obj->setFormFileFolder('./formdata');
$formproc_obj->SetHiddenInputTrapVarName('tde6032d6c83a987d660d');
$formproc_obj->SetFromAddress('contactus@maltroncraft.tk');
$formproc_obj->InitSMTP('smtp.gmail.com','maltroncraft@gmail.com','C4D94372378CFDDD75C9B54274B9A9A9',587);
$page_renderer =  new FM_FormPageDisplayModule();
$formproc_obj->addModule($page_renderer);

$admin_page =  new FM_AdminPageHandler();
$admin_page->SetPageTemplate(sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_admin_page_templ.txt"));
$admin_page->SetLogin('Owner','C4D94372378CFDDD75C9B54274B9A9A9');
$admin_page->SetLoginTemplate(sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_admin_page_login.txt"));
$formproc_obj->addModule($admin_page);

$validator =  new FM_FormValidator();
$validator->addValidation("Name","required","Please enter your First name.");
$validator->addValidation("Name2","required","Please enter in your Last name.");
$validator->addValidation("Email","email","Please enter a valid email to reply back to you.");
$validator->addValidation("Name3","required","Please enter your MCPE/Win 10 username. If you dont have one type \"none\"");
$validator->addValidation("Name4","required","Please enter your MC Java username. If you dont have one type \"none\"");
$validator->addValidation("Message","required","Please enter your message.");
$formproc_obj->addModule($validator);

$data_email_sender =  new FM_FormDataSender(sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_email_subj.txt"),sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_email_body.txt"),'%Email%');
$data_email_sender->AddToAddr('MaltronCraft <maltroncraft@gmail.com>');
$formproc_obj->addModule($data_email_sender);

$autoresp =  new FM_AutoResponseSender(sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_resp_subj.txt"),sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_resp_body.txt"));
$autoresp->SetToVariables('Name','Email');
$formproc_obj->addModule($autoresp);

$csv_maker =  new FM_FormDataCSVMaker(1024);
$csv_maker->AddCSVVariable(array('_sfm_form_submision_time_','_sfm_form_submision_date_','_sfm_referer_page_','_sfm_visitor_ip_','_sfm_visitor_browser_','_sfm_visitor_os_','_sfm_user_agent_','_sfm_unique_id_','Name','Name2','Email','Name3','Name4','Message'));
$formproc_obj->addModule($csv_maker);

$tupage =  new FM_ThankYouPage(sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_thank_u.txt"));
$formproc_obj->addModule($tupage);

$page_renderer->SetFormValidator($validator);
$formproc_obj->ProcessForm();

=======

*/

@ini_set("display_errors", 1);//the error handler is added later in FormProc
error_reporting(E_ALL);

require_once(dirname(__FILE__)."/includes/Contact_Us-lib.php");
$formproc_obj =  new SFM_FormProcessor('Contact_Us');
$formproc_obj->initTimeZone('default');
$formproc_obj->setFormID('383d16d2-ae8a-499c-8e64-ff86182483f2');
$formproc_obj->setFormKey('14876fbb-88ef-4e19-937c-8fc8b68f8a9a');
$formproc_obj->setLocale('en-US','M/d/yyyy');
$formproc_obj->setEmailFormatHTML(true);
$formproc_obj->EnableLogging(false);
$formproc_obj->SetDebugMode(false);
$formproc_obj->setIsInstalled(true);
$formproc_obj->SetPrintPreviewPage(sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_print_preview_file.txt"));
$formproc_obj->SetSingleBoxErrorDisplay(true);
$formproc_obj->setFormPage(0,sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_form_page_0.txt"));
$formproc_obj->AddElementInfo('Name','text','');
$formproc_obj->AddElementInfo('Name2','text','');
$formproc_obj->AddElementInfo('Email','email','');
$formproc_obj->AddElementInfo('Name3','text','');
$formproc_obj->AddElementInfo('Name4','text','');
$formproc_obj->AddElementInfo('Message','multiline','');
$formproc_obj->setIsInstalled(true);
$formproc_obj->setFormFileFolder('./formdata');
$formproc_obj->SetHiddenInputTrapVarName('tde6032d6c83a987d660d');
$formproc_obj->SetFromAddress('contactus@maltroncraft.tk');
$formproc_obj->InitSMTP('smtp.gmail.com','maltroncraft@gmail.com','C4D94372378CFDDD75C9B54274B9A9A9',587);
$page_renderer =  new FM_FormPageDisplayModule();
$formproc_obj->addModule($page_renderer);

$admin_page =  new FM_AdminPageHandler();
$admin_page->SetPageTemplate(sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_admin_page_templ.txt"));
$admin_page->SetLogin('Owner','C4D94372378CFDDD75C9B54274B9A9A9');
$admin_page->SetLoginTemplate(sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_admin_page_login.txt"));
$formproc_obj->addModule($admin_page);

$validator =  new FM_FormValidator();
$validator->addValidation("Name","required","Please enter your First name.");
$validator->addValidation("Name2","required","Please enter in your Last name.");
$validator->addValidation("Email","email","Please enter a valid email to reply back to you.");
$validator->addValidation("Name3","required","Please enter your MCPE/Win 10 username. If you dont have one type \"none\"");
$validator->addValidation("Name4","required","Please enter your MC Java username. If you dont have one type \"none\"");
$validator->addValidation("Message","required","Please enter your message.");
$formproc_obj->addModule($validator);

$data_email_sender =  new FM_FormDataSender(sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_email_subj.txt"),sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_email_body.txt"),'%Email%');
$data_email_sender->AddToAddr('MaltronCraft <maltroncraft@gmail.com>');
$formproc_obj->addModule($data_email_sender);

$autoresp =  new FM_AutoResponseSender(sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_resp_subj.txt"),sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_resp_body.txt"));
$autoresp->SetToVariables('Name','Email');
$formproc_obj->addModule($autoresp);

$csv_maker =  new FM_FormDataCSVMaker(1024);
$csv_maker->AddCSVVariable(array('_sfm_form_submision_time_','_sfm_form_submision_date_','_sfm_referer_page_','_sfm_visitor_ip_','_sfm_visitor_browser_','_sfm_visitor_os_','_sfm_user_agent_','_sfm_unique_id_','Name','Name2','Email','Name3','Name4','Message'));
$formproc_obj->addModule($csv_maker);

$tupage =  new FM_ThankYouPage(sfm_readfile(dirname(__FILE__)."/templ/Contact_Us_thank_u.txt"));
$formproc_obj->addModule($tupage);

$page_renderer->SetFormValidator($validator);
$formproc_obj->ProcessForm();

>>>>>>> 2f93be75c8412ab716c86f9f0781c7f9fec0bf01
?>
