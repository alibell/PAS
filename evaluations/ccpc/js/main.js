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
	Déplace automatiquement la div de gauche lors du scroll
**/

 $(function(){
   $(window).scroll(function () {
      if ($(this).scrollTop() > 90) { //si on a défilé de plus de 90px du haut vers le bas
          $('#evalccpcFiltres').addClass("evalccpcFiltresFixe");
      } else {
		$('#evalccpcFiltres').removeClass("evalccpcFiltresFixe")
     }
   });
});

// Compatibilité mobile

// Affichage des menu d'admin
$(".mobileCCPCMenuButton").on('click', function(){
	// On masque le bouton et le contenu de la page
	if (mobileSize == 'small')
	{
		$("#evalccpcContent").css('display', 'none');
	}
		$(".mobileCCPCMenuButton").css('display', 'none');	
	
	// On affiche le menu
	$('#evalccpcFiltres').css('display', 'block');
});

$(".mobileCCPCMenuButtonClose").on('click', function(){
	// On masque le bouton et le contenu de la page
	if (mobileSize == 'small')
	{
		$("#evalccpcContent").css('display', 'block');
	}
	$(".mobileCCPCMenuButton").css('display', 'block');
	
	// On affiche le menu
	$('#evalccpcFiltres').css('display', 'none');
});

// Correction des propriétés CSS manquantes
if (!Modernizr.cssvhunit || !Modernizr.csscalc) {
		var height = window.innerHeight;
		$('#evalccpcContentUnique').css('height', height-30+'px');
		$('#divResultats').css('height', height-50+'px');
		$('#evalccpcContentUniqueRetour').css('top', height/2+'px');
		$('#evalccpcFiltres').css('height', height-90+'px');
		$('#evalccpcContent').css('height', height-20+'px');
}