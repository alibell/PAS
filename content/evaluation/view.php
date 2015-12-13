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
		24/02/15 - view.php - Ali Bellamine
		Page chargé d'afficher le formulaire d'évaluation et de le traiter une fois les évaluations envoyés
	*/
	
	require '../../core/main.php';
	require '../../core/header.php';
	
	$erreur = array();
	
	/**
		Vérification de l'évaluation sélectionnée
			1. : Vérification de l'existence du formulaire
			2. : Vérification de l'installation du type de formulaire
			3. : Verification que le formulaire n'a pas déjà été complété
			4. : Chargement du script
	**/
	/**
		1. Vérification de l'existence du formulaire
	**/
	
	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$erreur = checkRegisterEvaluation($_GET['id'], $erreur);
	}
	else
	{
		$erreur[4] = true;
	}

	/**
		2. Verification de l'installation du plugin
	**/
	
	if (count($erreur) == 0)
	{
		$evaluationRegisterData = getEvalRegisterData($_GET['id']);
		$evaluationData = getEvalData($evaluationRegisterData['evaluation']['id']);
		$evaluationData['register'] = $evaluationRegisterData;
		$erreur = checkEvaluationType($evaluationData['type']['id'], $erreur);
	}
	
	/**
		3. Verification que le formulaire n'a pas déjà été complété
	**/
	
	// Récupération des informations sur les évaluations de l'utilisateur
	$evaluationList = getEvalList($_SESSION['id']);

	// On enregistre une erreur si l'utilisateur a déjà remplis le formulaire
	if (isset($evaluationList[$evaluationData['id']]['remplissage'] ['valeur']) && $evaluationList[$evaluationData['id']]['remplissage'] ['valeur'] == true)
	{
		$erreur[6] = true;
	}
	
	// On enregistre une erreur si la période de remplissage du script ne correspond pas
	if (time() < $evaluationList[$evaluationData['id']]['date']['debut'] || time() > $evaluationList[$evaluationData['id']]['date']['fin'])
	{
		$erreur[24] = true;
	}

	/**
		4. On charge le script
	**/

	if (count($erreur) == 0)
	{
		// Enregistre l'accord du Disclaimer
		if (isset($_POST['chartValidation']))
		{
			$_SESSION['DISCLAIMER'] = TRUE;
		}
		
		// Si Disclaimer non accepté => affiche le disclaimer, l'accord vaut pour la durée de la session
		if (!isset($_SESSION['DISCLAIMER']))
		{
			?>
			<div style = "width: 80%; display: block; margin: auto;">
				<div style = "display: inline-block;">
				<?php
					echo CHARTE;
				?>
				</div>
				<form method = "POST">
					<input type = "hidden" name = "chartValidation" />
					<input type = "submit" value = "<?php echo LANG_ADMIN_CHART_VALID; ?>" style = "color: white; background-color: #117D11; border-radius: 5px;" />
				</form>
			</div>
			<?php
		}
		else
		{
			// Récupération des informations concernant le script
			$evaluationTypeData = getEvalTypeData($evaluationData['type']['id']);
			
			// On déclare la constante PLUGIN_PATH contenant le chemin du plugin
			define('PLUGIN_PATH', $_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/evaluations/'.$evaluationTypeData['dossier'].'/');
			
			// On charge la page
			?>
			<div class = "formEvaluation">
			<?php
				// On charge le fichier de config si il est présent
				if (is_file(PLUGIN_PATH.'settings.php'))
				{
					include(PLUGIN_PATH.'settings.php');
				}
				
				// On refuse de charger le plugin si on est connecté en tant qu'un autre utilisateur et que cela n'a pas été autorisé
				if (isset($_SESSION['loginAS']) && (!defined('ALLOW_LOGIN_AS') || constant('ALLOW_LOGIN_AS') == FALSE))
				{
					header('Location: '.getPageUrl('eval', array('erreur' => serialize(array(33 => TRUE)))));

					// On arrête l'execution du script
					exit();
				}
				
				include(PLUGIN_PATH.'displayEvaluation.php');
			?>
			</div>
			<?php			
		}
	}
	else
	{
		header('Location: '.ROOT.'content/evaluation/index.php?erreur='.serialize($erreur));
	}
	
	require '../../core/footer.php';
	
	// On inclut le JavaScript du formulaire
	if (count($erreur) == 0 && isset($evaluationTypeData['optionnel']['js']))
	{
		foreach ($evaluationTypeData['optionnel']['js'] AS $js)
		{
			if ($js == 'main' || $js == 'displayEvaluation')
			{
			?>
				<script type="text/javascript" src="<?php echo PLUGIN_PATH.'js/'.$js.'.js'; ?>"></script> <!-- Gestion du menu -->
			<?php
			}
		}
	}
?>