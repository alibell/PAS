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


	session_start();
	ob_start();
	mb_internal_encoding("UTF-8");

	/*
		16/02/15 - main.php - Ali Bellamine
		Fichier principal, executant l'intégralité des séquences lancées à la chaque chargement de page
	*/

	// Si le dossier d'installation est toujours présent --> rediriger vers ce dernier
	if (is_dir('install'))
	{
		unset($_SESSION);
		session_destroy();
		header('Location: '.$_SERVER['HTTP_ORIGIN'].'install/index.php');
		exit();
	}
	
	// Paramètres
	require_once 'settings.php';
	
	// Paramètre du mode développeur
	if (DEVMODE == 1) {
		// Afficher les erreurs à l'écran
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
                
                // Introduction d'un compteur
                $timeStart = microtime(true);
	}
	
	
	// Librairies
        require_once $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/CAS-1.3.3/CAS.php'; // CAS
	require_once $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/swiftmailer-5.x/lib/swift_required.php'; // Mail
	require_once $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/htmLawed.php'; // htmLawed : permet de se protéger des failles XSS, licence LGPL
	
	// Fonctions
	require_once $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/fnCore.php';
	require_once $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/fnVisuel.php';
	require_once $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/fnChecker.php';
	require_once $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/fnMenu.php';
	require_once $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/fnEvaluation.php';
	require_once $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/fnUser.php';
	require_once $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/fnStage.php';
	require_once $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/fnMail.php';
		
	// Fichier de language
	if (is_file($_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/lang/'.LANG.'.php'))
	{
		require_once $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/lang/'.LANG.'.php';
	}
	else
	{
		require_once $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/core/lang/'.DEFAULT_LANG.'.php';	
	}

    /**
        Deconnexion des utilisateurs
     **/

    if (isset($_GET['logout']))
    {
        unset($_SESSION);
        phpCAS::client(CAS_VERSION_2_0,CAS_SERVER_URI, (int) constant('CAS_SERVER_PORT'),'');
        phpCAS::setServerServiceValidateURL(CAS_SERVER_VALIDATEURI);
		if (is_file(CAS_SERVER_CERTIFICATPATH))
		{
			phpCAS::setCasServerCACert(CAS_SERVER_CERTIFICATPATH);
		}
		else
		{
			phpCAS::setNoCasServerValidation();			
		}
        phpCAS::logout(array('service'=>ROOT.'index.php?msg=LANG_SUCCESS_LOGOUT'));
        session_destroy();
    }

    /**
    Etablis le status de visiteur si non connecté
     **/

    if (!isset($_SESSION['rang']))
    {
        $_SESSION['rang'] = 0;
    }

	/**
		Récupération des informations sur la page actuelle
	**/
	
	if ($currentPageData = getCurrentPageData())
	{
		if ($currentPageData['fullRight'][$_SESSION['rang']] == 0)
        {
                    
            // On invite l'utilisateur à se connecter au CAS
			phpCAS::client(CAS_VERSION_2_0,CAS_SERVER_URI, (int) constant('CAS_SERVER_PORT'),'');
			phpCAS::setServerServiceValidateURL(CAS_SERVER_VALIDATEURI);
			if (is_file(CAS_SERVER_CERTIFICATPATH))
			{
				phpCAS::setCasServerCACert(CAS_SERVER_CERTIFICATPATH);
			}
			else
			{
				phpCAS::setNoCasServerValidation();			
			}
            phpCAS::forceAuthentication();

            if(phpCAS::getUser()) { //Si l'utilisateur s'est connecté
				
				// Récupération des données serveur
				$test = phpCAS::checkAuthentication();
				
                // Récupération des données utilisateur
                $userId = getUserIdFromNbEtudiant(phpCAS::getUser());

                if (isset($userId) && $userId != FALSE) {
                    login($userId);
                } else {
                    $errorCode = serialize(array(32 => true));
                    phpCAS::logout(array('service'=>ROOT.'index.php?erreur='.$errorCode));
		}
            }

            // On revérifie l'état de la connexion
            if ($currentPageData['fullRight'][$_SESSION['rang']] == 0) {
                $errorCode = serialize(array(7 => true));
				header('Location: '.ROOT.'index.php?erreur='.$errorCode);
            }
		}
	}
	else
	{
		$errorCode = serialize(array(7 => true));
		header('Location: '.ROOT.'index.php?erreur='.$errorCode);
	}
	
	/**
		Connexion au nom d'un autre utilisateur
	**/
	
	if (isset($_GET['loginAS']) && isset($_GET['loginASId']) && is_numeric($_GET['loginASId']) && count(checkUser($_GET['loginASId'], array())) == 0 && !isset($_SESSION['loginAS']))
	{
		// On enregistre les données de connexion dans une variable session
		$userData = getUserData($_GET['loginASId']);
		
		if ($_SESSION['rang'] >= $userData['rang'])
		{		
			$_SESSION['loginAS']['newUser']['id'] = $userData['id'];
			$_SESSION['loginAS']['newUser']['nom'] = $userData['nom'];
			$_SESSION['loginAS']['newUser']['prenom'] = $userData['prenom'];
			$_SESSION['loginAS']['newUser']['rang'] = $userData['rang'];
			if (isset($userData['promotion']))
			{
				$_SESSION['loginAS']['newUser']['promotion'] = $userData['promotion'];
			}

			// On stocke l'ancien utilisateur
			$_SESSION['loginAS']['oldUser'] = $_SESSION;
			
			// On switch les utilisateurs
                        login($userData['id']);
		}
	}
	
	if (isset($_SESSION['loginAS']))
	{
		$isLogedAs = TRUE;
	}
	else
	{
		$isLogedAs = FALSE;
	}
	
	if (isset($_GET['unloginAs']))
	{
		if (isset($_SESSION['loginAS']['oldUser']))
		{
			$_SESSION['id'] = $_SESSION['loginAS']['oldUser']['id'];
			$_SESSION['nom'] = $_SESSION['loginAS']['oldUser']['nom'];
			$_SESSION['prenom'] = $_SESSION['loginAS']['oldUser']['prenom'];
			$_SESSION['rang'] = $_SESSION['loginAS']['oldUser']['rang'];
			if (isset($_SESSION['loginAS']['oldUser']['promotion']))
			{
				$_SESSION['promotion'] = $_SESSION['loginAS']['oldUser']['promotion'];
			}
			unset($_SESSION['loginAS']);
		}
	}
	
	/**
		Mise à jour des plugins d'évaluations
	**/
	updateEvaluationsTypes();
?>