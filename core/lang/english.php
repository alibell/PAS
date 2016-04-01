<?php
	/*
		22/02/15 - english.php - Ali Bellamine
		Fichier de language - Langue : Français
	*/
	
/*
	Interface utilisateur
*/
define('LANG_DISCONNECT','Disconnect');
define('LANG_INTERFACE_SELECTEVALUATIONTYPE','Select an evaluation type :');
define('LANG_INTERFACE_SELECTBUTTON','Select');
define('LANG_ADMIN_MENU_TITLE','Menu');

/*
	Installeur
*/

define('LANG_INSTALL_TITLE', 'PAS\'s installation');
define('LANG_INSTALL_SELECT_LANGUAGE', 'Select language');
define('LANG_INSTALL_NEXT_BUTTON', 'Next');	
define('LANG_INSTALL_CONFIRM_BUTTON', 'Confirm the installation');	
define('LANG_INSTALL_LANG_TITLE', 'Language');
define('LANG_INSTALL_SETTINGS_TITLE', 'PAS\'s settings');
define('LANG_INSTALL_REQUIREMENT_TITLE', 'Requirements');
define('LANG_INSTALL_BDD_TITLE', 'Mysql database connection');
define('LANG_INSTALL_NEWUSER_TITLE', 'Add a new user');
define('LANG_INSTALL_CONFIRM_TITLE', 'Confirm');
define('LANG_INSTALL_CONFIRM_FINISH', 'Installation completed.<br />Remove the folder "install" from PAS\'s root.');
define('LANG_INSTALL_BDD_VALEUR_SERVER','Database server');
define('LANG_INSTALL_BDD_VALEUR_PORT','Port');
define('LANG_INSTALL_BDD_VALEUR_DBNAME','Database name');
define('LANG_INSTALL_BDD_VALEUR_BDDUSERNAME','Database username');
define('LANG_INSTALL_BDD_VALEUR_PASSWORD','Database password');
define('LANG_INSTALL_NEWUSER_VALEUR_NOM','First name');
define('LANG_INSTALL_NEWUSER_VALEUR_PRENOM','Second name');
define('LANG_INSTALL_NEWUSER_VALEUR_NBETU','CAS\'s id');
define('LANG_INSTALL_NEWUSER_VALEUR_MAIL','Mail adress');
define('LANG_INSTALL_ERREUR_REQUIREMENT_NOTMET','Your server doest not match the requirements');
define('LANG_INSTALL_ERREUR_FORM_INCOMPLETE','The form is incomplete');
define('LANG_INSTALL_ERREUR_BDD_CONNECTION_IMPOSSIBLE','Database connection failed');
define('LANG_INSTALL_ERREUR_SETTINGS_URL_INVALID','The URL adress is in an invalid format');
define('LANG_INSTALL_ERREUR_SETTINGS_MAIL_INVALID','The mail adress is in an invalid format');
define('LANG_INSTALL_ERREUR_SETTINGS_NUMBER_INVALID', 'A number was attempted');
define('LANG_INSTALL_ERREUR_SETTINGS_FILE_INVALID', 'The selected file doesn\'t exist');
define('LANG_INSTALL_ERREUR_SETTINGS_SMTPCONNECTION_IMPOSSIBLE', 'The SMTP connection failed');
define('LANG_INSTALL_ERREUR_BDDINSTALL_UNKNOWERROR', 'Unknow error appended.');

/*
	Nom des menus
*/

	// Menus principaux
define('LANG_MENU_MAIN_ACCUEIL', 'Home');
define('LANG_MENU_MAIN_MYSERVICE', 'My service');
define('LANG_MENU_MAIN_EVALUATION', 'Evaluation');
define('LANG_MENU_MAIN_ADMINISTRATION', 'Administration');

	// Menus secondaires
define('LANG_MENU_SECONDARY_ACCUEIL', 'Home');
define('LANG_MENU_SECONDARY_MYSTUDENT', 'My students');
define('LANG_MENU_SECONDARY_MYEVAL', 'My evaluations');
define('LANG_MENU_SECONDARY_VIEWEVAL', 'View evaluations');
define('LANG_MENU_SECONDARY_USERS', 'Users');
define('LANG_MENU_SECONDARY_SERVICES', 'Services');
define('LANG_MENU_SECONDARY_EVALUATIONS', 'Evaluations');
define('LANG_MENU_SECONDARY_SETTINGS', 'Settings');
define('LANG_MENU_SECONDARY_CHARTE', 'Disclaimer');
define('LANG_MENU_SECONDARY_MAIL', 'Mail Campaign');
define('LANG_MENU_SECONDARY_BUG_MANAGER', 'Bug Manager');

/*
	Nom des pages
*/

define('LANG_PAGE_INDEX', 'Home');
define('LANG_PAGE_EVAL', 'Rate internship');
define('LANG_PAGE_LOGIN', 'Login');
define('LANG_PAGE_EVALDO', 'Evaluation form');
define('LANG_PAGE_EVALVIEW', 'View evaluations');
define('LANG_PAGE_ADMINUTILISATEURS', 'Manage users');
define('LANG_PAGE_ADMINSERVICES', 'Manage services');
define('LANG_PAGE_ADMINEVALUATIONS', 'Manage evaluation');
define('LANG_PAGE_ADMINUSERLIST', 'Users selector\'s ajax');
define('LANG_PAGE_MYSTUDENT', 'My students');
define('LANG_PAGE_SETTINGS', 'Settings');
define('LANG_PAGE_AJAXBUG', 'Bug notification\s ajax');
define('LANG_PAGE_BUG_ADMIN', 'Bug manager');
define('LANG_PAGE_CHARTE', 'Disclaimer');
define('LANG_PAGE_ADMINMAIL','Mail sending campaign');
define('LANG_PAGE_ABOUT', 'About PAS');

/*
	Erreurs
*/
define('LANG_ERRORS','Errors');
define('LANG_ERROR_USER_INVALID','Selected user is invalid.');
define('LANG_ERROR_NOT_REGISTERED','You are not registered in the software database.');
define('LANG_ERROR_SERVICE_INVALID','Selected service is invalid.');
define('LANG_ERROR_EVALUATION_INVALID','Selected evaluation is invalid.');
define('LANG_ERROR_EVALUATIONTYPE_INVALID','The evaluation module is disabled.');
define('LANG_ERROR_EVALUATION_DONE','You have already filled this evaluation form.');
define('LANG_ERROR_EVALUATION_WRONGDATE','The evaluation period does not match with the actual date.');
define('LANG_ERROR_NAVIGATION_NORIGHT','You do not have the right to display this content.');
define('LANG_ERROR_IMPORT_FILE','Error while sending file.');
define('LANG_ERROR_IMPORT_FILE_INVALID','Invalid file extension.');
define('LANG_ERROR_CHECK_NB_ETUDIANT','The student ID is invalid');
define('LANG_ERROR_AFFECTATION_SERVICE_ALREADY','This student is already affected in this service.');
define('LANG_ERROR_AFFECTATION_DATE_DEBUT','The start date is in an invalid format.');
define('LANG_ERROR_AFFECTATION_DATE_FIN','The end date is in an invalid format.');
define('LANG_ERROR_AFFECTATION_DATE_MISMATCH','The start and end date doesn\'t match together.');
define('LANG_ERROR_DATE_MISMATCH','The start and end date doesn\'t match together.');
define('LANG_ERROR_UNKNOWN','Unknown error.');
define('LANG_ERROR_AFFECTATION_INVALID', 'The selected affectation doesn\'t exist.');
define('LANG_ERROR_DATA_INVALID_FORMAT', 'The data are in an invalid format.');
define('LANG_ERROR_MAIL_NOSETTINGS','There is no email adress set.');
define('LANG_ERROR_MAIL_SEND','An error occured while sending the file.');
define('LANG_ERROR_MAIL_INVALID', 'The email adress format is invalid.');
define('LANG_ERROR_MAIL_RELOAD', 'Reload the page to continue sending mail.');
define('LANG_ERROR_URL_INVALID', 'The adress is invalid.');
define('LANG_ERROR_EVALUATION_NOSETTINGS', 'No parameters was set for this evaluation campaign.');
define('LANG_ERROR_FORM_INCOMPLETE', 'Form incomplete');
define('LANG_ERROR_DATA_INCOMPLETE', 'Incomplete data');
define('LANG_ERROR_PROMOTION_INEXISTANT', 'The specified class doesn\'t exist');
define('LANG_ERROR_RANG_BATCH_INVALID', 'The user right is invalid');
define('LANG_ERROR_HOSPITAL_INVALID', 'The selected hospital is not registered');
define('LANG_ERROR_CERTIFICATE_INVALID', 'The selected certificate is not registered');
define('LANG_ERROR_SPECIALITE_INVALID', 'The selected speciality is not registered');
define('LANG_ERROR_EVAL_LOGINAS_FORBIDDEN', 'The access to that evaluation type is forbidden when logged as an other user');

/*
	Message de confirmation
*/

define('LANG_SUCCESS_LOGOUT','Logout successful.');
define('LANG_SUCCESS_EVALUATION_FORM','Evaluation registered.');
define('LANG_SUCCESS_EVALUATION_SETTING','Configuration registered.');
define('LANG_SUCCESS_MAIL_SEND','The message was sent with success.');

/*
	Mois de l'année
*/
define('LANG_MONTH_1','January');
define('LANG_MONTH_2','Febuary');
define('LANG_MONTH_3','March');
define('LANG_MONTH_4','April');
define('LANG_MONTH_5','May');
define('LANG_MONTH_6','June');
define('LANG_MONTH_7','July');
define('LANG_MONTH_8','August');
define('LANG_MONTH_9','September');
define('LANG_MONTH_10','October');
define('LANG_MONTH_11','November');
define('LANG_MONTH_12','December');

/*
	Rang des utilisateurs
*/

		define('LANG_RANG_VALUE_0','Guest');
		define('LANG_RANG_VALUE_1','Student');
		define('LANG_RANG_VALUE_2','Teacher');
		define('LANG_RANG_VALUE_3','Administrator');
		define('LANG_RANG_VALUE_4','Super Asministrator');


/*
	Message propres aux pages
*/

	/*
		Accueil
	*/
	
	define('LANG_ACCUEIL_DESCRIPTION_1',"<strong>Rate</strong> your internship"); 
	define('LANG_ACCUEIL_DESCRIPTION_2',"<strong>Consult</strong> the evaluations data"); 
	define('LANG_ACCUEIL_DESCRIPTION_3',"<strong>Check</strong> that you have filled all your required evaluations"); 
	define('LANG_ACCUEIL_CONNEXION',"Connexion"); 
	define('LANG_ACCUEIL_ABOUT',"About PAS"); 

	/*
		Formulaires
	*/
	
	define('LANG_FORM_SUBMIT_FILE', 'Send file');
	
	/*
		Evaluation
	*/
	
	define('LANG_EVAL_LIST', 'Evaluations list');
	define('LANG_EVAL_LIST_NOEVAL', 'There are no evaluations for this time period.');
	define('LANG_EVAL_LIST_OPTION_NEXT', 'Next');
	define('LANG_EVAL_LIST_OPTION_NOW', 'Now');
	define('LANG_EVAL_LIST_OPTION_OLD', 'Archives');

	/*
		Pages d'administrations
	*/	
		define('LANG_ADMIN_TABLE_TITLE_ADMIN','Administration');			
		define('LANG_ADMIN_LISTE_TABLE_TITLE_NOM','Name');
		define('LANG_ADMIN_LISTE_TABLE_TITLE_ETUDIANTSNB','Number of registered students');
		define('LANG_ADMIN_LISTE_TABLE_TITLE_ETUDIANTS','Students list');

		// Utilisateurs
		define('LANG_ADMIN_UTILISATEURS_MENU_ITEM_LISTE','Users list');
		define('LANG_ADMIN_UTILISATEURS_MENU_ITEM_PROMOTION','Manage classes');
		define('LANG_ADMIN_UTILISATEURS_MENU_ITEM_ADD','Add users');
		define('LANG_ADMIN_UTILISATEURS_LISTE_FILTER_SEARCHBAR','Search an user');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NOM','First Name');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PRENOM','Second Name');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_MAIL','Mail');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NBETUDIANT','Student ID');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PROMOTION','Class');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PROMOTIONNB','Number of registered');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_RANG','Rank');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_ADMIN','Administration');		
		
			// Visualisation d'un profil
			define('LANG_ADMIN_UTILISATEURS_CAT_INFOSGENERALES','General informations');
			define('LANG_ADMIN_UTILISATEURS_CAT_ETUDIANT','Student informations');
			define('LANG_ADMIN_UTILISATEURS_CAT_CHEF','Chief informations');
			define('LANG_ADMIN_UTILISATEURS_CAT_STAGES','Internship');
			define('LANG_ADMIN_UTILISATEURS_FORM_SUBMIT_ADD','Add an user');		
			define('LANG_ADMIN_UTILISATEURS_FORM_SUBMIT_EDIT','Validate changes');		
			define('LANG_ADMIN_UTILISATEURS_FORM_SUBMIT_DELETE','Delete the profil and associated data');		
			define('LANG_ADMIN_UTILISATEURS_FORM_SUBMIT_DELETE_CONFIRM','Confirm');		
			
			// Promotions
			define('LANG_ADMIN_PROMOTION_LIST_ETUDIANTS','Student registered in the class');
			define('LANG_ADMIN_PROMOTION_NOM_TITRE','Class\'s name');
			define('LANG_ADMIN_PROMOTION_ADD_PROMOTION','Add an class');
			define('LANG_ADMIN_PROMOTION_FORM_SUBMIT_EDIT','Valid changes');		
			define('LANG_ADMIN_PROMOTION_FORM_SUBMIT_ADD','Add the classe');		
			define('LANG_ADMIN_PROMOTION_FORM_SUBMIT_DELETE','Delete the class and related data');		
			define('LANG_ADMIN_PROMOTION_FORM_SUBMIT_DELETE_CONFIRM','Confirm');		

			// Ajout des utilisateurs
			define('LANG_ADMIN_UTILISATEURS_MENU_IMPORTCSV','Import an user list file');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_IMPORTFILE','Import a CSV file containing first name, second name (optionnal), mail adresse (more than one can be set, adress should be separate by a ";"), student ID, class ID (optionnal), and user type  (1 for student, 2 for teacher).<br /> If an user already exist (student ID registered), the user profil will be actualised with file data.');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_DOWNLOADPROMOTIONLIST','Download classes ID list');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_DOWNLOADRAWCSV','File to import');	
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_NOM','First name');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_PRENOM','Second name');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_MAIL','Mail adress');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_NBETU','Student ID');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_PROMOTION','Class');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_RANG','User rights');			
			define('LANG_ADMIN_USERS_MENU_BATCH_TODO_INSERT', 'Users to add');
			define('LANG_ADMIN_USERS_MENU_BATCH_TODO_UPDATE', 'Users to update');
		
		// Campagnes d'envoi d'emails
		define('LANG_ADMIN_MAIL_MENU_ITEM_LISTE', 'Campaign List');
		
		// Services
		define('LANG_ADMIN_SERVICES_ID','Service ID');
		define('LANG_ADMIN_SERVICES_NOM','Service\' name');		
		define('LANG_ADMIN_SERVICES_NOM_SERVICEOF','Pr');		
		define('LANG_ADMIN_SERVICES_MENU_ITEM_LISTE','Manage service');		
		define('LANG_ADMIN_SERVICES_MENU_ITEM_SPECIALITE','Manage speciality');		
		define('LANG_ADMIN_SERVICES_MENU_ITEM_CERTIFICAT','Manage certificates');	
		define('LANG_ADMIN_SERVICES_MENU_ITEM_HOPITAUX','Manage hospitals');		
		define('LANG_ADMIN_SERVICES_MENU_ITEM_AFFECTATIONS','Students affectation');		
		define('LANG_ADMIN_SERVICES_LISTE_FILTER_SEARCHBAR','Search for a service');
		define('LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_NOM','Name');
		define('LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_HOPITAL','Hospital');
		define('LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_CHEF','Chief');
		define('LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_SPECIALITE','Speciality');
		
			// Visuation d'un service
			define('LANG_ADMIN_SERVICES_CAT_CERTIFICATS','Certificats');
			define('LANG_ADMIN_SERVICES_LIST_STUDENT','Students affected in the service');		
			define('LANG_ADMIN_SERVICES_FORM_SUBMIT_ADD','Add a service');		
			define('LANG_ADMIN_SERVICES_FORM_SUBMIT_EDIT','Validate changes');		
			define('LANG_ADMIN_SERVICES_FORM_SUBMIT_DELETE','Delete the service and related data');		
			define('LANG_ADMIN_SERVICES_FORM_SUBMIT_DELETE_CONFIRM','Confirm');		
		
			// Hopitaux
			define('LANG_ADMIN_HOPITAUX_LISTE_TABLE_TITLE_NOM','Name');
			define('LANG_ADMIN_HOPITAUX_LISTE_TABLE_TITLE_ALIAS','Label');
			define('LANG_ADMIN_HOPITAUX_LISTE_TABLE_TITLE_SERVICESNB','Number of registered services');
			define('LANG_ADMIN_HOPITAUX_ADD_HOPITAL','Add an hopital');
			define('LANG_ADMIN_HOPITAL_LISTE_SERVICES','Services list');
			define('LANG_ADMIN_HOPITAL_FORM_SUBMIT_EDIT','Change hospital\'s data');
			define('LANG_ADMIN_HOPITAL_FORM_SUBMIT_ADD','Add an hospital');
			define('LANG_ADMIN_HOPITAL_FORM_SUBMIT_DELETE','Delete the hospital and related data');
			define('LANG_ADMIN_HOPITAL_FORM_SUBMIT_DELETE_CONFIRM','Confirm');
			
			// Specialite

			define('LANG_ADMIN_SPECIALITE_LISTE_TABLE_TITLE_SERVICESNB','Number of registered services');
			define('LANG_ADMIN_SPECIALITE_ADD_SPECIALITE','Add a speciality');
			define('LANG_ADMIN_SPECIALITE_FORM_SUBMIT_EDIT','Change speciality\'s data');
			define('LANG_ADMIN_SPECIALITE_FORM_SUBMIT_ADD','Add a spécialité');
			define('LANG_ADMIN_SPECIALITE_FORM_SUBMIT_DELETE','Delete the speciality and related data');
			define('LANG_ADMIN_SPECIALITE_FORM_SUBMIT_DELETE_CONFIRM','Confirm');
			
			// Certificats
			
			define('LANG_ADMIN_CERTIFICAT_LISTE_TABLE_TITLE_PROMO','Class');
			define('LANG_ADMIN_CERTIFICAT_ADD_CERTIFICAT','Add a certificat');
			define('LANG_ADMIN_CERTIFICAT_FORM_SUBMIT_EDIT','Change certificate\'s data');
			define('LANG_ADMIN_CERTIFICAT_FORM_SUBMIT_ADD','Add a certificat');
			define('LANG_ADMIN_CERTIFICAT_FORM_SUBMIT_DELETE','Delete the certificate and related data');
			define('LANG_ADMIN_CERTIFICAT_FORM_SUBMIT_DELETE_CONFIRM','Confirm');
			
		// Affectations
			
			define('LANG_ADMIN_AFFECTATIONS_MENU_AFFECTATIONONE','Student\s affectation');
			define('LANG_ADMIN_AFFECTATIONS_MENU_BATCH','Import an affectation file');
			define('LANG_ADMIN_AFFECTATIONS_MENU_BATCH_TODO','Affectations to add');
			define('LANG_ADMIN_AFFECTATIONS_MENU_BATCH_IMPORTFILE','Please import a CSV file containing student ID, service ID and the affectation time interval.');
			define('LANG_ADMIN_AFFECTATIONS_MENU_BATCH_DOWNLOADSERVICE','Download services ID list');
			define('LANG_ADMIN_AFFECTATIONS_MENU_BATCH_DOWNLOADCSV','Download CSV File');
			define('LANG_ADMIN_AFFECTATIONS_DATE_DEBUT', 'Beginning Date (JJ/MM/AAA)');
			define('LANG_ADMIN_AFFECTATIONS_DATE_FIN', 'End Date (JJ/MM/AAA)');
			define('LANG_ADMIN_AFFECTATIONS_BATCH_SUCCESS', 'Affectations successfully imported.');
			define('LANG_ADMIN_AFFECTATIONS_FORM_SUBMIT_EDIT','Change affectation data');
			define('LANG_ADMIN_AFFECTATIONS_FORM_SUBMIT_ADD','Add and affectation');
			define('LANG_ADMIN_AFFECTATIONS_FORM_SUBMIT_DELETE','Delete affectation and related data');
			define('LANG_ADMIN_AFFECTATIONS_FORM_SUBMIT_DELETE_CONFIRM','Confirm');
			define('LANG_ADMIN_AFFECTATIONS_ADD_AFFECTATION','Add an affectation');
			
		// Evaluations
		
			define('LANG_ADMIN_EVALUATIONS_MENU_ITEM_LISTE', 'Evaluation campaign');
			define('LANG_ADMIN_EBALUATION_MENU_ITEM_MODULE', 'Evaluation addon');
			define('LANG_ADMIN_EVALUATIONS_LISTE_TABLE_TITLE_TYPE', 'Evaluation Type');
			define('LANG_ADMIN_EVALUATIONS_LISTE_TABLE_TITLE_REMPLISSAGE', 'Answer ratio');
			define('LANG_ADMIN_EVALUATIONS_LISTE_TABLE_TITLE_PERIODE', 'Date');
			define('LANG_ADMIN_EVALUATIONS_USERLIST_LASTUPDATE', 'Last update ');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_EDIT','Change evaluation data');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_BUTTON','Send an email to %d users.');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_OBJET', 'Object');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_OBJET_DEFAULT', 'RAPPEL : Evaluation non remplie');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_MSG', 'Message');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_MSG_DEFAULT', 'Vous n\'avez pas remplis l\'évaluation suivante : %s.<br />Veuillez vous rendre à l\'adresse suivante afin de la remplir : %s (copier-coller le lien dans votre barre d\'adresse)');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_CANCEL', 'Cancel');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_ADD','Add an évaluation');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_DELETE','Delete the evaluation campaign and related data');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_DELETE_CONFIRM','Confirm');
			define('LANG_ADMIN_EVALUATION_CSV_STATE','Evaluation state');
			define('LANG_ADMIN_EVALUATION_CSV_COMPLETE','Evaluation complete');
			define('LANG_ADMIN_EVALUATION_CSV_NOT_COMPLETE','Evaluation not complete');
			
		// Gestion des modules
		
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_NOM','Addon name');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_FOLDER','Addon path');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_STATE','State');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_STATE_OPTION_0','Inactive');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_STATE_OPTION_1','Active');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_RIGHT', 'Access  right');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_RIGHT_OPTION_0', 'no');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_RIGHT_OPTION_1', 'yes');
			define('LANG_ADMIN_MODULE_RIGHT_FORM_SUBMIT', 'Validate');
			
		// Envoie de mails
			define('LANG_ADMIN_SENDMAIL_TO', 'To');
			define('LANG_ADMIN_SENDMAIL_OBJECT', 'Subject');
			define('LANG_ADMIN_SENDMAIL_MESSAGE', 'Message');
			define('LANG_ADMIN_SENDMAIL_SEND', 'Send');
			define('LANG_ADMIN_SENDMAIL_SENT', 'All mail sent');
			define('LANG_ADMIN_SENDMAIL_ALLCHANGESSAVED', 'All changes saved');
			
		// Paramètres
		
			define('LANG_ADMIN_PARAMETRE_TITLES','Settings');
			define('LANG_ADMIN_PARAMETRE_TABLE_PARAMETRE','Setting');
			define('LANG_ADMIN_PARAMETRE_TABLE_VALEUR','Value');
			define('LANG_ADMIN_PARAMETRE_VALEUR_ROOT','Website adress');
			define('LANG_ADMIN_PARAMETRE_VALEUR_TITRE','Website name');
			define('LANG_ADMIN_PARAMETRE_VALEUR_LOCAL_PATH','Local path');
			define('LANG_ADMIN_PARAMETRE_VALEUR_MAIL_SMTP_HOST','SMTP Server');
			define('LANG_ADMIN_PARAMETRE_VALEUR_MAIL_SMTP_LOGIN','SMTP Login');
			define('LANG_ADMIN_PARAMETRE_VALEUR_MAIL_SMTP_PORT','SMTP Port');
			define('LANG_ADMIN_PARAMETRE_VALEUR_MAIL_SMTP_PASSWORD','SMTP Password');
			define('LANG_ADMIN_PARAMETRE_VALEUR_CONTACT_STAGE_MAIL','Contact mail');
			define('LANG_ADMIN_PARAMETRE_VALEUR_CAS_SERVER_URI','CAS Adress');
			define('LANG_ADMIN_PARAMETRE_VALEUR_CAS_SERVER_PORT','CAS Port');
			define('LANG_ADMIN_PARAMETRE_VALEUR_CAS_SERVER_VALIDATEURI','CAS Validation Adress');
			define('LANG_ADMIN_PARAMETRE_VALEUR_CAS_SERVER_CERTIFICATPATH','CAS Certificat');
			define('LANG_ADMIN_PARAMETRE_VALEUR_LANG','Language');
			define('LANG_ADMIN_PARAMETRE_MAIL_TEST','Send a mail test');
			define('LANG_ADMIN_PARAMETRE_MAIL_TEST_ADRESS','Email adress.');
			define('LANG_ADMIN_PARAMETRE_MAIL_TEST_TITLE','[PAS] Test mail');
			define('LANG_ADMIN_PARAMETRE_MAIL_TEST_CONTENT','This message is a test message. If you received it, it means your mail settings are working.');
			define('LANG_ADMIN_PARAMETRE_MAIL_TEST_SEND','Send');

		// Charte
		
			define('LANG_ADMIN_CHART_TITLES','Disclaimer');	
			define('LANG_ADMIN_CHART_SUBMIT','Validate');	
			define('LANG_ADMIN_CHART_VALID','I agree.');	

		// Gestion des bugs
		
			define('LANG_ADMIN_BUG_MANAGER_TITLES', 'List of notifications');
			define('LANG_ADMIN_BUG_MANAGER_SELECT_YEAR', 'Select a year');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_BUGDATE', 'Date');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_SESSIONVARIABLE', 'Session Variable');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_SERVERVARIABLE', 'Server Data');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_DESCRIPTION', 'Description');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_STATUT', 'State');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_STATUT_VALUE_0', 'In process');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_STATUT_VALUE_1', 'Done');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_NOBUGREGISTERED', 'There are no bug during this time interval.');

		// Mes étudiants
	
		define('LANG_STUDENT_LIST', 'Student list');
		define('LANG_EVAL_LIST_NOSERVICE', 'There are no services registered at your name');
		define('LANG_EVAL_LIST_NOSTUDENT', 'There are no student affected in the service in selected time interval');

	// A propos
		define('LANG_ABOUT_DESCRIPTION', '%1$s is a software of internship evaluations management.');
		define('LANG_ABOUT_LICENCE', '%1$s is under GPL V3 license.');
		define('LANG_ABOUT_LIBRAIRIE', 'The library <a href = "http://swiftmailer.org/">SwiftMailer</a> is used to send mail.<br />The font <a href = "http://swiftmailer.org/">FontAwesome</a> provide the icons.');	
		define('LANG_ABOUT_CONTACT', 'Contact : <a href = "mailto:contact@alibellamine.me">contact@alibellamine.me</a>');
	?>