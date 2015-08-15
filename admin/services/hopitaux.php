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
		13/05/15 - hopitaux.php - Ali Bellamine
		Affiche la liste des hopitaux
	*/

/**
	Routage selon la variable action
**/

	$allowedAction = array('list', 'view', 'edit', 'delete', 'add');
	if (isset($_GET['action']) && in_array($_GET['action'], $allowedAction))
	{
		$action = $_GET['action'];
	}
	else
	{
		$action = 'list';
	}

	/**
		1. Récupération des données
	**/
	
if ($action == 'list') {
	if (isset($_GET['order'])) { $order = $_GET['order']; } else { $order = 'nom'; }
	if (isset($_GET['desc'])) { $desc = true; } else { $desc = false; }
	
	$listeHopitaux  = getHospitalList($order, $desc);
}
else if ($action == 'view' || $action == 'delete' || $action == 'edit')
{
	// On récupère les données sur l'hopital
	if (count(checkHopital($_GET['id'], array())) == 0)
	{
		$hopitalData = getHospitalInfo($_GET['id']);
	}
	else
	{
		header('Location: '.ROOT.CURRENT_FILE.'?page='.$_GET['page']);
	}
}
		
	/**
		2. Traitement des formulaires
	**/
	
	if (isset($_POST) && count($_POST))
	{
		/*
			Préparation des données : on crée un array contenant toutes les données, ce dernier sera ensuite parcouru pour créer la requête SQL qui sera préparée
		*/
		
			if ($action == 'edit' || $action == 'delete')
			{
				$sqlData['id'] = $hopitalData['id']; // Id de l'utilisateur				
			}
			
			if ($action == 'edit' || $action == 'add')
			{
				foreach ($_POST AS $key => $value)
				{
					if ($key == 'nom' || $key == 'alias')
					{
						if ($value != '' && ((isset ($hopitalData) && $value != $hopitalData[$key]) || !isset($hopitalData)))
						{
							$sqlData[$key] = htmLawed($value);
						}
					}
				}
			}
			
		/**
			On lance les enregistrement dans la BDD
		**/
		
		$sqlInsert = FALSE; // Enregistre la bonne réussite des requêtes
		
		/**
			Pour les ajouts
		**/
		
		if ($action == 'add')
		{
			if (isset($sqlData) && count($sqlData) > 0 && isset($sqlData['nom']) && isset($sqlData['alias']))
			{
				$sql = 'INSERT INTO hopital (';
				$comma = FALSE; // Permet de ne pas mettre la virgule au premier tour de boucle
				foreach ($sqlData AS $key => $value)
				{				
					if ($key != 'id')
					{
						if ($comma)	{ $sql .= ', '; } else { $comma = TRUE; }
						
						$sql .= $key;
					}
				}
				$sql .= ') VALUES (';
				
				$comma = FALSE; // Permet de ne pas mettre la virgule au premier tour de boucle
				foreach ($sqlData AS $key => $value)
				{				
					if ($key != 'id')
					{
						if ($comma)	{ $sql .= ', '; } else { $comma = TRUE; }
						
						$sql .= ':'.$key;
					}
				}
				$sql .= ')';
			}
		}
		
		/**
			Pour les éditions
		**/
		
		else if ($action == 'edit')
		{
			if (isset($sqlData) && count($sqlData) > 1)
			{
				$sql = 'UPDATE hopital SET ';
				$comma = FALSE; // Permet de ne pas mettre la virgule au premier tour de boucle
				foreach ($sqlData AS $key => $value)
				{				
					if ($key != 'id')
					{
						if ($comma)	{ $sql .= ', '; } else { $comma = TRUE; }
						
						$sql .= $key.' = :'.$key.' ';
					}
				}
				$sql .= ' WHERE id = :id';
			}
		}
		
		/**
			Pour les suppressions
		**/
		else if ($action == 'delete')
		{
			if (isset($sqlData))
			{
				$sql = 'DELETE FROM hopital WHERE id = :id';	
			}
		}
		
		/*
			On execute la requête
		*/
		if (isset($sql) && $sql != '')
		{
			$res = $db -> prepare($sql);
			if ($res -> execute($sqlData))
			{
				$sqlAction = TRUE;
			}
		}
		
		/*
			Si insert correctement réalisé -> on redirige vers le list
		*/
		if (isset($sqlAction) && $sqlAction) {
			$tempGET = $_GET;
			unset($tempGET['action']);
			unset($tempGET['id']);
			header('Location: '.ROOT.CURRENT_FILE.'?'.http_build_query($tempGET));
		}
	}
	
	/**
		3. Affichage
	**/
	
	if ($action == 'list') {
		/*
			Liste des hopitaux
		*/
		
	?>
			<div id = "donnees">
			
			<?php
			/*
				Données
			*/
			
			// Création des liens
			$tempGET = $_GET;
			unset($tempGET['order']);
			unset($tempGET['desc']);
			$url = ROOT.CURRENT_FILE.'?'.http_build_query($tempGET);
			?>			
				<table  style = "margin-top: 10px;">
					<tr class = "headTR">
						<td><a href = "<?php echo $url.'&order=nom'; if (isset($_GET['order']) && $_GET['order'] == 'nom' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_HOPITAUX_LISTE_TABLE_TITLE_NOM; ?></a></td>
						<td><a href = "<?php echo $url.'&order=alias'; if (isset($_GET['order']) && $_GET['order'] == 'alias' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_HOPITAUX_LISTE_TABLE_TITLE_ALIAS; ?></a></td>
						<td><a href = "<?php echo $url.'&order=nb'; if (isset($_GET['order']) && $_GET['order'] == 'nb' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_HOPITAUX_LISTE_TABLE_TITLE_SERVICESNB; ?></a></td>
						<td><?php echo LANG_ADMIN_TABLE_TITLE_ADMIN; ?></td>
					</tr>
					<?php
						foreach($listeHopitaux AS $hopital)
						{
							?>
							<tr class = "bodyTR">
								<td><?php echo $hopital['nom']; ?></td>
								<td><?php echo $hopital['alias']; ?></td>
								<td><?php echo $hopital['nb']; ?></td>
								<td>
									<a href = "<?php echo ROOT.CURRENT_FILE.'?page=hopitaux&action=view&id='.$hopital['id']; ?>"><i class="fa fa-info"></i></a>
									<a href = "<?php echo ROOT.CURRENT_FILE.'?page=hopitaux&action=edit&id='.$hopital['id']; ?>"><i class="fa fa-pencil"></i></a>
									<a href = "<?php echo ROOT.CURRENT_FILE.'?page=hopitaux&action=delete&id='.$hopital['id']; ?>"><i class="fa fa-trash-o"></i></a>
								</td>
							</tr>
							<?php
						}
					?>
				</table>
			</div>
			
			<br />
			<a class = "bouton" href = "<?php echo ROOT.CURRENT_FILE.'?page=hopitaux&action=add&id='.$hopital['id']; ?>" title = "<?php echo LANG_ADMIN_HOPITAUX_ADD_HOPITAL; ?>"><i class="fa fa-plus-circle"></i></a>
<?php
	}
	else if ($action == 'view')
	{
		?>
			<h1><?php echo $hopitalData['nom']; ?></h1>
		
			<h2><?php echo LANG_ADMIN_HOPITAL_LISTE_SERVICES; ?> (<?php echo $hopitalData['nb']; ?>) :</h2>
		
			<div id = "donnees">
				<table  style = "margin-top: 10px;">
					<tr class = "headTR">
						<td><?php echo LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_NOM; ?></td>
						<td><?php echo LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_SPECIALITE; ?></td>
						<td><?php echo LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_CHEF; ?></td>
						<td><?php echo LANG_ADMIN_TABLE_TITLE_ADMIN; ?></td>
					</tr>
					<?php
					if (isset($hopitalData['services']))
					{
						foreach($hopitalData['services'] AS $serviceId => $serviceData)
						{
							$service = getServiceInfo($serviceId);
							?>
							<tr class = "bodyTR">
								<td><?php echo $service['nom']; ?></td>
								<td><?php echo $service['specialite']['nom']; ?></td>
								<td><?php echo $service['chef']['prenom'].' '.$service['chef']['nom']; ?></td>
								<td>
									<a href = "<?php echo ROOT.'admin/services/index.php?page=service&action=view&id='.$service['id']; ?>"><i class="fa fa-info"></i></a>
									<a href = "<?php echo ROOT.'admin/services/index.php?page=service&action=edit&id='.$service['id']; ?>"><i class="fa fa-pencil"></i></a>
									<a href = "<?php echo ROOT.'admin/services/index.php?page=service&action=delete&id='.$service['id']; ?>"><i class="fa fa-trash-o"></i></a>
								</td>
							</tr>
							<?php
						}
					}
					?>
				</table>
			</div>
		<?php
	}
	else if ($action == 'edit' || $action == 'add' || $action == 'delete')
	{
			if ($action == 'edit')
			{
		?>
			<h1><?php echo $hopitalData['nom']; ?></h1>
		<?php
			}
		?>
			<form class = "formEvaluation" method = "POST">
				
				<!-- Nom de l'hopital -->
				<label for = "nom"><?php echo LANG_ADMIN_HOPITAUX_LISTE_TABLE_TITLE_NOM; ?></label>
				<input type = "text" class = "<?php if ($action != 'edit' && $action != 'add') echo 'readonlyForm'; ?>" name = "nom" id = "nom" value = "<?php if (isset($_POST['nom'])) { echo $_POST['nom']; } else if (isset($hopitalData)) { echo $hopitalData['nom']; } ?>" <?php if ($action != 'edit' && $action != 'add') echo 'readonly'; ?> />

				<br />
				<!-- Alias de l'hopital : nom court utilisé pour simplifier l'afficher -->
				<label for = "alias"><?php echo LANG_ADMIN_HOPITAUX_LISTE_TABLE_TITLE_ALIAS; ?></label>
				<input type = "text" class = "<?php if ($action != 'edit' && $action != 'add') echo 'readonlyForm'; ?>" name = "alias" id = "alias" value = "<?php if (isset($_POST['alias'])) { echo $_POST['alias']; } else if (isset($hopitalData)) { echo $hopitalData['alias']; } ?>" <?php if ($action != 'edit' && $action != 'add') echo 'readonly'; ?> />

				<input type = "submit" id = "submit_<?php echo $action; ?>" value = "<?php echo constant('LANG_ADMIN_HOPITAL_FORM_SUBMIT_'.strtoupper($action)); ?>" />
			</form>
		<?php
	}
?>

<script>
	$('#submit_delete').on('click', function(e){
			if (!confirm("<?php echo LANG_ADMIN_HOPITAL_FORM_SUBMIT_DELETE_CONFIRM; ?>"))
			{
				e.preventDefault();
			}
	});
</script>