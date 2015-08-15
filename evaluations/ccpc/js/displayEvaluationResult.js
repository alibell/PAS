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

/**
	Chargement des évaluations
**/

$('.bodyTR').on('click',function(){
	if ($(this).attr('data-lien') != 'undefined')
	{
		$(location).attr('href', $(this).attr('data-lien'));
	}
});

/**
	Modération d'un commentaire
**/

$('.modereComment').on('click', function() {
	// On récupère le nom du champ et l'id de l'évaluation
	var parent = $(this).parent();
	var commentNomBDD = $(this).parent().attr('data-nomBDD');
	var theevaluationId = $(this).parent().attr('data-evaluationId');
	
	// On envoie les données en AJAX
	$.post(ajaxURI, {action: 'modereMessage', msgId: theevaluationId, nomBDD: commentNomBDD}, function (data) {
		// On modifie le CSS
		if (data == 'modere')
		{
			parent.addClass('moderateText');
		}
		else
		{
			parent.removeClass('moderateText');		
		}
	});
});

/**
	Affichage des réponses aux formulaires d'évaluation
**/

function eval_ccpc_DisplayEvaluation (evaluationId) {
	// On masque toutes les réponses
		$('.evaluationDataDisplay').css('display','none');
		
		// On affiche que les réponses de l'id selectionné
		$('.evaluationDataDisplay[data-evaluationid="'+evaluationId+'"]').css('display','inherit');
		
		// On met en avant les select de l'id selectionné
		$('option.evaluationDataDisplay[data-evaluationid="'+evaluationId+'"]').attr('selected','selected');
		
		// On affiche le formulaire
		$('#evaluationDataDisplayUserReponse').css('display', 'inherit');
}

$('.catEvaluationData select').on('change',function(){
	// id de l'évaluation sélectionnée
	if ($('.catEvaluationData select option:selected').attr('value') != '')
	{
		var evaluationId = $('.catEvaluationData select option:selected').attr('value');
	}

	if (typeof(evaluationId) != 'undefined' && evaluationId != '')
	{
		eval_ccpc_DisplayEvaluation(evaluationId);
	}
	else
	{
		// On masque les réponses
		$('.evaluationDataDisplay').css('display','none');
		
		// On masque le formulaire
		$('#evaluationDataDisplayUserReponse').css('display', 'none');
	}
});

/**
	Sélection des dates
**/

	// Récupération des dates
	var dateMin = $('#dateRangeSelector').attr('data-dateMin');
	var dateMax = $('#dateRangeSelector').attr('data-dateMax');
	
	// Affichage du selecteur de dates
	var settings = {
		format: 'x',
		startOfWeek: 'monday',
		startDate: new Date(dateMin*1000),
		endDate: new Date(dateMax*1000),
		setValue: function (s,s1,s2) {
			$('#dateRangeSelector').val(moment(parseInt(s1)).format('DD/MM/YYYY') + ' - ' + moment(parseInt(s2)).format('DD/MM/YYYY'));
			$('.dateRangeSelector[data-DateType=min]').val(moment(parseInt(s1)).format('X'));
			$('.dateRangeSelector[data-DateType=max]').val(moment(parseInt(s2)).format('X'));
		},
		showShortcuts: false,
		showTopbar: false
	};

	$('#dateRangeSelector').dateRangePicker(settings);
	
/**
	Compatibilité mobile
**/

// Plein écran dans les div text

	// launchFullscreen : met la div sélectionnée en plein écran
	function launchFullscreen(element) {
		// Si fullscreen : on quitte le mode fullscreen
		if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) 
		{
			if (document.exitFullscreen) {
				document.exitFullscreen();
			} else if (document.webkitExitFullscreen) {
				document.webkitExitFullscreen();
			} else if (document.mozCancelFullScreen) {
				document.mozCancelFullScreen();
			} else if (document.msExitFullscreen) {
				document.msExitFullscreen();
			}
		}
		// Sinon on met le mode fullscreen
		else
		{
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
	}

	$('.mobileCommentFullscreenButton').on('click', function(){
		launchFullscreen($(this).parent().parent().get(0));
	});