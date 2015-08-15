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
		18/02/15 - fnMenu.php - Ali Bellamine
		Fonctions en rapport avec le menu
	*/
	
	/**
	  * getMenu - Retourne un array contenant la liste des pages à inclure dans le menu
	  *
	  * @category : evaluationFunction
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  * 	[id d'un item du menu principal]['id'] => Id de l'item du menu principal<br>
	  * 	[id d'un item du menu principal]['name'] => Nom de l'item du menu principal<br>
	  * 	[id d'un item du menu principal]['children'][id du sous menu]['id'] => Id du sous menu<br>
	  * 	[id d'un item du menu principal]['children'][id du sous menu]['name'] => Nom du sous menu<br>
	  * 	[id d'un item du menu principal]['children'][id du sous menu]['file'] => Fichier vers lequel le sous menu pointe
	  */
	
	function getMenu()
	{
		global $db; // Permet d'utiliser la variable de la BDD dans la fonction
		$menu = array(); // Variable contenant le menu
		
		/*
			Récupération du menu depuis la BDD
		*/
		$sql = 'SELECT m.id mainMenuId, m.name mainMenuName, s.id secondaryMenuId, s.name secondaryMenuName, p.file pageFile
				  FROM mainMenu m 
				  INNER JOIN secondaryMenu s ON m.id = s.mainMenuId 
				  INNER JOIN page p ON p.id = s.page 
				  WHERE p.right'.$_SESSION['rang'].' = 1 
				  ORDER BY m.menuOrder, s.menuOrder, m.name, s.name';
		$res = $db -> query($sql);
		while ($res_f = $res -> fetch())
		{
			if (!isset($menu[$res_f['mainMenuId']]['id']))
			{
				$menu[$res_f['mainMenuId']]['id'] = $res_f['mainMenuId'];
				if (defined($res_f['mainMenuName']))
				{
					$menu[$res_f['mainMenuId']]['name'] = constant($res_f['mainMenuName']);
				}
				else
				{
					$menu[$res_f['mainMenuId']]['name'] = $res_f['mainMenuName'];				
				}
			}
			
			$menu[$res_f['mainMenuId']]['children'][$res_f['secondaryMenuId']]['id'] = $res_f['secondaryMenuId'];
			if (defined($res_f['secondaryMenuName']))
			{
				$menu[$res_f['mainMenuId']]['children'][$res_f['secondaryMenuId']]['name'] = constant($res_f['secondaryMenuName']);
			}
			else
			{
				$menu[$res_f['mainMenuId']]['children'][$res_f['secondaryMenuId']]['name'] = $res_f['secondaryMenuName'];
			}
			$menu[$res_f['mainMenuId']]['children'][$res_f['secondaryMenuId']]['file'] = $res_f['pageFile'];
		}
		
		return $menu;
	}
?>