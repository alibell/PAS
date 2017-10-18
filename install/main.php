<?php
/**
	Copyright (C) 2015 Ali BELLAMINE

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License along
	with this program; if not, write to the Free Software Foundation, Inc.,
	51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
**/

/*
	29/07/2015 - Ali Bellamine
	Script d'installation du CMS - main
*/

	session_start(); // Charge la variable session
	
	/*
		1. Variables permettant la création et la vérification des formulaires d'installation
	*/
	
		/*
			1.1. Liste des étapes d'installation
		*/
		
		$steps = array(
			'lang',
			'requirement',
			'bdd',
			'settings',
			'newUser',
			'confirm'
		);
		
		/*
			1.2 Pour chaque étape, array contenant les données de l'étape
		*/
		
			/*
				$page = lang
			*/
			$allowedLang = array('english' => 'English', 'french' => 'Français'); // Valeurs de langue possibles
	
			/* 
				$page = requirement
				
				On vérifie que le pré-requis au fonctionnement de PAS est présent et on le note dans la variable $_SESSION['requirement'] :
					- PHP >= 5.4
					- GD  (pChart)
					- FreeType (pChart)
					- zlib  (FPDF)
					- CURL (phpCAS)
					- dom (phpCAS)
					- openssl (phpCAS)
					- Ziparchive (PAS)
					- PDO (PAS)
			*/
			
			$requirementList = array('PHP 5.4' => FALSE, 'gd' => FALSE, 'FreeType' => FALSE, 'zlib' => FALSE, 'curl' => FALSE, 'dom' => FALSE, 'openssl' => FALSE, 'zip' => FALSE, 'PDO' => FALSE, 'pdo_mysql' => FALSE); 
			$load_ext = get_loaded_extensions(); // Liste des extensions chargés
			
			foreach ($requirementList AS $requirement => $value)
			{
				if ($requirement == 'PHP 5.4' && version_compare(PHP_VERSION, "5.4.0", ">="))
				{
					$requirementList[$requirement] = TRUE;
				}
				else if ($requirement != 'FreeType' && in_array($requirement, $load_ext))
				{
					$requirementList[$requirement] = TRUE;
				}
				else if ($requirement == 'FreeType' && $requirementList['gd'] && gd_info()['FreeType Support'])
				{
					$requirementList[$requirement] = TRUE;		
				}
			} 
	
			/*
				$page = bdd
				Liste des paramètres nécessaires pour la connexion à la BDD, array de la forme : 
					key : nom du paramètre | value : type de paramètre (number, string, password)
			*/
			
			$bddList = array(
				'SERVER' => 'string',
				'PORT' => 'number',
				'DBNAME' => 'string',
				'BDDUSERNAME' => 'string',
				'PASSWORD' => 'password'
			);
			
			/* 
				$page = settings
				Liste des paramètres à faire spécifier, de la forme : 
					key : nom du paramètre | value : array :
						key : type | value : type de paramètre (string, url, file, mail, number, password)
						key : required | value : true | false
			*/
			$settingsList = array(
				'ROOT' => array(
					'type' => 'url', 
					'required' => TRUE
				), 
				'TITRE' => array(
					'required' => TRUE, 
					'type' => 'string'
				), 
				'MAIL_SMTP_HOST' => array(
					'required' => TRUE, 
					'type' => 'string'
				), 
				'MAIL_SMTP_LOGIN' => array(
					'required' => TRUE, 
					'type' => 'string'
				), 
				'MAIL_SMTP_PORT' => array(
					'required' => TRUE, 
					'type' => 'number'
				), 
				'MAIL_SMTP_PASSWORD' => array(
					'required' => TRUE, 
					'type' => 'password'
				), 
				'CONTACT_STAGE_MAIL' => array(
					'required' => TRUE, 
					'type' => 'mail'
				), 
				'CAS_SERVER_URI' => array(
					'required' => TRUE, 
					'type' => 'string'
				),
				'CAS_SERVER_PORT' => array(
					'required' => TRUE, 
					'type' => 'number'
				), 
				'CAS_SERVER_VALIDATEURI' => array(
					'required' => TRUE, 
					'type' => 'string'
				), 
				'CAS_SERVER_CERTIFICATPATH' => array(
					'required' => FALSE, 
					'type' => 'file'
				)
			); 
			
			/*
				$page = user
				Liste des données demandés dans la page user
				Forme :
				key : nom de la donnée demandé | value : type de donné demandé
			*/
			
			$userList = array(
				'NOM' => 'string',
				'PRENOM' => 'string',
				'MAIL' => 'email',
				'NBETU' => 'string'
			);
	/*
		2. A chaque formulaire soumis, on enregistre la réponse dans une variable session, si c'est OK, sinon on enregistre l'erreur dans l'array $erreur
	*/

	$erreur = array();
	
	if (isset($_POST) && count($_POST) > 0)
	{
		/*
			2.1 : On récupère la page d'où le formulaire a été envoyé, cela permet de traiter différemment chaque formulaire
		*/
		if (isset($_POST['page']))
		{
			$formPage = $_POST['page'];
		}
		else
		{
			$formPage = '';
		}
		
		/*
			2.2 : On traite les formulaire de chaque page
		*/
		
			/*
				$formPage = lang
				On récupère la langue à utiliser
			*/
			if ($formPage == 'lang')
			{
				if (isset($_POST['selectLang']) && isset($allowedLang[$_POST['selectLang']]))
				{
					if (isset($_GET['goTo'])) { unset($_GET['goTo']); }
					$_SESSION['settings']['LANG'] = $_POST['selectLang'];
				}
			}
			
			/*
				$formPage = requirement
				Si toutes les conditions nécessaires sont remplis, on autorise à continuer l'installation
			*/
			else if ($formPage == 'requirement')
			{
				if (isset($_POST['requirementOK']) && $_POST['requirementOK'] == 'true')
				{
					if (isset($_GET['goTo'])) { unset($_GET['goTo']); }
					$_SESSION['requirement'] = TRUE;
				}
				else
				{
					$erreur['LANG_INSTALL_ERREUR_REQUIREMENT_NOTMET'] = TRUE;
				}
			}
			
			/*
				$formPage = bdd
				On essaie de se connecter à la base de donnée, si cela marche, on stocke les identifiants de la base de données dans l'array $_SESSION['bddSettings']
			*/
			
			else if ($formPage == 'bdd')
			{
				if (isset($_POST['PORT']) && is_numeric($_POST['PORT']) && isset($_POST['SERVER']) && isset($_POST['DBNAME']) && isset($_POST['BDDUSERNAME']))
				{
					if (!isset($_POST['PASSWORD']))
					{
						$_POST['PASSWORD'] = '';
					}
					
					try {
						$dbTest = new PDO('mysql:host='.$_POST['SERVER'].';port='.$_POST['PORT'].';dbname='.$_POST['DBNAME'], $_POST['BDDUSERNAME'], $_POST['PASSWORD'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT));
					} catch (PDOException $e) {
					}
					
					if (isset($dbTest))
					{
						if (isset($_GET['goTo'])) { unset($_GET['goTo']); }
						$_SESSION['bddSettings']['SERVER'] = $_POST['SERVER'];
						$_SESSION['bddSettings']['PORT'] = $_POST['PORT'];
						$_SESSION['bddSettings']['DBNAME'] = $_POST['DBNAME'];
						$_SESSION['bddSettings']['BDDUSERNAME'] = $_POST['BDDUSERNAME'];
						$_SESSION['bddSettings']['PASSWORD'] = $_POST['PASSWORD'];
					} 
					else
					{
						$erreur['LANG_INSTALL_ERREUR_BDD_CONNECTION_IMPOSSIBLE'] = TRUE;
					}
				}
				else
				{
					$erreur['LANG_INSTALL_ERREUR_FORM_INCOMPLETE'] = TRUE;
				}
			}
			
			/*
				$formPage = settings
				On parcours la liste des paramètres un par un, on effectue les vérifications selon le type de données dont il s'agit
				On vérifie que tous les champs obligatoire ont été remplis
			*/
			else if ($formPage == 'settings')
			{
				$formComplete = TRUE;
				foreach ($settingsList AS $settingName => $settingValue)
				{
					// On vérifie que le champs est remplit si il est obligatoire
					if ((!isset($_POST[$settingName]) || $_POST[$settingName] == '') && $settingValue['required'])
					{
						$formComplete = FALSE;
					}
										
					// On vérifie les paramètre selon leurs type
					if ($settingValue['type'] == 'url' && isset($_POST[$settingName]) && $_POST[$settingName] != '' && !filter_var($_POST[$settingName], FILTER_VALIDATE_URL))
					{
						$erreur['LANG_INSTALL_ERREUR_SETTINGS_URL_INVALID'] = TRUE;
					}
					else if ($settingValue['type'] == 'number' && isset($_POST[$settingName]) && $_POST[$settingName] != '' &&  !is_numeric($_POST[$settingName]))
					{
						$erreur['LANG_INSTALL_ERREUR_SETTINGS_NUMBER_INVALID'] = TRUE;
					}
					else if ($settingValue['type'] == 'mail' && isset($_POST[$settingName]) && $_POST[$settingName] != '' && !filter_var($_POST[$settingName], FILTER_VALIDATE_EMAIL))
					{
						$erreur['LANG_INSTALL_ERREUR_SETTINGS_MAIL_INVALID'] = TRUE;						
					}
					else if ($settingValue['type'] == 'file' && isset($_POST[$settingName]) && $_POST[$settingName] != '' && !is_file($_POST[$settingName]))
					{
						$erreur['LANG_INSTALL_ERREUR_SETTINGS_FILE_INVALID'] = TRUE;						
					}
				} 
				
				// Erreur si tous les champs obligatoires n'ont pas été remplis
				if (!$formComplete)
				{
					$erreur['LANG_INSTALL_ERREUR_FORM_INCOMPLETE'] = TRUE;
				}

				// On vérifie la connexion au serveur SMTP
				if (count($erreur) == 0)
				{
					// On charge swiftmailer
					include('../core/swiftmailer-5.x/lib/swift_required.php');
					
					if ($_POST['MAIL_SMTP_PORT'] == 587 || $_POST['MAIL_SMTP_PORT'] == 465) { $ssl = 'ssl'; } else { $ssl = ''; }
					$connectionSMTP = FALSE;
					
					// On essaie de se connecter en SMTP
					try{
						$transport = Swift_SmtpTransport::newInstance($_POST['MAIL_SMTP_HOST'], $_POST['MAIL_SMTP_PORT'], $ssl) -> setUsername($_POST['MAIL_SMTP_LOGIN']) -> setPassword($_POST['MAIL_SMTP_PASSWORD']) -> setTimeout(5);
						$mailer = \Swift_Mailer::newInstance($transport) ->getTransport() -> start();
						
						$connectionSMTP = TRUE;
					} 
					catch (Swift_TransportException $e) {
					} 
					catch (Exception $e) {
					} 
					
					if (!$connectionSMTP)
					{
						$erreur['LANG_INSTALL_ERREUR_SETTINGS_SMTPCONNECTION_IMPOSSIBLE'] = TRUE;
					}
				}
				
				// Si toujours aucunes erreur --> on enregistre les données dans la base de donnée
				if (count($erreur) == 0)
				{
					if (isset($_GET['goTo'])) { unset($_GET['goTo']); }

					foreach ($_POST AS $key => $value)
					{
						if (isset($settingsList[$key]))
						{
							$_SESSION['settings'][$key] = $value;
						}
					}
				}
			} 
			
			/*
				$formPage = newUser
				Vérifie les données et si ok les stocke dans $_SESSION['newUser']
			*/
			else if ($formPage == 'newUser')
			{
				$formComplete = TRUE;
				foreach ($userList AS $userName => $userValue)
				{
					// On vérifie que le champs est remplit si il est obligatoire
					if (!isset($_POST[$userName]) || $_POST[$userName] == '')
					{
						$formComplete = FALSE;
					}
										
					// On vérifie les paramètre selon leurs type
					if ($userValue == 'mail' && isset($_POST[$userName]) && $_POST[$userName] != '' && !filter_var($_POST[$userName], FILTER_VALIDATE_EMAIL))
					{
						$erreur['LANG_INSTALL_ERREUR_SETTINGS_MAIL_INVALID'] = TRUE;						
					}
				} 
				
				// Erreur si tous les champs obligatoires n'ont pas été remplis
				if (!$formComplete)
				{
					$erreur['LANG_INSTALL_ERREUR_FORM_INCOMPLETE'] = TRUE;
				}
				
				// Si  aucunes erreur --> on enregistre les données dans la base de donnée
				if (count($erreur) == 0)
				{
					if (isset($_GET['goTo'])) { unset($_GET['goTo']); }

					foreach ($_POST AS $key => $value)
					{
						if (isset($userList[$key]))
						{
							$_SESSION['newUser'][$key] = $value;
						}
					}
				}
			} 	
			
			/**
				$formPage = confirm
				--> On effectue l'installation :
					- On crée le fichier bddAccess.php
					- On crée la base de donnée
					- On enregistre les paramètres dans la base de donnée
					- On crée le premier utilisateur
			**/
			else if ($formPage == 'confirm')
			{
				// On crée le fichier bddAccess.php
				
				$bddAccessEquivalent = array('SERVER' => 'BDD_HOST', 'PORT' => 'BDD_PORT', 'DBNAME' => 'BDD_DBNAME', 'BDDUSERNAME' => 'BDD_USER', 'PASSWORD' => 'BDD_PASSWORD'); // Fait l'équivalent entre $_SESSION et le contenu du fichier
				$bddAccess = fopen('../core/bddAccess.php', 'w+');
				fwrite ($bddAccess, "<?php ".PHP_EOL);
				foreach ($_SESSION['bddSettings'] AS $key => $value)
				{
					fwrite($bddAccess, "define('".$bddAccessEquivalent[$key]."', '".$value."');".PHP_EOL);
				}
				fwrite ($bddAccess, "?>");
				fclose($bddAccess);
				
				// On crée la base de donnée
				
					// Connexion à la base de donnée
					try {
						$db = new PDO('mysql:host='.$_SESSION['bddSettings']['SERVER'].';port='.$_SESSION['bddSettings']['PORT'].';dbname='.$_SESSION['bddSettings']['DBNAME'], $_SESSION['bddSettings']['BDDUSERNAME'], $_SESSION['bddSettings']['PASSWORD'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT));
					} 
					catch (PDOException $e) {
						$erreur['LANG_INSTALL_ERREUR_BDDINSTALL_UNKNOWERROR'] = TRUE;
					}
					
					// On crée la structure
					if (isset($db))
					{
						// On charge le fichier contenant le requête
						require('query.php');
						try
						{
							$db -> query($sqlRemove);
							$db -> query($sql);
						}
						catch (PDOException $e) {
							$erreur['LANG_INSTALL_ERREUR_BDDINSTALL_UNKNOWERROR'] = TRUE;						
						}
					}
					
					// On enregistre les paramètres
					foreach ($_SESSION['settings'] AS $key => $value)
					{
						$res = $db -> prepare('INSERT INTO setting (alias, valeur) VALUE (?, ?)');
						$res -> execute(array($key, $value));
					}

					// On crée le compte utilisateur
					$res = $db -> prepare('INSERT INTO user (nbEtudiant, nom, prenom, mail, rang) VALUES (?, ?, ?, ?, 4)');
					$res -> execute(array($_SESSION['newUser']['NBETU'], $_SESSION['newUser']['NOM'], $_SESSION['newUser']['PRENOM'], serialize($_SESSION['newUser']['MAIL'])));
					
					if (count($erreur) > 0 && isset($db))
					{
						// On supprime ce qui a été importé
						$db -> query($sqlRemove);
					}
					
					$formSent = TRUE; // Enregistre qu'on a effectué l'enregistrement
			}
	}

	/*
		3. Routage : choisis la page à afficher
	*/
	if (!isset($_SESSION['settings']['LANG'])) // On a pas encore configuré la langue
	{
		$page = 'lang';
	}
	else if (!isset($_SESSION['requirement']) || !$_SESSION['requirement']) // Minimum au fonctionnement de PAS
	{
		$page = 'requirement';
	}
	else if (!isset($_SESSION['bddSettings']) || count($_SESSION['bddSettings']) == 0) // Connexion à la BDD
	{
		$page = 'bdd';
	}
	else if (count($_SESSION['settings']) == 1) // On a configuré la langue mais pas le reste
	{
		$page = 'settings';
	}
	else if (!isset($_SESSION['newUser']))
	{
		$page = 'newUser';
	}
	else
	{
		$page = 'confirm';
	}
	
		/*
		3.1 : Si $_GET['goTo'] : on va dans une page particulière
		*/

		if (isset($_GET['goTo']) && in_array($_GET['goTo'], $steps) && array_search($_GET['goTo'], $steps) < array_search($page, $steps))
		{
			$page = $_GET['goTo'];
		}

	/*
		4. On charge les fichiers nécessaires à l'affichage des pages
	*/
		
		/*
			4.1 Chargement du fichier de langue
		*/

		if (isset($_SESSION['settings']['LANG']) && isset($allowedLang) && is_file('../core/lang/'.$_SESSION['settings']['LANG'].'.php'))
		{
			$lang = $_SESSION['settings']['LANG'];
		}
		else
		{
			$lang = 'english';
		}
		
		require('../core/lang/'.$lang.'.php'); 
?>
