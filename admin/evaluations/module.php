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
		17/07/15 - module.php - Ali Bellamine
		Affiche la liste des modules d'évaluations intallés et permet d'en régler la confidentialité
	*/

/**
	Routage selon la variable action
**/

	$allowedAction = array('list', 'set');
	if (isset($_GET['action']) && in_array($_GET['action'], $allowedAction))
	{
		$action = $_GET['action'];
	}
	else
	{
		$action = 'list';
	}

	/**
		1. Récupération des données
	**/
	
$pageUtilisateurs = getPageUrl('adminUtilisateurs');

$listeModules = getEvaluationTypeList();

if ($action == 'set')
{
	if (isset($listeModules[$_GET['id']]))
	{
		$moduleData = $listeModules[$_GET['id']];
	}
	else
	{
		header('Location: '.ROOT.CURRENT_FILE.'?page='.$_GET['page']);
	}
}
		
	/**
		2. Traitement du formulaire
	**/
	
	if (isset($_POST) && count($_POST) && $action == 'set')
	{
		$allowedValue = array(0,1);
		$update = array();
		
		for ($n = 1; $n <= 4; $n++)
		{
			if (isset($_POST['result_access_'.$n]) && in_array($_POST['result_access_'.$n],$allowedValue) && $_SESSION['rang'] >= $n)
			{
				$updateArray['result_access_'.$n] = $_POST['result_access_'.$n];
			}
		}
		
		if (isset($updateArray) && count($updateArray) > 0 && isset($moduleData['id']))
		{
			$sql = 'UPDATE typeevaluation SET ';

			$firstLoop = TRUE;

			foreach ($updateArray AS $key => $value)
			{
				if ($firstLoop) { $firstLoop = FALSE; } else { $sql .= ', '; }
				$sql .= $key.' = :'.$key;
			}
			$sql .= ' WHERE id = :id';

			$updateArray['id'] = $moduleData['id'];
			
			$res = $db -> prepare($sql);
			$res -> execute($updateArray);
			
			// On redirige l'utilisateur
			header('Location: '.ROOT.CURRENT_FILE.'?page=module');
		}
	}
	
	/**
		3. Affichage
	**/
	
	if ($action == 'list') {
		/*
			Liste des modules
		*/
		
	?>
			<div id = "donnees">
			
			<?php
			/*
				Données
			*/
		
			// Création des liens
			$tempGET = $_GET;
			unset($tempGET['action']);
			$url = ROOT.CURRENT_FILE.'?'.http_build_query($tempGET);
			?>			
				<table  style = "margin-top: 10px;">
					<tr class = "headTR" style = "text-align: center;">
						<td><?php echo LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_NOM; ?></td>
						<td><?php echo LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_FOLDER; ?></td>
						<td><?php echo LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_STATE; ?></td>
						<td colspan = "4"><?php echo LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_RIGHT; ?></td>
						<td></td>
					</tr>
					<tr class = "headTR">
						<td></td>
						<td></td>
						<td></td>
						<?php
							for ($n = 1; $n <= 4; $n++)
							{
								?>
									<td><?php echo constant('LANG_RANG_VALUE_'.$n); ?></td>
								<?php
							}
						?>
						<td></td>
					</tr>
					<?php
						foreach($listeModules AS $module)
						{
							?>
							<tr class = "bodyTR">
								<td><?php echo $module['nom']; ?></td>
								<td><?php echo $module['dossier']; ?></td>
								<td><?php echo constant('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_STATE_OPTION_'.$module['actif']); ?></td>
								<?php
									foreach ($module['resultRight'] AS $rang => $droit)
									{
										?>
											<td style = "color: <?php if ($droit == 1) { echo 'green'; } else { echo 'red'; } ?>;" ><?php echo constant('LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_RIGHT_OPTION_'.$droit); ?></td>
										<?php
									}
								?>
								<td>
									<a href = "<?php echo ROOT.CURRENT_FILE.'?page=module&action=set&id='.$module['id']; ?>"><i class="fa fa-cog"></i></a>								
								</td>
							</tr>
							<?php
						}
					?>
				</table>
			</div>
	<?php
	}
	else if ($action == 'set')
	{
		?>
		<h1><?php echo $moduleData['nom']; ?></h1>

		
		<form class = "formEvaluation" method = "POST">
			<label for = "droitAccess"><?php echo LANG_ADMIN_MODULE_LISTE_TABLE_TITLE_RIGHT; ?></label><br />
			<?php
				for ($n = 1; $n <= 4; $n++)
				{
					?>
						<label for = "result_access_<?php echo $n; ?>"><input type = "checkbox" value = "1" name = "result_access_<?php echo $n; ?>" id = "result_access_<?php echo $n; ?>" <?php if ($_SESSION['rang'] < $n) { echo 'disabled'; } ?> <?php if (isset($_POST['result_access_'.$n]) && $_POST['result_access_'.$n] == 1) { echo 'checked'; } else if (isset($moduleData['resultRight'][$n]) && $moduleData['resultRight'][$n] == 1) { echo 'checked'; } ?>><?php echo constant('LANG_RANG_VALUE_'.$n); ?></label>
					<?php
				}
			?>
			
			<input type = "submit" value = "<?php echo LANG_ADMIN_MODULE_RIGHT_FORM_SUBMIT; ?>" />
		</form>
		<?php
	}
?>