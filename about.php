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
		31/07/15 - about.php - Ali Bellamine
		A propos de PAS
	*/
	
	require 'core/main.php';
	require 'core/header.php';
?>

<div id = "presentation" style = "overflow: auto;">
	<div class = "divBox" style = "text-align: center;">
		<span style = "padding: 10px; display: inline-block; margin-top: 10px; margin-top: 10px; background-color: #BBB5B5; border-radius: 5px; color: white;"><?php echo PROGRAM_NAME; ?> (<?php echo PROGRAM_VERSION; ?>)</span>
		<p>
			<?php printf(LANG_ABOUT_DESCRIPTION, constant('PROGRAM_NAME')); ?>
		</p>
		
		<p>
			<?php printf(LANG_ABOUT_LICENCE, constant('PROGRAM_NAME')); ?>
		</p>
		
		<p>
			<?php echo LANG_ABOUT_LIBRAIRIE; ?>
		</p>
		
		<p>
			<?php echo LANG_ABOUT_CONTACT; ?>
		</p>
	</div>
</div>

<?php
	require 'core/footer.php';
?>