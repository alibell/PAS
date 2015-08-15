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
		15/07/15 - etudiant.php - Ali Bellamine
		Page affichant la liste des étudiants dans le service
	*/
	require '../../core/main.php';
	require '../../core/header.php';
	
	/*
		1. Récupération des données
	*/

		// On récupère le filtre
		$defaultFilter = 'now';
		$allowedFilter = array('next', 'now', 'old');
		
		if (isset($_GET['selectTypeEvaluation']) && in_array($_GET['selectTypeEvaluation'], $allowedFilter))
		{
			$filter = $_GET['selectTypeEvaluation'];
		}
		else
		{
			$filter = $defaultFilter;
		}
		
		// On récupère l'id du service dont l'utilisateur est chef
		$listeService = array();
		
		$sql = 'SELECT id FROM service WHERE chef = ?';
		$res = $db -> prepare($sql);
		$res -> execute(array($_SESSION['id']));
		
		while ($res_f = $res -> fetch())
		{
			$listeService[$res_f['id']] = TRUE;
		}
		
		// On récupère les données concernant le service
		$serviceData = array();

			// Requête à effectuer
			if ($filter == 'now')
			{
				$sql = 'SELECT userId idEtudiant, ae.id affectationId FROM affectationexterne ae 
							INNER JOIN user u ON u.id = ae.userId 
							WHERE ae.service = :service AND ae.dateDebut <= :now AND ae.dateFin >= :now
							ORDER BY u.promotion ASC, nom ASC, prenom ASC';
			}
			else if ($filter == 'old')
			{
				$sql = 'SELECT userId idEtudiant, ae.id affectationId FROM affectationexterne ae 
							INNER JOIN user u ON u.id = ae.userId 
							WHERE ae.service = :service AND ae.dateFin < :now
							ORDER BY u.promotion ASC, nom ASC, prenom ASC';
			}
			else if ($filter == 'next')
			{
				$sql = 'SELECT userId idEtudiant, ae.id affectationId FROM affectationexterne ae 
							INNER JOIN user u ON u.id = ae.userId 
							WHERE ae.service = :service AND ae.dateDebut > :now
							ORDER BY u.promotion ASC, nom ASC, prenom ASC';
			}
			
			// On effectue la requête pour chaque service
			foreach ($listeService AS $serviceId => $service)
			{
				// Infos concernant le service
				$serviceData[$serviceId] = getServiceInfo($serviceId);
				
				// Liste des étudiants dans le service
				$res = $db -> prepare($sql);
				$res -> execute(array('service' => $serviceId, 'now' => TimestampToDatetime(time())));
				while ($res_f = $res -> fetch())
				{
					$serviceData[$serviceId]['etudiants'][$res_f['idEtudiant']] = getUserData($res_f['idEtudiant']);
					$serviceData[$serviceId]['etudiants'][$res_f['idEtudiant']]['affectationId'] = $res_f['affectationId'];
				}
			}
			
	/*
		2. Affichage des données
	*/
	
	?>
	<h1><?php echo LANG_STUDENT_LIST; ?></h1> <!-- Titre -->
	
	<!-- Div permettant de choisir les évaluations -->
	<div id = "selectTypeEvaluation" style = "margin: 10px;">
		<form method = "GET">
			<select name = "selectTypeEvaluation">
				<option value = "next" <?php if ($filter == 'next') { echo 'selected'; } ?>><?php echo LANG_EVAL_LIST_OPTION_NEXT; ?></option>
				<option value = "now" <?php if ($filter == 'now') { echo 'selected'; } ?>><?php echo LANG_EVAL_LIST_OPTION_NOW; ?></option>
				<option value = "old" <?php if ($filter == 'old') { echo 'selected'; } ?>><?php echo LANG_EVAL_LIST_OPTION_OLD; ?></option>
			</select>
		</form>
	</div>
	
	<table>
		<tr class = "headTR">
			<td><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NOM; ?></td>
			<td><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PRENOM; ?></td>
			<td><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_MAIL; ?></td>
			<td><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PROMOTION; ?></td>
			<td><?php echo LANG_ADMIN_AFFECTATIONS_DATE_DEBUT; ?></td>
			<td><?php echo LANG_ADMIN_AFFECTATIONS_DATE_FIN; ?></td>
		</tr>
	<?php
	$n = 0;

	if (isset($serviceData) && count($serviceData) > 0)
	{
	foreach ($serviceData AS $serviceDataId => $serviceDataValue)
	{	
		// On affiche le nom du service
		?>
			<td colspan = "6" style = "font-weight: bold; text-align: center;"><?php echo $serviceDataValue['FullName']; ?></td>
		<?php
		if (isset($serviceDataValue['etudiants']) && count($serviceDataValue['etudiants']) > 0)
		{
		foreach ($serviceDataValue['etudiants'] AS $etudiantId => $etudiantValue)
		{
		?>
			<tr class = "bodyTR">
				<td><?php echo $etudiantValue['nom']; ?></td>
				<td><?php echo $etudiantValue['prenom']; ?></td>
				<td>
				<?php
					$firstLoop = TRUE;
					foreach ($etudiantValue['mail'] AS $email)
					{
						if ($firstLoop) { $firstLoop = FALSE; } else { echo '<br />'; }
						echo $email;
					}
				?>
				</td>
				<td><?php echo $etudiantValue['promotion']['nom']; ?></td>
				<td><?php echo date('d/m/Y',$etudiantValue['service'][$etudiantValue['affectationId']]['dateDebut']); ?></td>
				<td><?php echo date('d/m/Y',$etudiantValue['service'][$etudiantValue['affectationId']]['dateFin']); ?></td>
			</tr>
		<?php

		}
		}
		else
		{
		?>
			<tr class = "bodyTR"><td colspan = "6"><?php echo LANG_EVAL_LIST_NOSTUDENT; ?></td></tr>
		<?php
		}
	}
	}
	else
	{
		?>
			<tr class = "bodyTR"><td colspan = "6"><?php echo LANG_EVAL_LIST_NOSERVICE; ?></td></tr>
		<?php
	}
		
		if ($n == 0) // Si il n'y a aucune évaluation pour ce filtre
		{
			?>
			<?php
		}
		?>
		</ul>
	<?php
	require '../../core/footer.php';
?>

<script>
	$('#selectTypeEvaluation select').on('change', function(){
		$(this).parent().submit();
	});
</script>