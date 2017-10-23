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

	/**
		ajaxContentEvaluation.php - 23/10/2017
		Ali Bellamine
		
		Page traitant les requêtes ajax relatives à l'affichage des formulaire évaluations
	**/
	
	/**
		On efface le buffer afin d'avoir un contenu de la page vide
	**/
	
	ob_end_clean();
	
	/**
		Récupération de la variable action
	**/
	
	$allowedAction = array('saveDraftEval', 'getSavedDraftEval');
        
	if (isset($_POST['action']) && in_array($_POST['action'], $allowedAction))
	{
		$action = $_POST['action'];
	}

	/**
		Sauvegarde du brouillon de l'évaluation
	**/
	if ($action == 'saveDraftEval' && isset($_POST['draft']))
	{
            if (!$isLogedAs) { // On interdit l'action aux loggé en temps que
                if (isSerialized(getEvaluationRegisterData()))
                {
                    $evaluateServiceTemp = unserialize(getEvaluationRegisterData());
                }
                $evaluateServiceTemp['data'] = $_POST['draft'];

                setEvaluationRegisterData(serialize($evaluateServiceTemp));
            }
	}
	
	/* 
		Arrête l'execution du script PHP
	*/
	
	exit();
?>