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
		18/02/15 - fnChecker.php - Ali Bellamine
		Fonctions destinées à la vérification des données
		
		Pour chaque fonction, on fournit :
		- La valeur
		- Un array contenant les code d'erreur existants
		- Des paramètres supplémentaires selon la fonction
		La fonction retourne l'array enrechis de nouveaux codes d'erreurs si il y en a
	*/

	/**
	  * checkValue - Vérifie l'existence d'une valeur dans un champs donné de la BDD, fonction générique permettant la créations des autres fonctions de type check
	  *
	  * @category : checkFunction
	  * @param $valeur Valeur à tester dans la base de données
	  * @param string $table Nom de la base contenant la valeur à tester
	  * @param string $chemp Nom du champ dont on teste la valeur
	  * @return boolean Résultat du test
	  *
	  *@Author Ali Bellamine
	  */
	  
	function checkValue ($valeur, $table, $champ) {
		global $db; // Permet d'utiliser la variable de la BDD dans la fonction
		
		if (isset($valeur))
		{
			$sql = 'SELECT count(*) nb FROM '.$table.' WHERE '.$champ.' = ? LIMIT 1';
			$res = $db -> prepare($sql);
			$res -> execute(array($valeur));
			$res_f = $res -> fetch();
			
			if ($res_f['nb'] == 0)
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	  * checkPromotion - Vérifie l'existence d'une promotion à partir de son id
	  *
	  * @category : checkFunction
	  * @param int $id Id de la promotion
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  *@Author Ali Bellamine
	  *
	  */
	
	function checkPromotion ($id, $erreur = array())
	{
		// Si id est bien une valeur numérique, on vérifie son existence dans la BDD
		if (!is_numeric($id) || checkValue($id, 'promotion', 'id') == FALSE)
		{
				$erreur[27] = true;
		}

		return $erreur;
	}
	
	/**
	  * checkUser - Vérifie l'existence d'un utilisateur à partir de son id
	  *
	  * @category : checkFunction
	  * @param int $id Id de l'utilisateur
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  * @Author Ali Bellamine
	  */
	
	function checkUser ($id, $erreur)
	{		
		// Si id est bien une valeur numérique, on vérifie son existence dans la BDD
		if (!is_numeric($id) || checkValue($id, 'user', 'id') == FALSE)
		{
				$erreur[1] = true;
		}
		
		return $erreur;
	}
	
	/**
	  * checkNbEtudiant - Vérifie l'existence d'un utilisateur à partir de son numéro d'étudiant
	  *
	  * @category : checkFunction
	  * @param int $nbEtudiant Numéro d'étudiant de l'utilisateur
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  * @Author Ali Bellamine
	  */
	
	function checkNbEtudiant ($nbEtudiant, $erreur)
	{
		// Si id est bien une valeur numérique, on vérifie son existence dans la BDD
		if (!is_numeric($nbEtudiant) || checkValue($nbEtudiant, 'user', 'nbEtudiant') == FALSE)
		{
				$erreur[1] = true;
		}

		return $erreur;
	}
	
	/**
	  * checkService - Vérifie l'existence d'un service à partir de son id
	  *
	  * @category : checkFunction
	  * @param int $id Identifiant du service
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  * @Author Ali Bellamine
	  */
	
	function checkService ($id, $erreur)
	{
		// Si id est bien une valeur numérique, on vérifie son existence dans la BDD
		if (!is_numeric($id) || checkValue($id, 'service', 'id') == FALSE)
		{
			$erreur[2] = true;
		}
		
		return $erreur;
	}
	
	/**
	  * checkHopital - Vérifie l'existence d'un hopital à partir de son id
	  *
	  * @category : checkFunction
	  * @param int $id Identifiant de l'hopital
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  * @Author Ali Bellamine
	  */
	
	function checkHopital ($id, $erreur)
	{
		// Si id est bien une valeur numérique, on vérifie son existence dans la BDD
		if (!is_numeric($id) || checkValue($id, 'hopital', 'id') == FALSE)
		{
			$erreur[29] = true;
		}

		return $erreur;
	}
	
	/**
	  * checkCertificat - Vérifie l'existence d'un certificat à partir de son id
	  *
	  * @category : checkFunction
	  * @param int $id Identifiant du certificat
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  * @Author Ali Bellamine
	  */
	
	function checkCertificat ($id, $erreur)
	{
		// Si id est bien une valeur numérique, on vérifie son existence dans la BDD
		if (!is_numeric($id) || checkValue($id, 'certificat', 'id') == FALSE)
		{
			$erreur[30] = true;
		}
		
		return $erreur;
	}
	
	/**
	  * checkSpecialite - Vérifie l'existence d'une spécialité à partir de son id
	  *
	  * @category : checkFunction
	  * @param int $id Identifiant de la spécialité
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  * @Author Ali Bellamine
	  */

	function checkSpecialite ($id, $erreur)
	{
		// Si id est bien une valeur numérique, on vérifie son existence dans la BDD
		if (!is_numeric($id) || checkValue($id, 'specialite', 'id') == FALSE)
		{
			$erreur[31] = true;
		}

		return $erreur;
	}
	
	/**
	  * checkEvaluation - Vérifie l'existence d'une campagne d'évaluation à partir de son id
	  *
	  * @category : checkFunction
	  * @param int $id Identifiant de la campagne d'évaluation
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  * @Author Ali Bellamine
	  */

	function checkEvaluation ($id, $erreur)
	{
		// Si id est bien une valeur numérique, on vérifie son existence dans la BDD
		if (!is_numeric($id) || checkValue($id, 'specialite', 'id') == FALSE)
		{
			$erreur[4] = true;
		}
		return $erreur;
	}
	
	/**
	  * checkRegisterEvaluation - Vérifie l'existence d'une instance de campagne d'évaluation auprès d'un utilisateur (base evaluationregister) à partir de son id
	  *
	  * @category : checkFunction
	  * @param int $id Identifiant de l'instance de campagne d'évaluation
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  * @Author Ali Bellamine
	  */
	
	function checkRegisterEvaluation ($id, $erreur)
	{
		// Si id est bien une valeur numérique, on vérifie son existence dans la BDD
		if (!is_numeric($id) || checkValue($id, 'evaluationregister', 'id') == FALSE)
		{
			$erreur[4] = true;
		}
		
		return $erreur;
	}
	
	/**
	  * checkEvaluationType - Vérifie l'existence d'un module d'évaluation à partir de son id
	  *
	  * @category : checkFunction
	  * @param int $id Identifiant du module d'évaluation
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  * @Author Ali Bellamine
	  */
	
	function checkEvaluationType ($id, $erreur)
	{
		// Si id est bien une valeur numérique, on vérifie son existence dans la BDD
		if (!is_numeric($id) || checkValue($id, 'typeevaluation', 'id') == FALSE)
		{
			$erreur[5] = true;
		}

		return $erreur;
	}
	
	/**
	  * checkAffectation - Vérifie l'existence de l'affectation d'un étudiant à un service à partir de son id
	  *
	  * @category : checkFunction
	  * @param int $id Identifiant de l'affectation de l'étudiant
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  * @Author Ali Bellamine
	  */
	
	function checkAffectation ($id, $erreur)
	{
		// Si id est bien une valeur numérique, on vérifie son existence dans la BDD
		if (!is_numeric($id) || checkValue($id, 'affectationexterne', 'id') == FALSE)
		{
			$erreur[15] = true;
		}
		
		return $erreur;
	}
	
	/**
	  * checkAffectation - Vérifie la validité des données d'affectation d'un étudiant et leurs concordance avant de les ajouter dans la base de donnée
	  *
	  * @category : checkFunction
	  * @param int $etudiant Identifiant de l'étudiant
	  * @param int $service Identifiant du service
	  * @param frenchdate $dateDebut Date du début de l'affectation
	  * @param frenchdate $dateFin Date de fin de l'affectation
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  * @Author Ali Bellamine
	  */

	function checkAffectationInsertData ($etudiant, $service, $dateDebut, $dateFin, $erreur)
	{
		$serviceCheck = FALSE;
		
		// On récupère les données sur l'étudiant si il existe
		if (count(checkUser($etudiant, array())) == 0)
		{
			$userData = getUserData($etudiant);
		}
		else
		{
			$erreur = checkUser($etudiant, $erreur);
		}
		
		// On récupère les infos sur le service
		if (count(checkService($service, array())) == 0)
		{
			$serviceCheck = TRUE;
		}
		else
		{
			$erreur = checkService($service, $erreur);
		}
		
		// Si le service et l'étudiant sont disponibles, on vérifie que l'étudiant n'est pas déjà inscrit dans le service
		if (isset($userData) && $serviceCheck)
		{
			if (isset($userData['service']))
			{
				foreach ($userData['service'] AS $affectationId => $affectationData)
				{
					if (isset($affectationData['id']) && DatetimeToTimestamp(FrenchdateToDatetime($dateDebut)) == $affectationData['dateDebut'] && DatetimeToTimestamp(FrenchdateToDatetime($dateFin)) == $affectationData['dateFin'])
					{
						$erreur[10] = TRUE;
					}
				}
			}
		}
		
		// On vérifie les dates
		if (isset($dateDebut) && (preg_match('#^([0-9]{2})([/-])([0-9]{2})\2([0-9]{4})$#', $dateDebut, $m) == 1 && checkdate($m[3], $m[1], $m[4])))
		{
			$dateDebutTimestamp = DatetimeToTimestamp($m[4].'-'.$m[3].'-'.$m[1]);
		}
		else
		{
			$erreur[11] = TRUE;
		}
		
		if (isset($dateFin) && (preg_match('#^([0-9]{2})([/-])([0-9]{2})\2([0-9]{4})$#', $dateFin, $m) == 1 && checkdate($m[3], $m[1], $m[4])))
		{
			$dateFinTimestamp = DatetimeToTimestamp($m[4].'-'.$m[3].'-'.$m[1]);
		}
		else
		{
			$erreur[12] = TRUE;
		}
		
		if (isset($dateDebutTimestamp) && isset($dateFinTimestamp) && $dateDebutTimestamp > $dateFinTimestamp)
		{
			$erreur[13] = TRUE;
		}
		
		return $erreur;
	}
	
	/**
	  * checkUserInsertData - Vérifie la validité des données d'ajout d'un profil utilisateur
	  *
	  * @category : checkFunction
	  * @param array $userData( Données utilisateur à tester
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction.
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  * @Author Ali Bellamine
	  *	 
	  *  $userData : <br>
	  *		"nom" => (string) nom de l'utilisateur<br>
	  *		"mail" => (array) liste des adresses email de l'utilisateur<br>
	  *		"nbEtu" => (string) numéro d'étudiant de l'utilisateur<br>
	  *		"rang" => (int) rang de l'utilisateur, compris entre 1 (étudiant) et 2 (invités)
	  */

    function checkUserInsertData ($userData, $erreur)
	{		
		// On vérifie que les champs obligatoires ont été remplis
		$required = array('nom', 'mail', 'nbEtu', 'rang');
		foreach ($required AS $requiredName)
		{
			if (!isset($userData[$requiredName]) || $userData[$requiredName] == '')
			{
				$erreur[26] = TRUE;
			}
		}
		
		// On vérifie si l'utilisateur existe déjà
		if (count(checkUser(getUserIdFromNbEtudiant($userData['nbEtu']), array())) == 0)
		{
			$erreur['exist'] = TRUE;
		}
		
		// On récupère les infos sur la promotion
		if (isset($userData['promotion']) && count(checkPromotion($userData['promotion'], array())) != 0)
		{
			$erreur = checkPromotion($userData['promotion'], $erreur);
		}
		
		// On vérifie les mails
		if (isset($userData['mail']) && is_array($userData['mail']))
		{
			foreach ($userData['mail'] AS $email)
			{
				if (!filter_var($email, FILTER_VALIDATE_EMAIL))
				{
					$erreur[19] = TRUE;
				}
			}
		}
		else
		{
			$erreur[19] = TRUE;		
		}
		
		// On vérifie le rang
		if (isset($userData['rang']) && $userData['rang'] != 1 && $userData['rang'] != 2)
		{
			$erreur[28] = TRUE;
		}
		
		return $erreur;
	}
?>