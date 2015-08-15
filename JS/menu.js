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
	Affiche le sous menu lorsqu'un item est sélectionné
*/

$('.mainMenu li').on('click', function(){

	var secondaryMenu = $(this).find('.secondaryMenu');
	
	// Si interface PC
	if ( $('#menuButton').css('display') == 'none')
	{
		if (secondaryMenu.css('visibility') == 'hidden') { 
			// On masque tous les sous menus
			$('.secondaryMenu').css('visibility','hidden');
			// On affiche celui sur lequel on a cliqué
			secondaryMenu.css('visibility','inherit');
		}
		else { secondaryMenu.css('visibility','hidden'); }
	}
	// Si interface smartphone
	else
	{
		if (secondaryMenu.css('display') == 'none') {
			// On masque tous les sous menus
			$('.secondaryMenu').css('display','none');
			
			// On affiche celui sur lequel on a cliqué
			secondaryMenu.css('display','inherit');
		}
		else
		{
			secondaryMenu.css('display','none');
		}
	}
});

$('.mainMenu li').on('mouseover', function(){
	var secondaryMenu = $(this).find('.secondaryMenu');

	// Si interface pc
	if ($('#menuButton').css('display') == 'none')
	{
		// On masque tous les sous menus
		$('.secondaryMenu').css('visibility','hidden');
		// On affiche celui sur lequel on a cliqué
		secondaryMenu.css('visibility','inherit');
	}
});

$('.mainMenu li').on('mouseout', function(){
	// On masque tous les sous menus
	$('.secondaryMenu').css('visibility','hidden');
});

/**
	Menu en haut lors du scroll
**/

 $(function(){
   $(window).scroll(function () {
      if ($(this).scrollTop() > 90 && $('#menuButton').css('display') == 'none') { //si on a défilé de plus de 90px du haut vers le bas et qu'on est pas sur un portable
          $('#headerMenu').addClass("headerFixe");
          $('#userPanel').addClass("userPanelFixe");
          $('#barreLaterale').addClass("barreLateraleFixe");
      } else {
		$('#headerMenu').removeClass("headerFixe")
		$('#userPanel').removeClass("userPanelFixe")
		$('#barreLaterale').removeClass("barreLateraleFixe")
     }
   });
});

/***
	Compatibilité mobile
***/

// Affichage du menu

$('#body').on( "click", function( e ){
    if (e.target.id != "headerMenu"  && e.target.id != "menuButton" && $( "#headerMenu" ).css('display') == 'block' && $('#menuButton').css('display') == 'block'){
        $( "#headerMenu" ).css('display', 'none');
    }
});

$('#menuButton').on('click', function(){
	if ($("#headerMenu").css('display') == 'none')
	{
		$("#headerMenu").css('display','block');
	}
	else
	{
		$("#headerMenu").css('display','none');
	}
});

// Affichage des menu d'admin

$(".mobileAdminMenuButton").on('click', function(){
	// On masque le bouton et le contenu de la page
	if ($('#evalccpcFiltres').css('width') == 100)
	{
		$("#corps").css('display', 'none');
	}
	$(".mobileAdminMenuButton").css('display', 'none');
	
	// On affiche le menu
	$('#barreLaterale').css('display', 'block');
});

$(".mobileAdminMenuButtonClose").on('click', function(){
	// On masque le bouton et le contenu de la page
	if ($('#evalccpcFiltres').css('width') == 100)
	{
		$("#corps").css('display', 'block');
	}
	$(".mobileAdminMenuButton").css('display', 'block');
	
	// On affiche le menu
	$('#barreLaterale').css('display', 'none');
});