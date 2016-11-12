<?php
	/*
		22/02/15 - french.php - Ali Bellamine
		Fichier de language - Langue : Français
	*/
	
/*
	Interface utilisateur
*/
define('LANG_DISCONNECT','Déconnexion');
define('LANG_INTERFACE_SELECTEVALUATIONTYPE','Veuillez sélectionner le type d\'évaluation :');
define('LANG_INTERFACE_SELECTBUTTON','Sélectionner');
define('LANG_ADMIN_MENU_TITLE','Menu');

/*
	Installeur
*/

define('LANG_INSTALL_TITLE', 'Installation de PAS');	
define('LANG_INSTALL_SELECT_LANGUAGE', 'Sélectionnez la langue');
define('LANG_INSTALL_NEXT_BUTTON', 'Suivant');	
define('LANG_INSTALL_CONFIRM_BUTTON', 'Confirmer l\'installation');	
define('LANG_INSTALL_LANG_TITLE', 'Langue');
define('LANG_INSTALL_SETTINGS_TITLE', 'Configuration');
define('LANG_INSTALL_REQUIREMENT_TITLE', 'Pré-requis');
define('LANG_INSTALL_BDD_TITLE', 'Connexion à la base de donnée mysql');
define('LANG_INSTALL_NEWUSER_TITLE', 'Création d\'un nouvel utilisateur');
define('LANG_INSTALL_CONFIRM_TITLE', 'Confirmation');
define('LANG_INSTALL_CONFIRM_FINISH', 'Installation complète.<br />Veuillez supprimer le dossier "install" situé à la racine de PAS.');
define('LANG_INSTALL_BDD_VALEUR_SERVER','Serveur de la base de donnée');
define('LANG_INSTALL_BDD_VALEUR_PORT','Port de la base de donnée');
define('LANG_INSTALL_BDD_VALEUR_DBNAME','Nom de la base de donnée');
define('LANG_INSTALL_BDD_VALEUR_BDDUSERNAME','Identifiant d\'accès à la base de donnée');
define('LANG_INSTALL_BDD_VALEUR_PASSWORD','Mot de passe d\'accès à la base de donnée');
define('LANG_INSTALL_NEWUSER_VALEUR_NOM','Nom de l\'administrateur');
define('LANG_INSTALL_NEWUSER_VALEUR_PRENOM','Prénom de l\'administrateur');
define('LANG_INSTALL_NEWUSER_VALEUR_NBETU','Identifiant de connexion au CAS');
define('LANG_INSTALL_NEWUSER_VALEUR_MAIL','Adresse email de l\'administrateur');
define('LANG_INSTALL_ERREUR_REQUIREMENT_NOTMET','Les pré-requis à l\'installation ne sont pas remplis');
define('LANG_INSTALL_ERREUR_FORM_INCOMPLETE','Veuillez remplir tous les champs du formulaire');
define('LANG_INSTALL_ERREUR_BDD_CONNECTION_IMPOSSIBLE','Connexion à la base de donnée impossible');
define('LANG_INSTALL_ERREUR_SETTINGS_URL_INVALID','L\'adresse URL fournie est dans un format invalide');
define('LANG_INSTALL_ERREUR_SETTINGS_MAIL_INVALID','L\'adresse email fournie est dans un format invalide');
define('LANG_INSTALL_ERREUR_SETTINGS_NUMBER_INVALID', 'Un nombre était attendu');
define('LANG_INSTALL_ERREUR_SETTINGS_FILE_INVALID', 'Le fichier indiqué n\'existe pas');
define('LANG_INSTALL_ERREUR_SETTINGS_SMTPCONNECTION_IMPOSSIBLE', 'Connexion au serveur SMTP impossible');
define('LANG_INSTALL_ERREUR_BDDINSTALL_UNKNOWERROR', 'Une erreur interne d\'origine inconnue à été rencontré.');

/*
	Nom des menus
*/

	// Menus principaux
define('LANG_MENU_MAIN_ACCUEIL', 'Accueil');
define('LANG_MENU_MAIN_MYSERVICE', 'Mon service');
define('LANG_MENU_MAIN_EVALUATION', 'Evaluation');
define('LANG_MENU_MAIN_ADMINISTRATION', 'Administration');

	// Menus secondaires
define('LANG_MENU_SECONDARY_ACCUEIL', 'Accueil');
define('LANG_MENU_SECONDARY_MYSTUDENT', 'Mes étudiants');
define('LANG_MENU_SECONDARY_MYEVAL', 'Mes évaluations');
define('LANG_MENU_SECONDARY_VIEWEVAL', 'Consulter les évaluations');
define('LANG_MENU_SECONDARY_USERS', 'Utilisateurs');
define('LANG_MENU_SECONDARY_SERVICES', 'Services');
define('LANG_MENU_SECONDARY_EVALUATIONS', 'Evaluations');
define('LANG_MENU_SECONDARY_SETTINGS', 'Paramètres');
define('LANG_MENU_SECONDARY_CHARTE', 'Charte d\'évaluation');
define('LANG_MENU_SECONDARY_MAIL', 'Campagne d\'email');
define('LANG_MENU_SECONDARY_BUG_MANAGER', 'Gestion des bugs');

/*
	Nom des pages
*/

define('LANG_PAGE_INDEX', 'Accueil');
define('LANG_PAGE_EVAL', 'Evaluation des stages');
define('LANG_PAGE_LOGIN', 'Connexion');
define('LANG_PAGE_EVALDO', 'Formulaire d\'évaluation');
define('LANG_PAGE_EVALVIEW', 'Consultation des évaluations');
define('LANG_PAGE_ADMINUTILISATEURS', 'Gestion des utilisateurs');
define('LANG_PAGE_ADMINSERVICES', 'Gestion des services');
define('LANG_PAGE_ADMINEVALUATIONS', 'Gestion des évaluations');
define('LANG_PAGE_ADMINUSERLIST', 'Ajax du selecteur d\'utilisateurs');
define('LANG_PAGE_MYSTUDENT', 'Mes étudiants');
define('LANG_PAGE_SETTINGS', 'Paramètres');
define('LANG_PAGE_AJAXBUG', 'Ajax de la signalisation des bugs');
define('LANG_PAGE_BUG_ADMIN', 'Gestion des bugs');
define('LANG_PAGE_CHARTE', 'Charte d\'évaluation');
define('LANG_PAGE_ADMINMAIL','Campagnes d\'envoi d\'email');
define('LANG_PAGE_ABOUT', 'A propos de PAS');
	
/*
	Erreurs
*/
define('LANG_ERRORS','Erreurs');
define('LANG_ERROR_USER_INVALID','L\'utilisateur sélectionné est invalide.');
define('LANG_ERROR_NOT_REGISTERED','Vous n\'êtes pas enregistré dans le logiciel.');
define('LANG_ERROR_SERVICE_INVALID','Le service sélectionné est invalide.');
define('LANG_ERROR_EVALUATION_INVALID','L\'évaluation sélectionnée est invalide.');
define('LANG_ERROR_EVALUATIONTYPE_INVALID','Le module d\'évaluation est actuellement désactivé.');
define('LANG_ERROR_EVALUATION_DONE','Vous avez déjà rempli le formulaire sélectionné.');
define('LANG_ERROR_EVALUATION_WRONGDATE','La période d\'évaluation ne correspond pas à la date actuelle.');
define('LANG_ERROR_NAVIGATION_NORIGHT','Vous ne disposez pas des droits nécessaires à l\'affichage de la page sélectionnée.');
define('LANG_ERROR_IMPORT_FILE','Erreur lors de l\'envoi du fichier.');
define('LANG_ERROR_IMPORT_FILE_INVALID','Le fichier est dans un format invalide.');
define('LANG_ERROR_CHECK_NB_ETUDIANT','Numéro d\'étudiant invalide.');
define('LANG_ERROR_AFFECTATION_SERVICE_ALREADY','L\'étudiant est déjà affecté dans le service.');
define('LANG_ERROR_AFFECTATION_DATE_DEBUT','La date de début de stage est dans un format invalide.');
define('LANG_ERROR_AFFECTATION_DATE_FIN','La date de fin de stage est dans un format invalide.');
define('LANG_ERROR_AFFECTATION_DATE_MISMATCH','La date de fin de stage doit être plus éloignée dans le temps que celle de début.');
define('LANG_ERROR_DATE_MISMATCH','La date de fin doit être plus éloignée dans le temps que celle de début.');
define('LANG_ERROR_UNKNOWN','Erreur d\'origine inconnue.');
define('LANG_ERROR_AFFECTATION_INVALID', 'L\'affectation sélectionnée n\'existe pas.');
define('LANG_ERROR_DATA_INVALID_FORMAT', 'Les données sont dans un format invalide.');
define('LANG_ERROR_MAIL_NOSETTINGS','Aucune adresse email n\'est configurée.');
define('LANG_ERROR_MAIL_SEND','Une erreur s\'est produite durant l\'envoi de l\'email.');
define('LANG_ERROR_MAIL_INVALID', 'L\'adresse email est dans un format invalide.');
define('LANG_ERROR_MAIL_RELOAD', 'Actualisez la page pour continuer l\'envoi.');
define('LANG_ERROR_URL_INVALID', 'L\'adresse fournie n\'est pas valide.');
define('LANG_ERROR_EVALUATION_NOSETTINGS', 'Aucun paramètre n\'a été rempli pour la campagne d\'évaluation sélectionnée');
define('LANG_ERROR_FORM_INCOMPLETE', 'Veuillez remplir tous les champs du formulaire');
define('LANG_ERROR_DATA_INCOMPLETE', 'Données incomplètes');
define('LANG_ERROR_PROMOTION_INEXISTANT', 'Promotion inexistante');
define('LANG_ERROR_RANG_BATCH_INVALID', 'Le rang de l\'utilisateur est invalide');
define('LANG_ERROR_HOSPITAL_INVALID', 'L\'hopital sélectionné n\'est pas enregistré');
define('LANG_ERROR_CERTIFICATE_INVALID', 'Le certificat sélectionné n\'est pas enregistré');
define('LANG_ERROR_SPECIALITE_INVALID', 'La spécialité sélectionnée n\'est pas enregistré');
define('LANG_ERROR_EVAL_LOGINAS_FORBIDDEN', 'Accès interdit aux connexions distantes pour ce module d\'évaluation');

/*
	Message de confirmation
*/

define('LANG_SUCCESS_LOGOUT','Déconnexion effectuée avec succès.');
define('LANG_SUCCESS_EVALUATION_FORM','Evaluation enregistrée avec succès.');
define('LANG_SUCCESS_EVALUATION_SETTING','Configuration enregistrée avec succès.');
define('LANG_SUCCESS_MAIL_SEND','Messages envoyés avec succès.');

/*
	Mois de l'année
*/
define('LANG_MONTH_1','Janvier');
define('LANG_MONTH_2','Février');
define('LANG_MONTH_3','Mars');
define('LANG_MONTH_4','Avril');
define('LANG_MONTH_5','Mai');
define('LANG_MONTH_6','Juin');
define('LANG_MONTH_7','Juillet');
define('LANG_MONTH_8','Aout');
define('LANG_MONTH_9','Septembre');
define('LANG_MONTH_10','Octobre');
define('LANG_MONTH_11','Novembre');
define('LANG_MONTH_12','Decembre');

/*
	Rang des utilisateurs
*/

		define('LANG_RANG_VALUE_0','Invité');
		define('LANG_RANG_VALUE_1','Etudiant');
		define('LANG_RANG_VALUE_2','Enseignant');
		define('LANG_RANG_VALUE_3','Administrateur');
		define('LANG_RANG_VALUE_4','Super Administrateur');


/*
	Message propres aux pages
*/

	/*
		Accueil
	*/
	
	define('LANG_ACCUEIL_DESCRIPTION_1',"<strong>Evaluez</strong> vos stages et vos ateliers"); 
	define('LANG_ACCUEIL_DESCRIPTION_2',"<strong>Consultez</strong> les données d'évaluations antérieures en toute simplicité"); 
	define('LANG_ACCUEIL_DESCRIPTION_3',"<strong>Vérifiez</strong> que vous avez remplis toutes vos évaluations"); 
	define('LANG_ACCUEIL_CONNEXION',"Connexion"); 
	define('LANG_ACCUEIL_ABOUT',"A propos de PAS"); 

	/*
		Formulaires
	*/
	
	define('LANG_FORM_SUBMIT_FILE', 'Envoyer le fichier');
	
	/*
		Evaluation
	*/
	
	define('LANG_EVAL_LIST', 'Liste des évaluations');
	define('LANG_EVAL_LIST_NOEVAL', 'Il n\'y a aucune évaluation pour cette période.');
	define('LANG_EVAL_LIST_OPTION_NEXT', 'A venir');
	define('LANG_EVAL_LIST_OPTION_NOW', 'Actuellement');
	define('LANG_EVAL_LIST_OPTION_OLD', 'Archives');

	/*
		Pages d'administrations
	*/	
		define('LANG_ADMIN_TABLE_TITLE_ADMIN','Administration');			
		define('LANG_ADMIN_LISTE_TABLE_TITLE_NOM','Nom');
		define('LANG_ADMIN_LISTE_TABLE_TITLE_ETUDIANTSNB','Nombre d\'étudiants enregistrés');
		define('LANG_ADMIN_LISTE_TABLE_TITLE_ETUDIANTS','Liste des étudiants');

		// Utilisateurs
		define('LANG_ADMIN_UTILISATEURS_MENU_ITEM_LISTE','Liste des utilisateurs');
		define('LANG_ADMIN_UTILISATEURS_MENU_ITEM_PROMOTION','Gérer les promotions');
		define('LANG_ADMIN_UTILISATEURS_MENU_ITEM_ADD','Ajouter des utilisateurs');
		define('LANG_ADMIN_UTILISATEURS_LISTE_FILTER_SEARCHBAR','Rechercher un utilisateur');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NOM','Nom');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PRENOM','Prénom');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_MAIL','Adresse email');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NBETUDIANT','N° étudiant');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PROMOTION','Promotion');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PROMOTIONNB','Nombre d\'inscrits');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_RANG','Statut');
		define('LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_ADMIN','Administration');		
		
			// Visualisation d'un profil
			define('LANG_ADMIN_UTILISATEURS_CAT_INFOSGENERALES','Informations générales');
			define('LANG_ADMIN_UTILISATEURS_CAT_ETUDIANT','Informations sur le parcours de l\'étudiant');
			define('LANG_ADMIN_UTILISATEURS_CAT_CHEF','Informations sur le chef de service');
			define('LANG_ADMIN_UTILISATEURS_CAT_STAGES','Stages');
			define('LANG_ADMIN_UTILISATEURS_FORM_SUBMIT_ADD','Ajouter un utilisateur');		
			define('LANG_ADMIN_UTILISATEURS_FORM_SUBMIT_EDIT','Valider les modifications');		
			define('LANG_ADMIN_UTILISATEURS_FORM_SUBMIT_DELETE','Supprimer le profil et les données associées');		
			define('LANG_ADMIN_UTILISATEURS_FORM_SUBMIT_DELETE_CONFIRM','Confirmer la suppression du profil et les données associées');		
			
			// Promotions
			define('LANG_ADMIN_PROMOTION_LIST_ETUDIANTS','Etudiants enregistrés dans la promotion');
			define('LANG_ADMIN_PROMOTION_NOM_TITRE','Nom de la promotion');
			define('LANG_ADMIN_PROMOTION_ADD_PROMOTION','Ajouter une promotion');
			define('LANG_ADMIN_PROMOTION_FORM_SUBMIT_EDIT','Valider les modifications');		
			define('LANG_ADMIN_PROMOTION_FORM_SUBMIT_ADD','Ajouter la promotion');		
			define('LANG_ADMIN_PROMOTION_FORM_SUBMIT_DELETE','Supprimer la promotion et l\'ensemble des profils associés');		
			define('LANG_ADMIN_PROMOTION_FORM_SUBMIT_DELETE_CONFIRM','Confirmer la suppression de la promotion et de l\'ensemble des profils associés');		
			
			// Ajout des utilisateurs
			define('LANG_ADMIN_UTILISATEURS_MENU_IMPORTCSV','Importer une liste d\'utilisateurs');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_IMPORTFILE','Veuillez importer un fichier CSV contenant le nom, prénom, adresse email (plusieurs adresses peuvent être mises, elle doivent être séparées d\'un point virgule), numéro d\'étudiant, identifiant de la promotion (peut être laissé vide), et le type d\'utilisateur (1 pour étudiant, 2 pour enseignant).<br /> Si un utilisateur existe déjà (numéro d\'étudiant déjà enregistré), les données le concernant seront alors actualisées.');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_DOWNLOADPROMOTIONLIST','Télécharger la liste des identifiants des promotions');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_DOWNLOADRAWCSV','Fichier CSV à compléter');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_NOM','Nom');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_PRENOM','Prénom');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_MAIL','Adresse email');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_NBETU','Numéro d\'étudiant');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_PROMOTION','Promotion');			
			define('LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_RANG','Rang');			
			define('LANG_ADMIN_USERS_MENU_BATCH_TODO_INSERT', 'Profils à ajouter');
			define('LANG_ADMIN_USERS_MENU_BATCH_TODO_UPDATE', 'Profils à mettre à jour');
		
		// Campagnes d'envoi d'emails
		define('LANG_ADMIN_MAIL_MENU_ITEM_LISTE', 'Liste des campagnes');
		
		// Services
		define('LANG_ADMIN_SERVICES_ID','ID du service');
		define('LANG_ADMIN_SERVICES_NOM','Nom du service');		
		define('LANG_ADMIN_SERVICES_NOM_SERVICEOF','Service de ');		
		define('LANG_ADMIN_SERVICES_MENU_ITEM_LISTE','Gérer les services');		
		define('LANG_ADMIN_SERVICES_MENU_ITEM_SPECIALITE','Gérer les spécialités');		
		define('LANG_ADMIN_SERVICES_MENU_ITEM_CERTIFICAT','Gérer les certificats');	
		define('LANG_ADMIN_SERVICES_MENU_ITEM_HOPITAUX','Gérer les hopitaux');		
		define('LANG_ADMIN_SERVICES_MENU_ITEM_AFFECTATIONS','Affectation des étudiants');		
		define('LANG_ADMIN_SERVICES_LISTE_FILTER_SEARCHBAR','Rechercher un service');
		define('LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_NOM','Nom');
		define('LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_HOPITAL','Hopital');
		define('LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_CHEF','Chef');
		define('LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_SPECIALITE','Spécialité');
		
			// Visuation d'un service
			define('LANG_ADMIN_SERVICES_CAT_CERTIFICATS','Certificats');
			define('LANG_ADMIN_SERVICES_LIST_STUDENT','Etudiants affectés au service');		
			define('LANG_ADMIN_SERVICES_FORM_SUBMIT_ADD','Ajouter le service');		
			define('LANG_ADMIN_SERVICES_FORM_SUBMIT_EDIT','Valider les modifications');		
			define('LANG_ADMIN_SERVICES_FORM_SUBMIT_DELETE','Supprimer le service et les données associées');		
			define('LANG_ADMIN_SERVICES_FORM_SUBMIT_DELETE_CONFIRM','Confirmer la suppression du service et les données associées');		
		
			// Hopitaux
			define('LANG_ADMIN_HOPITAUX_LISTE_TABLE_TITLE_NOM','Nom');
			define('LANG_ADMIN_HOPITAUX_LISTE_TABLE_TITLE_ALIAS','Alias');
			define('LANG_ADMIN_HOPITAUX_LISTE_TABLE_TITLE_SERVICESNB','Nombre de services enregistrées');
			define('LANG_ADMIN_HOPITAUX_ADD_HOPITAL','Ajouter un hopital');
			define('LANG_ADMIN_HOPITAL_LISTE_SERVICES','Liste des services');
			define('LANG_ADMIN_HOPITAL_FORM_SUBMIT_EDIT','Modifier les données concernant l\'hopital');
			define('LANG_ADMIN_HOPITAL_FORM_SUBMIT_ADD','Ajouter un hopital');
			define('LANG_ADMIN_HOPITAL_FORM_SUBMIT_DELETE','Supprimer l\'hopital et l\'ensemble des données associées');
			define('LANG_ADMIN_HOPITAL_FORM_SUBMIT_DELETE_CONFIRM','Confirmer la suppression de l\'hopital et de l\'ensemble des données associées');
			
			// Specialite

			define('LANG_ADMIN_SPECIALITE_LISTE_TABLE_TITLE_SERVICESNB','Nombre de services enregistrées');
			define('LANG_ADMIN_SPECIALITE_ADD_SPECIALITE','Ajouter une spécialité');
			define('LANG_ADMIN_SPECIALITE_FORM_SUBMIT_EDIT','Modifier les données concernant la spécialité');
			define('LANG_ADMIN_SPECIALITE_FORM_SUBMIT_ADD','Ajouter une spécialité');
			define('LANG_ADMIN_SPECIALITE_FORM_SUBMIT_DELETE','Supprimer la spécialité et l\'ensemble des données associées');
			define('LANG_ADMIN_SPECIALITE_FORM_SUBMIT_DELETE_CONFIRM','Confirmer la suppression de la spécialité et de l\'ensemble des données associées');
			
			// Certificats
			
			define('LANG_ADMIN_CERTIFICAT_LISTE_TABLE_TITLE_PROMO','Promotion');
			define('LANG_ADMIN_CERTIFICAT_ADD_CERTIFICAT','Ajouter un certificat');
			define('LANG_ADMIN_CERTIFICAT_FORM_SUBMIT_EDIT','Modifier les données concernant le certificat');
			define('LANG_ADMIN_CERTIFICAT_FORM_SUBMIT_ADD','Ajouter un certificat');
			define('LANG_ADMIN_CERTIFICAT_FORM_SUBMIT_DELETE','Supprimer le certificat et l\'ensemble des données associées');
			define('LANG_ADMIN_CERTIFICAT_FORM_SUBMIT_DELETE_CONFIRM','Confirmer la suppression du certificat et de l\'ensemble des données associées');
			
		// Affectations
			
			define('LANG_ADMIN_AFFECTATIONS_MENU_AFFECTATIONONE','Affectation d\'un étudiant');
			define('LANG_ADMIN_AFFECTATIONS_MENU_BATCH','Importer un fichier d\'affectation');
			define('LANG_ADMIN_AFFECTATIONS_MENU_BATCH_TODO','Affectations à ajouter');
			define('LANG_ADMIN_AFFECTATIONS_MENU_BATCH_IMPORTFILE','Veuillez importer un fichier CSV contenant le numéro d\'étudiant de l\'étudiant, l\'identifiant du service et la période d\'affectation.');
			define('LANG_ADMIN_AFFECTATIONS_MENU_BATCH_DOWNLOADSERVICE','Télécharger la liste des identifiants de services');
			define('LANG_ADMIN_AFFECTATIONS_MENU_BATCH_DOWNLOADCSV','Fichier CSV à compléter');
			define('LANG_ADMIN_AFFECTATIONS_DATE_DEBUT', 'Date de début (JJ/MM/AAAA)');
			define('LANG_ADMIN_AFFECTATIONS_DATE_FIN', 'Date de fin (JJ/MM/AAAA)');
			define('LANG_ADMIN_AFFECTATIONS_BATCH_SUCCESS', 'Enregistrement des affectations effectué avec succès.');
			define('LANG_ADMIN_AFFECTATIONS_FORM_SUBMIT_EDIT','Modifier les données concernant l\'affectation');
			define('LANG_ADMIN_AFFECTATIONS_FORM_SUBMIT_ADD','Ajouter une affectation');
			define('LANG_ADMIN_AFFECTATIONS_FORM_SUBMIT_DELETE','Supprimer l\'affectation et l\'ensemble des données associées');
			define('LANG_ADMIN_AFFECTATIONS_FORM_SUBMIT_DELETE_CONFIRM','Confirmer la suppression de l\'affectation et de l\'ensemble des données associées');
			define('LANG_ADMIN_AFFECTATIONS_ADD_AFFECTATION','Enregistrer une nouvelle affectation');
			
		// Evaluations
		
			define('LANG_ADMIN_EVALUATIONS_MENU_ITEM_LISTE', 'Campagnes d\'évaluation');
			define('LANG_ADMIN_EBALUATION_MENU_ITEM_MODULE', 'Modules d\'évaluation');
			define('LANG_ADMIN_EVALUATIONS_LISTE_TABLE_TITLE_TYPE', 'Type d\'évaluation');
			define('LANG_ADMIN_EVALUATIONS_LISTE_TABLE_TITLE_REMPLISSAGE', 'Pourcentage de remplissage');
			define('LANG_ADMIN_EVALUATIONS_LISTE_TABLE_TITLE_PERIODE', 'Date');
			define('LANG_ADMIN_EVALUATIONS_USERLIST_LASTUPDATE', 'Dernière actualisation : ');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_EDIT','Modifier les données concernant l\'évaluation');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_BUTTON','Envoyer un email de rappel aux %d personnes n\'ayant pas rempli leurs évaluation.');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_OBJET', 'Object');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_OBJET_DEFAULT', 'RAPPEL : Evaluation non remplie');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_MSG', 'Message');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_MSG_DEFAULT', 'Vous n\'avez pas rempli l\'évaluation suivante : %s.<br />Veuillez vous rendre à l\'adresse suivante afin de la remplir : %s (copier-coller le lien dans votre barre d\'adresse)');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_CANCEL', 'Annuler l\'envoie');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_ADD','Ajouter une évaluation');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_DELETE','Supprimer l\'évaluation et l\'ensemble des données associées');
			define('LANG_ADMIN_EVALUATION_FORM_SUBMIT_DELETE_CONFIRM','Confirmer la suppression de l\'évaluation et de l\'ensemble des données associées');
			define('LANG_ADMIN_EVALUATION_CSV_STATE','Evaluation');
			define('LANG_ADMIN_EVALUATION_CSV_COMPLETE','Evaluation remplie');
			define('LANG_ADMIN_EVALUATION_CSV_NOT_COMPLETE','Evaluation non remplie');
			
		// Gestion des modules
		
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_NOM','Nom du module');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_FOLDER','Repertoire du module');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_STATE','Statut');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_STATE_OPTION_0','Inactif');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_STATE_OPTION_1','Actif');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_RIGHT', 'Droits d\'accès');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_RIGHT_OPTION_0', 'non');
			define('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_RIGHT_OPTION_1', 'oui');
			define('LANG_ADMIN_MODULE_RIGHT_FORM_SUBMIT', 'Valider');
		
		// Envoie de mails
			define('LANG_ADMIN_SENDMAIL_TO', 'Destinataires');
			define('LANG_ADMIN_SENDMAIL_OBJECT', 'Objet');
			define('LANG_ADMIN_SENDMAIL_MESSAGE', 'Message');
			define('LANG_ADMIN_SENDMAIL_SEND', 'Envoyer les emails');
			define('LANG_ADMIN_SENDMAIL_SENT', 'Envoi terminé');
			define('LANG_ADMIN_SENDMAIL_ALLCHANGESSAVED', 'Modifications enregistrées avec succès');
		
		// Paramètres
		
			define('LANG_ADMIN_PARAMETRE_TITLES','Paramètres');
			define('LANG_ADMIN_PARAMETRE_TABLE_PARAMETRE','Paramètre');
			define('LANG_ADMIN_PARAMETRE_TABLE_VALEUR','Valeur');
			define('LANG_ADMIN_PARAMETRE_VALEUR_ROOT','Adresse du site');
			define('LANG_ADMIN_PARAMETRE_VALEUR_TITRE','Nom du site');
			define('LANG_ADMIN_PARAMETRE_VALEUR_LOCAL_PATH','Repertoire local');
			define('LANG_ADMIN_PARAMETRE_VALEUR_MAIL_SMTP_HOST','Serveur SMTP');
			define('LANG_ADMIN_PARAMETRE_VALEUR_MAIL_SMTP_LOGIN','Identifiant SMTP');
			define('LANG_ADMIN_PARAMETRE_VALEUR_MAIL_SMTP_PORT','Port SMTP');
			define('LANG_ADMIN_PARAMETRE_VALEUR_MAIL_SMTP_PASSWORD','Mot de passe SMTP');
			define('LANG_ADMIN_PARAMETRE_VALEUR_CONTACT_STAGE_MAIL','Adresse email de contact');
			define('LANG_ADMIN_PARAMETRE_VALEUR_CAS_SERVER_URI','Adresse du CAS');
			define('LANG_ADMIN_PARAMETRE_VALEUR_CAS_SERVER_PORT','Port du CAS');
			define('LANG_ADMIN_PARAMETRE_VALEUR_CAS_SERVER_VALIDATEURI','Adresse de validation des connexions au CAS');
			define('LANG_ADMIN_PARAMETRE_VALEUR_CAS_SERVER_CERTIFICATPATH','Certificat pour le CAS');
			define('LANG_ADMIN_PARAMETRE_VALEUR_LANG','Langue');
			define('LANG_ADMIN_PARAMETRE_MAIL_TEST','Envoyer un email de Test');
			define('LANG_ADMIN_PARAMETRE_MAIL_TEST_ADRESS','Adresse email du destinataire.');
			define('LANG_ADMIN_PARAMETRE_MAIL_TEST_TITLE','[PAS] Email de test');
			define('LANG_ADMIN_PARAMETRE_MAIL_TEST_CONTENT','Ceci est un email de test. Si vous recevez ce message, vos paramètres email de PAS sont correctement configurés.');
			define('LANG_ADMIN_PARAMETRE_MAIL_TEST_SEND','Envoyer');

		// Charte
		
			define('LANG_ADMIN_CHART_TITLES','Charte d\'évaluation');	
			define('LANG_ADMIN_CHART_SUBMIT','Valider');	
			define('LANG_ADMIN_CHART_VALID','J\'accepte les termes de la charte et je m’engage à les respecter.');	
			
		// Gestion des bugs
		
			define('LANG_ADMIN_BUG_MANAGER_TITLES', 'Liste des signalements');
			define('LANG_ADMIN_BUG_MANAGER_SELECT_YEAR', 'Sélectionnez une année');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_BUGDATE', 'Date');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_SESSIONVARIABLE', 'Variable Session');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_SERVERVARIABLE', 'Données Serveur');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_DESCRIPTION', 'Description');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_STATUT', 'Etat');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_STATUT_VALUE_0', 'Non traité');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_STATUT_VALUE_1', 'Traité');
			define('LANG_ADMIN_BUG_MANAGER_TABLE_NOBUGREGISTERED', 'Il n\'y a aucun bug signalé sur cette période.');
			
	// Mes étudiants
	
		define('LANG_STUDENT_LIST', 'Liste des étudiants');
		define('LANG_EVAL_LIST_NOSERVICE', 'Aucun service n\'est enregistré à votre nom');
		define('LANG_EVAL_LIST_NOSTUDENT', 'Il n\'a aucun étudiant dans le service pour la période sélectionnée.');
		
	// A propos
		define('LANG_ABOUT_DESCRIPTION', '%1$s est un logiciel de gestion des évaluations de stage.<br />Souple et modulable, %1$s est adaptable à toutes les situations.');
		define('LANG_ABOUT_LICENCE', '%1$s est distribué sous licence GPL V3.');
		define('LANG_ABOUT_LIBRAIRIE', 'L\'envoi d\'email se fait à l\'aide de librairie <a href = "http://swiftmailer.org/">SwiftMailer</a>, aucune modification n\'a été apportée au code source de la librairie.<br />Les icones sont issues de la police d\'écriture <a href = "http://swiftmailer.org/">FontAwesome</a>.');	
		define('LANG_ABOUT_CONTACT', 'Contact : <a href = "mailto:contact@alibellamine.me">contact@alibellamine.me</a>');
	?>