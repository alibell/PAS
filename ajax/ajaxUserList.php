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
		24/05/15 - ajaxUserList.php - Ali Bellamine
		Code AJAX permettant de gérer les utilisateurs concernés par une campagne d'évaluation
	*/
	
	require '../core/main.php';
	
	/*
		Routeur
	*/
	$allowedAction = array('getUserList', 'addUserList', 'insertNewUserEvaluation', 'deleteEvaluation');
	$action = FALSE;
	if (isset($_POST['action']) && in_array($_POST['action'], $allowedAction))
	{
		$action = $_POST['action'];
	}
	
	// Action : getUserList : renvoie un JSON contenant la liste des utilisateur
	if ($action == 'getUserList')
	{
		if (isset($_POST['evaluation']) && count(checkEvaluation($_POST['evaluation'], array())) == 0)
		{
			$evaluationData = getEvalData($_POST['evaluation']);
			header('Content-Type: application/json');
			echo json_encode($evaluationData['orderedUsers']);
		}
	}
	
	// Action : addUserList : renvoie la liste des utilisateurs à partir des filtres envoyés
	if ($action == 'addUserList')
	{
		$users = array();

		// Liste des utilisateurs déjà sélectionnées
		if (isset($_POST['id']) && count(checkEvaluation($_POST['id'], array())) == 0)
		{
			$evaluationData = getEvalData($_POST['id']);
		}

		// On récupère le type de données voulus
		$allowedTypeUser = array('etudiant','enseignant');
		if (isset($_POST['filtres']['typeUser']))
		{
			foreach ($_POST['filtres']['typeUser'] AS $key => $value)
			{
				if (in_array($key, $allowedTypeUser))
				{
					$typeUser = $key;
				}
				else
				{
					$typeUser = $allowedTypeUser[0];
				}
				break;
			}		
		}
		else
		{
			$typeUser = $allowedTypeUser[0];
		}
		
		/*
			On crée la requête
		*/
		
		if ($typeUser == 'etudiant')
		{
			$sql = 'SELECT u.nom nom, u.prenom prenom, u.id id, p.id promotionId, p.nom promotionNom
					FROM user u
					LEFT JOIN promotion p ON u.promotion = p.id
					WHERE u.rang = 1 ';
					
			/*
				On crée le $whereSQL
			*/
			
			$whereSQL = '';
			
			if (isset($_POST['filtres']))
			{	
				if (isset($_POST['filtres']['promotion']))
				{
					foreach ($_POST['filtres']['promotion'] AS $promotionId => $promotionValue)
					{
						if (count(checkPromotion($promotionId, array())) == 0)
						{
							$whereSQL .= ' AND p.id = '.$promotionId;
						}
					}
				}
				
				if (isset($_POST['filtres']['certificat']) && count ($_POST['filtres']['certificat']) > 0)
				{
					$whereSQL .= ' AND (';
					$addOR = FALSE;
					foreach ($_POST['filtres']['certificat'] AS $certificatId => $certificatValue)
					{
						if (count(checkCertificat($certificatId, array())) == 0)
						{
							if ($addOR) { $whereSQL .= ' OR '; } else { $addOR = TRUE; }
							$whereSQL .= ' (SELECT count(*) FROM  affectationexterne INNER JOIN servicecertificat ON servicecertificat.idService = affectationexterne.service WHERE userId = u.id AND servicecertificat.idCertificat = '.$certificatId.' AND affectationexterne.dateDebut <= "'.TimestampToDatetime(time()).'" AND affectationexterne.dateFin >= "'.TimestampToDatetime(time()).'" LIMIT 1) = 1';
						}					
					}
					$whereSQL .= ')';
				}
			}
			
			$orderSQL = ' ORDER BY u.nom, u.prenom';
		}
		else if ($typeUser == 'enseignant')
		{
			$sql = 'SELECT u.nom nom, u.prenom prenom, u.id id
					FROM user u
					WHERE u.rang > 1 ';
			
			/*
				On crée le $whereSQL
			*/
			
			$whereSQL = '';
			
			if (isset($_POST['filtres']))
			{	
				if (isset($_POST['filtres']['promotion']))
				{
					foreach ($_POST['filtres']['promotion'] AS $promotionId => $promotionValue)
					{
						if (count(checkPromotion($promotionId, array())) == 0)
						{
							$whereSQL .= ' AND (SELECT count(*) FROM user INNER JOIN affectationexterne ON user.id =  affectationexterne.userId INNER JOIN service ON service.id = affectationexterne.service WHERE user.promotion = '.$promotionId.' AND service.chef = u.id  AND affectationexterne.dateDebut <= "'.TimestampToDatetime(time()).'" AND affectationexterne.dateFin >= "'.TimestampToDatetime(time()).'"  LIMIT 1) = 1 ';
						}
					}
				}
				
				if (isset($_POST['filtres']['certificat']) && count ($_POST['filtres']['certificat']) > 0)
				{
					$whereSQL .= ' AND (';
					$addOR = FALSE;
					foreach ($_POST['filtres']['certificat'] AS $certificatId => $certificatValue)
					{
						if (count(checkCertificat($certificatId, array())) == 0)
						{
							if ($addOR) { $whereSQL .= ' OR '; } else { $addOR = TRUE; }
							$whereSQL .= ' (SELECT count(*) FROM service INNER JOIN servicecertificat ON servicecertificat.idService = service.id WHERE service.chef = u.id AND servicecertificat.idCertificat = '.$certificatId.' LIMIT 1) = 1';
						}
					}
					$whereSQL .= ')';
				}
			}
			
			$orderSQL = ' ORDER BY u.nom, u.prenom';
		}
		
		/*
			On envoie la requête et retourne les infos
		*/

		if (isset($sql) && isset($whereSQL) && isset($orderSQL))
		{
			if ($res = $db -> query($sql.$whereSQL.$orderSQL))
			{
				while ($res_f = $res -> fetch())
				{
					if ((isset($_POST['filtres']['exclude']) && !isset($_POST['filtres']['exclude'][$res_f['id']])) || !isset($_POST['filtres']['exclude']))
					{
						$users[$res_f['id']]['id'] = $res_f['id'];
						$users[$res_f['id']]['nom'] = $res_f['nom'];
						$users[$res_f['id']]['prenom'] = $res_f['prenom'];
						
						if (isset($evaluationData) && isset($evaluationData['users'][$res_f['id']]))
						{
							$users[$res_f['id']]['selected'] = 1;
						}
						else
						{
							$users[$res_f['id']]['selected'] = 0;
						}
						
						if (isset($res_f['promotionNom']))
						{
							$users[$res_f['id']]['promotion']['id'] = $res_f['promotionId'];
							$users[$res_f['id']]['promotion']['nom'] = $res_f['promotionNom'];						
						}
					}		
				}
				header('Content-Type: application/json');
				echo json_encode($users);
			}
		}
	}
	
	// Action : insertNewUserEvaluation : ajoute de nouveaux utilisateurs dans l'évaluation à partir d'un array fournit
	if ($action == 'insertNewUserEvaluation')
	{		
		if (isset($_POST['id']) && count(checkEvaluation($_POST['id'], array())) == 0 && isset($_POST['users']) && count($_POST['users']) > 0)
		{
			$evaluationId = $_POST['id']; // ID de l'évaluation
			
			// Liste des utilisateurs déjà enregistré pour évaluation
			if (isset($_POST['id']) && count(checkEvaluation($_POST['id'], array())) == 0)
			{
				$evaluationData = getEvalData($evaluationId);
				$userList = $evaluationData['users'];
			}

			// On ajoute les utilisateurs un par un
			foreach ($_POST['users'] AS $userId)
			{
				if (count(checkUser($userId, array())) == 0 && !isset($userList[$userId]))
				{
					$sql = 'INSERT INTO evaluationregister (evaluationId, userId, evaluationStatut) VALUES (:evaluationId, :userId, 0)';
					$res = $db -> prepare($sql);
					$res_f = $res -> execute(array('evaluationId' => $evaluationId, 'userId' => $userId));
				}
			}
		}
		
		echo 'ok';
	}
	
	// Action : deleteEvaluation : ne marche que si l'évaluation est non remplis
	if ($action == 'deleteEvaluation')
	{
		if (isset($_POST['id']) && count(checkRegisterEvaluation($_POST['id'], array())) == 0)
		{
			$evaluationRegister = $_POST['id'];
			
			// On récupère le statut de l'évaluation enregistré
			$sql = 'SELECT evaluationStatut FROM evaluationregister WHERE id = ?';
			$res = $db -> prepare($sql);
			if ($res_f = $res -> execute(array($evaluationRegister)))
			{
				if ($res_f[0] == 0)
				{
					$sql = 'DELETE FROM evaluationregister WHERE id = ?';
					$res = $db -> prepare($sql);
					$res_f = $res -> execute(array($evaluationRegister));
				}
			}
		}
	}
?>