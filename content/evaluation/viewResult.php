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
		26/02/15 - viewResult.php - Ali Bellamine
		Page chargé d'afficher les résultats des formulaires d'évaluation
	*/
	
	require '../../core/main.php';
	require '../../core/header.php';
	
	/*
		Récupération de la liste des type d'évaluation installés
	*/
	$evaluationTypeList = getEvaluationTypeList();
	if (count($evaluationTypeList) != 0 && !isset($_GET['evaluationType']))
	{
	?>
		<div id = "selecteurEvaluation">
		<form method = "GET" action = "<?php echo ROOT.'content/evaluation/viewResult.php'; ?>">
			<label for ="evaluationType" style = "float: inherit; margin: auto;"><?php echo LANG_INTERFACE_SELECTEVALUATIONTYPE; ?>
			<select name = "evaluationType" id = "evaluationType">
				<?php
				foreach ($evaluationTypeList AS $evaluationType)
				{
					if ($evaluationType['actif'] == 1 && isset($evaluationType['resultRight'][$_SESSION['rang']]) && $evaluationType['resultRight'][$_SESSION['rang']] == 1)
					{
					?>
						<option value = "<?php echo $evaluationType['id']; ?>"><?php echo $evaluationType['nom']; ?></option>
					<?php
					}
				}
				?>
			</select>
			<input type = "submit" class = "inlineSubmit" value = "<?php echo LANG_INTERFACE_SELECTBUTTON; ?>" />
			</label>
		</form>
		</div>
	<?php
	}
	
	/**
		3. Vérification et chargement du module
	**/
	
	if (isset($_GET['evaluationType']))
	{
		if (isset($evaluationTypeList[$_GET['evaluationType']]) && $evaluationTypeList[$_GET['evaluationType']]['actif'] == 1 && isset($evaluationTypeList[$_GET['evaluationType']]['resultRight'][$_SESSION['rang']]) && $evaluationTypeList[$_GET['evaluationType']]['resultRight'][$_SESSION['rang']] == 1)
		{
			// On enregistre les informations sur le type d'évaluation sélectionné dans $evaluationTypeData
			$evaluationTypeData = getEvalTypeData($evaluationTypeList[$_GET['evaluationType']]['id']);
			
			// On déclare la constante PLUGIN_PATH contenant le chemin du plugin
			define('PLUGIN_PATH', $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/evaluations/'.$evaluationTypeData['dossier'].'/');

			// On charge le fichier de config si il est présent
			if (is_file(PLUGIN_PATH.'settings.php'))
			{
				include(PLUGIN_PATH.'settings.php');
			}
			
			// On refuse de charger le plugin si on est connecté en tant qu'un autre utilisateur et que cela n'a pas été autorisé
			if (isset($_SESSION['loginAS']) && (!defined('ALLOW_LOGIN_AS') || constant('ALLOW_LOGIN_AS') == FALSE))
			{
				header('Location: '.getPageUrl('evalView', array('erreur' => serialize(array(33 => TRUE)))));

				// On arrête l'execution du script
				exit();
			}
			
			// On charge le module
			include(PLUGIN_PATH.'displayEvaluationResult.php');
		}
	}
	
	require '../../core/footer.php';
	
	// On inclut le JavaScript du formulaire
	if (isset($evaluationTypeData['optionnel']['js']))
	{
		foreach ($evaluationTypeData['optionnel']['js'] AS $js)
		{
			if ($js == 'main' || $js == 'displayEvaluationResult')
			{
			?>
				<script type="text/javascript" src="<?php echo ROOT.'evaluations/'.$evaluationTypeData['dossier'].'/js/'.$js.'.js'; ?>"></script> <!-- Gestion du menu -->
			<?php
			}
		}
	}
?>