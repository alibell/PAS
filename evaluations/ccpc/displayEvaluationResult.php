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

	// Insertion du fichier de langue
	if (is_file(PLUGIN_PATH.'lang/'.LANG.'.php'))
	{
		require PLUGIN_PATH.'lang/'.LANG.'.php'; // Fichier de langue
	}
	else
	{
		require PLUGIN_PATH.'lang/'.DEFAULT_LANG.'.php';
	}	

	// Insertion du fichier de fonctions
	require(PLUGIN_PATH.'core/fnDisplayEvaluationResult.php'); // Fonctions propres à l'affichage des résultats d'épreuves
	require(PLUGIN_PATH.'core/fnGraphGen.php'); // Affichage des graphiques
	include(PLUGIN_PATH.'core/fnAdmin.php'); // Administration du module

	// On nettoie le cache --> supprime tous les fichiers de + de 30 jours : afin d'éviter la surconsommation d'espace disque
	eval_ccpc_clearCache();
	
	// Détermination du délai avant de pouvoir consulter les évaluations
	if ($_SESSION['rang'] == 1)
	{
		define('CONFIG_EVAL_CCPC_DELAIDISPOEVAL', CONFIG_EVAL_CCPC_DELAIDISPOEVAL_STUDENT);		
	}
	else
	{
		define('CONFIG_EVAL_CCPC_DELAIDISPOEVAL', CONFIG_EVAL_CCPC_DELAIDISPOEVAL_TEACHER);
	}

	$erreur = array();
	$urlPage = http_build_query($_GET); // URL avec les $_GET
	initTable(); // Met à jour la structure de la BDD si besoin
	
	/**
		Routage -- On détermine la variable $action
	**/
		$action = 'listEvaluation'; // Page par défault
		
		if (isset($_GET['service'])) // Si on a cliqué sur un service : on affiche la page du service, si ce dernier existe
		{
			if (count(checkService($_GET['service'], array())) == 0)
			{
				$action = 'evaluationDetails';
			}
		}
		else if (isset($_GET['ajax']) && is_file (PLUGIN_PATH.'ajax/'.$_GET['ajax'].'.php'))
		{
			$action = 'loadAjax';
		}
		else if (isset($_GET['page']) && is_file (PLUGIN_PATH.'displayEvaluationResult/'.$_GET['page'].'.php'))
		{
			$action = 'loadPage';
		}

	/**
		1. Récupération des données
	**/
	
		/***
			Page spécifique à l'affichage de la liste des évaluations
		***/
		
		// Liste des évaluations
		if ($action == 'listEvaluation')
		{
			include(PLUGIN_PATH.'displayEvaluationResult/listEvaluationData.php');
		}
		// Données propres à une évaluation
		else if ($action == 'evaluationDetails')
		{
			if (isset($_GET['service']) && is_numeric($_GET['service']))
			{
				include(PLUGIN_PATH.'displayEvaluationResult/contentEvaluationData.php');
			}
			else
			{
				unset($_GET['service'] );
				header('Location: '.ROOT.CURRENT_FILE.'?'.http_build_query($_GET));
			}
		}
		// Chargement de la page Ajax
		else if ($action == 'loadAjax')
		{
			include(PLUGIN_PATH.'ajax/'.$_GET['ajax'].'.php');
		}
		
	
	/**
		2. Affichage des données
	**/
	
?>
	<div id = "divResultats">
	<?php
		// Liste des évaluations
		if ($action == 'listEvaluation')
		{
			include(PLUGIN_PATH.'displayEvaluationResult/listEvaluationDisplay.php');
		}
		// Données propres à une évaluation
		else if ($action == 'evaluationDetails')
		{
			include(PLUGIN_PATH.'displayEvaluationResult/contentEvaluationDisplay.php');
		}
		// Chargement d'une page spécifié par la variable $_GET['page']
		else if ($action == 'loadPage')
		{
			include(PLUGIN_PATH.'displayEvaluationResult/'.$_GET['page'].'.php');
		}
	?>
	</div>