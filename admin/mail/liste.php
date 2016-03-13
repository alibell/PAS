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
		31/01/16 - liste.php - Ali Bellamine
		Affiche la liste des campagnes d'email
	*/

	/**
		1. Récupération de la liste des campagnes d'email
	**/
	
	$listeCampagne = getMailCampagnList();
	/**
		2. Affichage de la liste des campagnes d'email
	**/
	
	?>
	<table>
		<tr class = "headTR">
			<th>Nom de la campagne d'envoi d'email</th>
			<th>Date</th>
			<th>Etat</th>
			<th></th>
		</tr>
		
		
		<?php
		foreach ($listeCampagne AS $campagne) {
		?>
			<tr class = "bodyTR">
				<td><strong><?php echo $campagne['nom']; ?></strong></td>
				<td style = "text-align: center;"><?php echo date('d/m/Y', $campagne['date']); ?></td>
				<td style = "text-align: center;"><?php if ($campagne['statut'] == 1) { echo 'Terminé'; } else { echo 'En cours'; } ?><br />(<span style = "color: green;"><?php echo $campagne['nb'][1]; ?></span> / <?php echo ($campagne['nb'][0] + $campagne['nb'][1]); ?>)</td>
				<td style = "text-align: center;"><a href = "<?php echo getPageUrl('mail', array('page' => 'campagne', 'id' => $campagne['codeCampagne'])); ?>"><i class="fa fa-envelope"></i></a></td>
			</tr>
		<?php
		}
		?>
	</table>