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
	Script d'installation du CMS
*/

	// On inclut le header
	include('main.php');
	include('header.php');

?>
	<!-- Création du formulaire -->
	<?php
		if (isset($_SERVER['HTTPS']))
		{
			$http = 'https://';
		}
		else
		{
			$http = 'http://';
		}
	?>

	<form action = "<?php echo $http.$_SERVER["HTTP_HOST"].$_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET); ?>" method = "POST">
	<?php
		if (isset($page))
		{
			?>
				<input type = "hidden" name = "page" value = "<?php echo $page; ?>" />
			<?php
		}
	?>
	
<?php
	/* page : lang 
		L'utilisateur est invité à sélectionner la langue pour continuer
	*/
	if ($page == 'lang')
	{
		?>
				<label for = "selectLang"><?php echo LANG_INSTALL_SELECT_LANGUAGE; ?></label>
				<select name = "selectLang" id = "selectLang">
					<?php
						foreach ($allowedLang AS $thelang => $thelangvalue)
						{
							?>
								<option value = "<?php echo $thelang; ?>" <?php if ($thelang == $lang) { echo 'selected'; } ?>><?php echo $thelangvalue; ?></option>
							<?php
						}
					?>
				</select>
		<?php
	}	
	else if ($page == 'requirement')
	{
		$requirementOK = TRUE;
		?>
		<div id = "requirementListDiv">
		<h1><?php echo LANG_INSTALL_REQUIREMENT_TITLE; ?></h1>
		<ul class = "requirementList">
			<?php
			foreach ($requirementList AS $requirement => $requirementValue)
			{
				if (!$requirementValue) { $requirementOK = FALSE; }
				?>
					<li style = "color: <?php if ($requirementValue) { echo '#108E10'; } else { echo '#C31B1B'; } ?>;"><?php echo $requirement; ?></li>
				<?php
			}
			?>
		</ul>
		</div>
		<?php
		if ($requirementOK)
		{
			?>
				<input type = "hidden" name = "requirementOK" value = "true" />
			<?php
		}
	}
	else if ($page == 'bdd')
	{
		?>
			<h1><?php echo LANG_INSTALL_BDD_TITLE; ?></h1>
		<?php
		foreach ($bddList AS $bddName => $bddType)
		{
		?>
			<div>
				<label for = "<?php echo $bddName; ?>"><?php echo constant('LANG_INSTALL_BDD_VALEUR_'.$bddName); ?></label>
				<input style = "width: 90%;" id = "<?php echo $bddName; ?>" placeholder = "<?php echo constant('LANG_INSTALL_BDD_VALEUR_'.$bddName); ?>" name = "<?php echo $bddName; ?>" value = "<?php if (isset($_SESSION['bddSettings'][$bddName])) { echo $_SESSION['bddSettings'][$bddName]; } else if (isset($_POST[$bddName])) { echo $_POST[$bddName]; } else if ($bddName == 'PORT') { echo 3306; } ?>" type = "<?php if ($bddType == 'password') { echo 'password'; } else if ($bddType == 'number') { echo 'number'; } else { echo 'text'; } ?>"  <?php if ($bddName != 'PASSWORD') { echo 'required'; } ?> />
			</div>
		<?php
		}
	}
	else if ($page == 'settings')
	{
		?>
			<h1><?php echo LANG_INSTALL_SETTINGS_TITLE; ?></h1>
		<?php
		foreach ($settingsList AS $settingName => $settingValue)
		{
		?>
			<div>
				<label for = "<?php echo $settingName; ?>"><?php echo constant('LANG_ADMIN_PARAMETRE_VALEUR_'.$settingName); ?></label>
				<input style = "width: 90%;" placeholder = "<?php echo constant('LANG_ADMIN_PARAMETRE_VALEUR_'.$settingName); ?>" value = "<?php if (isset($_SESSION['settings'][$settingName])) { echo $_SESSION['settings'][$settingName]; } else if (isset($_POST[$settingName])) { echo $_POST[$settingName]; } ?>" id = "<?php echo $settingName; ?>" name = "<?php echo $settingName; ?>" type = "<?php if ($settingValue['type'] == 'password') { echo 'password'; } else if ($settingValue['type'] == 'number') { echo 'number'; } else if ($settingValue['type'] == 'mail') { echo 'email'; } else if ($settingValue['type'] == 'url') { echo 'url'; } else { echo 'text'; } ?>"  <?php if ($settingValue['required']) { echo 'required'; } ?> />
			</div>
		<?php
		} 
	}
	else if ($page == 'newUser')
	{
		?>
			<h1><?php echo LANG_INSTALL_NEWUSER_TITLE; ?></h1>
		<?php
		foreach ($userList AS $userName => $userValue)
		{
		?>
			<div>
				<label for = "<?php echo $userName; ?>"><?php echo constant('LANG_INSTALL_NEWUSER_VALEUR_'.$userName); ?></label>
				<input style = "width: 90%;" placeholder = "<?php echo constant('LANG_INSTALL_NEWUSER_VALEUR_'.$userName); ?>" value = "<?php if (isset($_SESSION['newUser'][$userName])) { echo $_SESSION['newUser'][$userName]; } else if (isset($_POST[$userName])) { echo $_POST[$userName]; } ?>" id = "<?php echo $userName; ?>" name = "<?php echo $userName; ?>" type = "<?php if ($userValue == 'password') { echo 'password'; } else if ($userValue == 'mail') { echo 'mail'; } else { echo 'text'; } ?>"  required />
			</div>
		<?php
		} 	
	}
	else if ($page == 'confirm')
	{	
		?>
			<h1><?php echo LANG_INSTALL_CONFIRM_TITLE; ?></h1>
			
			<?php
			if (!isset($formSent) || !$formSent || ($formSent && count($erreur) > 0))
			{
				$displayButton = TRUE;
			?>
			<table>
				<!-- Base de donnée -->
				<tr class = "headTR">
					<td colspan = "2">
						<?php echo LANG_INSTALL_BDD_TITLE; ?>
					</td>
				</tr>
				<?php
				foreach ($_SESSION['bddSettings'] AS $key => $value)
				{
				?>
				<tr class = "bodyTR">
					<td><?php echo constant('LANG_INSTALL_BDD_VALEUR_'.$key); ?></td>
					<td><?php echo $value; ?></td>
				</tr>
				<?php
				}
				?>
				
				<!-- Réglages -->
				<tr class = "headTR">
					<td colspan = "2">
						<?php echo LANG_INSTALL_SETTINGS_TITLE; ?>
					</td>
				</tr>
				<?php
				foreach ($_SESSION['settings'] AS $key => $value)
				{
				?>
				<tr class = "bodyTR">
					<td><?php echo constant('LANG_ADMIN_PARAMETRE_VALEUR_'.$key); ?></td>
					<td><?php echo $value; ?></td>
				</tr>
				<?php
				}
				?>

				<!-- Compte utilisateur -->
				<tr class = "headTR">
					<td colspan = "2">
						<?php echo LANG_INSTALL_NEWUSER_TITLE; ?>
					</td>
				</tr>
				<?php
				foreach ($_SESSION['newUser'] AS $key => $value)
				{
				?>
				<tr class = "bodyTR">
					<td><?php echo constant('LANG_INSTALL_NEWUSER_VALEUR_'.$key); ?></td>
					<td><?php echo $value; ?></td>
				</tr>
				<?php
				}
				?>
			</table>
		<?php
		}
		else
		{
			?>
			<div>
				<?php echo LANG_INSTALL_CONFIRM_FINISH; ?>
			</div>
			<?php
		}
	}

	// Bouton d'action : suivant ou terminer
	if ($page == 'lang' || $page == 'settings' || ($page == 'requirement' && $requirementOK) || $page == 'bdd' || $page == 'newUser')
	{
		?>
			<input type = "submit" value = "<?php echo LANG_INSTALL_NEXT_BUTTON; ?>" />
		<?php
	}	
	else if ($page == 'confirm' && isset($displayButton) && $displayButton)
	{
		?>
			<input type = "submit" value = "<?php echo LANG_INSTALL_CONFIRM_BUTTON; ?>" />
		<?php	
	}
?>
	</form>

<?php
	// On inclut le footer
	include('footer.php');
?>