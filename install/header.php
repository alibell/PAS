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
	Script d'installation du CMS - header
*/
?>

<!DOCTYPE html>

<html lang="fr">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
    <title>
	<?php 
		echo LANG_INSTALL_TITLE;  // Nom du formulaire d'installation
	?>
	</title>
    
	<!-- CSS -->

	<link rel="stylesheet" href="../theme/main.css">
	<link rel="stylesheet" href="install.css">
</head>

<body>

	<!-- Header -->
	<div id = "header">
		<?php echo LANG_INSTALL_TITLE; ?>
	</div>
		

	<!-- Corps de la page -->
	<div id = "body">
		<div class = "body">
		
			<div class = "leftPanel">
				<ul>
				<?php
					$class = 'done';
					
					foreach ($steps AS $step)
					{
						if ($step == $page) { $class = 'notdone'; }
						
						if ($class != 'notdone' || $step == $page)
						{
							?>
							<a href = '?goTo=<?php echo $step; ?>'>
							<?php
						}
					?>
							<li class = "<?php if ($step != $page) { echo $class; } ?>"><?php echo constant('LANG_INSTALL_'.strtoupper($step).'_TITLE');; ?></li>
					<?php
						if ($class != 'notdone' || $step == $page)
						{
							?>
							</a>
							<?php
						}
					}
				?>
				</ul>
			</div>
			
			<div class = "rightBox">
				<!-- Affichage des erreur -->
				<?php
				if (isset($erreur) && count($erreur) > 0)
				{
					?>
					<ul class = "erreur">
						<?php
						foreach ($erreur AS $codeErreur => $valeurErreur)
						{
						?>
							<li><?php echo constant($codeErreur); ?></li>
						<?php
						}
						?>
					</ul>
					<?php
				}
				?>