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
		19/07/2015 - Ali BELLAMINE
		admin/settings.php - Page de configuration des paramètres de la plateforme
	**/
	require '../core/main.php';
	require '../core/header.php';
	
	/**
		1. Récupération des données
	**/
	
		// Liste des paramètres
		$paramList = array();
		$sql = 'SELECT alias nom, valeur valeur FROM setting';
		$res = $db -> query($sql);
		while ($res_f = $res -> fetch())
		{
			$paramList[$res_f['nom']] = $res_f['valeur'];
		}
		
	/**
		2. Traitement des formulaires --> on enregistre la modification
	**/
	
	if (isset($_POST) && count($_POST) > 0)
	{
		if (isset($_POST) && isset($_POST['action']) && $_POST['action'] == 'updateValue' && isset($_POST['key']) && isset($paramList[$_POST['key']]) && isset($_POST['value']) && $_POST['value'] != '' && $_POST['value'] != $paramList[$_POST['key']])
		{
			ob_end_clean();
				$sql = 'UPDATE setting SET valeur = ? WHERE alias = ? LIMIT 1';
				$res = $db -> prepare($sql);
				$res -> execute(array($_POST['value'], $_POST['key']));
			exit();
		}
		else if (isset($_POST) && isset($_POST['action']) && $_POST['action'] == 'getValue' && isset($_POST['key']) && isset($paramList[$_POST['key']]))
		{
			ob_end_clean();
				echo $paramList[$_POST['key']];
			exit();
		}
	}
	
	/**
		3. Affichage des données
	**/
	
	?>
		<h1><?php echo LANG_ADMIN_PARAMETRE_TITLES; ?></h1>
		<table style = "margin-top: 10px;">
			<tr class = "headTR">
				<th><?php echo LANG_ADMIN_PARAMETRE_TABLE_PARAMETRE; ?></th>
				<th><?php echo LANG_ADMIN_PARAMETRE_TABLE_VALEUR; ?></th>
			</tr>
			<?php
				if (isset($paramList) & count ($paramList) > 0)
				{
					foreach ($paramList AS $key => $value)
					{
						?>
							<tr>
								<td>
									<?php 
										if (defined('LANG_ADMIN_PARAMETRE_VALEUR_'.$key))
										{
											echo constant('LANG_ADMIN_PARAMETRE_VALEUR_'.$key);
										}
										else
										{
											echo $key;
										}
									?>
								</td>
								<td><span contenteditable = "true" class = "settingsSpan" style = "padding: 10px; border-radius: 5px;" data-key = "<?php echo $key; ?>"><?php echo $value; ?></span></td>
							</tr>
						<?php
					}
				}
			?>
		</table>
	<?php
	
	require '../core/footer.php';
?>

<script>
	// Comportement de la touche entrée
	$('.settingsSpan').on('keypress', function(e){
		if (e.keyCode == 13)
		{
			e.preventDefault();
			$(this).blur();
		}
	});

	// Enregistre le changement
	$('.settingsSpan').on('focusout', function(){
		var key = $(this).attr('data-key');
		
		$.post("<?php echo ROOT.CURRENT_FILE; ?>", {action: 'updateValue', key: key, value: $(this).html()}, function(){
			// On met à jour le contenu du span
			$.post("<?php echo ROOT.CURRENT_FILE; ?>", {action: 'getValue', key: key}, function(e){
				console.log(e);
				$(this).html(e);
			});
		});
	});
</script>