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
		19/07/15 - ajaxBug.php - Ali Bellamine
		Code AJAX permettant de récupérer les bug signalés
	*/
	
	require '../core/main.php';
	
	/*
		Routeur
	*/
	$allowedAction = array('registerBug');
	$action = FALSE;
	if (isset($_POST['action']) && in_array($_POST['action'], $allowedAction))
	{
		$action = $_POST['action'];
	}
	
	// Action : registerBug : enregistre le bug dans le BDD
	if ($action == 'registerBug')
	{
		// On met les données dans un array
		$bugArray = array();
		$bugArray['bugServerData'] = serialize($_SERVER);
		$bugArray['bugSessionVariable'] = serialize($_SESSION);
		if (isset($_POST['description']) && $_POST['description'] != '')
		{
			$bugArray['bugDescription'] = htmLawed($_POST['description']);
		}
		else
		{
			$bugArray['bugDescription'] = '';
		}
		$bugArray['bugDate'] = TimestampToDatetime(time());
		
		/*
			On enregistre dans la BDD
		*/
		
		$sql = 'INSERT INTO bug (';
		$firstLoop = TRUE;
		foreach ($bugArray AS $key => $value)
		{
			if ($firstLoop) { $firstLoop = FALSE; } else { $sql .= ', '; }
			$sql .= $key;
		}
		$sql .= ') VALUES (';
		$firstLoop = TRUE;
		foreach ($bugArray AS $key => $value)
		{
			if ($firstLoop) { $firstLoop = FALSE; } else { $sql .= ', '; }
			$sql .= ':'.$key;
		}
		$sql .= ')';
		
		$res = $db -> prepare ($sql);
		if ($res -> execute($bugArray))
		{
			echo 'ok';
		}
	}
?>