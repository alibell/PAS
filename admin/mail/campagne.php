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
		31/01/16 - campagne.php - Ali Bellamine
		Affiche les détails d'une campagne : liste des destinataires, contenu des emails, et permet l'envoie des emails
	*/
	
	/**
		0. Ajax
	**/
	
	if (isset($_GET['AJAX']) && $_GET['AJAX'] = true)
	{
		if (isset($_GET['action']) && $_GET['action'] == 'sendmail' && isset($_GET['actionId']) && is_numeric($_GET['actionId']))
		{
			$test = sendMailFromCampaign($_GET['actionId']);
			exit();
		}
	}
	
	/**
		1. Récupération des informations relative à une campagne d'envoie de mail
	**/
	
	$campaignData = getMailCampaignData($_GET['id']);
	
	if (!$campaignData)
	{
		header('Location: '.getPageUrl('mail'));
	}

	/**
		2. Affichage de la liste des mails à envoyer
	**/
	
	?>
	
	<!-- Liste des destinataires -->
	
	<div id = "destinataires">
		<h1 id = "destinatairesTitle">Destinataires</h1>
		<ul>
		<?php
		foreach ($campaignData['destinataires'] AS $destinataire)
		{
			$userInfo = getUserData($destinataire['id']);
			?>
			<li data-id = "<?php echo $userInfo['id']; ?>" class = "<?php if ($destinataire['statut'] == 1) { echo 'sent'; } ?>">
				<?php echo $userInfo['prenom'].' '.$userInfo['nom']; ?>
				<?php if (count($destinataire['erreurs']) > 0) { ?><i style = "float: left; padding-right: 5px; color: #652D2D; font-weight: bold;" class="fa fa-exclamation-triangle"></i><?php } ?>
				<i class="fa fa-arrow-right DestinataireSelected" style = "float: right; color: #0C43C0; font-weight: bold; display: none;"></i>
			</li>
			<?php 
		}
		?>
		</ul>
	</div>
	
	<!-- Message -->
	<div id = "message">
		<div>
			<h1>Objet</h1>
			<input id = "objetInput" type = "text" style = "width: 90%;">
		</div>
		<div>
			<h1>Message</h1>
			<div style = "width: 92%; margin: auto;"><textarea style = "height: 300px;" id = "messageInput"></textarea></div>
		</div>
		<div id = "mailattachment">
		</div>
	</div> 
	<!-- Envoie des messages -->
	<button id = "sendMailButton" <?php if ($campaignData['statut'] == 1) { echo 'disabled'; } ?>>
		<?php 
			if ($campaignData['statut'] == 1) { 
				echo 'Envoi terminé'; 
			} 
			else {
				echo 'Envoyer les emails';
			}
		?>
	</button>
	
	<!-- Div d'envoie des mails -->
	<div id = "grayBackground"></div> <!-- tableau noir, masqué de base -->
	<script>	
	<!-- URL de l'Ajax -->
	var ajaxURI = window.location.href;
	
	<!-- Editeur de texte -->
	tinymce.init({
		selector: "textarea",
		menubar: false,
		statusbar: false,
		init_instance_callback : function(editor) { // Chargement du premier mail à l'ouverture de la page
			if (editor['id'] = 'messageInput')
			{
				displayMail($('#destinataires ul li').first().attr('data-id'));
			}
		}
	});

	<!-- On remplit un objet JS avec le contenu des mails -->	
	<?php
		$jsonArray = array();
		foreach ($campaignData['destinataires'] AS $destinataires)
		{
			$jsonArray[$destinataires['id']]['messageId'] = $destinataires['messageId'];
			$jsonArray[$destinataires['id']]['objet'] = $destinataires['objet'];
			$jsonArray[$destinataires['id']]['message'] = $destinataires['message'];
			$jsonArray[$destinataires['id']]['statut'] = $destinataires['statut'];
			$jsonArray[$destinataires['id']]['attachments'] = $destinataires['attachments'];
			ob_end_flush(); ob_start();
				displayErreur($destinataires['erreurs']);
				$jsonArray[$destinataires['id']]['erreurs'] = ob_get_contents();
			ob_end_clean();	ob_start();
			
		}
	?>
	
	var mailData = <?php echo json_encode($jsonArray); ?>;
	var nbToSend = <?php echo $campaignData['nb'][0]; ?>;
	var nbSent = 0;
	
	<!-- Affichage des mails -->
	function displayMail (id)
	{
		if (typeof(mailData[id]) != 'undefined')
		{
			<!-- Affichage des erreurs -->
			$('.erreur').remove();
			$('#message').prepend(mailData[id]['erreurs']);
			
			<!-- On met en évidence le mail selectionné -->
			$('.DestinataireSelected').css('display', 'none');
			$('#destinataires li[data-id='+id+']').children('.DestinataireSelected').css('display', 'inline');
			
			<!-- On remplit les champs -->
			$('#objetInput').val(mailData[id]['objet']);
			test = mailData[id]['message'];
			tinyMCE.get('messageInput').setContent(test);
			
			<!-- On affiche les pièces jointes -->
			$('#mailattachment div').remove(); // On supprime ceux de l'ancien message
			$.each(mailData[id]['attachments'], function(key, value){
				$('#mailattachment').append('<div><a target = "_blank" href = "'+value['url']+'">'+key+'</a></div>'); // On affiche ceux du message actuel
			});
		}
	}
	
	<!-- On affiche le contenu d'un message au clic -->
	$('#destinataires li').on('click', function(){
		displayMail($(this).attr('data-id'));
	});
	
	<!-- Envoie des emails -->
	$('#sendMailButton').on('click', function(){
		// Tableau noir
		$('#grayBackground').css('display', 'block');
		
		// Div d'envoie des mails
		$('body').append('<div class="sendMailBox"><img src = "<?php echo ROOT.'/theme/img/loader.gif'; ?>" /><div class = "loadingBox"><div></div></div></div>');
		$('.loadingBox div').css('width', Math.round(100*nbSent/nbToSend)+'%'); // Avancé des envoies
		
		// Envoie des mails un par un
		var sendingMail = false; // Passe à false lorsqu'un mail n'est plus en cours d'envoie
		
		$.each(mailData, function(key, value){	
			if (value['statut'] == 0)
			{
				$.get(ajaxURI, {AJAX: 1, action: 'sendmail', actionId: value['messageId']}, function(e){
					$(document).ajaxStop(function() { location.reload(true); }); // Recharge la page à la fin de l'appel AJAX
					
					// Affichage de l'avancée
					nbSent++;
					$('.loadingBox div').css('width', Math.round(100*nbSent/nbToSend)+'%');
				});
			}
		});
	});
	</script>