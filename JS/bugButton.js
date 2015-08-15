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
	Affiche la fenêtre de signalement
*/

$('#bug').on('click', function(){
	// On affiche le formulaire
	formBugWindow = $.featherlight($('#bugForm'), { contentFilters: ['jquery']});
});

// Envoie les données du formulaire à la page ajax
$('#bugForm form').on('submit', function(e){
	e.preventDefault();
	
	$.post(ajaxBugURI, $(this).serialize(), function(res) {
		if (res == 'ok')
		{
			// On ferme la fenêtre
			formBugWindow.close();
		
			// On efface le contenue du textarea
			$('#bugForm form textarea').val('');
		}
	});
});