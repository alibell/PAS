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
		admin - 11/03/2016
		Ali Bellamine
		
		Permet de gérer l'envoi de campagnes de mails aux services
	*/
	
	// On vérifie que l'utilisateur à les droits d'accès à la page
	if ($_SESSION['rang'] < 3)
	{
		header('Location: '.ROOT.CURRENT_FILE.'?evaluationType='.$evaluationTypeData['id'].'&erreur='.serialize(array(7 => true)));
	}
	
	/**
		1. Traitement des données / requêtes
	**/
	
		// Création d'une campagne d'envoie d'email
		if (isset($_POST['AJAX']) && isset($_POST['action']) && $_POST['action'] == 'addMail')
		{
			ob_end_clean();
			
			// Titre du mail
			$title = 'Test';
				
			// Objet du mail
			$object = 'Test mail';
			
			// Contenu du mail
			$message = "Test 3";
				
			// Pièce jointe
					
				// On génère le PDF
				$pdfPath = generatePDF(getEvaluationCCPCFullData($_POST['service'], $_POST['promotion'], $_POST['debutStage'], $_POST['finStage'], TRUE), FALSE, TRUE)['pdfPath'];
				if (!file_exists(PLUGIN_PATH.'attachments/'.basename($pdfPath)) || !is_file(PLUGIN_PATH.'attachments/'.basename($pdfPath)))
				{
					copy($pdfPath, PLUGIN_PATH.'attachments/'.basename($pdfPath));
				}
								
			$attachments = array('Evaluations.pdf' => array('path' => PLUGIN_PATH.'attachments/'.basename($pdfPath), 'url' => ROOT.'evaluations/ccpc/attachments/'.basename($pdfPath)));
				
			// Destinataire
			$chefId = getServiceInfo($_POST['service'])['chef']['id'];
			// On crée le mail
			addMailCampagne($_POST['codeCampagne'], $title, $chefId, $object, $message, $attachments);
			
			exit();
		}
	
	/**
		2. Récupération de la liste des campagnes de mails envoyés
	**/

		// Récupération de la liste des années
		$yearList = array();
		$campaignList = array();
		$campaignPossibilities = array();
		
		// -> Depuis les campagnes déjà lancées
		$sql = 'SELECT EXTRACT(YEAR FROM date) FROM mail WHERE codeCampagne LIKE \'EVALCCPC%\' GROUP BY EXTRACT(YEAR FROM date) ORDER BY EXTRACT(YEAR FROM date) DESC';
		$res = $db -> query($sql);
		while ($res_f = $res -> fetch())
		{
			$yearList[$res_f[0]] = $res_f[0];
		}
		
		// -> Depuis les données d'évaluation disponibles
		$sql = 'SELECT DISTINCT EXTRACT(YEAR FROM finStage) FROM eval_ccpc_resultats ORDER BY `finStage` DESC';		
		$res = $db -> query($sql);
		while ($res_f = $res -> fetch())
		{
			$yearList[$res_f[0]] = $res_f[0];
		}
		
		// Année sélectionnée
		if (isset($_GET['year']) && isset($yearList[$_GET['year']]))
		{
			$year = $_GET['year'];
		}
		else
		{
			$year = date('Y', time()); // Maintenant
			$yearList[$year] = date('Y', time());
		}
		
		krsort($yearList);
		
		// Récupération de la liste des campagnes de mail lancés
		if ($year != FALSE)
		{
			$sql = 'SELECT codeCampagne codeCampagne FROM mail WHERE codeCampagne LIKE \'EVALCCPC%\' AND EXTRACT(YEAR FROM date) = ? GROUP BY codeCampagne ORDER BY date DESC';
			$res = $db -> prepare($sql);
			$res -> execute(array($year));
			while ($res_f = $res -> fetch())
			{
				// On récupère les informations sur la campagne
				$campaignList[$res_f['codeCampagne']] = getMailCampaignData($res_f['codeCampagne']);
				
				// On extrait les infos propres au code campagne : on les met dans $campaignList[codeCampagne]['evaluationData']
				$tempCodeCampagne = explode('-', $res_f['codeCampagne']);
				$campaignList[$res_f['codeCampagne']]['evaluationData']['date']['debut'] = $tempCodeCampagne[1];
				$campaignList[$res_f['codeCampagne']]['evaluationData']['date']['fin'] = $tempCodeCampagne[2];
				$campaignList[$res_f['codeCampagne']]['evaluationData']['promotion'] = getPromotionData($tempCodeCampagne[3]);
			}
		}
		
		// Récupération de la liste des campagnes de mails pouvant être envoyées
		
		$sql = 'SELECT DISTINCT `debutStage`, `finStage`, `promotion` FROM eval_ccpc_resultats WHERE EXTRACT(YEAR FROM finStage) = ? ORDER BY `finStage` DESC, `debutStage` DESC, `promotion` DESC';
		$res = $db -> prepare($sql);
		$res -> execute(array($year));
		
		while ($res_f = $res -> fetch())
		{
			// Récupération de la liste des services à contacter
			$sql = 'SELECT e.service service FROM eval_ccpc_resultats e WHERE e.debutStage = ? AND e.finStage = ? AND e.promotion = ? GROUP BY e.service';
			$res2 = $db -> prepare ($sql);
			$res2 -> execute(array($res_f['debutStage'], $res_f['finStage'], $res_f['promotion']));
			
			// CodeCampaign
				
				// Liste des codes campagne existant pour cette période
				$listeCode = array();
				$sql = 'SELECT DISTINCT codeCampagne FROM `mail` WHERE `codeCampagne` LIKE "EVALCCPC-'.DatetimeToTimestamp($res_f['debutStage']).'-'.DatetimeToTimestamp($res_f['finStage']).'-'.$res_f['promotion'].'-%"';
				$res3 = $db -> query($sql);
				while ($res3_f = $res3 -> fetch())
				{
					$listeCode[] = explode('-', $res3_f[0])[4];
				}
				sort($listeCode); // On range dans l'ordre croissant sans garder l'association clé-valeur
				
				if (count($listeCode) == 0) { $codeCampagneNumber = 1; }
				else { $codeCampagneNumber = $listeCode[count($listeCode)-1] + 1; }
				
			$codeCampagne = 'EVALCCPC-'.DatetimeToTimestamp($res_f['debutStage']).'-'.DatetimeToTimestamp($res_f['finStage']).'-'.$res_f['promotion'].'-'.$codeCampagneNumber;
			
			$campaignPossibilities[] = array('codeCampagne' => $codeCampagne, 'debutStage' => DatetimeToTimestamp($res_f['debutStage']), 'finStage' => DatetimeToTimestamp($res_f['finStage']), 'promotion' => getPromotionData($res_f['promotion']), 'services' => $res2 -> fetchAll());
		}
		
	/**
		3. Affichage de l'interface
	**/

	?>
	<div id  = "evalccpcContentUnique">
		<!-- Barre du haut -->
		<div id = "evalccpcAdminSelectFiltre">
			<form method = "GET">
				<!-- Bouton de retour en arrière -->
				<?php
					$tempGet = $_GET;
				?>
				<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGet); ?>"><span style = "font-size: 1.2em; padding: 5px;"><i class="fa fa-arrow-circle-left"></i></span></a>				
				
				<select name = "year">
					<?php
						foreach ($yearList AS $item)
						{
							?>
								<option <?php if ($year == $item) { echo 'selected'; } ?>><?php echo $item; ?></option>
							<?php
						}
					?>
				</select>	
				
				<!-- On met toutes les variables $_GET en hidden -->
				<?php
					foreach ($_GET AS $key => $value)
					{
						if ($key != 'year')
						{
							?>
								<input type = "hidden" name = "<?php echo $key; ?>" value = "<?php echo $value; ?>" />
							<?php
						}
					}
				?>

				<!-- Bouton d'ajout d'une campagne -->
				<?php
				if (count($campaignPossibilities) > 0)
				{
					?>
					<a href = "#" class = "addCampaign" style = "font-size: 1.2em; padding: 5px;"><i class="fa fa-plus-circle"></i></a>
					<?php
				}
				?>
			</form>
		</div>
		
	<?php
		// On affiche un form contenant les possibilités de créer une nouvelle campagne
		if (count($campaignPossibilities) > 0) {
			?>
			<div id = "addCampaign" style = "margin-bottom: 10px; display: none;">
			<form id = "createCampaign" method = "POST">
				<select name = "createCampaign">
				<?php
					foreach ($campaignPossibilities AS $key => $campaignPossibilitie)
					{
						?>
							<option value = '<?php echo $key; ?>'><?php echo $campaignPossibilitie['promotion']['nom'].' - Période du '.date('d/m/Y', $campaignPossibilitie['debutStage']).' Au '.date('d/m/Y', $campaignPossibilitie['finStage']); ?></option>
						<?php
					}
				?>
				</select>
				
				<input style = "display: inline-block;" type = "submit" value = "<?php echo LANG_FORM_CCPC_ADMIN_MAILER_TABLE_CREATECAMPAIGN; ?>" />
			</form>
			</div>
			<?php
		}
	?>
		
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
			?>
				
			<!-- Tableau affichant les campagnes de mail lancées -->
			<table>
				<tr class = "headTR" style = "text-align: center;">
					<td><?php echo LANG_FORM_CCPC_ADMIN_MAILER_TABLE_FROM; ?></td>
					<td><?php echo LANG_FORM_CCPC_ADMIN_MAILER_TABLE_TO; ?></td>
					<td><?php echo LANG_FORM_CCPC_ADMIN_MAILER_TABLE_PROMOTION; ?></td>
					<td><?php echo LANG_FORM_CCPC_ADMIN_MAILER_TABLE_CAMPAIGNSTART; ?></td>
					<td><?php echo LANG_FORM_CCPC_ADMIN_MAILER_TABLE_STATE; ?></td>
					<td></td>
				</tr>
				<?php
				if (count($campaignList) > 0)
				{
					foreach ($campaignList AS $campaign)
					{
					?>
					<tr class = "bodyTR" style = "text-align: center;">
						<td><?php echo date('d/m/Y', $campaign['evaluationData']['date']['debut']); ?></td>
						<td><?php echo date('d/m/Y', $campaign['evaluationData']['date']['fin']); ?></td>
						<td><?php echo $campaign['evaluationData']['promotion']['nom']; ?></td>
						<td><?php echo date('d/m/Y', DatetimeToTimestamp($campaign['date'])); ?></td>
						<td><?php if ($campaign['statut'] == 1) { echo 'Terminé'; } else { echo 'En cours'; } ?><br />(<span style = "color: green;"><?php echo $campaign['nb'][1]; ?></span> / <?php echo ($campaign['nb'][0] + $campaign['nb'][1]); ?>)</td>
						<td><a target = "_blank" href = "<?php echo getPageUrl('mail', array('page' => 'campagne', 'id' => $campaign['codeCampagne'])); ?>"><i class="fa fa-envelope"></i></a></td>
					</tr>
					<?php
					}
				}
				else
				{
					?>
					<tr style = "text-align: center;">
						<td colspan = "6"><?php echo LANG_FORM_CCPC_ADMIN_MAILER_NOCAMPAIGN; ?></td>
					</tr>
					<?php
				}
				?>
			</table>
		</div>
		
	<!-- Div de création des mails -->
	<div id = "grayBackground"></div> <!-- tableau noir, masqué de base -->
	
<script>
var ajaxURI = window.location.href;
var mailURI = '<?php echo getPageUrl('mail', array('page' => 'campagne')); ?>'+'&id=';

$('select[name="year"]').on('change', function(){
	$(this).parent().submit();
});

$('.addCampaign').on('click', function(){
	$('#addCampaign').css('display', 'block');
});

var campaignPossibilities = <?php echo json_encode($campaignPossibilities); ?>;

// Création d'une campagne d'email
$('#createCampaign').on('submit', function(e){
	
	e.preventDefault(); // On empêche la soumission du formulaire
	
	// On récupère l'object contenant les données
	var campaignData = campaignPossibilities[$('#createCampaign option:selected').val()];
	
	// Tableau noir
	$('#grayBackground').css('display', 'block');
	
	var nbAdd = 0;
	var nbToAdd = campaignData['services'].length;
	
	// Div d'envoie des mails
	$('body').append('<div class="sendMailBox"><img src = "<?php echo ROOT.'/theme/img/loader.gif'; ?>" /><div class = "loadingBox"><div></div></div></div>');
	$('.loadingBox div').css('width', Math.round(100*nbAdd/nbToAdd)+'%'); // Avancé des envoies
	
	// Ajout des mails un par un
	var createMail = false; // Passe à false lorsqu'un mail n'est plus en cours d'envoie
	
	$.each(campaignData['services'], function(key, value){	
		$.post(ajaxURI, {AJAX: 1, action: 'addMail', debutStage: campaignData['debutStage'], finStage: campaignData['finStage'], promotion: campaignData['promotion']['id'], codeCampagne: campaignData['codeCampagne'], service: value[0]}, function(e){
			$(document).ajaxStop(function() { window.location.href = mailURI+campaignData['codeCampagne']; }); // Affiche la page d'envoie à la fin

			// Affichage de l'avancée
			nbAdd++;
			$('.loadingBox div').css('width', Math.round(100*nbAdd/nbToAdd)+'%');			
		});
	});
});
</script>