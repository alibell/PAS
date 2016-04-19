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
		16/02/15 - settings.php - Ali Bellamine
		Paramètres du site
	*/
	
	/**
		Accès à la BDD
	**/
	
	include('bddAccess.php');
	
	/**
		Racine du programme
	**/
	
	$_SERVER['DOCUMENT_ROOT'] = dirname(dirname( __FILE__ ));

	/**
		Configuration du CHARSET
	**/
	
	mb_internal_encoding("UTF-8");
	
	/**
		Configuration du timezone (GMT)
	**/
	date_default_timezone_set('Europe/London');
	
	/**
		Connexion à la base de données
	**/
	
	$db = new PDO('mysql:host='.BDD_HOST.';port='.BDD_PORT.';dbname='.BDD_DBNAME,BDD_USER,BDD_PASSWORD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	
	/*
		Constantes
	*/

	define('PROGRAM_NAME', 'PAS');
	define('PROGRAM_VERSION', '1.3.3');
	define('LOCAL_PATH', ''); // Fix, @todo : supprimer tous les LOCAL_PATH du code
	define('DEFAULT_LANG','english'); // Langue par défaut, si aucun fichier de langue n'est présent
	
	/*
		Récupération des paramètres du site
	*/

	$res = $db -> query('SELECT * FROM setting');
	while ($res_f = $res -> fetch())
	{
		define(strtoupper($res_f['alias']),$res_f['valeur']); 
	}	
	
	/*
		Création de la variable constante CURRENT_FILE
	*/
	$pathTemp = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	$rootParsed = parse_url(ROOT);
	$pathTempTable = explode($rootParsed['host'].$rootParsed['path'], $pathTemp, 2);
	define('CURRENT_FILE',$pathTempTable[1]);
?>