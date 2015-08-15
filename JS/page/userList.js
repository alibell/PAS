/*
	userList.js : gestion des utilisateurs pour les évaluations
	25/05/2015 - Ali Bellamine
*/

/**
	Variables
**/

	var excludeName = {};

/**
	Fonctions nécessaires au fonctionnement de la page
**/

	// launchFullscreen : met la div sélectionnée en plein écran
	function launchFullscreen(element) {
		  if(element.requestFullscreen) {
			element.requestFullscreen();
		  } else if(element.mozRequestFullScreen) {
			element.mozRequestFullScreen();
		  } else if(element.webkitRequestFullscreen) {
			element.webkitRequestFullscreen();
		  } else if(element.msRequestFullscreen) {
			element.msRequestFullscreen();
		  }
	}

	// updateUserList : Met à jour la liste des utilisateurs à l'aide de l'ajax

	function updateUserList () {
		$.post(ajaxURL, {action: 'getUserList', evaluation: evaluationId}, function(data){
			
			$('#evaluationUserList').html(''); // on efface tout
			
			$('#evaluationUserList').append('<div id = "EvaluationListUserNb">'+data.nb.remplis+'/'+data.nb.total+'</div>'); // On affiche le nombre d'évaluations
			var lock = $('.lockButton ').attr('data-value') ;

			$.each(data, function(key, value){
				if (key != 'nb' && typeof(value) != 'undefined')
				{
					if (value.statut == 1) { var statut = 'evaluated'; } else { var statut = 'noevaluated';  }
					if (lock == 1 && value.statut == 0) { var editButton = '<i class="fa fa-times deleteUserFromList editUserList" style="float: right; color: white; cursor: pointer;"></i>'; } else { var editButton = ''; } // Bouton d'édition
					if (typeof(value.promotion) != 'undefined') { var promotion = ' ('+value.promotion.nom+')'; } else { var promotion = ''; } // Promotion
					
					$('#evaluationUserList').append('<div data-registerId = "'+value.registerId+'" class = "EvaluationListUserNom '+statut+'">'+value.prenom+' '+value.nom+promotion+' '+editButton+'</div>'); // On affiche les noms un par un
				}		
			});
				
				var d = new Date();
				$('#evaluationUserList').append('<span class = "EvaluationListUserNomLastUpdate">'+lastUpdateText+' '+d.toLocaleDateString()+'  '+d.toLocaleTimeString()+'</span>'); // On affiche la date de dernière MAJ
		});
	}

	// updateUserList : affiche les utilisateurs dans la div de sélection des utilisateurs

	function updateAddUsersList () {
		// JSON contenant la liste des filtres sélectionnés
		var listeFiltres = {};

		// Filtres sélectionnées
		$('#addUsersRightPanel input:checked').each(function(){
			if (typeof(listeFiltres[$(this).attr('data-type')]) == 'undefined') { listeFiltres[$(this).attr('data-type')] = {}; }
			listeFiltres[$(this).attr('data-type')][$(this).attr('data-value')] = true;
		});
		
		// Noms exclus
		listeFiltres['exclude'] = excludeName;
		// On efface les users sélectionnés
		$('#addUsersLeftPanel').html('');

		// Requête 
		$.post(ajaxURL, {action: 'addUserList', id: evaluationId, filtres: listeFiltres}, function(data){
			$.each(data, function(key, value){
				if (value.selected == 1) { var selected = 'selected'; }
				else { var selected = 'noselected'; }
				
				var html = '<div class = "EvaluationListUserNom '+selected+'" data-id = "'+value.id+'">'+value.prenom+' '+value.nom;
				if (typeof(value.promotion) != 'undefined') { html = html + ' ('+value.promotion.nom+')'; }
				html = html + '<i class = "fa fa-times deleteUserFromList" style = "float: right; color: white; cursor: pointer;"></i></div>';
				$('#addUsersLeftPanel').append(html); // On affiche les noms un par un
			});
		});
	}
	
	// clearAddUserList : remet l'état du formulaire d'ajout d'utilisateurs à 0
	function clearAddUserList () {
		// On décoche ce qui a été coché
			$('input').removeAttr('checked'); 
			
		// On recoche ce qui était pas défault
			$('input.defaultRadio').prop('checked',true);

		// On efface la liste des utilisateurs exclues des ajouts
			excludeName = {};
			$('.excludeList').remove();
		
		// On met à jour la liste
			updateAddUsersList();
	}

/**
	Interaction avec l'utilisateur
**/

/*
	Hors de la page d'ajout des utilisateurs
*/

	// Déverouillage de l'édition
		
		$('.lockButton').on('click', function(){
			var value = $(this).attr('data-value');
			if (value == 0)
			{
				updateUserList();
				$(this).attr('data-value',1); // On passe en activé
				$(this).removeClass('fa-lock'); // On supprime la classe fa-lock
				$(this).addClass('fa-unlock'); // On ajoit la classe fa-unlock
				$('.editUserList').css('display','inherit'); // On affiche les boutons d'édition
			}
			else
			{
				$(this).attr('data-value',0); // On passe en désactivé
				$(this).removeClass('fa-unlock'); // On supprime la classe fa-unlock
				$(this).addClass('fa-lock'); // On ajoit la classe fa-lock
				$('.editUserList').css('display','none'); // On affiche les boutons d'édition
			}
		});
		
	// Toolbar
	
		$('#evaluationUserListToolbox .button').on('click', function(){
			var action = $(this).children().attr('data-action');
			
			// Mode plein écran
			if (action == 'fullscreen')
			{
				launchFullscreen(document.getElementById('evaluationUserList'));
			}
			// Page d'ajout d'un utilisateur
			if (action == 'add')
			{
				$('#addUsers').css('display','block');
				updateAddUsersList();
			}
		});
		
	// Suppression d'un utilisateur
	$('#evaluationUserList').on('click', '.EvaluationListUserNom .deleteUserFromList', function(){
		// Récupération de l'id de l'utilisateur
		registerId = $(this).parent().attr('data-registerId');
		
		// On envoie la requête en ajax
		$.post(ajaxURL, {action: 'deleteEvaluation', id: registerId}, function(){
			// On met à jour la liste des utilisateurs ajoutés
			updateUserList();			
		});
	});
		
/*
	Dans la page d'ajout des utilisateurs
*/

	// Met à jour la liste des utilisateurs à chaque modification des filtres
	$('#addUsersRightPanel input').on('click', function(){
		updateAddUsersList();
	});
	
	// Met à jour la liste des utilisateurs à chaque ajout d'un utilisateur bannis
	$('#addUsersLeftPanel').on('click', '.deleteUserFromList', function(){
		var id = $(this).parent().attr('data-id');
		var nom = $(this).parent().html();
		
		// On ajoute le nom dans les noms à exclure
		if (typeof(excludeName[id]))
		{
			excludeName[id] = true;
		}
		
		// On ajoute le nom dans la liste des noms exclus
		$('#listeExclude').append('<div class = "excludeList" data-id = "'+id+'" style = "clear: both; padding-top: 10px; text-decoration: line-through ;">'+nom+'</div>');
		
		// On actualise la liste
		updateAddUsersList();
	});
	
	// Met à jour la liste des utilisateurs à chaque suppression d'un utilisateur bannis
	$('#addUsersRightPanel').on('click', '.deleteUserFromList', function(){
		var id = $(this).parent().attr('data-id');
		
		// On supprime le nom des noms à exclure
		if (typeof(excludeName[id]))
		{
			delete excludeName[id];
		}
		
		// On ajoute le nom dans la liste des noms exclus
		$('.excludeList[data-id="'+id+'"]').remove();
		
		// On actualise la liste
		updateAddUsersList();
	});
	
	// Ferme la page d'ajout des utilisateurs
	$('.closeButton').on('click',function(){
		$('#addUsers').css('display','none');
	});
	
	// Ajout des utilisateurs sélectionnées
	$('#addUserConfirm').on('click', function(){
		if (confirm($(this).attr('data-msg')))
		{
			// On récupère la liste des utilisateurs
			listeUsers = new Array;
			
			$('#addUsersLeftPanel').children().each(function(){
				listeUsers.push($(this).attr('data-id'));
			});
			
			// On envoie la liste en AJAX
			$.post(ajaxURL, {action: 'insertNewUserEvaluation', id: evaluationId, users: listeUsers}, function(data){
				if (data == 'ok')
				{
					// On remet l'état des formulaires à 0
					clearAddUserList();
					
					// On met à jour la liste des utilisateurs ajoutés
					updateUserList();
					
					// On ferme la fenêtre
					$('#addUsers').css('display','none');					
				}
			});
		}
	});