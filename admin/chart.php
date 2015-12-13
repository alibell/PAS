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
		13/12/2015 - Ali BELLAMINE
		admin/chart.php - Page de configuration de la charte d'évaluation
	**/
	require '../core/main.php';
	require '../core/header.php';
	
	/**
		1. Récupération des données
	**/
	
		// Récupération de la charte
		$sql = 'SELECT valeur FROM setting WHERE alias = \'CHARTE\'';
		$res = $db -> query($sql);
		$res_f = $res -> fetch();
		
	/**
		2. Traitement des formulaires --> on enregistre la modification
	**/
	
	if (isset($_POST) && count($_POST) > 0)
	{
		if (isset($_POST) && isset($_POST['charte']))
		{
			$sql = 'UPDATE setting SET valeur = ? WHERE alias = \'CHARTE\' LIMIT 1';
			$res = $db -> prepare($sql);
			$res -> execute(array(htmlawed($_POST['charte'])));
			
			header('Location: '.ROOT.CURRENT_FILE);
		}
	}
	
	/**
		3. Affichage des données
	**/
	
	?>
		<h1><?php echo LANG_ADMIN_CHART_TITLES; ?></h1>
		
		<form method = "POST">
			<textarea name = "charte"><?php echo $res_f[0]; ?></textarea>
			<input type = "submit" value = "<?php echo LANG_ADMIN_CHART_SUBMIT; ?>" />
		</form>
	<?php
	
	require '../core/footer.php';
?>

<script>
tinymce.init({
	selector: "textarea",
	height: 600,
	menubar: false,
	statusbar: false
});
</script>