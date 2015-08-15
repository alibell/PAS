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
		22/02/15 - fnUser.php - Ali Bellamine
		Fonctions de gestion des utilisateurs
	*/
	
	/**
	  * getUserIdFromNbEtudiant - Retourne l'identifiant d'un utilisateur à partir de son numéro d'étudiant
	  *
	  * @category userFunction
	  * @param string $nbEtu Numéro d'étudiant de l'utilisateur
	  * @return int Identifiant de l'utilisateur, FALSE si une erreur a été rencontrée
	  *
	  * @Author Ali Bellamine
	  *
	  */
	
	function getUserIdFromNbEtudiant ($nbEtu)
	{
		global $db;
		if (count(checkNbEtudiant($nbEtu, array())) == 0)
		{
			$sql = 'SELECT id FROM user WHERE nbEtudiant = ? LIMIT 1';
			$res = $db -> prepare($sql);
			$res -> execute(array($nbEtu));
			if ($res_f = $res -> fetch())
			{
				$id = $res_f['id'];
			}
		}
		
		if (isset($id)) { return $id; }
		else { return FALSE; }
	}
	
	/**
	  * getUserData - Retourne les informations relatives à un utilisateur
	  *
	  * @category userFunction
	  * @param int $id Identifiant de l'utilisateur
	  * @return array Array contenant les informations relatives à l'utilisateur
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	['id'] => (int) Identifiant de l'utilisateur<br>
	  *	['nom'] => (string) Nom de l'utilisateur<br>
	  *	['prenom'] => (string) Prénom de l'utilisateur<br>
	  *	['nbEtudiant'] => (string) Numéro d'étudiant de l'utilisateur<br>
	  *	['mail'] => (array) Array contenant les adresses email de l'utilisateur<br>
	  *	['rang'] => (int) Rang de l'utilisateur, de 0 (invité) à 4 (super administrateur)<br>
	  *	['promotion']['id'] (optionnel) => (int) Identifiant de la promotion de l'utilisateur<br>
	  *	['promotion']['nom'] (optionnel) => (string) Nom de la promotion de l'utilisateur<br>
	  *	['service'][identifiant de l'affectation de l'utilisateur][] (optionnel) => (array) Informations relatives au service, voir {@link getServiceInfo()}<br>
	  *	['service'][identifiant de l'affectation de l'utilisateur]['idAffectation] (optionnel) => (int) Identifiant de l'affectation de l'utilisateur<br>
	  *	['service'][identifiant de l'affectation de l'utilisateur]['dateDebut] (optionnel) => (string) Date de début de la période d'affectation sous forme de Timestamp<br>
	  *	['service'][identifiant de l'affectation de l'utilisateur]['dateFin] (optionnel) => (string) Date de fin de la période d'affectation sous forme de Timestamp<br>
	  *	['service'][identifiant de l'affectation de l'utilisateur]['currentAffectation] (optionnel) => (int) 0 si l'utilisateur n'est actuellement pas affecté dans le service, 1 si il y est actuellement affecté<br>
	  *	['chef'][identifiant du service][] (optionnel) => (array) Array contenant les informations relatives au service dont l'utilisateur est chef
	  *
	  */
	
	function getUserData($id)
	{
		/*
			Initialisation des variables
		*/
		global $db; // Permet l'accès à la BDD
		$erreur = array();
		$user = array();
		
		/*
			On vérifie l'existance de l'utilisateur
		*/
		$erreur = checkUser($id, $erreur);
		if (count($erreur) == 0)
		{

			// Récupérations des données utilisateur
			$sql = 'SELECT u.id userId, u.nom userNom, u.prenom userPrenom, u.nbEtudiant nbEtudiant, u.rang userRang, u.mail userMail, p.id promotionId, p.nom promotionNom
					  FROM user u
					  LEFT JOIN promotion p ON u.promotion = p.id
					  WHERE u.id = ?
					  LIMIT 1';
			$res = $db -> prepare($sql);
			$res -> execute(array($id));
			
			// On construit l'array contenant les données utilisateur
			if ($res_f = $res -> fetch())
			{
				$user['id'] = $res_f['userId'];
				$user['nom'] = $res_f['userNom'];
				$user['prenom'] = $res_f['userPrenom'];
				if (isSerialized($res_f['userMail']))
				{
					$user['mail'] = unserialize($res_f['userMail']);
				}
				else
				{
					$user['mail'] = array($res_f['userMail']);
				}
				
				if (isset($res_f['nbEtudiant']))
				{
					$user['nbEtudiant'] = $res_f['nbEtudiant'];
				}
				else
				{
					$user['nbEtudiant'] = '';
				}
				
				$user['rang'] = $res_f['userRang'];
				
				if (isset($res_f['promotionId'])) { 
					$user['promotion']['nom'] = $res_f['promotionNom']; 
					$user['promotion']['id'] = $res_f['promotionId']; 
				}
				
				// Si il s'agit d'un étudiant
				
					// On récupère les affectations dans les services
					$sql = 'SELECT s.id serviceId, ae.dateDebut dateDebut, ae.dateFin dateFin, ae.id idAffectation
							  FROM affectationexterne ae
							  INNER JOIN service s ON ae.service = s.id
							  WHERE ae.userId = ?
							  ORDER BY ae.dateFin DESC';
					$res = $db -> prepare($sql);
					$res -> execute(array($id));
					
					while ($res_f = $res -> fetch())
					{
						$user['service'][$res_f['idAffectation']] = getServiceInfo($res_f['serviceId']);
						$user['service'][$res_f['idAffectation']]['idAffectation'] = $res_f['idAffectation'];
						$user['service'][$res_f['idAffectation']]['dateDebut'] = DatetimeToTimestamp($res_f['dateDebut']);
						$user['service'][$res_f['idAffectation']]['dateFin'] = DatetimeToTimestamp($res_f['dateFin']);
						if ($user['service'][$res_f['idAffectation']]['dateDebut'] < time() AND $user['service'][$res_f['idAffectation']]['dateFin'] > time())
						{
							$user['service'][$res_f['idAffectation']]['currentAffectation'] = 1;
						}
						else
						{
							$user['service'][$res_f['idAffectation']]['currentAffectation'] = 0;
						}
					}
					
				// Si il s'agit d'un chef
					// On récupère le service dont il est chef
					$sql = 'SELECT s.id serviceId
							  FROM service s
							  WHERE s.chef = ?';
					$res = $db -> prepare($sql);
					$res -> execute(array($id));
					
					while ($res_f = $res -> fetch())
					{
						$user['chef'][$res_f['serviceId']] = getServiceInfo($res_f['serviceId']);
					}
			}

			return $user;
		}
		else
		{
			return false;
		}
	}
	
	/**
	  * getEnseignantList - Retourne la liste des enseignants (liste des utilisateurs dont le rang est supérieur où égal à 2)
	  *
	  * @category userFunction
	  * @return array Array contenant la liste des enseignants
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	[identifiant de l'utilisateur][] => (array) Array contenant les informations relatives à l'utilisateur, voir {@link getUserData}
	  *
	  */
	
	function getEnseignantList () {
		global $db;
		
		// On recherche les utilisateurs  n'ayant pas de numéro d'étudiants et de rang >= 2
		$sql = 'SELECT id FROM user WHERE rang >= 2 ORDER BY nom, prenom';
		$res = $db -> query($sql);
		
		$enseignants = array();
		while($res_f = $res -> fetch())
		{			
			$enseignants[$res_f['id']] = getUserData($res_f['id']);
		}
		
		return $enseignants;
	}
	
	/**
	  * getPromotionList - Retourne la liste des promotions
	  *
	  * @category userFunction
	  * @param string $order Paramètre selon lequel sont classés les résultats ('id', 'nom'ou 'nb')
	  * @param boolean $desc Ordre selon lequel on classe les résultats (TRUE : decroissant, FALSE : croissant)
	  * @return array Array contenant la liste des promotions
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	[identifiant de la promotion]['id'] => (int) Identifiant de la promotion<br>
	  *	[identifiant de la promotion]['nom'] => (string) Nom de la promotion<br>
	  *	[identifiant de la promotion]['nb'] => (int) Nombre d'utilisateurs inscrits dans la promotion
	  *
	  */

  function getPromotionList ($order = 'nom', $desc = false) {
		global $db;
		
		/*
			On met le filtre
		*/
		
		$allowedOrder = array('id', 'nom', 'nb');
		if (in_array($order, $allowedOrder))
		{
			$orderSql = $order;
		}
		else
		{
			$orderSql = 'nom';
		}
		
		$sql = 'SELECT p.id id, p.nom nom, (SELECT count(*) FROM user WHERE promotion = p.id LIMIT 1) nb FROM promotion p ORDER BY '.$orderSql;
		if ($desc) { $sql .= ' DESC'; }
		$res = $db -> query($sql);
		
		$promotion = array();
		while($res_f = $res -> fetch())
		{			
			$promotion[$res_f['id']]['id'] = $res_f['id'];
			$promotion[$res_f['id']]['nom'] = $res_f['nom'];
			$promotion[$res_f['id']]['nb'] = $res_f['nb'];
		}
		
		return $promotion;
	}
	
	/**
	  * getPromotionData - Retourne les informations relatives à une promotion
	  *
	  * @category userFunction
	  * @param int $id Identifiant de la promotion
	  * @return array Array contenant les informations relatives à la promotion
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	['id'] => (int) Identifiant de la promotion<br>
	  *	['nom'] => (string) Nom de la promotion<br>
	  *	['nb'] => (int) Nombre d'utilisateurs inscrits dans la promotion<br>
	  *	['users'][identifiant de l'utilisateur]['id'] => (int) Identifiant d'un utilisateur inscrit dans la promotion<br>
	  *	['users'][identifiant de l'utilisateur]['nom'] => (string) Nom de l'utilisateur inscrit dans la promotion<br>
	  *	['users'][identifiant de l'utilisateur]['prenom'] => (string) Prénom de l'utilisateur inscrit dans la promotion<br>
	  *	['users'][identifiant de l'utilisateur]['nbEtudiant'] => (string) Numéro d'étudiant de l'utilisateur inscrit dans la promotion<br>
	  *	['users'][identifiant de l'utilisateur]['rang'] => (int) Rang de l'utilisateur inscrit dans la promotion (entre 0 et 4)<br>
	  *
	  */
	
	function getPromotionData ($id) {
		global $db;
		
		// Verification de l'id
		if (count(checkPromotion($id, array())) == 0)
		{
			$promotion = array();
			
			$sql = 'SELECT p.id, p.nom, (SELECT count(*) FROM user WHERE promotion = p.id LIMIT 1) nb FROM promotion p WHERE p.id = ? LIMIT 1';
			$res = $db -> prepare($sql);
			$res -> execute(array($id));
			if ($res_f = $res -> fetch())
			{
				$promotion['id'] = $res_f['id'];
				$promotion['nom'] = $res_f['nom'];
				$promotion['nb'] = $res_f['nb'];
			}
			
			// Liste des étudiants inscrits dans le services
			
			$sql = 'SELECT u.id, u.nom nom, u.prenom prenom, u.rang rang, u.nbEtudiant
						FROM user u
						WHERE u.promotion = ?
						ORDER BY u.nom ASC';
			$res = $db -> prepare($sql);
			$res -> execute(array($id));
			
			while ($res_f = $res -> fetch())
			{
				$promotion['users'][$res_f['id']]['id'] = $res_f['id'];
				$promotion['users'][$res_f['id']]['prenom'] = $res_f['prenom'];
				$promotion['users'][$res_f['id']]['nbEtudiant'] = $res_f['nbEtudiant'];
				$promotion['users'][$res_f['id']]['nom'] = $res_f['nom'];
				$promotion['users'][$res_f['id']]['rang'] = $res_f['rang'];
			}
			
			return $promotion;
		}
		else
		{
			return FALSE;
		}
	}
?>