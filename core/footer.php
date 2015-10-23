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
?>
		</div>
	</div>
</body>
</html>

<!-- Bouton permettant de signaler un bug -->
<div id = "bug"><i class="fa fa-exclamation-triangle" title = "Signaler un problème"></i></div>
<div style = "display: none;">
	<div id = "bugForm">
		<form>
			<h1>Signaler un problème</h1>
			
			<label>Description du problème</label>
			<textarea id = "bugFormDescription" name = "description"></textarea>
			
			<input type = "hidden" name = "action" value = "registerBug" />
			<input type = "submit" value = "Valider" style = "margin-bottom: 10px;" />
		</form>
	</div>
</div>

<!-- Javascript -->	
	<script type="text/javascript" src="<?php echo ROOT.'JS/menu.js'; ?>"></script> <!-- Gestion du menu -->
	<script type="text/javascript" src="<?php echo ROOT.'JS/bugButton.js'; ?>"></script> <!-- Signalement des bugs -->
	<script>
		var ajaxBugURI = "<?php echo getPageUrl('ajaxBug'); ?>";
		
		<!-- Variables indiquant si on est sur un mobile / une tablette ou non -->
		function getDevice() {			
			if (window.matchMedia('screen and (max-width: 1024px) and (min-width: 320px)').matches == true)
			{
				isMobile = true;
				if (window.matchMedia('screen and (min-width: 721px) and (max-width: 1024px)').matches == true)
				{
					mobileSize = 'mid';
				}
				else
				{
					mobileSize = 'small';
				}
			}
			else
			{
				isMobile = false;
			}
		}

		<!-- Gestion de l'affichage des tableaux -->
		function mobileTableDisplay () {
			if (isMobile)
			{
				$('table').each(function(){
					if ($(this).hasClass('stacktable') == false)
					{
						$(this).stacktable();
					}
				});
			}
		}
		
		$(window).on('resize', function(){
			getDevice();
			mobileTableDisplay();
		});
		
		getDevice();
		mobileTableDisplay();
	</script>