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
		admin - 07/07/2015
		Ali Bellamine
		
		Permet de gérer les services détectées comme à problème
	*/
	
	// On vérifie que l'utilisateur à les droits d'accès à la page
	if ($_SESSION['rang'] < 3)
	{
		header('Location: '.ROOT.CURRENT_FILE.'?evaluationType='.$evaluationTypeData['id'].'&erreur='.serialize(array(7 => true)));
	}
	
	/**
		1. Récupération des données
	**/
	
		unset($_GET['msg']);
		unset($_GET['erreur']);
	
                // Filtre par data
                
                
		// Variable permettant la génération des fichiers
		$allowedDownloadAction = array(
			'CSVmoderateComment' => array('type' => 'CSV', 'comment' => TRUE, 'moderation' => TRUE),
			'CSVunmoderateComment' => array('type' => 'CSV', 'comment' => TRUE, 'moderation' => FALSE),
			'PDFnoComment' => array('type' => 'PDF', 'comment' => FALSE, 'moderation' => FALSE),
			'PDFmoderateComment' => array('type' => 'PDF', 'comment' => TRUE, 'moderation' => TRUE),
			'PDFunmoderateComment' => array('type' => 'PDF', 'comment' => TRUE, 'moderation' => FALSE),
			'all' => TRUE
		);
		
		// On récupère la variable action
		$allowedAction = array('view', 'add', 'edit', 'delete', 'mail', 'update');
		
		if (isset($_GET['action']) && in_array($_GET['action'], $allowedAction))
		{
			$action = $_GET['action'];
		}
		else
		{
			$action = 'list';
		}
	
		// On récupère la liste des campagnes d'évaluations
		$listeFiltre = eval_ccpc_getFilterList();
		
		// On récupère les donnée propre au filtre sélectionnée
		if (isset($_GET['filtreId']) && count(eval_ccpc_checkFiltre($_GET['filtreId'], array())) == 0)
		{
                    // Liste des années pour lesquelles il y a des données
                    $yearList = eval_ccpc_getFilterYearList($_GET['filtreId']);
                    
                    // L'année est sélectionnée par la variable annee, si elle est absente on prent l'année en cours, si elle vaut "all" on affiche tout
                    if (isset($_GET['selectDate']) && ($_GET['selectDate'] == 'all' || $yearList[$_GET['selectDate']] == 1)) {
                        $selectedYear = $_GET['selectDate'];
                    } else {
                        $selectedYear = date('Y', time());
                    }
                    if ($selectedYear == 'all') { 
                        $filtreData = eval_ccpc_getFilterDetails($_GET['filtreId'], TimestampToDatetime(0), TimestampToDatetime(time()));
                    } else {
                        $filtreData = eval_ccpc_getFilterDetails($_GET['filtreId'], $selectedYear.'-01-01', $selectedYear.'-12-31');
                    }
		}
		
	/**
		2. Traitement des données de formulaire
	**/
	$error = array();

	// Suppression d'un filtre
	if ($action == 'delete')
	{
		// URL où rediriger à la fin
		$tempGET = $_GET;
		unset($tempGET['filtreId']);
		unset($tempGET['action']);
		$redirectURL = ROOT.CURRENT_FILE.'?'.http_build_query($tempGET);
		
		if (isset($_GET['filtreId']) && count(eval_ccpc_checkFiltre($_GET['filtreId'])) == 0)
		{
			$sql = 'DELETE FROM eval_ccpc_filtres WHERE id = ?';
			$res = $db -> prepare($sql);
			$res -> execute(array($_GET['filtreId']));
			header('Location: '.$redirectURL);
		}
		else
		{
			header('Location: '.$redirectURL);
		}
	}
	
	// Mise à jour des données stages détectées par un filtre
	if ($action == 'update' && isset($filtreData))
	{
		// 1. On récupère la liste de tous les services
		$listeServices = getServiceList();
		
		// 2. Pour chaque service on teste le filtre sur une période de 12 mois
		
			// Détermination des dates minimales et maximales
			$DateMax = time();
			$DateMin = time()-365*24*3600;
			
			// On lance la recherche pour chaque service
			foreach ($listeServices AS $service)
			{
				$listePromotion = array();
				$listeDate = array();
				
				// On détermine la liste des promotions représentées dans le service
				$sql = 'SELECT DISTINCT promotion FROM `eval_ccpc_resultats` WHERE service = ?';				
				
				$res = $db -> prepare ($sql);
				$res -> execute(array($service['id']));
				
				while ($res_f = $res -> fetch())
				{
					$listePromotion[] = $res_f[0];
				}
				
				// On détermine les couples de dates à tester
				$sql = 'SELECT DISTINCT debutStage, finStage FROM `eval_ccpc_resultats` WHERE service = ? AND debutStage >= ? AND finStage <= ?';
				$res = $db -> prepare ($sql);
				$res -> execute(array($service['id'], TimestampToDatetime($DateMin), TimestampToDatetime($DateMax)));
				
				while ($res_f = $res -> fetch())
				{
					$listeDate[] = array('DateMin' => DatetimeToTimestamp($res_f['debutStage']), 'DateMax' => DatetimeToTimestamp($res_f['finStage']));
				}
				
				// On peux lancer le scan du service
				
				foreach ($listeDate AS $IntervalleDates)
				{
					foreach ($listePromotion AS $promotionId)
					{
						eval_ccpc_applyFilter($service['id'], $promotionId, $IntervalleDates['DateMin'], $IntervalleDates['DateMax']);
					}
				}
			}
			echo 'a';
			$tempGET = $_GET;
			unset($tempGET['action']);
			header('Location: '.ROOT.CURRENT_FILE.'?'.http_build_query($tempGET));
	}
	
	// Ajout et edition des filtres
	if (isset($_POST) && count($_POST) > 0)
	{
		$postData = array();
		
		if ($action == 'add' || $action == 'edit')
		{
			// On récupère l'image uploadée
			if (isset($_FILES['icone']) && $_FILES['icone']['error'] == UPLOAD_ERR_OK)
			{
				$imgName = uniqid().'_'.$_FILES['icone']['name'];
				move_uploaded_file($_FILES['icone']['tmp_name'], PLUGIN_PATH.'img/'.$imgName);
				
				if (is_file(PLUGIN_PATH.'img/'.$imgName))
				{
					$_POST['img']['path'] = PLUGIN_PATH.'img/'.$imgName;
					$_POST['img']['uri'] = ROOT.'evaluations/ccpc/img/'.$imgName;
				}
			}
		
			// On parcours $_POST
			foreach ($_POST AS $key => $value)
			{
				if ($key =='query' && $value != '')
				{
					$postData[$key] = $value;
				}
				else if ($key == 'mail_titre' || $key == 'mail_objet' || ($key == 'nom' && $value != ''))
				{
					$postData[$key] = htmLawed($value);
				}
				else if ($key == 'promotion' && $value == 1)
				{
					$postData[$key] = 1;
				}
				else if ($key == 'img')
				{
					// On vérifie que l'image est bien une image et de bonne dimensions
					$size = getimagesize($value["path"]);

					$size['width'] = $size[0];
					$size['height'] = $size[1];
					if (isset($size['mime']) && isset($size['width']) && isset($size['height']))
					{
						if ($size['mime'] != 'image/png' || $size['width'] != 128 || $size['width'] != 128)
						{
							$erreur['LANG_FORM_CCPC_ADMIN_FILTER_ERROR_FORMAT'] =  TRUE;
							unset($_POST[$key]);
							unset($_POST[$key]); // Afin d'éviter qu'elle soit utilisée comme preview
						}
						else
						{
							$postData['icone'] = $value['uri'];
						}
					}
				}
				else if ($key == 'currentImg' && !isset($_POST['img']))
				{
					$_POST['img']['uri'] = $value;
					$postData['icone'] = $value;
				}
			}
			
			if (!isset($postData['promotion'])) { $postData['promotion'] = 0; }
			
			// On génère la requête sql dans la BDD
			if ($action == 'add')
			{
				if (isset($postData['nom']) && isset($postData['query']) && isset($postData['mail_titre']) && isset($postData['mail_objet']) && isset($postData['promotion']))
				{
					$sql = 'INSERT INTO eval_ccpc_filtres (';
					$firstLoop = TRUE;
					foreach ($postData AS $key => $value)
					{
						if (!$firstLoop) { $sql .= ', '; }
						$sql .= $key;
						$firstLoop = FALSE;
					}
					$sql .= ') VALUES (';
					$firstLoop = TRUE;
					foreach ($postData AS $key => $value)
					{
						if (!$firstLoop) { $sql .= ', '; }
						$sql .= ':'.$key;
						$firstLoop = FALSE;
					}
					$sql .= ')';
				}
			}
			else if ($action == 'edit' && isset($filtreData))
			{
				$postData['id'] = $filtreData['id'];
			
				$sql = 'UPDATE eval_ccpc_filtres SET ';
				$firstLoop = TRUE;
				foreach ($postData AS $key => $value)
				{
					if ($key != 'id')
					{
						if (!$firstLoop) { $sql .= ', '; }
						$sql .= $key.' = :'.$key;
						$firstLoop = FALSE;
					}
				}
				$sql .= ' WHERE id = :id';
			}
			
			if (count($erreur) == 0 && isset($sql))
			{
				$res = $db -> prepare($sql);
				$res -> execute($postData);
				$tempGet = $_GET;
				unset($tempGet['action']);
				if ($action == 'edit' && $filtreData['query'] != $postData['query']) // Si on a effectué un edit, on met à jour tous les stages filtrés selon les nouvelles règles
				{
					foreach ($filtreData['detected'] AS $detected)
					{
						foreach ($detected AS $servicesDetected)
						{
							foreach ($servicesDetected AS $serviceDetectedData)
							{
								if ($serviceDetectedData['promotion'] != 0) { $promotion = $serviceDetectedData['promotion']; } else { $promotion = FALSE; }
								eval_ccpc_applyFilter($serviceDetectedData['service']['id'], $serviceDetectedData['promotion'], $serviceDetectedData['date']['debut'], $serviceDetectedData['date']['fin']);
							}
						}
					}
				}
				
				header('Location: '.ROOT.CURRENT_FILE.'?'.http_build_query($tempGet));
			}
		}
	}
	
	// Téléchargement des fiches PDF
	if (isset($_GET['dateDebut']) && isset($_GET['dateFin']) && isset($_GET['serviceId']) && (($_GET['serviceId'] == 'all' &&  isset($filtreData['detected'][$_GET['dateFin']][$_GET['dateDebut']])) || (is_numeric($_GET['serviceId']) && isset($filtreData['detected'][$_GET['dateFin']][$_GET['dateDebut']][$_GET['serviceId']]) && count($filtreData['detected'][$_GET['dateFin']][$_GET['dateDebut']][$_GET['serviceId']]) > 0)))
	{
		
		if (isset($_GET['download']) && isset($allowedDownloadAction[$_GET['download']]))
		{
			$action = $allowedDownloadAction[$_GET['download']];
			$serviceId = $_GET['serviceId'];
			if (isset($filtreData['promotion']) && count(checkPromotion($filtreData['promotion'], array())) == 0)
			{
				$promotion = $filtreData['promotion'];
			}
			else
			{
				$promotion = FALSE;
			}
			
			// Pour un service sélectionné
			if (is_numeric($serviceId) && count (checkService($serviceId, array())) == 0)
			{
				// On crée le fichier
				if ($action['type'] == 'CSV')
				{
					downloadFILE(generateCSV(getEvaluationCCPCFullData($serviceId, $promotion, $_GET['dateDebut'], $_GET['dateFin'], $action['moderation']), TRUE)['csvPath'], getServiceInfo($serviceId)['FullName'].'.csv');
				}
				else if ($action['type'] == 'PDF')
				{
					downloadFILE(generatePDF(getEvaluationCCPCFullData($serviceId, $promotion, $_GET['dateDebut'], $_GET['dateFin'], $action['moderation']), $action['comment'], TRUE)['pdfPath'], getServiceInfo($serviceId)['FullName'].'.pdf');				
				}
			}
			else if ($serviceId == 'all')
			{
				// On crée l'archive
				$zip = new ZipArchive(); 
				$zipPath = PLUGIN_PATH.'cache/'.$filtreData['nom'].'_'.date('Y-d-m',$_GET['dateDebut']).'_'.date('Y-d-m',$_GET['dateFin']).'.zip';
				if($zip->open($zipPath, ZipArchive::CREATE) === true)
				{
					foreach ($filtreData['detected'][$_GET['dateFin']][$_GET['dateDebut']] AS $serviceDetected)
					{
						$serviceData = getServiceInfo($serviceDetected['service']['id']);
						
						// On crée les fichiers
						$csvPath = generateCSV(getEvaluationCCPCFullData($serviceData['id'], $promotion, $_GET['dateDebut'], $_GET['dateFin'], TRUE), TRUE)['csvPath'];
						$pdfPath = generatePDF(getEvaluationCCPCFullData($serviceData['id'], $promotion, $_GET['dateDebut'], $_GET['dateFin'], TRUE), TRUE, TRUE)['pdfPath'];
						
						// On les ajoute à l'archive
						if ($zip->addEmptyDir ($serviceData['FullName']))
						{
							$zip -> addFile($csvPath, $serviceData['FullName'].'/'.$serviceData['FullName'].'.csv');
							$zip -> addFile($pdfPath, $serviceData['FullName'].'/'.$serviceData['FullName'].'.pdf');
						}
					}
					
					$zip->close();
					downloadFILE($zipPath);
				}
			}
		}
	}
	
	// Envoie des emails
	
	if ($action == 'mail' && isset($_POST['action']) && $_POST['action'] == 'envoyerMail')
	{
		if (isset($_GET['dateDebut']) && isset($_GET['dateFin']) && isset($filtreData['detected'][$_GET['dateFin']][$_GET['dateDebut']]))
		{
			/**
				Contenue des messages à envoyer
			**/
			
			$erreur = array();
			
			$_SESSION['sendMail']['settings']['objet'] = htmLawed($_POST['titre']);
			$_SESSION['sendMail']['settings']['message'] = htmLawed($_POST['message']);
			
				/*
					Pièces jointes
				*/
				
				$allowedJoin = $allowedDownloadAction;
				unset($allowedJoin['all']);
				$allowedJoin['none'] = FALSE;

				// Pour le PDF	
				if (isset($_POST['pdfJoin']) && isset($allowedJoin[$_POST['pdfJoin']]) && ($_POST['pdfJoin'] == 'none' || $allowedJoin[$_POST['pdfJoin']]['type'] == 'PDF'))
				{
					if ($_POST['pdfJoin'] != 'none')
					{
						foreach ($filtreData['detected'][$_GET['dateFin']][$_GET['dateDebut']] AS $serviceDetected)
						{
							$_SESSION['sendMail']['settings']['pdf'] = generatePDF(getEvaluationCCPCFullData($serviceDetected['service']['id'], $serviceDetected['promotion'], $_GET['dateDebut'], $_GET['dateFin'], $allowedJoin[$_POST['pdfJoin']]['moderation']), $allowedJoin[$_POST['pdfJoin']]['comment'], TRUE)['pdfPath'];
						}
					}
				}
				else { $erreur['LANG_ERROR_CCPC_UNKNOWN'] = TRUE; }
				
				// Pour le CSV	
				if (isset($_POST['csvJoin']) && isset($allowedJoin[$_POST['csvJoin']]) && ($_POST['csvJoin'] == 'none' || $allowedJoin[$_POST['csvJoin']]['type'] == 'CSV'))
				{
					if ($_POST['csvJoin'] != 'none')
					{
						foreach ($filtreData['detected'][$_GET['dateFin']][$_GET['dateDebut']] AS $serviceDetected)
						{
							$_SESSION['sendMail']['settings']['csv'] = generateCSV(getEvaluationCCPCFullData($serviceDetected['service']['id'], $serviceDetected['promotion'], $_GET['dateDebut'], $_GET['dateFin'], $allowedJoin[$_POST['csvJoin']]['moderation']), TRUE)['csvPath'];
						}
					}
				}
				else { $erreur['LANG_ERROR_CCPC_UNKNOWN'] = TRUE; }
			
			/**
				Liste des messages à envoyer
			**/
			
			if (count($erreur) == 0)
			{
				foreach ($filtreData['detected'][$_GET['dateFin']][$_GET['dateDebut']] AS $serviceDetected)
				{
					$serviceData = getServiceInfo($serviceDetected['service']['id']);
					$chefData = getUserData($serviceData['chef']['id']);
					
					$_SESSION['sendMail']['current'][$chefData['id']] = $chefData; // On ajoute les envoie à faire dans current
				}
				
				$_SESSION['sendMail']['nb'] = count($_SESSION['sendMail']['current']); // Liste du nombre d'envoie à faire
				$_SESSION['sendMail']['done'] = array(); // Liste des envoie déjà effectués */
			}
			else
			{
				unset($_SESSION['sendMail']);
			}
		}
	}
	else if (isset($_POST['action']) && $_POST['action'] == 'annulerEnvoie')
	{
		unset($_SESSION['sendMail']);
	}	
	
	/**
		3. Affichage des données
	**/
	
	?>
	<div id  = "evalccpcContentUnique">
		<!-- Barre du haut -->
		<div id = "evalccpcAdminSelectFiltre">
			<?php 
			if ($action == 'list') {
			?>
			<form method = "GET">
				<!-- Bouton de retour en arrière -->
				<?php
					$tempGet = $_GET;
					unset($tempGet['action']);
					unset($tempGet['page']);
					
					$tempGetInnerPage = $_GET;
					unset($tempGetInnerPage['action']);
					unset($tempGetInnerPage['msg']);
					unset($tempGetInnerPage['erreur']);	
				?>
				<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGet); ?>"><span style = "font-size: 1.2em; padding: 5px;"><i class="fa fa-arrow-circle-left"></i></span></a>				
				<!-- On met toutes les variables $_GET en hidden -->
				<?php
					foreach ($_GET AS $key => $value)
					{
						if ($key != 'filtreId')
						{
							?>
								<input type = "hidden" name = "<?php echo $key; ?>" value = "<?php echo $value; ?>" />
							<?php
						}
					}
				?>
				<select name = "filtreId">
					<option><?php echo LANG_FORM_CCPC_ADMIN_SELECT_FILTER; ?></option>
					<?php
					foreach ($listeFiltre AS $filtre)
					{
						?>
						<option value = "<?php echo $filtre['id']; ?>" <?php if (isset($_GET['filtreId']) && $_GET['filtreId'] == $filtre['id']) { echo 'selected'; } ?>><?php echo $filtre['nom']; ?></option>
						<?php
					}
					?>
				</select>
				
				<!-- Bouton d'ajout de filtre -->
				
				<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGetInnerPage).'&action=add'; ?>"><span style = "font-size: 1.2em; padding: 5px;"><i class="fa fa-plus-circle"></i></span></a>
				<!-- Bouton d'édition et de suppression de filtre -->
				<?php
					if (isset($_GET['filtreId']))
					{
						?>
						<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGetInnerPage).'&action=edit'; ?>"><span style = "font-size: 1.2em; padding: 5px;"><i class="fa  fa-pencil-square-o"></i></span></a>
						<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGetInnerPage).'&action=delete'; ?>"><span style = "font-size: 1.2em; padding: 5px;"><i class="fa  fa-trash-o"></i></span></a>
						<?php
					}
				?>
				<!-- Bouton d'actualisation des données d'un filtre -->
				<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGetInnerPage).'&action=update'; ?>"><span style = "font-size: 1.2em; padding: 5px;"><i class="fa fa-refresh"></i></span></a>
			</form>
			<?php
			}
			else if ($action == 'add' || $action == 'edit' || $action == 'mail')
			{
				$tempGet = $_GET;
				unset($tempGet['action']);
				unset($tempGet['msg']);
				unset($tempGet['erreur']);				
				?>
				<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGet); ?>"><span style = "font-size: 1.2em; padding: 5px;"><i class="fa fa-arrow-circle-left"></i></span></a>
				<?php
			}
			?>
		</div>
		
		<!-- Div du bas -->
		<div id = "evalccpcAdminContent">
			<?php
				/**
					Affichage des erreurs
				**/
				
				if (isset($erreur) && count($erreur) > 0) {
				?>
				<div class = "erreur">
					<ul>
					<?php
					foreach ($erreur AS $error => $errorValue)
					{
					?>
						<li><?php echo constant($error); ?></li>
					<?php 
					}
					?>
					</ul>
				</div>
				<?php
				}
			
				if ($action == 'add' || $action == 'edit')
				{
				?>
				<h1><?php echo LANG_FORM_CCPC_ADMIN_FILTER_ADD_TITLE; ?></h1>
				<form method = "POST" enctype="multipart/form-data">

					<!-- Nom du filtre -->
					<label for = "nom"><?php echo LANG_FORM_CCPC_ADMIN_FILTER_FILTER_NAME; ?> :</label>
					<input type = "text" id = "nom"  style = "width: 90%;" name = "nom" value = "<?php if (isset($_POST['nom'])) { echo $_POST['nom']; } else if (isset($filtreData['nom'])) { echo $filtreData['nom']; } ?>" required /><br />

					<!-- Règle de filtrage-->
					<label for = "query"><?php echo LANG_FORM_CCPC_ADMIN_FILTER_FILTER_QUERY; ?> : <i class="fa fa-question-circle help" data-featherlight = ".helpBoxFiltrage"></i></label>
					<div class="helpBox helpBoxFiltrage"><?php echo LANG_FORM_CCPC_ADMIN_SELECT_FILTER_ADD_HELP_QUERY; ?></div>
					
					<input type = "text" id = "query" name = "query" style = "width: 90%;" value = "<?php if (isset($_POST['query'])) { echo $_POST['query']; } else if (isset($filtreData['query'])) { echo $filtreData['query']; } ?>" required /><br />

					<!-- Titre des mails automatique -->
					<label for = "mail_titre"><?php echo LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_OBJECT; ?> :</label>
					<input type = "text" id = "mail_titre" name = "mail_titre"  style = "width: 90%;" value = "<?php if (isset($_POST['mail_titre'])) { echo $_POST['mail_titre']; } else if (isset($filtreData['mail']['titre'])) { echo $filtreData['mail']['titre']; } ?>" required /><br />

					<!-- Objet des mails automatique -->
					<label for = "mail_objet" style = "float: none;"><?php echo LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_CONTENT; ?> :</label>
					<textarea id = "mail_objet" name = "mail_objet"  style = "width: 90%;" required ><?php if (isset($_POST['mail_objet'])) { echo $_POST['mail_objet']; } else if (isset($filtreData['mail']['objet'])) { echo $filtreData['mail']['objet']; } ?></textarea><br />

					<!-- Restreint à une promotion-->
					<label for = "promotion">
						<input type = 'checkbox'  id = "promotion" value = "1" name = "promotion" <?php if (count($_POST) == 0 && isset($_POST['promotion']) && $_POST['promotion'] == 1) { echo 'checked'; } else if (isset($filtreData['promotion']) && $filtreData['promotion'] == 1) { echo 'checked'; } ?>>						<?php echo LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_PROMOTION; ?><br />
					</label><br />

					<!-- Icone -->
					<label for "icon"><?php echo LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_ICON; ?></label>
					<input type = "hidden" name = "currentImg" value = "<?php if (isset($_POST['img']['uri'])) { echo $_POST['img']['uri']; } else if (count($_POST) == 0 && isset($filtreData['icone'])) { echo $filtreData['icone']; } ?>" />
					<input type = "file"  id = "icone" name = "icone" /><br />
					<i><?php echo LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_RULES; ?></i><br />
					
					<?php if (isset($_POST['img']['uri']) || (count($_POST) == 0 && isset($filtreData['icone']))) { ?>
						<img src = "<?php if (isset($_POST['img']['uri'])) { echo $_POST['img']['uri']; } else if (isset($filtreData['icone'])) { echo $filtreData['icone']; } ?>" />
					<?php } ?>

					<input type = "submit" formnovalidate="true" />
				</form>
				<?php
				}
				else if ($action == 'list')
				{
					if (isset($filtreData))
					{
						?>
							<!-- Liste des périodes de stage à gauche -->
							<div id = "listePeriodeStage">
								<!-- Filtre par année -->

                                                                <form method = "GET">
                                                                   <!-- On conserve les variables actuelles dans un champs hidden -->
                                                                    <?php
                                                                    foreach ($_GET AS $key => $value) {
                                                                        if ($key != 'dateDebut' && $key != 'dateFin' && $key != 'serviceId' && $key != 'selectDate') {
                                                                            ?>
                                                                                <input type ="hidden" name ="<?php echo $key; ?>" value ="<?php echo $value; ?>" />
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                                
                                                                    <select name = "selectDate" id = "selectDate">
                                                                            <option value = "all">Toutes les périodes</option>
                                                                            <?php
                                                                                    foreach ($yearList AS $Annee => $AnneeValue)
                                                                                    {
                                                                                            ?>
                                                                                                    <option value = "<?php echo $Annee; ?>" <?php if ($selectedYear == $Annee) { echo 'selected'; } ?>><?php echo $Annee; ?></option>
                                                                                            <?php
                                                                                    }
                                                                            ?>
                                                                    </select>
                                                                </form>
								
								<ul>
								<?php
									// Création des liens
									$tempGET = $_GET;
									unset($tempGET['dateDebut']);
									unset($tempGET['dateFin']);
									
									// Garde en mémoire la liste des combinaisons de dates affichés, au format DateDebut-DateFin
									$listDisplayed = array();
									
									if (isset($filtreData['detected']))
									{
										foreach ($filtreData['detected'] AS $value)
										{
											foreach ($value AS $value2)
											{
												foreach ($value2 AS $service)
												{
													if (!isset($listDisplayed[$service['date']['debut'].'-'.$service['date']['fin']]))
													{
														?>
															<li class = "<?php if (isset($_GET['dateDebut']) && isset($_GET['dateFin']) && $_GET['dateDebut'] == $service['date']['debut'] && $_GET['dateFin'] == $service['date']['fin']) { echo 'selected'; } ; ?>" data-Annee = "<?php echo date('Y', $service['date']['fin']); ?>"><a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&dateDebut='.$service['date']['debut'].'&dateFin='.$service['date']['fin']; ?>"><?php echo date('d/m/Y', $service['date']['debut']).' - '.date('d/m/Y', $service['date']['fin']); ?></a></li>
														<?php
														$listDisplayed[$service['date']['debut'].'-'.$service['date']['fin']] = TRUE;
													}
												}
											}
										}
									}
								?>
								</ul>
							</div>
							<div id = "listeServices">					
								<?php
									if (isset($_GET['dateDebut']) && isset($_GET['dateFin']) && isset($filtreData['detected'][$_GET['dateFin']][$_GET['dateDebut']]))
									{
										?>
										<!-- Toolbox -->
										<div id = "toolbox">
											<!-- Télécharger toutes les données au format ZIP -->
											<a href="<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($_GET).'&serviceId=all&download=all'; ?>"><span style="font-size: 1.2em; padding: 5px;"><i class="fa fa-download"></i></span></a>
											
											<!-- Envoyer un mail avec les données d'évaluation -->
											<a href="<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($_GET).'&serviceId=all&action=mail'; ?>"><span style="font-size: 1.2em; padding: 5px;"><i class="fa fa-envelope-o"></i></span></a>
										</div>
										
										<table>
										<tr class = "headTR">
											<td><?php echo LANG_FORM_CCPC_FILTER_SERVICE_TITLE; ?></td>
											<td>PDF</td>
											<td>CSV</td>
											<td></td>
										</tr>
										<?php
										foreach ($filtreData['detected'][$_GET['dateFin']][$_GET['dateDebut']] AS $serviceDetection)
										{
											// On détermine le lien à générer
											$linkGetData = array('evaluationType' => 1, 'service' => $serviceDetection['service']['id'], 'FILTER' => array('date' => array('min' => $serviceDetection['date']['debut'], 'max' => $serviceDetection['date']['fin'])));
											
											$serviceData = getServiceInfo($serviceDetection['service']['id']); // Infos sur le service											
											?>
												<tr class = "bodyTR" syle = "cursor: auto;">
													<td><?php echo $serviceData['FullName']; ?></td>
													<td><a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($_GET).'&serviceId='.$serviceData['id'].'&download=PDFnoComment'; ?>"><?php echo LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_NOCOMMENT; ?></a> - <a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($_GET).'&serviceId='.$serviceData['id'].'&download=PDFmoderateComment'; ?>"><?php echo LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_MODERATECOMMENT; ?></a> - <a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($_GET).'&serviceId='.$serviceData['id'].'&download=PDFunmoderateComment'; ?>"><?php echo LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_UNMODERATECOMMENT; ?></a></td>
													<td><a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($_GET).'&serviceId='.$serviceData['id'].'&download=CSVmoderateComment'; ?>"><?php echo LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_MODERATECOMMENT; ?></a> - <a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($_GET).'&serviceId='.$serviceData['id'].'&download=CSVunmoderateComment'; ?>"><?php echo LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_UNMODERATECOMMENT; ?></a></td>
													<td><a href = "<?php echo getPageUrl('evalView', $linkGetData); ?>"><i class="fa fa-bar-chart"></i></a></td>
												</tr>
											<?php
										}
										?>
										</table>
										<?php
									}
								?>
							</div>
						<?php						
					}
				}
				else if ($action == 'mail')
				{
					?>
					<div id = "sendMailBox">
					<?php
					if (!isset($_SESSION['sendMail']) || (isset($_SESSION['sendMail']) && isset($_SESSION['sendMail']['current']) && count($_SESSION['sendMail']['current']) == 0))
					{
						?>
						<div>
							<form method = "POST">
								<label for = "titre">Objet</label>
								<input type = "text" id = "titre" name  = "titre" style = "width: 90%;" value = "<?php if (isset($_POST['tire'])) { echo $_POST['titre']; } else { echo $filtreData['mail']['titre']; } ?>" /><br />
								
								<label for = "message" style = "float: none;">Message</label>
								<textarea name = "message" style = "width: 90%;" id = "message"><?php if (isset($_POST['message'])) { echo $_POST['message']; } else { echo $filtreData['mail']['objet']; } ?></textarea>
								
								<label>Pièces jointe :</label><br />
								
								<label for = "pdfnone"><input type = "radio" name = "pdfJoin" id = "pdfnone" value = "none" />Aucun PDF</label><br />
								<label for = "pdfnoComment"><input type = "radio" name = "pdfJoin" id = "pdfnoComment" value = "PDFnoComment" checked />PDF : <?php echo LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_NOCOMMENT; ?></label><br />
								<label for = "pdfmoderateComment"><input type = "radio" name = "pdfJoin" id = "pdfmoderateComment" value = "PDFmoderateComment" />PDF : <?php echo LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_MODERATECOMMENT; ?></label><br />
								<label for = "pdfunmoderateComment"><input type = "radio" name = "pdfJoin" id = "pdfunmoderateComment" value = "PDFunmoderateComment" />PDF : <?php echo LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_UNMODERATECOMMENT; ?></label><br /><br />
								
								<label for = "csvnone"><input type = "radio" name = "csvJoin" id = "csvnone" value = "none" checked />Aucun CSV</label><br />
								<label for = "csvmoderateComment"><input type = "radio" name = "csvJoin" id = "csvmoderateComment" value = "CSVmoderateComment" />CSV : <?php echo LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_MODERATECOMMENT; ?></label><br />
								<label for = "csvunmoderateComment"><input type = "radio" name = "csvJoin" id = "csvunmoderateComment" value = "CSVunmoderateComment" />CSV : <?php echo LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_UNMODERATECOMMENT; ?></label><br /><br />
								
								<input type = "hidden" name = "action" value = "envoyerMail" />
								<input type = "submit" formnovalidate="true" value = "<?php echo LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_SEND; ?>" />
							</form>
						</div>
						<?php
					}
					else
					{
						$erreur = array();
						
						$n = 0;
						$mailTitle = $_SESSION['sendMail']['settings']['objet'];
						$mailBody = $_SESSION['sendMail']['settings']['message'];
						
						foreach ($_SESSION['sendMail']['current'] AS $userMail)
						{
							if ($n == 30) { break; } // Arrête au bout de 30 mails envoyés
							
							// Pièces jointes
							$attach = array();
							
							if (isset($_SESSION['sendMail']['settings']['pdf']))
							{
								$attach[] = array('name' => 'evaluation.pdf', 'path' => $_SESSION['sendMail']['settings']['pdf']);
							}
							if (isset($_SESSION['sendMail']['settings']['csv']))
							{
								$attach[] = array('name' => 'evaluation.csv', 'path' => $_SESSION['sendMail']['settings']['csv']);
							}
							
							// On met en forme la liste des email à contacter
							$mailToSend = array();
							foreach ($userMail['mail'] AS $email)
							{
								$mailToSend[$email] = $userMail['prenom'].' '.$userMail['nom'];
							}
							
							// Envoie un mail
							if (isset($mailToSend) && count($mailToSend) > 0)
							{
								$erreur[$userMail['id']] = sendMail($mailToSend,$mailTitle,$mailBody,array(), $attach); // On envoie le mail et on stocke l'erreur dans un array propre au mail si il y a une erreur
								
								// On retire l'email de la liste
								if (!isset($erreur[$userMail['id']]) || count($erreur[$userMail['id']]) == 0)
								{
									$_SESSION['sendMail']['done'][$userMail['id']] = $userMail;
									unset($_SESSION['sendMail']['current'][$userMail['id']]);
									if (isset($erreur[$userMail['id']])) { unset($erreur[$userMail['id']]); } // On supprime l'erreur
								}
								
								$n++; // Incrémente le nombre de mail envoyés 
							}
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
							$tempGET = $_GET;
							unset($tempGET['action']);
							unset($tempGET['serviceId']);
							header('Location: '.ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&msg=LANG_SUCCESS_MAIL_SEND');
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
									<td><?php echo $_SESSION['sendMail']['current'][$erreurId]['mail']; ?></td>
									<td><?php displayErreur($erreurData); ?></td>
								</tr>
								<?php
							}
							?>
							</table>
							<br />
							Actualisez la page pour continuer l'envoi
							
							<!-- Bouton pour la suite -->
							<div>
								<form method = "POST">
									<input type = "hidden" name = "action" value = "annulerEnvoie" />
									<input type = "submit" value = "Annuler l'envoie" />
								</form>
							</div>
							
						<?php
						}
					}				
					?>
					<?php
				}
			?>
		</div>
	</div>

<script>
	<!-- Message d'aide -->
	$('.help').on('click', function(){
		// Récupération du texte à afficher
		$(this).featherlight(content);
	});
	
	<!-- Envoie des formulaires -->
	$('#evalccpcAdminSelectFiltre select').on('change', function(){
		$(this).parent().submit();
	});
	
	$('.fa-trash-o').on('click', function(e){
		if (!confirm('Confirmer la suppression du filtre et des données associées ?'))
		{
			e.preventDefault();
		}
	});
	
	<!-- Filtre par date -->
 	$('#selectDate').on('change', function(){
		$(this).parent().submit();
	});
	
	<!-- Editeur de texte -->
	
	<?php if ($action == 'add' || $action == 'edit' || $action == 'mail')
	{
		if ($action == 'add' || $action == 'edit')
		{
			$selector = "#mail_objet";
		}
		else if ($action == 'mail')
		{
			$selector = '#message';
		}
	?>
	tinymce.init({
		selector: "<?php echo $selector; ?>",
		menubar: false,
		statusbar: false
	});
	<?php
	}
	?>
</script>