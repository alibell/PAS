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
		25/04/15 - promotions.php - Ali Bellamine
		Affiche la liste des promotions
	*/

/**
	Routage selon la variable action
**/

	$allowedAction = array('list', 'dl', 'view', 'edit', 'delete', 'add');
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
		
	$pageUtilisateurs = getPageUrl('adminUtilisateurs');

	if ($action == 'list' || $action == 'dl') {

		if (isset($_GET['order'])) { $order = $_GET['order']; } else { $order = 'nom'; }
		if (isset($_GET['desc'])) { $desc = true; } else { $desc = false; }
		
		$listePromotions  = getPromotionList($order, $desc);
	}
	else if ($action == 'view' || $action == 'delete' || $action == 'edit')
	{
		// On récupère les données sur la promotion
		if (count(checkPromotion($_GET['id'], array())) == 0)
		{
                        // On nettoie la promotion si l'option a été sélectionnée
                        if (isset($_GET['empty'])) {
                            $sql = 'UPDATE user SET promotion = NULL WHERE promotion = ?';
                            $res = $db -> prepare($sql);
                            $res -> execute(array($_GET['id']));
                        }
                
			$promotionData = getPromotionData($_GET['id']);
		}
		else
		{
			header('Location: '.ROOT.CURRENT_FILE.'?page='.$_GET['page']);
		}
	}

	// On retourne la liste au format CSV si $action == 'dl'
	if ($action == 'dl')
	{
		/*
			Création de l'array à retourner
		*/
		
		$promotionsCSV = array(array('id', LANG_ADMIN_PROMOTION_NOM_TITRE));
		
		foreach ($listePromotions AS $promotion)
		{
			$promotionsCSV[] = array($promotion['id'], $promotion['nom']);
		}
		
		// Téléchargement du CSV
		downloadCSV($promotionsCSV, 'promotions.csv');
	}
		
	/**
		2. Traitement des formulaires
	**/
	
	if (isset($_POST) && count($_POST))
	{
		/*
			Préparation des données : on crée un array contenant toutes les données, ce dernier sera ensuite parcouru pour créer la requête SQL qui sera préparée
		*/
		
			if ($action == 'add')
			{
				if (isset($_POST['nom']) && $_POST['nom'] != '')
				{
					$sql = 'INSERT INTO promotion (nom) VALUE (?)';
				}
			}
			
			if ($action == 'edit' || $action == 'delete')
			{
				$sqlData['id'] = $promotionData['id']; // Id de l'utilisateur				
			}
			
			if ($action == 'edit')
			{
				foreach ($_POST AS $key => $value)
				{
					if ($key == 'nom')
					{
						if ($value != '' && $value != $promotionData[$key])
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
			Pour les éditions
		**/
		
		if ($action == 'edit')
		{
			if (isset($sqlData) && count($sqlData) > 1)
			{
				$sql = 'UPDATE promotion SET ';
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
				
				$res = $db -> prepare($sql);
				if ($res -> execute($sqlData))
				{
					$sqlAction = TRUE;
				}
			}
		}
		
		/**
			Pour les suppressions
		**/
		else if ($action == 'delete')
		{
			if (isset($sqlData))
			{
				$sql = 'DELETE FROM promotion WHERE id = :id';	
				$res = $db -> prepare($sql);
				if ($res -> execute($sqlData))
				{
					$sqlAction = TRUE;
				}
			}
		}
		
		/**
			Pour les ajout de promotion
		**/
		else if ($action == 'add')
		{
			$res = $db -> prepare($sql);
			if($res -> execute(array(htmLawed($_POST['nom']))))
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
			Liste des promotions
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
						<td><a href = "<?php echo $url.'&order=promotion'; if (isset($_GET['order']) && $_GET['order'] == 'promotion' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PROMOTION; ?></a></td>
						<td><a href = "<?php echo $url.'&order=nb'; if (isset($_GET['order']) && $_GET['order'] == 'nb' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PROMOTIONNB; ?></a></td>
						<td><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_ADMIN; ?></td>
					</tr>
					<?php
						foreach($listePromotions AS $promotion)
						{
							?>
							<tr class = "bodyTR">
								<td><?php echo $promotion['nom']; ?></td>
								<td><?php echo $promotion['nb']; ?></td>
								<td>
									<a href = "<?php echo ROOT.CURRENT_FILE.'?page=promotions&action=view&id='.$promotion['id']; ?>"><i class="fa fa-info"></i></a>
									<a href = "<?php echo ROOT.CURRENT_FILE.'?page=promotions&action=edit&id='.$promotion['id']; ?>"><i class="fa fa-pencil"></i></a>
									<a href = "<?php echo ROOT.CURRENT_FILE.'?page=promotions&action=delete&id='.$promotion['id']; ?>"><i class="fa fa-trash-o"></i></a>
								</td>
							</tr>
							<?php
						}
					?>
				</table>
			</div>
			
			<br />
			<a class = "bouton" href = "<?php echo ROOT.CURRENT_FILE.'?page=promotions&action=add&id='.$promotion['id']; ?>" title = "<?php echo LANG_ADMIN_PROMOTION_ADD_PROMOTION; ?>"><i class="fa fa-plus-circle"></i></a>
<?php
	}
	else if ($action == 'view')
	{
		?>
			<h1><?php echo $promotionData['nom']; ?></h1>
		
			<h2><?php echo LANG_ADMIN_PROMOTION_LIST_ETUDIANTS; ?> (<?php echo $promotionData['nb']; ?>) :</h2>
                        
                        <!-- Bouton permettant de nettoyer la promotion -->
                        <a href = "<?php echo makeURIWithGetParameter(array('empty' => 'true')); ?>"><button id = "cleanPromotion"><?php echo LANG_ADMIN_PROMOTION_CLEAN; ?></button></a>
		
			<div id = "donnees">
				<table  style = "margin-top: 10px;">
					<tr class = "headTR">
						<td><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NOM; ?></td>
						<td><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PRENOM; ?></td>
						<td><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NBETUDIANT; ?></td>
						<td><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_RANG; ?></td>
						<td><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_ADMIN; ?></td>
					</tr>
					<?php
					if (isset($promotionData['users']))
					{
						foreach($promotionData['users'] AS $user)
						{
							?>
							<tr class = "bodyTR">
								<td><?php echo $user['nom']; ?></td>
								<td><?php echo $user['prenom']; ?></td>
								<td><?php echo $user['nbEtudiant']; ?></td>
								<td><?php echo constant('LANG_RANG_VALUE_'.$user['rang']); ?></td>
								<td>
									<a href = "<?php echo $pageUtilisateurs.'page=profil&action=view&id='.$user['id']; ?>"><i class="fa fa-user"></i></a>
									<a href = "<?php echo $pageUtilisateurs.'page=profil&action=edit&id='.$user['id']; ?>"><i class="fa fa-pencil"></i></a>
									<a href = "<?php echo $pageUtilisateurs.'page=profil&action=delete&id='.$user['id']; ?>"><i class="fa fa-trash-o"></i></a>
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
			<h1><?php echo $promotionData['nom']; ?></h1>
		<?php
			}
		?>
			<form class = "formEvaluation" method = "POST">
				<label for = "nom"><?php echo LANG_ADMIN_PROMOTION_NOM_TITRE; ?></label>
				<input type = "text" class = "<?php if ($action != 'edit' && $action != 'add') echo 'readonlyForm'; ?>" name = "nom" id = "nom" value = "<?php if (isset($_POST['nom'])) { echo $_POST['nom']; } else if (isset($promotionData)) { echo $promotionData['nom']; } ?>" <?php if ($action != 'edit' && $action != 'add') echo 'readonly'; ?> />
				<input type = "submit" id = "submit_<?php echo $action; ?>" value = "<?php echo constant('LANG_ADMIN_PROMOTION_FORM_SUBMIT_'.strtoupper($action)); ?>" />
			</form>
		<?php
	}
?>

<script>
	$('#submit_delete').on('click', function(e){
            if (!confirm("<?php echo LANG_ADMIN_PROMOTION_FORM_SUBMIT_DELETE_CONFIRM; ?>"))
            {
		e.preventDefault();
            }
	});
        
        $('#cleanPromotion').on('click', function(e)) {
            if (!confirm("<?php echo LANG_ADMIN_PROMOTION_FORM_SUBMIT_CLEAN_CONFIRM; ?>"))
            {
		e.preventDefault();
            }
        }
</script>