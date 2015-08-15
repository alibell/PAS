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
		
		/*
			0. Initialisation des variables
		*/
		
		$erreur = array();
		
		/*
			1. Récupération des données
		*/
		
		$surveyData = eval_getSurveyData($evaluationData['id']);
		
		if (isset($surveyData['code'])) { $surveyCode = $surveyData['code']; } // Code permettant la validation de l'évaluation
		else { $surveyCode = eval_random(10); } // Si l'évaluation n'existe pas on le génère

		/*
			2. Traitement du formulaire
		*/
		
		if (isset($_POST) && count($_POST) > 0)
		{
			// On initialise la BDD si elle n'existe pas
			eval_initTable();
			
			// On traite les données
			
				$queryData = array();
				$queryData['evaluation'] = $evaluationData['id'];
				
				// On verifie les données
				if (isset($_POST['surveyLink']) && filter_var($_POST['surveyLink'], FILTER_VALIDATE_URL))
				{
					$queryData['surveyLink'] = $_POST['surveyLink'];
				}
				else
				{
					$erreur[22] = true;
				}
				
				// On enregistre dans la BDD
				if (count($erreur) == 0)
				{
					// Si surveyData n'existe pas : on enregistre les données
					if (!isset($surveyData) || count($surveyData) == 0)
					{
						$queryData['surveyCode'] = $surveyCode;
						$sql = 'INSERT INTO eval_survey_settings (evaluation, surveyLink, surveyCode) VALUES (:evaluation, :surveyLink, :surveyCode);';
					}
					// Si surveyData existe : on modifie les données actuelles			
					else
					{
						$sql = 'UPDATE eval_survey_settings SET surveyLink = :surveyLink WHERE evaluation = :evaluation;';
					}
					
					$res = $db -> prepare($sql);
					if ($res -> execute($queryData))
					{
						// On valide l'enregistrement des préfèrences
						validateEvaluationSettings();
					}
					else
					{
						// On enregistre l'erreur
						$erreur[14] = true;
					}
				}
		}
		
		/*
			3. Affichage du formulaire
		*/
		
			// Affichage des erreurs
			displayErreur($erreur);
			
			// Affichage du formulaire
	?>
			<h2><?php echo LANG_EVALUATIONS_SURVEY_ADMIN_TITLE; ?></h2>
	
			<form method = "POST" />
				<!-- Lien du formulaire -->
				<label for = "surveyLink"><?php echo LANG_EVALUATIONS_SURVEY_ADMIN_FORMLINK_TITLE; ?></label>
				<input type = "text" name = "surveyLink" id = "surveyLink" style = "width: 50%;" value = "<?php if (isset($_POST['surveyLink'])) { echo $_POST['surveyLink']; } else if (isset($surveyData['link'])) { echo $surveyData['link']; } ?>" required />
				
				<input type = "submit" value = "<?php echo LANG_EVALUATIONS_SURVEY_ADMIN_SUBMIT; ?>" />
			</form>
			
			<?php echo LANG_EVALUATIONS_SURVEY_ADMIN_URLSET; ?>
			<input type = "text" style = "width: 100%;"  value = "<?php echo getPageUrl('evalDo').'id='.$evaluationData['id'].'&eval='.$surveyCode; ?>" readonly />