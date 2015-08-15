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
		
		/**
			Inclusion des fichiers nécessaires au bon fonctionnement de la page
		**/
		
		if (is_file(PLUGIN_PATH.'lang/'.LANG.'.php'))
		{
			require PLUGIN_PATH.'lang/'.LANG.'.php'; // Fichier de langue
		}
		else
		{
			require PLUGIN_PATH.'lang/'.DEFAULT_LANG.'.php';
		}
		require(PLUGIN_PATH.'core/fnCore.php'); // Insertion du fichier de fonctions

		/**
			0. On prépare les variables 
		**/
		
			$erreur = array();
			$valid = false;
			
		/**
			1. On récupère les données propres à l'évaluation
		**/
			eval_initTable();
			$surveyData = eval_getSurveyData($evaluationData['id']);
			
		/**
			2. On traite le retour du formulaire
		**/
		if (isset($_GET['eval']) && $_GET['eval'] == $surveyData['code'])
		{
			$valid = true;
			validateEvaluation();	
		}
			
		/**
			3. Vérification des données
		**/
		
			if (!isset($surveyData) || count($surveyData) == 0)
			{
				$erreur[23] = true;
			}
			
		/**
			4. Affichage des données
		**/
		
		displayErreur($erreur);
		
		if (isset($surveyData) && count($surveyData) > 0 && !$valid)
		{
			// On retourne vers le formulaire
			header('Location: '.$surveyData['link']);
		}
	?>
	
<script>
</script>