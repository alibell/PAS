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
		21/04/15 - profil.php - Ali Bellamine
		Affiche un profil utilisateur et les options d'édition
	*/

	/***
		I. Récupération des informations
	***/
	
	$pageUtilisateurs = getPageUrl('adminUtilisateurs');
	
	/**
		Routage - Choisie la page selon $_GET['action']
	**/
	
	// Liste des action autorisés
	$allowedAction = array('view','edit', 'delete', 'add');
	if (isset($_POST['action']) &&  in_array($_POST['action'], $allowedAction))
	{
		$action = $_GET['action'];
	}
	else if (isset($_GET['action']) && in_array($_GET['action'], $allowedAction))
	{
		$action = $_GET['action'];
	}
	else
	{
		header('Location: '.$pageUtilisateurs);
	}
	
	/**
		Récupération des informations sur l'utilisateur
	**/
	
	// Récupération de l'id de l'utilisateur
	if ($action != 'add')
	{
		if (isset($_GET['id']))
		{
			if (count(checkUser($_GET['id'], array())) == 0) // Verification de sa validité
			{
				$userInfo = getUserData($_GET['id']);
			}
			else
			{
				header('Location: '.$pageUtilisateurs);
			}
		}
		else
		{
			header('Location: '.$pageUtilisateurs);
		}
	}
	
	/***
		II. Traitement des formulaires
	***/
	
	if (isset($_POST) && count($_POST))
	{
		/*
			Préparation des données : on crée un array contenant toutes les données, ce dernier sera ensuite parcouru pour créer la requête SQL qui sera préparée
		*/
		
			if ($action != 'add') 
			{
				$sqlData['id'] = $userInfo['id']; // Id de l'utilisateur
			}
			
			if ($action == 'edit' || $action == 'add')
			{
				foreach ($_POST AS $key => $value)
				{
					if ($key == 'nom' || $key == 'prenom')
					{
						if ($value != '' && (($action == 'edit' && $value != $userInfo[$key]) || $action == 'add'))
						{
							$sqlData[$key] = htmLawed($value);
							if ($key == 'nom')
							{
								$sqlData[$key] = strtoupper($sqlData[$key]);
							}
						}
					}
					else if ($key == 'nbEtudiant' && $action == 'add' && count(checkNbEtudiant($value, array())) > 0)
					{
						$sqlData[$key] = $value;
					}
					else if ($key == 'mail')
					{
						// On sérialize le contenu du champs mail
							$tempMailArray = array();
							
							// Tout d'abord on récupère les email séparés d'un ";"
							$tempMail = explode(';', $value);
							
							// On vérifie chaque adresse email
							foreach ($tempMail AS $email)
							{
								if (filter_var($email, FILTER_VALIDATE_EMAIL) && !in_array($email, $tempMailArray))
								{
									$tempMailArray[] = $email;
								}
							}
						
							if (isset($tempMailArray) && is_array($tempMailArray))
							{
								$sqlData[$key] = serialize($tempMailArray);
							}
							else
							{
								$erreur[19] = true;
							}
					}
					else if ($key == 'rang' && is_numeric($value) && (($action == 'edit' && $value != $userInfo['rang']) || $action == 'add') && $value <= $_SESSION['rang'])
					{
						$sqlData[$key] = $value;
					}
					else if ($key == 'promotion' && is_numeric($value) && $action == 'edit' && $value != $userInfo[$key] && count(checkPromotion($value, array())) == 0)
					{
						$sqlData[$key] = $value;
					}
					else if ($key == 'affectation' && $action == 'edit')
					{					
						$sqlAffectationData = array();
						
						foreach ($_POST['affectation'] AS $affectationId => $affectationValue)
						{
							if (is_numeric($affectationId) && $affectationValue != $userInfo['service'][$affectationId]['id'] && count(checkService($affectationValue, array())) == 0)
							{
								$sqlAffectationData[$affectationId] = array('id' => $affectationId, 'service' => $affectationValue);
							}
						}
					}
				}
			}
			
		/**
			On lance les enregistrement dans la BDD
		**/
		
		$sqlInsert = FALSE; // Enregistre la bonne réussite des requêtes
		
		/**
			Pour les edit
		**/
		
		if ($action == 'edit')
		{
			if (isset($sqlData) && count($sqlData) > 1)
			{
				$sql = 'UPDATE user SET ';
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
					$sqlInsert = TRUE;
				}
			}

			if (isset($sqlAffectationData) && count($sqlAffectationData) > 0)
			{
				foreach ($sqlAffectationData AS $AffectationData)
				{
					$sql = 'UPDATE affectationexterne SET ';
					$comma = FALSE; // Permet de ne pas mettre la virgule au premier tour de boucle
					foreach ($AffectationData AS $key => $value)
					{				
						if ($key != 'id')
						{
							if ($comma)	{ $sql .= ', '; } else { $comma = TRUE; }
							
							$sql .= $key.' = :'.$key.' ';
						}
					}
					$sql .= ' WHERE id = :id';
					
					$res = $db -> prepare($sql);
					if ($res -> execute($AffectationData))
					{
						$sqlInsert = TRUE;
					}
				}
			}
		}
		else if ($action == 'add')
		{
			$neededValues = array('nom', 'prenom', 'mail', 'nbEtudiant', 'rang');
			$addOk = TRUE;
			foreach ($neededValues AS $neededValue)
			{
				if (!isset($sqlData[$neededValue]))
				{
					$addOk = FALSE;
				}
			}

			if ($addOk)
			{
				$comma = FALSE; // Permet de ne pas mettre la virgule au premier tour de boucle
				
				$sql = 'INSERT INTO user ( ';
				foreach ($sqlData AS $key => $value)
				{				
					if ($comma)	{ $sql .= ', '; } else { $comma = TRUE; }
						
					$sql .= $key;
				}
				$sql .= ') VALUES (';
				
				$comma = FALSE; // Permet de ne pas mettre la virgule au premier tour de boucle
				
				foreach ($sqlData AS $key => $value)
				{				
					if ($comma)	{ $sql .= ', '; } else { $comma = TRUE; }
						
					$sql .= ':'.$key;
				}		
				$sql .= ')';
				
				$res = $db -> prepare($sql);
				if ($res -> execute($sqlData))
				{
					$sqlInsert = TRUE;
				} 
			}
			else
			{
				$erreur[25] = TRUE;
			}
		}
		else if ($action == 'delete')
		{
			if (isset($sqlData) && $_SESSION['rang'] >= $userInfo['rang']) // On ne peux supprimer que les profil dont on a un rang supérieur ou égal
			{
				$sql = 'DELETE FROM user WHERE id = :id';	
				$res = $db -> prepare($sql);
				if ($res -> execute($sqlData))
				{
					$sqlInsert = TRUE;
				}
			}
		}
		
		/*
			Si insert correctement réalisé -> on redirige vers le view
		*/
		if ($sqlInsert) {
			$tempGET = $_GET;
			unset($tempGET['action']);
			header('Location: '.$pageUtilisateurs);
		}
	}
	
	/***
		III. Affichage
	***/
	
	if (isset($erreur))
	{
		displayErreur($erreur);
	}
	
	if ((isset($userInfo) || $action == 'add') && isset($action))
	{
		/**
			Barre de navigation
		**/
			
			$tempGET = $_GET;
			unset($tempGET['action']);
			
		?>
			<div class = "barreNavigation">
				<a href = "<?php echo ROOT.CURRENT_FILE.'?page=liste'; ?>"><i class="fa fa-list barreNavigationBouton"></i></a></a>
				<?php if (isset($userInfo)) { ?>
					<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action=view'; ?>"><i class="fa fa-user barreNavigationBouton"></i></a></a>
					<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action=edit'; ?>"><i class="fa fa-pencil barreNavigationBouton"></i></a></a>
					<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action=delete'; ?>"><i class="fa fa-trash-o barreNavigationBouton"></i></a></a>
				<?php } ?>
			</div>
		
			<form class = "formEvaluation" method = "POST">
		<?php
		
		/**
			Informations générales
		**/
			
			// On ecrit sous forme de texte la valeur retourné par getUserData
			if (isset($userInfo['mail']) && is_array($userInfo['mail']))
			{
				$firstLoop = TRUE;
				$userInfoMail = '';
				foreach ($userInfo['mail'] AS $email)
				{
					if ($firstLoop) { $firstLoop = FALSE; } else { $userInfoMail .= ';'; }
					$userInfoMail .= $email;	
				}
			}
			else if (isset($userInfo['mail']))
			{
				$userInfoMail = $userInfo['mail'];
			}
			
				?>
				<fieldset>
					<legend><?php echo LANG_ADMIN_UTILISATEURS_CAT_INFOSGENERALES; ?></legend>
					
					<label for = "nom"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NOM; ?></label>
					<input type = "text"  id = "nom" name = "nom" class = "<?php if ($action != 'edit' && $action != 'add') echo 'readonlyForm'; ?>" value = "<?php if (isset($_POST['nom'])) { echo $_POST['nom']; } else if (isset($userInfo['nom'])) { echo $userInfo['nom']; } ?>" <?php if ($action != 'edit' && $action != 'add') echo 'readonly'; ?> />
					<br />
					<label for = "prenom"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PRENOM; ?></label>
					<input type = "text" id = "prenom" name = "prenom" class = "<?php if ($action != 'edit' && $action != 'add') echo 'readonlyForm'; ?>" value = "<?php  if (isset($_POST['prenom'])) { echo $_POST['prenom']; } else if (isset($userInfo['prenom'])) { echo $userInfo['prenom']; } ?>" <?php if ($action != 'edit' && $action != 'add') echo 'readonly'; ?> />
					<br />
					<label for = "mail"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_MAIL; ?></label>
					
					<?php if ($action == 'edit' || $action == 'add') { ?>
						<input type = "text" id = "mail" name = "mail" class = "<?php if ($action != 'edit' && $action != 'add') echo 'readonlyForm'; ?>" value = "<?php  if (isset($_POST['mail'])) { echo $_POST['mail']; } else if (isset($userInfo['mail'])) { echo $userInfoMail; } ?>" <?php if ($action != 'edit' && $action != 'add') echo 'readonly'; ?> />
					<?php } else {
						?>
						<br />
						<?php
						foreach ($userInfo['mail'] AS $email)
						{
							?>
								<input style = "width: 100%;" type = "text" class = "readonlyForm" value = "<?php  echo $email; ?>" readonly /><br />
							<?php
						}
					} ?>
					<br />

					<label for = "nbEtudiant"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NBETUDIANT; ?></label>
					<input type = "text" id = "nbEtudiant" name = "nbEtudiant" class = "<?php if ($action != 'add') { echo 'readonlyForm'; } ?>" value = "<?php if (isset($_POST['nbEtudiant'])) { echo $_POST['nbEtudiant']; } else if (isset($userInfo['nbEtudiant'])) {  echo $userInfo['nbEtudiant']; } ?>" <?php if ($action != 'add') { echo 'readonly'; } ?> />
					<br />
							
					<label for = "rang"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_RANG; ?></label>
					<select id = "rang" name = "rang" class = "<?php if ($action != 'edit' && $action != 'add') { echo 'readonlyForm'; } ?>" <?php if ($action != 'edit' && $action != 'add') { echo 'disabled'; }; ?>>
						<?php
						if (isset($_POST['rang']) && $_POST['rang'] < $_SESSION['rang'])
						{
							$defaultValue = $_POST['rang'];
						}
						else
						{
							$defaultValue = $userInfo['rang'];
						}
						for ($i = 1; $i <= 4; $i++)
						{
							?>
								<option value = "<?php echo $i; ?>"  <?php if ($i == $defaultValue) { echo 'selected'; } ?> /><?php echo constant('LANG_RANG_VALUE_'.$i); ?></option>
							<?php
						}
						?>
					</select>
				</fieldset>
					
				<?php
			/**
				Informations propres aux étudiants
			**/
			
			if ($action != 'add' && ((isset($userInfo['promotion']) && count($userInfo['promotion']) > 0) || (isset($userInfo['service']) && count($userInfo['service']) > 0) || $userInfo['rang'] ==  1))
			{
				?>
					<fieldset>
					<legend><?php echo LANG_ADMIN_UTILISATEURS_CAT_ETUDIANT; ?></legend>
					
					<?php
							$listePromotions = getPromotionList();
							if (isset($_POST['promotion']) && isset($listePromotions[$_POST['promotion']]))
							{
								$defaultValue = $_POST['promotion'];
							}
							else if (isset($userInfo['promotion']))
							{
								$defaultValue = $userInfo['promotion']['id'];
							}
							?>
							<label for = "promotion"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PROMOTION; ?></label>
							<select id = "promotion" name = "promotion" class = "<?php if ($action != 'edit') echo 'readonlyForm'; ?>" <?php if ($action != 'edit') echo 'disabled'; ?>>
							<?php
							foreach($listePromotions AS $promotionData)
							{
							?>
								<option value = "<?php echo $promotionData['id']; ?>"  <?php if ($promotionData['id'] == $defaultValue) { echo 'selected'; } ?> /><?php echo $promotionData['nom']; ?></option>
							<?php
							}
							?>
							</select>
							<br />

							<div class = "formTitle"><?php echo LANG_ADMIN_UTILISATEURS_CAT_STAGES; if ($action == 'edit') { ?>&nbsp <a href = "<?php echo getPageUrl('adminServices').'page=affectations&action=affectation&action2=liste&id='.$userInfo['id']; ?>"><i class="fa fa-cog"></i></a><?php } ?></div>
						<?php
						if (isset($userInfo['service']))
						{
							$listeServices = getServiceList();
										
							foreach($userInfo['service'] AS $service)
							{
							
								if (isset($_POST['affectation'][$service['idAffectation']]) && isset($listeServices[$_POST['affectation'][$service['idAffectation']]]))
								{
									$defaultValue = $_POST['affectation'][$service['idAffectation']];
								}
								else
								{
									$defaultValue = $service['id'];
								}
							
								?>
								<label for = "affectation<?php echo $service['idAffectation']; ?>"><b><?php echo date('d/m/Y', $service['dateDebut']).' - '.date('d/m/Y', $service['dateFin']); ?></b></label>
								<select id = "affectation<?php echo $service['idAffectation']; ?>" name = "affectation[<?php echo $service['idAffectation']; ?>]" class = "<?php if ($action != 'edit') echo 'readonlyForm'; ?>" <?php if ($action != 'edit') echo 'disabled'; ?>>
									<?php
										foreach($listeServices AS $serviceId => $serviceInfo)
										{
										?>
											<option value = "<?php echo $serviceId; ?>"  <?php if ($serviceId == $defaultValue) { echo 'selected'; } ?> /><?php echo $serviceInfo['FullName']; ?></option>
										<?php
										}
									?>
								</select>
								<?php
								if ($action == "edit")
								{
									?>
								&nbsp	<a href = "<?php echo getPageUrl('adminServices').'page=affectations&action=affectation&action2=view&id='.$service['idAffectation']; ?>"><i class="fa fa-cog"></i></a>
									<?php
								}
							}
						}
					?>
					</fieldset>
				<?php
			}
			
			/**
				Informations propres aux chefs
			**/
			
			if ($action != 'add' && (isset($userInfo['chef']) && count($userInfo['chef']) > 0))
			{
				?>
					<fieldset>
					<legend><?php echo LANG_ADMIN_UTILISATEURS_CAT_CHEF; ?></legend>
					
					<?php
						if (isset($userInfo['chef']))
						{
							foreach($userInfo['chef'] AS $chefServiceInfo)
							{
							?>
							<input type = "text"  id = "chef<?php echo $chefServiceInfo['id']; ?>" name = "chef[]" class = 'readonlyForm fullwidth' value = "<?php echo $chefServiceInfo['FullName']; ?>" readonly />
							<br />
						<?php
							}
						}
					?>
					</fieldset>
				<?php
			}
			
			/**
				Bouton de validation du formulaire
			**/
			if ($action == 'edit' || $action == 'delete' || $action == 'add')
			{
				?>
					<input type = "hidden" name = "action" value = "<?php echo $action; ?>" />
					<input type = "submit" id = "submit_<?php echo $action; ?>" value = "<?php echo constant('LANG_ADMIN_UTILISATEURS_FORM_SUBMIT_'.strtoupper($action)); ?>" />
				<?php
			}
		?>
			</form>
		<?php
	}
	else
	{
		header('Location: '.$pageUtilisateurs);
	}
?>

<script>
	$('#submit_delete').on('click', function(e){
			if (!confirm('<?php echo LANG_ADMIN_UTILISATEURS_FORM_SUBMIT_DELETE_CONFIRM; ?>'))
			{
				e.preventDefault();
			}
	});
</script>