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
		Page d'accueil des évaluations - Affiche la liste des évaluations
	*/
	require '../../core/main.php';
	require '../../core/header.php';
	
	/*
		1. Récupération des données
	*/
	
		// On récupère la liste des évaluations
		$evals = getEvalList($_SESSION['id']);
		$evalTypes = getEvaluationTypeList();
		
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
	
	/*
		2. Affichage des données
	*/
	
	?>
	<h1><?php echo LANG_EVAL_LIST; ?></h1> <!-- Titre -->
	
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
	
	<?php
	
	?>
		
		<table>
			<tr class = "headTR">
				<td><?php echo LANG_ADMIN_LISTE_TABLE_TITLE_NOM; ?></td>
				<td><?php echo LANG_ADMIN_EVALUATIONS_LISTE_TABLE_TITLE_TYPE; ?></td>
				<td><?php echo LANG_ADMIN_EVALUATIONS_LISTE_TABLE_TITLE_PERIODE; ?></td>
				<td></td>
			</tr>
		<?php
		$n = 0;

		if ($evals = getEvalList($_SESSION['id']))
		{
		foreach ($evals AS $eval)
		{
			if (($filter == 'now' && time() >= $eval['date']['debut'] && time() <= $eval['date']['fin']) || ($filter == 'next' && time() < $eval['date']['debut']) || ($filter == 'old' && time() >= $eval['date']['fin']))
			{
			if (isset($evalTypes[$eval['type']['id']]['actif']) && $evalTypes[$eval['type']['id']]['actif'] == 1)
			{
			?>
				<tr class = "bodyTR <?php if (isset($eval['remplissage']['valeur']) && $eval['remplissage']['valeur'] == true) { echo 'evaluationDone'; } ?>">
					<td><?php echo $eval['nom']; ?></td>
					<td><?php echo $eval['type']['nom']; ?></td>
					<td><?php echo date('d/m/Y',$eval['date']['debut']).' - '.date('d/m/Y',$eval['date']['fin']); ?></td>
					<td>
						<?php
						if (isset($eval['remplissage']['valeur']) && $eval['remplissage']['valeur'] == true)
						{
							?>
								<i style = "color: green;" class="fa fa-check-square-o"></i>
							<?php
						}
						else if ($filter == 'now')
						{
						?>
							<a  href = "<?php echo ROOT.'content/evaluation/view.php?id='.$eval['registerId']; ?>"><i class="fa fa-external-link"></i></a>
						<?php
						}
						?>
					</td>
				</tr>
			<?php
				
				$n++;
			}
			}
		}
		}
		
		if ($n == 0) // Si il n'y a aucune évaluation pour ce filtre
		{
			?>
			<tr class = "bodyTR"><td colspan = "4"><?php echo LANG_EVAL_LIST_NOEVAL; ?></td></tr>
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