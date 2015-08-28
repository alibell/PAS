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
		17/05/15 - affectations.php - Ali Bellamine
		Gère l'affectation des étudiants aux services
	*/

	$pageUtilisateurs = getPageUrl('adminUtilisateurs');
	$pageServices = getPageUrl('adminServices');
	$erreur = array();
	
/**
	Routage selon la variable action
**/

	$allowedAction = array('home', 'batch', 'affectation');
	$allowedAction2 = array('batch' => array('import', 'dlcsv'), 'affectation' => array('liste','view','add','edit','delete'));
	if (isset($_GET['action']) && in_array($_GET['action'], $allowedAction))
	{
		$action = $_GET['action'];
	}
	else
	{
		$action = 'home';
	}
	
	if (isset($allowedAction2[$action]) && count($allowedAction2[$action]) > 0)
	{
		if (isset($_GET['action2']) && in_array($_GET['action2'], $allowedAction2[$action]))
		{
			$action2 = $_GET['action2'];
		}
		else
		{
			$action2 = $allowedAction2[$action][0];
		}
	}
	
	/**
		1. Traitement des données
	**/
	
	if ($action == 'batch')
	{
		// Traitement des CSV importés
		if ($action2 == 'import' && count($_POST) > 0)
		{
			// Si fichier CSV envoyé
			
			if (isset($_POST['upload']) && isset($_FILES['csv']) && $_POST['upload'] == 'ok')
			{
				$fileName = explode('.', $_FILES['csv']['name']);
				if (is_file($_FILES['csv']['tmp_name']) && $csv = fopen($_FILES['csv']['tmp_name'], 'r') && $fileName[count($fileName)-1] == 'csv')
				{
					// Récupération de la liste à partir du CSV
					$affectations = readCSV($_FILES['csv']['tmp_name'], ';', FALSE);
					
					/* On parcours le fichier $affectations et :
					 *	   - On enregistre dans l'array $affectationsOk['insert'] les valeurs  prêtes à être insérées
					 *    - On enregistre dans l'array $affectationsOk['erreur'] les valeurs qui posent probleme et qui ne doivent pas être insérées
					 */
					 
					 $affectationOk['insert'] = array();
					 $affectationOk['erreur'] = array();
					 
					foreach ($affectations AS $affectationId => $affectationData)
					{
						if (count($affectationData) >= 4)
						{
							$erreurAffectation = checkAffectationInsertData(getUserIdFromNbEtudiant($affectationData[0]), $affectationData[1], $affectationData[2], $affectationData[3], array());
							if (count($erreurAffectation) == 0)
							{
								$affectationOk['insert'][$affectationId] = array('etudiant' => getUserIdFromNbEtudiant($affectationData[0]), 'service' => $affectationData[1], 'dateDebut' => $affectationData[2], 'dateFin' => $affectationData[3]);
							}
							else
							{
								$affectationOk['erreur'][$affectationId] = $erreurAffectation;							
							}
						}
					}

					// On stocke les données dans un $_SESSION afin de les rendre ré-utilisable dans le page de traitement
					$_SESSION['affectations'] = array();
					$_SESSION['affectations']['data'] = $affectationOk;
					$_SESSION['affectations']['rawdata'] = $affectations;
				}
				else
				{
					$erreur[8] = TRUE;
				}
			}
			else if (isset($_POST['valid']) && $_POST['valid'] == 1)
			{
				// Liste les erreurs qui ont eu lieu
				$affectationsErreur = array();
				
				// On parcours l'array $_SESSION['affectations']['data']['insert'] et on les insert
				foreach ($_SESSION['affectations']['data']['insert'] AS $affectationInsertId => $affectationInsertData)
				{
					$tempErreur = array();
					$tempErreur = checkAffectationInsertData($affectationInsertData['etudiant'], $affectationInsertData['service'], $affectationInsertData['dateDebut'], $affectationInsertData['dateFin'], $tempErreur); // On verifie la validité des données

					if (count($tempErreur) == 0)
					{
						$sql = 'INSERT INTO affectationexterne (userId, service, dateDebut, dateFin) VALUES (?, ?, ?, ?)';
						$res = $db -> prepare($sql);
						$resT = $res -> execute(array($affectationInsertData['etudiant'], $affectationInsertData['service'], FrenchdateToDatetime($affectationInsertData['dateDebut']), FrenchdateToDatetime($affectationInsertData['dateFin'])));
						if (!$resT)
						{
							$affectationsErreur[$affectationInsertId][14] = TRUE; // On enregistre l'erreur
						}
					}
					else
					{
						// On enregistre les erreurs
						$affectationsErreur[$affectationInsertId] = $tempErreur;
					}
				}
				
				// Si aucunes erreurs on redirige vers l'acceuil
				if (count($affectationsErreur) == 0)
				{
					header('Location: '.ROOT.CURRENT_FILE.'?page=affectations&msg=LANG_ADMIN_AFFECTATIONS_BATCH_SUCCESS');
				}
			}
			else
			{
				$erreur[8] = TRUE;
			}
		}
		// Téléchargement du CSV vide
		else if ($action2 == 'dlcsv')
		{
			$arrayCSV[] = array(LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NBETUDIANT, LANG_ADMIN_SERVICES_ID, LANG_ADMIN_AFFECTATIONS_DATE_DEBUT, LANG_ADMIN_AFFECTATIONS_DATE_FIN);
			downloadCSV($arrayCSV, 'import.csv');
		}
	}
	else if ($action == 'affectation')
	{	
		// Liste des affectation d'un utilisateur
		if ($action2 == 'liste')
		{
			// Récupération de l'ID de l'utilisateur
			if (isset($_GET['id']) && count(checkUser($_GET['id'], array())) == 0)
			{
				// On récupère les données de l'utilisateur
				$userData = getUserData($_GET['id']);
			}
			else
			{
				header('Location: '.$pageUtilisateurs.'erreur='.serialize(checkUser($_GET['id'], array())));
			}
		}
		else if ($action2 == 'view' || $action2 == 'edit' || $action2 == 'delete')
		{
			// Récupération des informations sur l'affectation
			if (isset($_GET['id']) && count(checkAffectation($_GET['id'], array())) == 0)
			{
				// On récupère les données de l'utilisateur
				$affectationData = getAffectationData($_GET['id']);
				$affectationData['user'] = getUserData($affectationData['user']['id']);
				$affectationData['service'] = getServiceInfo($affectationData['service']['id']);
			}
			else
			{
				header('Location: '.$pageUtilisateurs.'erreur='.serialize(checkAffectation($_GET['id'], array())));
			}
		}
		else if ($action2 == 'add')
		{
			// Récupération de l'ID de l'utilisateur
			if (isset($_GET['id']) && count(checkUser($_GET['id'], array())) == 0)
			{
				// On récupère les données de l'utilisateur
				$userData = getUserData($_GET['id']);
			}
			else
			{
				header('Location: '.$pageUtilisateurs.'erreur='.serialize(checkUser($_GET['id'], array())));
			}
		}
		
		// On traite le $_POST
		if (isset($_POST) && count($_POST) > 0)
		{
			/*
				Préparation des données : on crée un array contenant toutes les données, ce dernier sera ensuite parcouru pour créer la requête SQL qui sera préparée
			*/
			
				if ($action2 == 'add')
				{
					$sqlData['userId'] = $userData['id']; // Id de l'utilisateur				
				}
				else if ($action2 == 'edit' || $action2 == 'delete')
				{
					$sqlData['id'] = $affectationData['id']; // Id de l'affectation	
				}
				
				if ($action2 == 'edit' || $action2 == 'add')
				{

					foreach ($_POST AS $key => $value)
					{
						if ($key == 'service')
						{
							if ($value != '' && count(checkService($value, array())) == 0)
							{
								$sqlData[$key] = $value;
							}
							else
							{
								$erreur = checkService($value, $erreur);
							}
						}
						else if ($key == 'dateDebut' || $key == 'dateFin')
						{
							// On convertit les dates si elle sont valides
							$temp = explode('/', $value);
							if(isset($value) && count($temp) > 3 && checkdate($temp[1], $temp[0], $temp[2]))
							{
								$convertedDate = DatetimeToTimestamp(FrenchdateToDatetime($value)); // Date au format timestamp
								$validDate = TRUE;
								
								// Si dateDebut : on refuse le cas où la date est supérieure à la marge sup
								if ($key == 'dateDebut' && ((isset($_POST['dateFin']) && $convertedDate >= DatetimeToTimestamp(FrenchdateToDatetime($_POST['dateFin']))) || ($action2 == 'edit' && $convertedDate >= $affectationData['service']['date']['fin'])))
								{
									$erreur[13] = TRUE;
									$validDate = FALSE;
								}
								else if ($key == 'dateFin' && ((isset($_POST['dateDebut']) && $convertedDate <= DatetimeToTimestamp(FrenchdateToDatetime($_POST['dateDebut']))) || ($action2 == 'edit' && $convertedDate <= $affectationData['service']['date']['debut'])))
								{
									$erreur[13] = TRUE;
									$validDate = FALSE;
								}
								
								if ($validDate)
								{
									$sqlData[$key] = TimestampToDatetime($convertedDate);
								}
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
					if ($action2 == 'add')
					{
						if (isset($sqlData) && count($sqlData) > 0 && isset($sqlData['userId']) && isset($sqlData['dateDebut']) && isset($sqlData['dateFin']) && isset($sqlData['service']))
						{
							$sql = 'INSERT INTO affectationexterne (';
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
					
					else if ($action2 == 'edit')
					{
						if (isset($sqlData) && count($sqlData) > 1)
						{
							$sql = 'UPDATE affectationexterne SET ';
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
					else if ($action2 == 'delete')
					{
						if (isset($sqlData))
						{
							$sql = 'DELETE FROM affectationexterne WHERE id = :id';	
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
						unset($tempGET['action2']);
						unset($tempGET['id']);
						$url = ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action2=liste&id=';
						if (isset($userData)) { $url .= $userData['id']; } else if (isset($affectationData)) { $url .= $affectationData['user']['id']; }
						header('Location: '.$url);
					}
				}
		}
	}	
	
	/**
		2. Affichage
	**/
	
		displayErreur($erreur);
	
		// Affichage des erreurs
		
	
		// $action : home - Affiche le choix entre gestion d'un utilisateur où affectation en masse
		
		if ($action == "home")
		{
		?>
		<a href = "<?php echo $pageUtilisateurs; ?>"><?php echo LANG_ADMIN_AFFECTATIONS_MENU_AFFECTATIONONE; ?></a><br /><br />
		<a href = "<?php echo ROOT.CURRENT_FILE.'?page=affectations&action=batch'; ?>"><?php echo LANG_ADMIN_AFFECTATIONS_MENU_BATCH; ?></a>
<?php
		}
		
		// $action : batch - Affiche la page de traitement des csv
	
		else if ($action == "batch")
		{
			if ($action2 == 'import')
			{
				/* Si fichier CSV importé et lu */
				if (isset($affectationOk) && count($affectationOk) > 0)
				{

					// Affichage des erreurs
					if (isset($affectationOk['erreur']) && count($affectationOk['erreur']) > 0)
					{
						?>
						<h1><?php echo LANG_ERRORS; ?></h1>
						<table>
						
							<tr class = "headTR">
								<th><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NBETUDIANT; ?></th>
								<th><?php echo LANG_ADMIN_SERVICES_ID; ?></th>
								<th><?php echo LANG_ADMIN_AFFECTATIONS_DATE_DEBUT; ?></th>
								<th><?php echo LANG_ADMIN_AFFECTATIONS_DATE_FIN; ?></th>
								<th></th>
							</tr>
						<?php
						foreach($affectationOk['erreur'] AS $affectationId => $affectationErreur)
						{
							?>
							<tr class = "bodyTR">
								<td><?php echo $affectations[$affectationId][0]; ?></td>
								<td><?php echo $affectations[$affectationId][1]; ?></td>
								<td><?php echo $affectations[$affectationId][2]; ?></td>
								<td><?php echo $affectations[$affectationId][3]; ?></td>
								<td><?php displayErreur($affectationErreur); ?></td>
							</tr>
							<?php
						}
						?>
						</table>

						<?php
					}
					
					// Affichage des données à insérer
					if (isset($affectationOk['insert']) && count($affectationOk['insert']) > 0)
					{
						?>
						<h1><?php echo LANG_ADMIN_AFFECTATIONS_MENU_BATCH_TODO; ?></h1>
						<table>
						
							<tr class = "headTR">
								<th><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NOM.' '.LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PRENOM; ?></th>
								<th><?php echo LANG_ADMIN_SERVICES_NOM; ?></th>
								<th><?php echo LANG_ADMIN_AFFECTATIONS_DATE_DEBUT; ?></th>
								<th><?php echo LANG_ADMIN_AFFECTATIONS_DATE_FIN; ?></th>
							</tr>
						<?php
						foreach($affectationOk['insert'] AS $affectationId => $affectationData)
						{
							$userData = getUserData($affectationData['etudiant']);
							$serviceData = getServiceInfo($affectationData['service']);
							?>
							<tr class = "bodyTR">
								<td><?php echo $userData['nom'].' '.$userData['prenom']; ?></td>
								<td><?php echo $serviceData['FullName']; ?></td>
								<td><?php echo $affectationData['dateDebut']; ?></td>
								<td><?php echo $affectationData['dateFin']; ?></td>
							</tr>
							<?php
						}
						?>
						</table>
						<?php
					}
					
					if (count($affectationOk['insert']) == 0 && count($affectationOk['erreur']) == 0)
					{
						header('Location: '.ROOT.CURRENT_FILE.'?page=affectations&erreur='.serialize(array(16 => TRUE)));
					}
					
					?>
					<form method = "POST" action = "">
						<input type = "hidden" name = "valid" value = "1" />
						<input type = "submit" value = "Valider" />
					</form>
					<?php
				}
				
				/* Si importation effectué mais présence d'erreur */
				else if (isset($affectationsErreur) && count($affectationsErreur) > 0)
				{
					?>
					<h1><?php echo LANG_ADMIN_ERREUR; ?></h1>
					<table>
						<tr class = "headTR">
							<th><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NOM.' '.LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PRENOM; ?></th>
							<th><?php echo LANG_ADMIN_SERVICES_NOM; ?></th>
							<th><?php echo LANG_ADMIN_AFFECTATIONS_DATE_DEBUT; ?></th>
							<th><?php echo LANG_ADMIN_AFFECTATIONS_DATE_FIN; ?></th>
							<th></th>
						</tr>
					<?php
					foreach($affectationsErreur AS $affectationErreurId => $affectationErreurData)
					{
						$userData = getUserData($_SESSION['affectations']['data']['insert'][$affectationErreurId]['etudiant']);
						$serviceData = getServiceInfo($_SESSION['affectations']['data']['insert'][$affectationErreurId]['service']);
						
						?>
						<tr class = "bodyTR">
							<td><?php echo $userData['nom'].' '.$userData['prenom']; ?></td>
							<td><?php echo $serviceData['FullName']; ?></td>
							<td><?php echo $_SESSION['affectations']['data']['insert'][$affectationErreurId]['dateDebut']; ?></td>
							<td><?php echo $_SESSION['affectations']['data']['insert'][$affectationErreurId]['dateFin']; ?></td>
							<td><?php displayErreur($affectationErreurData); ?></td>
						</tr>
						<?php
					}
					?>
					</table>
					<?php
				}
				
				/* Si aucun fichier CSV importé */
				else
				{
				?>
				<p><?php echo LANG_ADMIN_AFFECTATIONS_MENU_BATCH_IMPORTFILE; ?></p>
				
				<a href = "<?php echo $pageServices.'page=liste&action=dl'; ?>"><?php echo LANG_ADMIN_AFFECTATIONS_MENU_BATCH_DOWNLOADSERVICE; ?></a><br />
				<a href = "<?php echo ROOT.CURRENT_FILE.'?page=affectations&action=batch&action2=dlcsv'; ?>"><?php echo LANG_ADMIN_AFFECTATIONS_MENU_BATCH_DOWNLOADCSV; ?></a><br /><br />
				
				<div class = "uploadBox">
					<form action="" method="post"  enctype="multipart/form-data">
						<input type="hidden" name="upload" value="ok">
						<input type="file" name="csv">
						<input type="submit" value="<?php echo LANG_FORM_SUBMIT_FILE; ?>">
					</form>
				</div>
				<?php
				}
			}
		}
		else if ($action == 'affectation')
		{
			if ($action2 == 'liste')
			{
				?>
				<h1><?php echo $userData['prenom'].' '.$userData['nom']; ?><?php if (isset($userData['promotion'])) { echo ' ('.$userData['promotion']['nom'].')'; } ?></h1>
				<?php
				// On affiche la liste des affectations
				if (isset($userData['service']) && count($userData['service']) > 0)
				{
					?>
					<table  style = "margin-top: 10px;">
						<tr class = "headTR">
							<td><?php echo LANG_ADMIN_SERVICES_NOM; ?></td>
							<td><<?php echo LANG_ADMIN_AFFECTATIONS_DATE_DEBUT; ?></td>
							<td><<?php echo LANG_ADMIN_AFFECTATIONS_DATE_FIN; ?></td>
							<td><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_ADMIN; ?></td>
						</tr>
					<?php
					foreach($userData['service'] AS $affectationData)
					{
						?>
						<tr class = "bodyTR">
							<td><?php echo $affectationData['FullName']; ?></td>
							<td><?php echo date('d/m/Y',$affectationData['dateDebut']); ?></td>
							<td><?php echo date('d/m/Y',$affectationData['dateFin']); ?></td>
							<td>
								<a href = "<?php echo ROOT.CURRENT_FILE.'?page=affectations&action=affectation&action2=view&id='.$affectationData['idAffectation']; ?>"><i class="fa fa-info"></i></a>
								<a href = "<?php echo ROOT.CURRENT_FILE.'?page=affectations&action=affectation&action2=edit&id='.$affectationData['idAffectation']; ?>"><i class="fa fa-pencil"></i></a>
								<a href = "<?php echo ROOT.CURRENT_FILE.'?page=affectations&action=affectation&action2=delete&id='.$affectationData['idAffectation']; ?>"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>
						<?php
					}
					?>
					</table>
					<br />
					<?php
				}
				?>
					<a class = "bouton" href = "<?php echo ROOT.CURRENT_FILE.'?page=affectations&action=affectation&action2=add&id='.$userData['id']; ?>" title = "<?php echo LANG_ADMIN_AFFECTATIONS_ADD_AFFECTATION; ?>"><i class="fa fa-plus-circle"></i></a>
			<?php
			}
			else if ($action2 == 'add' || $action2 == 'edit' || $action2 == 'view' || $action2 == 'delete')
			{
				if ($action2 != 'add')
				{
				$tempGET = $_GET;
				unset($tempGET['action2']);
				$tempGET2 = $tempGET;
				unset($tempGET2['id']);
				?>
				<h1><?php echo $affectationData['user']['prenom'].' '.$affectationData['user']['nom']; ?><?php if (isset($affectationData['user']['promotion'])) { echo ' ('.$affectationData['user']['promotion']['nom'].')'; } ?></h1>
				<div class = "barreNavigation">
					<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET2).'&action2=liste&id='.$affectationData['user']['id']; ?>"><i class="fa fa-list barreNavigationBouton"></i></a></a>
					<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action2=view'; ?>"><i class="fa fa-info barreNavigationBouton"></i></a></a>
					<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action2=edit'; ?>"><i class="fa fa-pencil barreNavigationBouton"></i></a></a>
					<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action2=delete'; ?>"><i class="fa fa-trash-o barreNavigationBouton"></i></a></a>
				</div>
				<?php
				}
				else
				{
					$tempGET = $_GET;
					unset($tempGET['action2']);
					?>
					<h1><?php echo $userData['prenom'].' '.$userData['nom']; ?><?php if (isset($userData['promotion'])) { echo ' ('.$userData['promotion']['nom'].')'; } ?></h1>
					<div class = "barreNavigation">
						<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&action2=liste&id='.$userData['id']; ?>"><i class="fa fa-list barreNavigationBouton"></i></a></a>
					</div>
					<?php
				}
				?>
				
				<form class = "formEvaluation" method = "POST">
				<fieldset>
					<!-- Nom du service -->
					<?php
					$listeServices = getServiceList();
					// Valeur à pré-sélectionner
					if (isset($_POST['service']) && isset($listeServices[$_POST['service']]))
					{
						$defaultValue = $_POST['service'];
					}
					else if (isset($affectationData['service']['id']) && isset($listeServices[$affectationData['service']['id']]))
					{
						$defaultValue = $affectationData['service']['id'];
					}
					?>
					<label for = "service"><?php echo LANG_ADMIN_SERVICES_NOM; ?></label>
					<select id = "service" name = "service" class = "<?php if ($action2 != 'edit' && $action2 != 'add') echo 'readonlyForm'; ?>" <?php if ($action2 != 'edit' && $action2 != 'add') echo 'disabled'; ?>>
						<?php
							foreach($listeServices AS $serviceId => $serviceInfo)
							{
							?>
								<option value = "<?php echo $serviceId; ?>"  <?php if (isset($defaultValue) && $serviceId == $defaultValue) { echo 'selected'; } ?> /><?php echo $serviceInfo['FullName']; ?></option>
							<?php
							}
						?>
					</select><br />
					<!-- Dates -->
					<label for = "dateDebut"><?php echo LANG_ADMIN_AFFECTATIONS_DATE_DEBUT; ?></label>
					<input type = "hidden" name = "dateDebut" value = "<?php if (isset($_POST['dateDebut'])) { echo $_POST['dateDebut']; } else if (isset($affectationData['date']['debut'])) { echo date('j/n/Y', $affectationData['date']['debut']); } ?>" />
					<input type = "button" style = "width: 300px;" class = "<?php if ($action2 != 'edit' && $action2 != 'add') echo 'readonlyForm'; ?>" id = "dateDebut" value = "<?php if (isset($_POST['dateDebut'])) { echo $_POST['dateDebut']; } else if (isset($affectationData['date']['debut'])) { echo date('j/n/Y', $affectationData['date']['debut']); } ?>" <?php if ($action2 != 'edit' && $action2 != 'add') { echo 'readonly'; } ?> /><br />

					<label for = "dateFin"><?php echo LANG_ADMIN_AFFECTATIONS_DATE_FIN; ?></label>
					<input type = "hidden" name = "dateFin" value = "<?php if (isset($_POST['dateFin'])) { echo $_POST['dateFin']; } else if (isset($affectationData['date']['fin'])) { echo date('j/n/Y', $affectationData['date']['fin']); } ?>" />
					<input type = "button" style = "width: 300px;" class = "<?php if ($action2 != 'edit' && $action2 != 'add') echo 'readonlyForm'; ?>" id = "dateFin" value = "<?php if (isset($_POST['dateFin'])) { echo $_POST['dateFin']; } else if (isset($affectationData['date']['fin'])) { echo date('j/n/Y', $affectationData['date']['fin']); }  ?>" <?php if ($action2 != 'edit' && $action2 != 'add') { echo 'readonly'; } ?> /><br />

					<?php
					if ($action2 != 'view')
					{
					?>
						<input type = "submit" id = "submit_<?php echo $action2; ?>" value = "<?php echo constant('LANG_ADMIN_AFFECTATIONS_FORM_SUBMIT_'.strtoupper($action2)); ?>" />
						<?php
					}
					?>
				</fieldset>
				</form>
				<?php
			}
		}
?>

<script>
	<?php
	if ($action2 == 'edit' || $action2 == 'add')
	{
		?>
			// Barre de recherche pour les services
			$('#service').chosen();
			
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
						console.log(el);
						el.val(date.toLocaleDateString());
						$('input[name="'+ el.attr('id')+'"]').val(date.toLocaleDateString()); // On met à jour la valeur de l'input caché correspondant
					})
				});
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
</script>