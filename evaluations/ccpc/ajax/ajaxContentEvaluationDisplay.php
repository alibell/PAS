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
		ajaxContentEvaluationDisplay.php - 07/07/2015
		Ali Bellamine
		
		Page traitant les requêtes ajax relatives à l'affichage des évaluations
	**/
	
	/**
		On efface le buffer afin d'avoir un contenu de la page vide
	**/
	
	ob_end_clean();
	
	/**
		Récupération de la variable action
	**/
	
	$allowedAction = array('modereMessage');
	if (isset($_POST['action']) && in_array($_POST['action'], $allowedAction))
	{
		$action = $_POST['action'];
	}

	/**
		Requête relative à la modération des message
	**/
	if ($action == 'modereMessage' && isset($_POST['msgId']) && is_numeric($_POST['msgId']) && isset($_POST['nomBDD']) && $_POST['nomBDD'] != '')
	{
		if ($_SESSION['rang'] >= 3) {

		$msgId = $_POST['msgId'];
		$nomBDD = $_POST['nomBDD'];
		
		// On récupère la liste des messages actuellement censure pour cette évaluation
		$sql = 'SELECT moderation FROM eval_ccpc_resultats WHERE id = ? LIMIT 1';
		$res = $db -> prepare($sql);
		$res -> execute(array($msgId));
		if ($res_f = $res -> fetch())
		{
			$moderationString = $res_f[0];
			// On deserialize si le texte est sérializé
			if ($moderationArray = unserialize($moderationString))
			{
				// On vérifie si $nomBDD est dedans, si oui : on le supprime, si non : on l'ajoute
				if (isset($moderationArray[$nomBDD]))
				{
					unset($moderationArray[$nomBDD]);
					$modereAction = 'demodere';
				}
				else
				{
					$moderationArray[$nomBDD] = true;
					$modereAction = 'modere';
				}
			}
			// Sinon on enregistre un simple array
			else
			{
				// On crée l'array avec la key $nomBDD en true
				$moderationArray[$nomBDD] = true;
				$modereAction = 'modere';
			}
		}
		
		/*
			On envoie les nouvelles données données à la base de donnée
		*/
		if (isset($moderationArray))
		{
			$sql = 'UPDATE eval_ccpc_resultats SET moderation = ? WHERE id = ? LIMIT 1 ';
			$res = $db -> prepare($sql);
			if ($res -> execute(array(serialize($moderationArray), $msgId)))
			{
				// Retourne l'action effectuée (modere ou demodere) au JS
				echo $modereAction;
			}
		}
		
		}
	}

	
	/* 
		Arrête l'execution du script PHP
	*/
	
	exit();
?>