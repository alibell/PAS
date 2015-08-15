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
		25/02/15 - fnCore.php - Ali Bellamine
		Fonctions propres aux affichages graphiques
	*/

	/**
	  * displayProgressionBar - Affiche une barre de progression
	  *
	  * @category : displayFunction
	  *
	  * @param int $width Largueur de la barre de progression (200px par défaut)
	  * @param int $height Hauteur de la barre de progression (300px par défaut)
	  * @param int $value Position dans la barre de progression (entre 0 et 1)
	  * @Author Ali Bellamine
	  */

function displayProgressionBar($width, $height, $value)
{
	$cssValue = ceil($value*100);
	?>
		<div style = " width: <?php echo $width; ?>; height: <?php echo $height; ?>; border: solid 2px rgb(18, 18, 45); border-radius: 5px; margin: 10px; display: inline-block;">
			<div style = "height: 100%; width: <?php echo $cssValue; ?>%; background-color: rgb(11, 90, 168);">
			</div>
		</div>
	<?php
}
?>