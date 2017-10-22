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
		16/02/15 - index.php - Ali Bellamine
		Page d'accueil du site
	*/
	
	require 'core/main.php';
	require 'core/header.php';
        
        if (DEVMODE == 1) {
            if (isset($_POST) && isset($_POST['devModeId'])) {
                login($_POST['devModeId']);
                header('Location: index.php');
            }            
        }
?>

<div id = "presentation">
	<div class = "divBox">
		<div class = "presentationLeft">
			<img src = "<?php echo ROOT.'theme/img/demoEvaluation.png'; ?>" />
		</div>
		<div class = "presentationRight">
			<span id = "titre"><?php echo TITRE; ?></span>
			<ul>
				<li><?php echo LANG_ACCUEIL_DESCRIPTION_1; ?></li>
				<li><?php echo LANG_ACCUEIL_DESCRIPTION_2; ?></li>
				<li><?php echo LANG_ACCUEIL_DESCRIPTION_3; ?></li>
			</ul>
			
			<?php
			// Bouton de connexion
			if ($_SESSION['rang'] == 0)
			{
			?>
				<a class = "connexionBouton" href = "<?php echo getPageUrl('login'); ?>"><?php echo LANG_ACCUEIL_CONNEXION; ?></a>
			<?php
				if (DEVMODE == 1) {
                                        $sql = 'SELECT * FROM user';
                                        $res = $db -> query($sql);
                                    
					?>
					<form class = 'form-inline' method = "POST" action = "">
                                                <select name = "devModeId" class = "form-control" id = "devModeLogin">
						<?php
                                                    while ($user = $res -> fetch()) {
                                                    ?>
                                                        <option value = "<?php echo $user['id']; ?>"><?php echo $user['nom'].' '.$user['prenom'].' ('.$user['rang'].')'; ?></option>
                                                    <?php
                                                    }
                                                ?>
						</select>
                                            <button type = "submit"><?php echo LANG_ACCUEIL_CONNEXION; ?></button>
					</form>
					<?php
				}
			}
			?>		
		</div>
	</div>
	
	<div id = "aboutPAS"><a href = "<?php echo getPageUrl('about'); ?>"><?php echo LANG_ACCUEIL_ABOUT; ?></a></div>
</div>

<?php
	require 'core/footer.php';
?>

<script>
    <?php
        if (DEVMODE == 1) {
        ?>
            $('#devModeLogin').chosen();
        <?php
        }
    ?>
</script>