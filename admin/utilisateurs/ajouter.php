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
		25/04/15 - ajouter.php - Ali Bellamine
		Permet d'ajouter de nouveaux étudiants dans la plateforme
	*/
	
	/**
		1. Récupération des données
	**/
	
	$allowedAction = array('menu', 'importCSVFile');
	
	if (isset($_GET['action']) && in_array($_GET['action'], $allowedAction))
	{
		$action = $_GET['action'];
	}
	else
	{
		$action = 'menu';
	}
	
	/**
		2. Traitement des donnés
	**/

	if ($action == 'importCSVFile')
	{
		// Téléchargement du CSV vide
		if (isset($_GET['downloadCSV']))
		{
			$arrayCSV[] = array(LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_NOM, LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_PRENOM, LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_MAIL, LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_NBETU, LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_PROMOTION, LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_RANG);
			downloadCSV($arrayCSV, 'import.csv');
		}
		
		// Traitement des CSV importés

			// Si fichier CSV envoyé
			
			if (isset($_POST['upload']) && isset($_FILES['csv']) && $_POST['upload'] == 'ok')
			{
				$fileName = explode('.', $_FILES['csv']['name']);
				if (is_file($_FILES['csv']['tmp_name']) && $csv = fopen($_FILES['csv']['tmp_name'], 'r') && $fileName[count($fileName)-1] == 'csv')
				{
					// Récupération de la liste à partir du CSV
					$users = readCSV($_FILES['csv']['tmp_name'], ';', FALSE);
					
					// Corresponsance CSV -> Valeur
					$correspondance = array('nom' => 0,  'prenom' => 1, 'mail' => 2, 'nbEtu' => 3, 'promotion' => 4, 'rang' => 5);

					/* On parcours le fichier $users et :
					 *	   - On enregistre dans l'array $userOK['insert'] les valeurs  prêtes à être insérées
					 *	   - On enregistre dans l'array $userOK['update'] les valeurs  prêtes à être mises à jours
					 *    - On enregistre dans l'array $userOK['erreur'] les valeurs qui posent probleme et qui ne doivent pas être insérées
					 */
					 
					 $userOK['insert'] = array();
					 $userOK['update'] = array();
					 $userOK['erreur'] = array();
					 
					foreach ($users AS $usersId => $usersData)
					{					
						$userData = array();
						foreach ($usersData AS $key => $value)
						{
							// On transforme la structure des mails
							if (array_search($key, $correspondance) == 'mail')
							{
								$mailList = explode(';', $value);
								$value = array();
								foreach ($mailList AS $mailValue)
								{
									$value[] = $mailValue;
								}
							}
							
							$userData[array_search($key, $correspondance)] = $value;
						}
					
						// Nombre de champs
						
						if (count($usersData) >= 6)
						{
							$erreurUser = checkUserInsertData($userData ,array());
							if (count($erreurUser) == 0)
							{
								$userOK['insert'][$usersId] = $userData;
							}
							else
							{
								if (count($erreurUser) == 1 && isset($erreurUser['exist']))
								{
									$userOK['update'][$usersId] = $userData;
								}
								else
								{
									if (isset($erreurUser['exist'])) { unset($erreurUser['exist']); }
									$userOK['erreur'][$usersId] = $erreurUser;				
								}
							}
						}
					}
					
					$_SESSION['users'] = $userOK; // Permet de transmettre les infos à la page d'ajout des utilisateurs
				}
				else
				{
					$erreur[8] = TRUE;
				}
			}
			else if (isset($_POST['valid']) && $_POST['valid'] == 1)
			{
				// Liste les erreurs qui ont eu lieu
				$usersErreur = array();
				
				// On parcours l'array $_SESSION['users']['insert'] et on les insert
				if (isset($_SESSION['users']['insert']))
				{
					foreach ($_SESSION['users']['insert'] AS $userDataId => $userData)
					{
						$tempErreur = array();
						$tempErreur = checkUserInsertData($userData, $tempErreur); // On verifie la validité des données
						
						// On ajoute les données manquantes
						if (!isset($userData['prenom'])) { $userData['prenom'] = ''; }
						if (!isset($userData['promotion'])) { $userData['promotion'] = 'NULL'; }

						if (count($tempErreur) == 0)
						{
							$sql = 'INSERT INTO user (nbEtudiant, nom, prenom, mail, promotion, rang) VALUES (?, ?, ?, ?, ?, ?)';
							$res = $db -> prepare($sql);
							$resT = $res -> execute(array($userData['nbEtu'], strtoupper($userData['nom']), $userData['prenom'], serialize($userData['mail']), $userData['promotion'], $userData['rang']));
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
				}
				
				// On parcours l'array $_SESSION['users']['insert'] et on les insert
				if (isset($_SESSION['users']['update']))
				{
					foreach ($_SESSION['users']['update'] AS $userDataId => $userData)
					{
						$tempErreur = array();
						$tempErreur = checkUserInsertData($userData, $tempErreur); // On verifie la validité des données
						
						// On ajoute les données manquantes
						if (!isset($userData['prenom'])) { $userData['prenom'] = ''; }
						if (!isset($userData['promotion'])) { $userData['promotion'] = 'NULL'; }

						if (count($tempErreur) == 1 && isset($tempErreur['exist']))
						{
							// On récupère l'id de l'utilisateur
							$userId = getUserIdFromNbEtudiant($userData['nbEtu']);
							
							if (isset($userId) && is_numeric($userId) && count(checkUser($userId, array())) == 0)
							{
								$sql = 'UPDATE user SET nbEtudiant = ?, nom = ?, prenom = ?, mail = ?, promotion = ?, rang = ? WHERE id = ?';
								$res = $db -> prepare($sql);
								$resT = $res -> execute(array($userData['nbEtu'], strtoupper($userData['nom']), $userData['prenom'], serialize($userData['mail']), $userData['promotion'], $userData['rang'], $userId));
								if (!$resT)
								{
									$affectationsErreur[$affectationInsertId][14] = TRUE; // On enregistre l'erreur
								}
							}
							else
							{
								$affectationsErreur[$affectationInsertId][14] = TRUE; // On enregistre l'erreur						
							}
						}
						else
						{
							// On enregistre les erreurs
							if (isset($tempErreur['exist'])) { unset($tempErreur['exist']); }
							$affectationsErreur[$affectationInsertId] = $tempErreur;
						}
					}
				}
				
				// Si aucunes erreurs on redirige vers l'acceuil
				if (count($affectationsErreur) == 0)
				{
					header('Location: '.ROOT.CURRENT_FILE.'?page=liste&msg=LANG_ADMIN_AFFECTATIONS_BATCH_SUCCESS');
				}
			}
			else
			{
				$erreur[8] = TRUE;
			}
	}
	
	/**
		3. Affichage des données
	**/
	
		// Menu
		if ($action == 'menu')
		{
			$tempGET = $_GET;
			?>
			<div>
				<a href = "<?php $tempGET['page'] = 'profil'; $tempGET['action'] = 'add'; echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET); ?>"><?php echo LANG_ADMIN_UTILISATEURS_FORM_SUBMIT_ADD; ?></a><br /><br />
				<a href = "<?php $tempGET['page'] = 'ajouter'; $tempGET['action'] = 'importCSVFile'; echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET); ?>"><?php echo LANG_ADMIN_UTILISATEURS_MENU_IMPORTCSV; ?></a>
			</div>
			<?php
		}
		
		// Import du fichier CSV
		else if ($action == 'importCSVFile')
		{
			/* Si fichier CSV importé et lu */
			if (isset($userOK) && count($userOK) > 0)
			{
				// Affichage des erreurs
				if (isset($userOK['erreur']) && count($userOK['erreur']) > 0)
				{
					?>
					<h1><?php echo LANG_ERRORS; ?></h1>
					<table>					
						<tr class = "headTR">
							<th><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_NOM; ?></th>
							<th><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_PRENOM; ?></th>
							<th><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_MAIL; ?></th>
							<th><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_NBETU; ?></th>
							<th><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_PROMOTION; ?></th>
							<th><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_RANG; ?></th>
							<th></th>
						</tr>
					<?php
					foreach($userOK['erreur'] AS $userId => $userErreur)
					{
						?>
						<tr class = "bodyTR">
							<td>
								<?php 
									if (isset($users[$userId][$correspondance['nom']])) { 
										echo $users[$userId][$correspondance['nom']]; 
									}
								?>
							</td>
							<td>
								<?php 
									if (isset($users[$userId][$correspondance['prenom']])) { 
										echo $users[$userId][$correspondance['prenom']]; 
									}
								?>
							</td>
							<td>
								<?php 
								if (isset($users[$userId][$correspondance['mail']])) { 
									echo $users[$userId][$correspondance['mail']]; 
								} 
								?>
							</td>
							<td>
								<?php 
									if (isset($users[$userId][$correspondance['nbEtu']])) { 
										echo $users[$userId][$correspondance['nbEtu']]; 
									}
								?>
							</td>
							<td>
								<?php 
									if (isset($users[$userId][$correspondance['promotion']]) && count(checkPromotion($users[$userId][$correspondance['promotion']], array())) == 0) { 
										echo getPromotionData($users[$userId][$correspondance['promotion']])['nom']; 
									}
								?>
							</td>
							<td>
								<?php 
									if (isset($users[$userId][$correspondance['rang']]) && defined('LANG_RANG_VALUE_'.$users[$userId][$correspondance['rang']])) { 
										echo constant('LANG_RANG_VALUE_'.$users[$userId][$correspondance['rang']]);
									}
								?>
							</td>
							<td><?php displayErreur($userErreur); ?></td>
						</tr>
						<?php
					}
					?>
					</table>

					<?php
				}
				
				// Affichage des données à insérer et à mettre à jour
				if ((isset($userOK['insert']) && count($userOK['insert']) > 0) || (isset($userOK['update']) && count($userOK['update']) > 0))
				{
					foreach ($userOK AS $userOkName => $userOkData)
					{
					if ($userOkName == 'update' || $userOkName == 'insert')
					{
					?>
					<h1><?php echo constant('LANG_ADMIN_USERS_MENU_BATCH_TODO_'.strtoupper($userOkName)); ?></h1>
					<table>
					
						<tr class = "headTR">
							<th><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_NOM; ?></th>
							<th><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_PRENOM; ?></th>
							<th><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_MAIL; ?></th>
							<th><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_NBETU; ?></th>
							<th><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_PROMOTION; ?></th>
							<th><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_TABLE_RANG; ?></th>
						</tr>
					<?php
						foreach ($userOkData AS $userId => $userData)
						{
						?>
						<tr class = "bodyTR">
							<td><?php echo $userData['nom']; ?></td>
							<td>
								<?php 
									if (isset($userData['prenom'])) { 
										echo $userData['prenom']; 
									}
								?>
							</td>
							<td>
								<?php 
								$firstLoop = TRUE;
								foreach ($userData['mail'] AS $email)
								{
									if ($firstLoop) { $firstLoop = FALSE; } else { echo '<br />'; }
									echo $email;
								}
								?>
							</td>
							<td><?php echo $userData['nbEtu']; ?></td>
							<td>
								<?php 
									if (isset($userData['promotion'])) { 
										echo getPromotionData($userData['promotion'])['nom']; 
									}
								?>
							</td>
							<td><?php echo constant('LANG_RANG_VALUE_'.$userData['rang']); ?>
							</td>
						</tr>
						<?php
						}
					?>
					</table>
					<?php
					}
					}
				}
				
				if (count($userOK['insert']) == 0 && count($userOK['erreur']) == 0 && count($userOK['update']) == 0)
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
			<p><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_IMPORTFILE; ?></p>
			
			<a href = "<?php echo getPageUrl('adminUtilisateurs', array('page' => 'promotions', 'action' => 'dl')); ?>"><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_DOWNLOADPROMOTIONLIST; ?></a><br />
			<a href = "<?php echo ROOT.CURRENT_FILE.'?page=ajouter&action=importCSVFile&downloadCSV'; ?>"><?php echo LANG_ADMIN_UTILISATEURS_MENU_BATCH_DOWNLOADRAWCSV; ?></a><br /><br />
			
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
?>