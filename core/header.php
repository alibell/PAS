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
?>

<!DOCTYPE html>

<html lang="fr">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
    <title>
	<?php 
		echo TITRE;  // Nom de la plateforme
		if (isset($currentPageData['name']) && $currentPageData['name'] != '') // Titre de la page
		{
			echo ' - '.$currentPageData['name'];
		}
	?>
	</title>
    
	<!-- CSS -->

	<!-- Chargement du CSS spécifique au plugin si un plugin est chargé -->
	<?php
		if (CURRENT_FILE == 'content/evaluation/viewResult.php' || CURRENT_FILE == 'content/evaluation/view.php')
		{
			if (CURRENT_FILE == 'content/evaluation/viewResult.php' && isset($_GET['evaluationType']))
			{
				// On récupère l'info sur le module si il est chargé
				$evaluationType = getEvalTypeData ($_GET['evaluationType']);
			}
			else if (CURRENT_FILE == 'content/evaluation/view.php' && isset($_GET['id']))
			{
				// On récupère l'info sur le module si il est chargé
				$evaluationType = getEvalTypeData ($_GET['id']);
			}
			if (isset($evaluationType['dossier']) && is_file($_SERVER['DOCUMENT_ROOT'].'/'.LOCAL_PATH.'evaluations/'.$evaluationType['dossier'].'/css/main.css'))
			{
				?>
				<link rel="stylesheet" href="<?php echo ROOT.'evaluations/'.$evaluationType['dossier'].'/css/main.css'; ?>"> <!-- CSS spécifique du plugin -->
				<?php
			}
		}
	?>
	<link rel="stylesheet" href="<?php echo ROOT.'theme/font-awesome-4.3.0/css/font-awesome.min.css'; ?>"> <!-- Font-Awesome, licenses : SIL OFL 1.1, MIT and CC BY 3.0 -->
	<link rel="stylesheet" href="<?php echo ROOT.'JS/glDatePicker-2.0/styles/glDatePicker.default.css'; ?>"> <!-- Feuille CSS de glDatePicker -->
	<link rel="stylesheet" href="<?php echo ROOT.'JS/glDatePicker-2.0/styles/glDatePicker.flatwhite.css'; ?>"> <!-- Feuille CSS de glDatePicker -->
	<link rel="stylesheet" href="<?php echo ROOT.'theme/daterangepicker.css'; ?>"> <!-- Feuille CSS de daterangepicker -->
	<link rel="stylesheet" href="<?php echo ROOT.'JS/chosen-1.4.2/chosen.min.css'; ?>"> <!-- Feuille CSS de chosen -->
	<link rel="stylesheet" href="<?php echo ROOT.'theme/featherlight.min.css'; ?>"> <!-- Feuille CSS de featherlight -->

	<link rel="stylesheet" href="<?php echo ROOT.'theme/main.css'; ?>"> <!-- CSS commun à toutes les pages -->
	<link rel="stylesheet" href="<?php echo ROOT.'theme/mobile.css'; ?>"> <!-- CSS Responsive Design commun à toutes les pages -->
	<!-- Chargement du CSS sécifique à la page -->
	<?php if(isset($currentPageData['css'] ) && $currentPageData['css']  != '')
	{
		?>
		<link rel="stylesheet" href="<?php echo ROOT.'theme/'.$currentPageData['css']; ?>"> <!-- CSS spécifique de la page -->	
		<?php
	}
	?>

	<!-- Favicon -->
	<link href="Theme/img/favicon.ico" rel="shortcut icon" type="image/x-icon" />

	<!-- Javascript -->	
	<script type="text/javascript" src="<?php echo ROOT.'JS/jQuery/jquery-2.1.3.min.js'; ?>"></script> <!-- jQuery, license MIT -->
	<script type="text/javascript" src="<?php echo ROOT.'JS/glDatePicker-2.0/glDatePicker.min.js'; ?>"></script> <!-- glDatePicker, license MIT -->
	<script type="text/javascript" src="<?php echo ROOT.'JS/moment.min.js'; ?>"></script> <!-- moment, gestion des dates en JS, license MIT -->
	<script type="text/javascript" src="<?php echo ROOT.'JS/jquery.daterangepicker.js'; ?>"></script> <!-- daterangepicker, license MIT -->
	<script type="text/javascript" src="<?php echo ROOT.'JS/charts/Chart.min.js'; ?>"></script> <!-- chart.js, license MIT -->
	<script type="text/javascript" src="<?php echo ROOT.'JS/chosen-1.4.2/chosen.jquery.min.js'; ?>"></script> <!-- chosen.js, license MIT -->
	<script type="text/javascript" src="<?php echo ROOT.'JS/featherlight.min.js'; ?>"></script> <!-- featherlight.min.js, license MIT -->
	<script type="text/javascript" src="<?php echo ROOT.'JS/tinymce/tinymce.min.js'; ?>"></script> <!-- tinymce 4.2.1, license LGPL -->
</head>

<body>

	<!-- Header -->
	<div id = "header">
		
		<?php
		// Pannel utilisateur
		if ($_SESSION['rang'] > 0)
		{
			?>
			<div id = "userPanel">
				<div>
				<!-- Nom de l'utilisateur -->
				<span id = "userName">
				<?php
					if (isset($_SESSION['loginAS']['oldUser']))
					{
						?>
						<span style = "color: #E47373;">
						<a href = "<?php echo getPageUrl('index', array('unloginAs' => 1)); ?>"><i class="fa fa-undo"></i></a>
						<?php
						echo substr($_SESSION['loginAS']['oldUser']['prenom'],0,1).'. '.$_SESSION['loginAS']['oldUser']['nom']. ' > ';
						?>
						</span>
						<?php
					}
					echo substr($_SESSION['prenom'],0,1).'. '.$_SESSION['nom'];
				?>
				</span>
				
				<!-- Bouton de deconnexion -->
				<?php
					// Création de l'URL de logout : on récupère l'URL en cours, on ajoute les $_GET existant et on finit pas rajouter logout en bout
					$logoutURL = ROOT.CURRENT_FILE.'?';
					if (isset($_GET))
					{
						foreach ($_GET AS $key => $value)
						{
							if ($key != 'logout' && is_string($value))
							{
								$logoutURL .= $key.'='.$value.'&';
							}
						}
					}
					$logoutURL .= 'logout';
				?>
				<a href = "<?php echo $logoutURL; ?>" title = "<?php echo LANG_DISCONNECT; ?>"><i class="fa fa-sign-out"></i></a>
				</div>
			</div>
			<?php
		}
		?>
		
		<div id = "menuButton">
			<i class="fa fa-bars"></i>
		</div>
		
		<div id = "headerLogo">
		</div>
		<div id  = "headerTitle">
			<?php echo TITRE; ?>
		</div>
		
		<!-- Menu -->
		<?php
			// Récupération du menu
			$menu = getMenu();
		?>
		<div id = "headerMenu">
			<ul class = "mainMenu">
				<?php
				// Création du menu
				foreach ($menu AS $item)
				{
					$noSubMenu = FALSE;
					// Si il n'y a qu'un seul sous menu, on affiche directement le bouton au lieu
					if (count($item['children']) == 1)
					{
						$noSubMenu = TRUE;
						$children = array_values($item['children'])[0];
					}
					?>
					<li>
						<?php
						if ($noSubMenu)
						{
						?>
							<a href = "<?php echo ROOT.$children['file']; ?>">
						<?php
						}
						?>
						<span class = "primaryMenuText">
							<?php echo $item['name']; ?>
						</span>
						<?php
						if ($noSubMenu)
						{
						?>
						</a>
						<?php
						}
						else
						{
						?>
							<ul class = "secondaryMenu">
								<?php
									foreach($item['children'] AS $page)
									{
										?>
										<li><a href = "<?php echo ROOT.$page['file']; ?>"><?php echo $page['name'];?></a></li>
										<?php
									}
								?>
							</ul>
						<?php
						}
						?>	
					</li>
					<?php
				}
				?>
			</ul>
		</div>
	</div>
		

	<!-- Corps de la page -->
	<div id = "body">
		<div class = "body">
	
			<!-- Affichage des messages et des erreurs transmis par l'url -->
			<?php 
				if (isset($_GET['erreur']) && $_GET['erreur'] != '')
				{
					$erreurArray = unserialize($_GET['erreur']);
					displayErreur($erreurArray);
				}
				if (isset($_GET['msg']) && $_GET['msg'] != '')
				{
					?>
					<ul class = "msg">
						<li><?php echo constant($_GET['msg']); ?>
					</ul>
					<?php
				}
			?>