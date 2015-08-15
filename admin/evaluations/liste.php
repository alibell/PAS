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
		21/05/15 - liste.php - Ali Bellamine
		Contient la liste des campagnes d'évaluation en cours
	*/

	/**
		0. Création des variables nécessaire à l'execution de la page
	**/
	$erreur = array();
	
	/**
		1. Récupération des données
	**/

		/*
			Routeur
		*/
		
		$allowedAction = array('liste','add', 'view', 'edit', 'delete','settings','userList','mail');
		if (isset($_GET['action']) && in_array($_GET['action'], $allowedAction))
		{
			$action = $_GET['action'];
		}
		else
		{
			$action = $allowedAction[0];
		}
		
		/*
			Récupération de la liste des campagnes d'évaluation si $action = liste
		*/
		
		if ($action == 'liste')
		{
			$evaluations = getEvalCampaignList(); // On récupère la liste des évaluations
			$listeAnnee = array();
			
			$sql = "SELECT year(dateDebut) dateDebut, year(dateFin) dateFin FROM `evaluation` GROUP BY year(dateDebut), year(dateFin)"; // On récupère la liste des années pour lesquelles il y a des évaluations
			$res = $db -> query($sql);
			while ($res_f = $res -> fetch())
			{
				if (!isset($listeAnnee[$res_f['dateDebut']]))
				{
					$listeAnnee[$res_f['dateDebut']] = true;
				}
				if (!isset($listeAnnee[$res_f['dateFin']]))
				{
					$listeAnnee[$res_f['dateFin']] = true;
				}
			}
		}
		
		/*
			On récupère les données propre à une évaluation si elle existe
		*/
		
		else if ($action == 'view' || $action == 'edit' || $action == 'delete' || $action == 'userList' || $action == 'settings' || $action == 'mail')
		{
			if (isset($_GET['id']) && count(checkEvaluation($_GET['id'], array())) == 0)
			{
				$evaluation = getEvalData($_GET['id']);
			}
			else
			{
				header('Location: '.ROOT.CURRENT_FILE.'?action=liste&erreur='.serialize(checkEvaluation($_GET['id'], array()))); // on renvoie vers la liste des évaluations si celle sélectionnée n'existe pas
			}
		}
		
	/**
		2. Traitement des formulaires
	**/
	
	if (isset($_POST) && count($_POST) > 0)
	{
		// Pour les ajout, edit, suppression -> traitement habituel des données
		if ($action == 'edit' || $action == 'delete' || $action == 'add')
		{
			/*
				Préparation des données : on crée un array contenant toutes les données, ce dernier sera ensuite parcouru pour créer la requête SQL qui sera préparée
			*/
		
			$sqlData = array();
			
			if ($action == 'edit' || $action == 'delete')
			{
				$sqlData['id'] = $evaluation['id']; // Id de l'affectation	
			}
			
			if ($action == 'edit' || $action == 'add')
			{
				foreach ($_POST AS $key => $value)
				{
					if ($key == 'nom')
					{
						if ($value != '')
						{
							$sqlData[$key] = htmLawed($value);
						}
						else
						{
							$erreur[17] = true;
						}
					}
					else if ($key == 'dateDebut' || $key == 'dateFin')
					{
						// On convertit les dates si elle sont valides
						$temp = explode('/', $value);
						if(isset($value) && count($temp) >= 3 && checkdate($temp[1], $temp[0], $temp[2]))
						{
							$convertedDate = DatetimeToTimestamp(FrenchdateToDatetime($value)); // Date au format timestamp
							$validDate = TRUE;
							
							// Si dateDebut : on refuse le cas où la date est supérieure à la marge sup
							if ($key == 'dateDebut' && ((isset($_POST['dateFin']) && $convertedDate >= DatetimeToTimestamp(FrenchdateToDatetime($_POST['dateFin']))) || ($action== 'edit' && $convertedDate >= $evaluation['date']['fin'])))
							{
								$erreur[18] = TRUE;
								$validDate = FALSE;
							}
							else if ($key == 'dateFin' && ((isset($_POST['dateDebut']) && $convertedDate <= DatetimeToTimestamp(FrenchdateToDatetime($_POST['dateDebut']))) || ($action == 'edit' && $convertedDate <= $evaluation['date']['debut'])))
							{
								$erreur[18] = TRUE;
								$validDate = FALSE;
							}
							
							if ($validDate)
							{
								$sqlData[$key] = TimestampToDatetime($convertedDate);
							}
						}
					}
					else if ($key == 'type')
					{
						if (count(checkEvaluationType($value, array())) == 0)
						{
							$sqlData[$key] = $value;
						}
						else
						{
							$erreur = checkEvaluationType($value, $erreur);
						}
					}
				}
			}
			
			/*
				On enregistre les données dans la BDD
			*/
			$sqlInsert = FALSE; // Enregistre la bonne réussite des requêtes
			
			/**
				Pour les ajouts
			**/
			if (isset($sqlData))
			{
				if ($action == 'add')
				{
					if (isset($sqlData) && count($sqlData) > 0 && isset($sqlData['dateDebut']) && isset($sqlData['dateFin']) && isset($sqlData['nom']) &&  isset($sqlData['type']))
					{
						$sql = 'INSERT INTO evaluation (';
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
						$sql = 'UPDATE evaluation SET ';
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
						$sql = 'DELETE FROM evaluation WHERE id = :id';	
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
					Si action correctement réalisé -> on redirige vers le list
				*/
				if (isset($sqlAction) && $sqlAction) {
					$tempGET = $_GET;
					unset($tempGET['action']);
					
					/* On trace les ajouts à travers différentes étapes */
					if ($action == 'add') 
					{ 
						$_SESSION['evaluationAdd'] = TRUE;
						$evaluationId = $db -> LastInsertId();
							
						$url = ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action=settings&id='.$evaluationId;						
					}
					else
					{
						$url = ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action=liste';
					}
					
						header('Location: '.$url);
				}
			}
		}
		else if ($action == 'mail')
		{
			if (isset($_POST['action']) && $_POST['action'] == 'envoyerMail')
			{
				// On définit le contenu des mails
				if (isset($_POST['titre']) && $_POST['titre'] != '' && isset($_POST['message']) && $_POST['message'] != '')
				{
					// On enregistre le contenu dans $_SESSION['sendMail']['settings']
					$_SESSION['sendMail']['settings']['objet'] = htmLawed($_POST['titre']);
					$_SESSION['sendMail']['settings']['message'] = htmLawed($_POST['message']);
					
					// On enregistre la liste des mail à envoyer dans une variable session
					foreach ($evaluation['users'] AS $user)
					{
						if ($user['statut'] == 0)
						{
							$_SESSION['sendMail']['current'][$user['id']] = $user; // On ajoute les envoie à faire dans current
						}
					}
					$_SESSION['sendMail']['nb'] = count($_SESSION['sendMail']['current']); // Liste du nombre d'envoie à faire
					$_SESSION['sendMail']['done'] = array(); // Liste des envoie déjà effectués
				}
			}
			else if ($_POST['action'] == 'annulerEnvoie')
			{
				unset($_SESSION['sendMail']);
			}
		}
	}
	
	if (isset($_SESSION['evaluationAdd']) && $action != 'settings' && $action != 'userList' && $action != 'add')
	{
		unset($_SESSION['evaluationAdd']); // Suppression automatique du traceur d'ajout d'évaluation
	}
	
	/**
		3. Affichage
	**/
	
		// 3.1 : On affiche les erreurs
		
		if (isset($erreur) && count($erreur) > 0)
		{
			displayErreur($erreur);
		}
		
		// 3.2 : Si $action = liste : on affiche la liste des évaluations
		
		if ($action == 'liste')
		{
			if (isset($evaluations) && count($evaluations) > 0)
			{
				if (isset($listeAnnee) && count($listeAnnee) > 0)
				{
					krsort($listeAnnee); // On range l'array par ordre décroissant
					/* Valeur par défault */
					if (isset($_GET['annee']) && isset($listeAnnee[$_GET['annee']]))
					{
						$defaultAnnee = $_GET['annee'];
					}
					else
					{
						$defaultAnnee = array_keys($listeAnnee)[0];
					}

					$tempGET = $_GET; unset($tempGET['annee']);
					?>
					<form method = "GET" action = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET); ?>">
						<select id = "selectAnnee" name = "annee">
							<?php
								foreach ($listeAnnee AS $Annee => $AnneeValue)
								{
									?>
										<option value = "<?php echo $Annee; ?>" <?php if ($defaultAnnee == $Annee) { echo 'selected'; } ?>><?php echo $Annee; ?></option>
									<?php
								}
							?>
						</select>
					</form>
					<?php
				}
				?>
				<table  style = "margin-top: 10px;">
					<tr class = "headTR">
						<td><?php echo LANG_ADMIN_LISTE_TABLE_TITLE_NOM; ?></td>
						<td><?php echo LANG_ADMIN_EVALUATIONS_LISTE_TABLE_TITLE_TYPE; ?></td>
						<td><?php echo LANG_ADMIN_EVALUATIONS_LISTE_TABLE_TITLE_REMPLISSAGE; ?></td>
						<td><?php echo LANG_ADMIN_EVALUATIONS_LISTE_TABLE_TITLE_PERIODE; ?></td>
						<td><?php echo LANG_ADMIN_TABLE_TITLE_ADMIN; ?></td>
					</tr>
				<?php
				foreach ($evaluations AS $evaluation)
				{
					if (date('Y', $evaluation['date']['debut']) == $defaultAnnee || date('Y', $evaluation['date']['fin']) == $defaultAnnee)
					{
					?>
					<tr class = "bodyTR">
						<td><?php echo $evaluation['nom']; ?></td>
						<td><?php echo $evaluation['type']['nom']; ?></td>
						<td>
							<?php
							if ($evaluation['nb']['total'] != 0)
							{
								$per = round($evaluation['nb']['remplis']/$evaluation['nb']['total'], 2)*100;
							}
							else
							{
								$per = 0;
							}
							
							echo $per.' % ('.$evaluation['nb']['remplis'].' / '.$evaluation['nb']['total'].')'
							?>
						</td>
						<td><?php echo date('d/m/Y',$evaluation['date']['debut']).' - '.date('d/m/Y',$evaluation['date']['fin']); ?></td>
						<td>
							<a href = "<?php echo ROOT.CURRENT_FILE.'?page=liste&action=view&id='.$evaluation['id']; ?>"><i class="fa fa-info"></i></a>
							<?php if (isset($evaluation['type']['data']['optionnel']['php']) && in_array('configEvaluation', $evaluation['type']['data']['optionnel']['php'])) { ?>
								<a href = "<?php echo ROOT.CURRENT_FILE.'?page=liste&action=settings&id='.$evaluation['id']; ?>"><i class="fa fa-cog"></i></a>
							<?php } ?>
							<a href = "<?php echo ROOT.CURRENT_FILE.'?page=liste&action=userList&id='.$evaluation['id']; ?>"><i class="fa fa-users"></i></a>
							<?php
								// Si il y a des évaluations non remplis, on peux envoyer un mail
								if (($evaluation['nb']['total'] - $evaluation['nb']['remplis']) > 0)
								{
								?>
								<a href = "<?php echo ROOT.CURRENT_FILE.'?page=liste&action=mail&id='.$evaluation['id']; ?>"><i class="fa fa-envelope-o"></i></a>
								<?php
								}
							?>
							<a href = "<?php echo ROOT.CURRENT_FILE.'?page=liste&action=edit&id='.$evaluation['id']; ?>"><i class="fa fa-pencil"></i></a>
							<a href = "<?php echo ROOT.CURRENT_FILE.'?page=liste&action=delete&id='.$evaluation['id']; ?>"><i class="fa fa-trash-o"></i></a>
						</td>
					</tr>
					<?php
					}
				}
				?>
				</table>
				<?php
			}
			?>
			<br />
			<a class = "bouton" href = "<?php echo ROOT.CURRENT_FILE.'?page=liste&action=add'; ?>"><i class="fa fa-plus-circle"></i></a>
		<?php
		}
		
		if ($action == 'edit' || $action == 'delete' || $action == 'view' || $action == 'settings' || $action == 'userList' || $action == 'mail')
		{
		$tempGET = $_GET;
		unset($tempGET['action']);
		unset($tempGET['msg']);
	?>
		<h1><?php echo $evaluation['nom']; ?></h1> <!-- Nom de l'évaluation -->
		<div class = "barreNavigation">
			<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action=liste'; ?>"><i class="fa fa-list barreNavigationBouton"></i></a>
			<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action=view'; ?>"><i class="fa fa-info barreNavigationBouton"></i></a>
			<?php if (isset($evaluation['type']['data']['optionnel']['php']) && in_array('configEvaluation', $evaluation['type']['data']['optionnel']['php'])) {?>
				<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action=settings'; ?>"><i class="fa fa-cog barreNavigationBouton"></i></a>
			<?php } ?>
			<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action=userList'; ?>"><i class="fa fa-users barreNavigationBouton"></i></a>
			<?php
			if (($evaluation['nb']['total'] - $evaluation['nb']['remplis']) > 0)
			{
			?>
			<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action=mail'; ?>"><i class="fa fa-envelope-o barreNavigationBouton"></i></a>
			<?php
			}
			?>
			<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action=edit'; ?>"><i class="fa fa-pencil barreNavigationBouton"></i></a>
			<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action=delete'; ?>"><i class="fa fa-trash-o barreNavigationBouton"></i></a>
			
			<?php if ($action == 'userList') { ?><i class="fa fa-lock barreNavigationBouton lockButton" data-value = "0" style = "float: right; cursor: pointer; user-select: none;"></i></a></a><?php } ?>
		</div>
	<?php
		}
		else if ($action == 'add')
		{
			$tempGET = $_GET;
			unset($tempGET['action']);
			?>
			<div class = "barreNavigation">
				<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action=liste'; ?>"><i class="fa fa-list barreNavigationBouton"></i></a></a>
			</div>
			<?php
		}
		
		if ($action == 'view' || $action == 'edit' || $action == 'delete' || $action == 'add')
		{
		?>
			<form class = "formEvaluation" method = "POST">
				<fieldset>
					<legend><?php echo LANG_ADMIN_UTILISATEURS_CAT_INFOSGENERALES; ?></legend>
					
					<!-- Nom de l'évaluation -->
					<label for = "nom"><?php echo LANG_ADMIN_LISTE_TABLE_TITLE_NOM; ?></label>
					<input type = "text" class = "<?php if ($action != 'edit' && $action != 'add') echo 'readonlyForm'; ?>" name = "nom" id = "nom" value = "<?php if (isset($_POST['nom'])) { echo $_POST['nom']; } else if (isset($evaluation)) { echo $evaluation['nom']; } ?>" <?php if ($action != 'edit' && $action != 'add') echo 'readonly'; ?> /><br />
					
					<!-- Type d'évaluations -->
						<?php
						// On récupère la liste des types d'évaluations
						$evaluationTypeList = getEvaluationTypeList();
						
						// Valeur par défault
						if (isset($_POST['type']) && isset($evaluationTypeList[$_POST['type']]))
						{
							$defaultValue = $_POST['type'];
						}
						else if (isset($evaluation['type']) && isset($evaluationTypeList[$evaluation['type']['id']]))
						{
							$defaultValue = $evaluation['type']['id'];
						}
						
						// On affiche le select
						?>
						<label for = "type"><?php echo LANG_ADMIN_EVALUATIONS_LISTE_TABLE_TITLE_TYPE; ?></label>
						<select name = "type" id = "type">
						<?php
							foreach ($evaluationTypeList AS $evaluationType)
							{
								?>
								<option value = "<?php echo $evaluationType['id']; ?>" <?php if (isset($defaultValue) && $defaultValue == $evaluationType['id']) { echo 'selected'; } ?>><?php echo $evaluationType['nom']; ?></option>
								<?php
							}
						?>
						</select><br />
					
					<!-- Dates -->
					<label for = "dateDebut"><?php echo LANG_ADMIN_AFFECTATIONS_DATE_DEBUT; ?></label>
					<input type = "hidden" name = "dateDebut" value = "<?php if (isset($_POST['dateDebut'])) { echo $_POST['dateDebut']; } else if (isset($evaluation['date']['debut'])) { echo date('j/n/Y', $evaluation['date']['debut']); } ?>" />
					<input type = "button" class = "dateButton <?php if ($action != 'edit' && $action != 'add') echo 'readonlyForm'; ?>" id = "dateDebut" value = "<?php if (isset($_POST['dateDebut'])) { echo $_POST['dateDebut']; } else if (isset($evaluation['date']['debut'])) { echo date('j/n/Y', $evaluation['date']['debut']); } ?>" <?php if ($action != 'edit' && $action != 'add') { echo 'readonly'; } ?> /><br />

					<label for = "dateFin"><?php echo LANG_ADMIN_AFFECTATIONS_DATE_FIN; ?></label>
					<input type = "hidden" name = "dateFin" value = "<?php if (isset($_POST['dateFin'])) { echo $_POST['dateFin']; } else if (isset($evaluation['date']['fin'])) { echo date('j/n/Y', $evaluation['date']['fin']); } ?>" />
					<input type = "button" class = "dateButton <?php if ($action != 'edit' && $action != 'add') echo 'readonlyForm'; ?>" id = "dateFin" value = "<?php if (isset($_POST['dateFin'])) { echo $_POST['dateFin']; } else if (isset($evaluation['date']['fin'])) { echo date('j/n/Y', $evaluation['date']['fin']); }  ?>" <?php if ($action != 'edit' && $action != 'add') { echo 'readonly'; } ?> /><br />

				<?php if ($action != 'view') { ?><input type = "submit" id = "submit_<?php echo $action; ?>" value = "<?php echo constant('LANG_ADMIN_EVALUATION_FORM_SUBMIT_'.strtoupper($action)); ?>" /><?php } ?>
			</form>
		<?php
		}
		else if ($action == 'userList')
		{ 
	
			// Création de la div contenant la liste des utilisateurs et qui sera remplis en ajax
			?>
			<div id = "evaluationUserList">
			</div>
			
			<!-- Div contenant les outils d'ajout d'utilisateurs -->
			<div id = "evaluationUserListToolbox">
				<div class = "button editUserList" style = "display: none; float: left;"><i class="fa fa-plus-square" data-action = "add"></i></div>
				<div class = "button right"><i class="fa fa-desktop" data-action = "fullscreen"></i></div>
			</div>
			
			<!-- Div d'ajout des utilisateurs -->
			<?php
				$listePromotions = getPromotionList();
				$listeCertificats = getCertificatList();
			?>
			<div id = "addUsers" style = "display: none;">
				<!-- Liste des utilisateurs : remplis par le JS -->
				<div id = "addUsersLeftPanel">
				</div>
				
				<!-- Filtres -->
				<div id = "addUsersRightPanel">
					<i class="fa fa-times closeButton"></i> <!-- Bouton de fermeture -->
					
					<!-- Liste des filtres -->
					<div style = "overflow: auto; height: calc(100% - 50px);">
						
						<!-- Enseignant OU étudiant -->
						<div style = "margin: auto; text-align: center;">
							<input checked class = "defaultRadio" type="radio" name="typeSelection" data-type = "typeUser" data-value = "etudiant" value="etudiant">Etudiants
							<input type="radio" name="typeSelection" data-type = "typeUser" data-value = "enseignant" value="enseignant">Enseignants
						</div>
						
						<!-- Filtre par promotion -->
						<div class = "addUsersRightPanelTitle">Promotions</div>
						
						<label style = "padding-left: 10px;" for = "checkBoxPromotion">Aucune
							<input checked class = "defaultRadio" id = "checkBoxPromotion" data-type = "promotion" data-value = "" name = "promotion" type = "radio" />
						</label>
						<?php
						foreach ($listePromotions AS $promotion)
						{
							?>					
							<label style = "padding-left: 10px;" for = "checkBoxPromotion<?php echo $promotion['id']; ?>"><?php echo $promotion['nom']; ?>
								<input id = "checkBoxPromotion<?php echo $promotion['id']; ?>" data-type = "promotion" data-value = "<?php echo $promotion['id']; ?>" name = "promotion" type = "radio" />
							</label>
							<?php
						}
						?>
						
						<!-- Filtre par certificat -->
						<div class = "addUsersRightPanelTitle">Certificats</div>
						<?php
						foreach ($listeCertificats AS $certificat)
						{
							?>
							<label style = "padding-left: 10px;" for = "checkBoxCertificat<?php echo $certificat['id']; ?>"><?php echo $certificat['nom']; ?>
								<input id = "checkBoxCertificat<?php echo $certificat['id']; ?>" data-type = "certificat" data-value = "<?php echo $certificat['id']; ?>" type = "checkbox" />
							</label>
							<?php
						}
						?>
						
						<div id = "listeExclude"></div>
					</div>
					<div style = "height: 30%; width: 100%; bottom: 0px; text-align: center;">
						<button id = "addUserConfirm" data-msg = "Confirmer l'ajout des utilisateurs ?" style = "display: inline-block; padding: 10px;">Ajouter</button>
					</div>
				</div>
			</div>
			<?php
		}
		else if ($action == 'mail')
		{
			?>
			<div id = "sendMailBox">
				<span><?php echo $evaluation['nb']['remplis'].' évaluations remplis sur '.$evaluation['nb']['total']; ?></span>
				<?php
				if (!isset($_SESSION['sendMail']) || (isset($_SESSION['sendMail']) && isset($_SESSION['sendMail']['current']) && count($_SESSION['sendMail']['current']) == 0))
				{
					if (($evaluation['nb']['total']-$evaluation['nb']['remplis']) > 0)
					{
					?>
					<div>
						<form method = "POST">
							<label for = "titre"><?php echo LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_OBJET; ?></label>
							<input type = "text" id = "titre" name  = "titre" style = "width: 90%;" value = "<?php if (isset($_POST['tire'])) { echo $_POST['titre']; } else { echo LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_OBJET_DEFAULT; } ?>" /><br />
							
							<label for = "message" style = "float: none;"><?php echo LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_MSG; ?></label>
							<textarea name = "message" style = "width: 90%;" id = "message"><?php if (isset($_POST['message'])) { echo $_POST['message']; } else { echo sprintf(LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_MSG_DEFAULT, $evaluation['nom'], getPageUrl('evalDo', array('id' => $evaluation['id']))); } ?></textarea>
								
							<input type = "hidden" name = "action" value = "envoyerMail" />
							<input type = "submit" formnovalidate="true" value = "<?php echo sprintf(LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_BUTTON, ($evaluation['nb']['total']-$evaluation['nb']['remplis'])); ?>" />
						</form>
					</div>
					<?php
					}
				}
				else
				{
					$erreur = array();
					$mailTitle = $_SESSION['sendMail']['settings']['objet'];
					$mailBody = $_SESSION['sendMail']['settings']['message'];
					$n = 0;
					foreach ($_SESSION['sendMail']['current'] AS $userMail)
					{
						if ($n == 30) { break; } // Arrête au bout de 30 mails envoyés
						
						// On met en forme la liste des email à contacter
						$mailToSend = array();
						foreach ($userMail['mail'] AS $email)
						{
							$mailToSend[$email] = $userMail['prenom'].' '.$userMail['nom'];
						}
						
						// Envoie un mail
						if (isset($mailToSend) && count($mailToSend) > 0)
						{
							$erreur[$userMail['id']] = sendMail($mailToSend,$mailTitle,$mailBody,array()); // On envoie le mail et on stocke l'erreur dans un array propre au mail si il y a une erreur
						}

						// On retire l'email de la liste
						
						if (!isset($erreur[$userMail['id']]) || count($erreur[$userMail['id']]) == 0)
						{
							$_SESSION['sendMail']['done'][$userMail['id']] = $userMail;
							unset($_SESSION['sendMail']['current'][$userMail['id']]);
							if (isset($erreur[$userMail['id']])) { unset($erreur[$userMail['id']]); } // On supprime l'erreur
						}
						
						$n++; // Incrémente le nombre de mail envoyés
					}
					
					/*
						Affichage de la progression
					*/
					
					if ($_SESSION['sendMail']['nb'] != 0)
					{
						$progression = round(count($_SESSION['sendMail']['done'])/$_SESSION['sendMail']['nb'],2);
					}
					else
					{
						$progression = 1;
					}
					
					?>
					<div style = "text-align: center;">
						<?php
							displayProgressionBar('90%', '30px', $progression);
							echo '<br />'.count($_SESSION['sendMail']['done']).' / '.$_SESSION['sendMail']['nb'];
						?>
					</div>
					<?php
					
					/*
						Suite : soit en actualise la page, soit en affiche les erreurs si il y en a
					*/
					
					if (count($erreur) == 0)
					{
						header('Location: '.ROOT.CURRENT_FILE.'?'.http_build_query($_GET).'&msg=LANG_SUCCESS_MAIL_SEND');
					}
					else
					{
						// On affiche les erreurs
						?>
						<h1><?php echo LANG_ERRORS; ?></h1>
						<table>
						
							<tr class = "headTR">
								<th><?php echo LANG_ADMIN_LISTE_TABLE_TITLE_NOM; ?></th>
								<th><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PRENOM; ?></th>
								<th><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_MAIL; ?></th>
								<th></th>
							</tr>
						<?php
						foreach($erreur AS $erreurId => $erreurData)
						{
							?>
							<tr class = "bodyTR">
								<td><?php echo $_SESSION['sendMail']['current'][$erreurId]['nom']; ?></td>
								<td><?php echo $_SESSION['sendMail']['current'][$erreurId]['prenom']; ?></td>
								<td>
								<?php 
									if (isset($_SESSION['sendMail']['current'][$erreurId]['mail']) && is_array($_SESSION['sendMail']['current'][$erreurId]['mail']))
									{
										$firstLoop = TRUE;
										foreach ($_SESSION['sendMail']['current'][$erreurId]['mail'] AS $email)
										{
											if ($firstLoop) { $firstLoop = FALSE; } else { echo '<br />'; }
											echo $email;
										}
									}
								?>
								</td>
								<td><?php displayErreur($erreurData); ?></td>
							</tr>
							<?php
						}
						?>
						</table>
						<br />
						<?php echo LANG_ERROR_MAIL_RELOAD; ?>
						
						<!-- Bouton pour la suite -->
						<div>
							<form method = "POST">						
								<input type = "hidden" name = "action" value = "annulerEnvoie" />
								<input type = "submit" value = "<?php echo LANG_ADMIN_EVALUATION_FORM_SUBMIT_SEND_MAIL_CANCEL; ?>" />
							</form>
						</div>
						
					<?php
					}
				}
			?>	
			</div>
			<?php
		}
		else if ($action == 'settings')
		{
			// Page contenant les réglages de l'instance d'évaluation
			 if (isset($evaluation['type']['data']['optionnel']['php']) && in_array('configEvaluation', $evaluation['type']['data']['optionnel']['php'])) {
			
				// On déclare la constante PLUGIN_PATH contenant le chemin du plugin
				define('PLUGIN_PATH', $_SERVER['DOCUMENT_ROOT'].'/'.LOCAL_PATH.'/evaluations/'.$evaluation['type']['dossier'].'/');
				$evaluationData = $evaluation;

				// On inclut la page				
				include(PLUGIN_PATH.'configEvaluation.php');
			 }
			 else
			 {
				 // Si il s'agit d'un nouvelle évaluation, on redirige vers la page d'ajout des utilisateurs
				 if (isset($_SESSION['evaluationAdd']))
				 {
					header('Location: '.ROOT.CURRENT_FILE.'?page=liste&action=userList&id='.$evaluation['id']);
				 }
				 else
				 {
					 header('Location: '.ROOT.CURRENT_FILE.'?page=liste&action=liste');
				 }			 
			 }
		}
?>

<?php
// Code JS pour la liste des utilisateurs
 if ($action == 'userList')
{
	?>
	<script type="text/javascript" src="<?php echo ROOT.'JS/page/userList.js'; ?>"></script> <!-- jQuery, license MIT -->
	<?php
}
?>
<script>

	<?php
	if ($action == 'edit' || $action == 'add')
	{
		?>		
			// Calendrier pour les dates
			$('input[type="button"]').each(function(){
				if ($(this).val() != '')
				{
					var dateValues = $(this).val().split('/');
					var currentDate = new Date(dateValues[2], dateValues[1]-1, dateValues[0]);
				}
				else
				{
					var currentDate = null;
				}
				
				$(this).glDatePicker({
					selectedDate: currentDate,
					closeText: 'Fermer',
					prevText: 'Précédent',
					nextText: 'Suivant',
					currentText: 'Aujourd\'hui',
					monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
					monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
					dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
					dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
					dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
					weekHeader: 'Sem.',
					dateFormat: 'dd/mm/YYYY',
					onClick: (function(el, cell, date, data) {
						el.val(date.toLocaleDateString());
						$('input[name="'+ el.attr('id')+'"]').val(date.toLocaleDateString()); // On met à jour la valeur de l'input caché correspondant
					})
				});
			});
		<?php
	}
	else if ($action == 'userList')
	{
		?>
		var evaluationId = <?php echo $evaluation['id']; ?>;	
		var ajaxURL = '<?php echo getPageUrl('ajaxUserList'); ?>';	
		var lastUpdateText = "<?php echo LANG_ADMIN_EVALUATIONS_USERLIST_LASTUPDATE; ?>";

		$(document).ready(function(){
			updateUserList();
			
			setInterval(updateUserList, 10000);
		});
		<?php
	}
	?>

	$('#submit_delete').on('click', function(e){
			if (!confirm("<?php echo LANG_ADMIN_AFFECTATIONS_FORM_SUBMIT_DELETE_CONFIRM; ?>"))
			{
				e.preventDefault();
			}
	});
	
	/*
		TinyMCE pour l'objet du mail
	*/
	
	tinymce.init({
		selector: "#message",
		menubar: false,
		statusbar: false
	});
	
	/*
		Filtres dans le choix des évaluations
	*/
	
	$('#selectAnnee').on('change', function(){
		$(this).parent().submit();
	});
</script>