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
		24/04/15 - index.php - Ali Bellamine
		Page d'accueil de la gestion des services  - Routeur de la gestion des services
	*/
	require '../../core/main.php';
	require '../../core/header.php';
	
	/**
		Page divisée en 2 parties :
			- Bandeau latéral contenant le menu
			- Corps contenant la page à afficher, dépendant de la variable $_GET['page']
	**/
	
		/*
			Récupération de la variable $_GET['page']
		*/
		
		$listePage = array('liste' => LANG_ADMIN_SERVICES_MENU_ITEM_LISTE, 'specialite' => LANG_ADMIN_SERVICES_MENU_ITEM_SPECIALITE, 'certificat' => LANG_ADMIN_SERVICES_MENU_ITEM_CERTIFICAT, 'hopitaux' => LANG_ADMIN_SERVICES_MENU_ITEM_HOPITAUX, 'affectations' => LANG_ADMIN_SERVICES_MENU_ITEM_AFFECTATIONS,'service' => ''); // Liste des pages liés à la gestion des utilisateurs
		
		if (isset($_GET['page']) && isset($listePage[$_GET['page']]))
		{
			$currentPage = $_GET['page'];
		}
		else
		{
			$currentPage = 'liste';
		}
				
		/*
			Chargement des pages
		*/
		
		$url = ROOT.CURRENT_FILE.'?';
		
		?>
		<div id = "adminPage">
			<div id = "barreLaterale">
				<div id = "barreLateraleTitre">
					<?php echo LANG_ADMIN_MENU_TITLE; ?>
				</div>
				<ul id = "barreLateraleMenu">
					<?php
						foreach ($listePage AS $pageFile => $pageName)
						{
							if ($pageName != '')
							{
								?>
									<a  class = "<?php if ($currentPage == $pageFile) { echo 'barreLateraleSelected'; } ?>" href =  "<?php echo $url.'page='.$pageFile; ?>"><li><?php echo $pageName; ?></li></a>
								<?php
							}
						}
					?>
				</ul>
				<div class = "mobileAdminMenuButtonClose"><i class="fa fa-caret-up"></i></div>
			</div>

			<!-- Bouton permettant d'afficher le menu sur les portables -->
			<div class = "mobileAdminMenuButton"><i class="fa fa-caret-down"></i></div>

			<div id = "corps">
				<?php
					if (is_file($currentPage.'.php'))
					{
						require($currentPage.'.php');
					}
				?>
			</div>
		</div>
		<?php
	
	require '../../core/footer.php';
?>