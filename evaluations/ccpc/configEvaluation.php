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

		/*
			Fichiers de fonctions
		*/
		require(PLUGIN_PATH.'core/fnDisplayEvaluationResult.php'); // Fonctions propres à l'affichage des résultats d'épreuves
		require(PLUGIN_PATH.'core/fnDisplayEvaluation.php'); // Fonctions propres à l'affichage des formulaires d'évaluation
		
		/*
			0. Initialisation des variables
		*/
		
		$erreur = array();
		
		/*
			1. Récupération des données
		*/
		
		$evaluationSettingsData = eval_ccpc_getSettings($evaluationData['id']);

		/*
			2. Traitement du formulaire
		*/
		
		if (isset($_POST) && count($_POST) > 0)
		{
			if (isset($_POST['dateDebut']) && isset($_POST['dateFin']))
			{
				$TimeStampDateDebut = DatetimeToTimestamp(FrenchdateToDatetime($_POST['dateDebut']));
				$TimeStampDateFin = DatetimeToTimestamp(FrenchdateToDatetime($_POST['dateFin']));
				if ($TimeStampDateDebut <= $TimeStampDateFin)
				{
					// On convertit les dates
					$evaluationSettingsData['dateDebut'] = $TimeStampDateDebut;
					$evaluationSettingsData['dateFin'] = $TimeStampDateFin;

					// On essaie d'enregistrer les réglages
					if (eval_ccpc_setSettings($evaluationSettingsData))
					{
						// On valide le réglage
						validateEvaluationSettings();
					}
					else
					{
						$erreur['LANG_ERROR_CCPC_UNKNOWN'] = TRUE;
					}
				}
				else
				{
					$erreur['LANG_ERROR_CCPC_INVALIDDATE'] = TRUE;
				}
			}
			else
			{
				$erreur['LANG_ERROR_CCPC_INCOMPLETEFORM'] = TRUE;
			}
		}

		/*
			3. Affichage du formulaire
		*/
		
			// Affichage des erreurs
			if (count($erreur) > 0)
			{
				?>
				<ul class = "erreur">
				<?php
				foreach ($erreur AS $error => $errorState)
				{
					?><li><?php echo constant($error); ?></li><?php
				}				
				?>
				</ul>
				<?php
			}
			
			// Affichage du formulaire
	?>
			<h2><?php echo LANG_FORM_CCPC_SETTINGS_TITLE; ?></h2>
	
			<form class="formEvaluation" method="POST">
				<fieldset>
					<legend><?php echo LANG_FORM_CCPC_SETTINGS_PERIOD_TITLE; ?></legend>
										
					<!-- Date -->
					<label for = "dateDebut"><?php echo LANG_FORM_CCPC_SETTINGS_PERIOD_START; ?></label>
					<input type = "hidden" name = "dateDebut" value = "<?php if (isset($_POST['dateDebut'])) { echo $_POST['dateDebut']; } else if (isset($evaluationSettingsData['dateDebut']) && $evaluationSettingsData['dateDebut'] != FALSE) { echo date('j/n/Y', $evaluationSettingsData['dateDebut']); } ?>" />
					<input type = "button" class = "dateButton" id = "dateDebut" value = "<?php if (isset($_POST['dateDebut'])) { echo $_POST['dateDebut']; } else if (isset($evaluationSettingsData['dateDebut']) && $evaluationSettingsData['dateDebut'] != FALSE) { echo date('j/n/Y', $evaluationSettingsData['dateDebut']); } ?>" /><br />

					<label for = "dateFin"><?php echo LANG_FORM_CCPC_SETTINGS_PERIOD_END; ?></label>
					<input type = "hidden" name = "dateFin" value = "<?php if (isset($_POST['dateFin'])) { echo $_POST['dateFin']; } else if (isset($evaluationSettingsData['dateFin']) && $evaluationSettingsData['dateFin'] != FALSE) { echo date('j/n/Y', $evaluationSettingsData['dateFin']); } ?>" />
					<input type = "button" class = "dateButton" id = "dateFin" value = "<?php if (isset($_POST['dateFin'])) { echo $_POST['dateFin']; } else if (isset($evaluationSettingsData['dateFin']) && $evaluationSettingsData['dateFin'] != FALSE) { echo date('j/n/Y', $evaluationSettingsData['dateFin']); }  ?>" /><br />

				<input type="submit" id="submit_add" value="<?php echo LANG_FORM_CCPC_SETTINGS_SUBMIT; ?>">			
			</form>
			
			
	<script>
	// Calendrier
	$('input[type="button"]').each(function(){
		if ($(this).val() != '')
		{
			var dateValues = $(this).val().split('/');
			var currentDate = new Date(dateValues[2], dateValues[1]-1, dateValues[0]);
		}
		else
		{
			var currentDate = null;
		}
		
		$(this).glDatePicker({
			selectedDate: currentDate,
			closeText: 'Fermer',
			prevText: 'Précédent',
			nextText: 'Suivant',
			currentText: 'Aujourd\'hui',
			monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
			monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
			dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
			dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
			dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
			weekHeader: 'Sem.',
			dateFormat: 'dd/mm/YYYY',
			onClick: (function(el, cell, date, data) {
				el.val(date.toLocaleDateString());
				$('input[name="'+ el.attr('id')+'"]').val(date.toLocaleDateString()); // On met à jour la valeur de l'input caché correspondant
			})
		});
	});
	</script>