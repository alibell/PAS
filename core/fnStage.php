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
		22/02/15 - fnMenu.php - Ali Bellamine
		Fonctions relatives à la gestion des stages
	*/
	
	/**
	  * getCertificatList - Retourne la liste des certificats enregistrés
	  *
	  * @category stageFunction
	  * @param string $order Paramètre selon lequel sont classés les résultats ('id', 'nom', 'nbServices' ou 'promo')
	  * @param boolean $desc Ordre selon lequel on classe les résultats (TRUE : decroissant, FALSE : croissant)
	  * @return array Array contenant la liste des certificats enregistrés
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	[id du certificat]['id'] => (int) Identifiant du certificat<br>
	  *	[id du certificat]['nom'] => (string) Nom du certificat<br>
	  *	[id du certificat]['promotion']['nom'] => (string) Nom de la promotion associée au certificat<br>
	  *	[id du certificat]['nb']['services'] => (int) Nombre de services associés au certificat
	  *
	  */
	
	function getCertificatList ($order = 'nom', $desc = false) {
		global $db;
		
		$allowedOrder = array('id', 'nom', 'nbServices', 'promo');
		if (in_array($order, $allowedOrder))
		{
			$orderSql = $order;
		}
		else
		{
			$orderSql = 'nom';
		}
		
		
		$sql = 'SELECT c.id id, c.nom nom, p.nom promo, (SELECT count(*) FROM servicecertificat WHERE idCertificat = c.id LIMIT 1) nbServices FROM certificat c INNER JOIN promotion p ON p.id = c.promotion ORDER BY '.$orderSql.' ';
		if ($desc) { $sql .= ' DESC'; }
		$res = $db -> query($sql);
		
		$certificats = array();
		while($res_f = $res -> fetch())
		{
			$certificats[$res_f['id']]['id'] = $res_f['id'];
			$certificats[$res_f['id']]['nom'] = $res_f['nom'];
			$certificats[$res_f['id']]['promotion']['nom'] = $res_f['promo'];
			$certificats[$res_f['id']]['nb']['services'] = $res_f['nbServices'];
		}
		
		return $certificats;
	}
	
	/**
	  * getCertificatInfo - Retourne les informations relatives au certificat
	  *
	  * @category stageFunction
	  * @param int $id Identifiant du certificat
	  * @return array Array contenant les informations relatives au certificat
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	['id'] => (int) Identifiant du certificat<br>
	  *	['nom'] => (string) Nom du certificat<br>
	  *	['promotion']['id'] => (int) Id de la promotion associée au certificat<br>
	  *	['promotion']['nom'] => (string) Nom de la promotion associée au certificat<br>
	  *	['services'][id du service]['id'] => (int) Id des services associés au certificat<br>
	  *	['etudiants'][id de l'étudiant]['id'] => (int) Id des étudiants associés au certificat<br>
	  *	['nb']['services'] => (int) Nombre de services associés au certificat<br>
	  *	['nb']['etudiants'] => (int) Nombre d'étudiants associés au certificat
	  *
	  */
	
	function getCertificatInfo($id)
	{
		/*
			Initialisation des variables
		*/
		global $db; // Permet l'accès à la BDD
		$erreur = array();
		$specialite = array();
		
		/*
			On vérifie l'existance du certificat
		*/
		$erreur = checkCertificat($id, $erreur);
		if (count($erreur) == 0)
		{

			// Récupérations des données
			$sql = 'SELECT c.id id, c.nom nom, p.nom promo, p.id promotionId, (SELECT count(*) FROM servicecertificat WHERE idCertificat = c.id LIMIT 1) nbServices 
						FROM certificat c 
						INNER JOIN promotion p ON p.id = c.promotion WHERE c.id = ? LIMIT 1';
			$res = $db -> prepare($sql);
			$res -> execute(array($id));
			
			$certificat = array();
			
			// On construit l'array contenant les données
			if ($res_f = $res -> fetch())
			{
				$certificat['id'] = $res_f['id'];
				$certificat['nom'] = $res_f['nom'];
				$certificat['promotion']['nom'] = $res_f['promo'];
				$certificat['promotion']['id'] = $res_f['promotionId'];
				$certificat['nb']['services'] = $res_f['nbServices'];
			}
			
			// Liste des services enregistrés dans le certificat
			
			$sql = 'SELECT s.id id
						FROM servicecertificat sc
						INNER JOIN service s ON s.id = sc.idService
						INNER JOIN hopital h ON h.id = s.hopital
						WHERE sc.idCertificat = ?
						ORDER BY h.nom  ASC, s.nom ASC';
			$res = $db -> prepare($sql);
			$res -> execute(array($id));
			
			while ($res_f = $res -> fetch())
			{
				$certificat['services'][$res_f['id']]['id'] = $res_f['id'];
			}
			
			// Liste des étudiants enregistrés dans le certificat
			
			$certificat['etudiants'] = array();
			
			$sql = 'SELECT u.id id
						FROM servicecertificat sc
						INNER JOIN affectationexterne ae ON ae.service = sc.idService
						INNER JOIN user u ON u.id = ae.userId
						WHERE sc.idCertificat = :certificat AND ae.dateDebut <= :now AND ae.dateFin >= :now
						ORDER BY u.nom  ASC, u.prenom ASC';
			$res = $db -> prepare($sql);
			$res -> execute(array('certificat' => $id, 'now' => TimestampToDatetime(time())));
			
			while ($res_f = $res -> fetch())
			{
				$certificat['etudiants'][$res_f['id']]['id'] = $res_f['id'];
			}
			
			$certificat['nb']['etudiants'] = count($certificat['etudiants']);
			
			return $certificat;
		}
		else
		{
			return false;
		}
	}
	
	/**
	  * getSpecialiteList - Retourne la liste des spécialités enregistrés
	  *
	  * @category stageFunction
	  * @param string $order Paramètre selon lequel sont classés les résultats ('id', 'nom' ou 'nb')
	  * @param boolean $desc Ordre selon lequel on classe les résultats (TRUE : decroissant, FALSE : croissant)
	  * @return array Array contenant la liste des certificats enregistrés
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	[id de la spécialité][] => (array) Array contenant les données relatives à la spécialité, voir {@link getSpecialiteInfo()}
	  *
	  */
	
	function getSpecialiteList ($order = 'nom', $desc = false) {
		global $db;
		
		$allowedOrder = array('id', 'nom', 'nb');
		if (in_array($order, $allowedOrder))
		{
			$orderSql = $order;
		}
		else
		{
			$orderSql = 'nom';
		}
		
		
		$sql = 'SELECT sp.id id, sp.nom nom, (SELECT count(*) FROM service WHERE specialite = sp.id LIMIT 1) nb FROM specialite sp ORDER BY '.$orderSql.' ';
		if ($desc) { $sql .= ' DESC'; }
		$res = $db -> query($sql);
		
		$specialite = array();
		while($res_f = $res -> fetch())
		{
			$specialite[$res_f['id']] = getSpecialiteInfo($res_f['id']);
		}
		
		return $specialite;
	}
	
	/**
	  * getSpecialiteInfo - Retourne les informations relatives à la spécialité
	  *
	  * @category stageFunction
	  * @param int $id Identifiant de la spécialité
	  * @return array Array contenant les informations relatives à la spécialité
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	['id'] => (int) Identifiant de la spécialité<br>
	  *	['nom'] => (string) Nom de la spécialité<br>
	  *	['nb'] => (int) Nombre de services associés à la spécialité<br>
	  *	['services'][id du service]['id'] => (int) Identifiant des services associés à la spécialité
	  *
	  */
	
	function getSpecialiteInfo($id)
	{
		/*
			Initialisation des variables
		*/
		global $db; // Permet l'accès à la BDD
		$erreur = array();
		$specialite = array();
		
		/*
			On vérifie l'existance de la spécialité
		*/
		$erreur = checkSpecialite($id, $erreur);
		if (count($erreur) == 0)
		{

			// Récupérations des données de la spécialité
			$sql = 'SELECT sp.id id, sp.nom nom, (SELECT count(*) FROM service WHERE specialite = sp.id LIMIT 1) nb
					  FROM specialite sp
					  WHERE sp.id = ?
					  LIMIT 1';
			$res = $db -> prepare($sql);
			$res -> execute(array($id));
			
			$specialite = array();
			
			// On construit l'array contenant les données de l'hopital
			if ($res_f = $res -> fetch())
			{
				$specialite['id'] = $res_f['id'];
				$specialite['nom'] = $res_f['nom'];
				$specialite['nb'] = $res_f['nb'];
			}
			
			// Liste des services enregistrés dans l'hopital
			
			$sql = 'SELECT s.id id
						FROM service s
						INNER JOIN hopital h
						ON s.hopital = h.id
						WHERE s.specialite = ?
						ORDER BY h.nom  ASC, s.nom ASC';
			$res = $db -> prepare($sql);
			$res -> execute(array($id));
			
			while ($res_f = $res -> fetch())
			{
				$specialite['services'][$res_f['id']]['id'] = $res_f['id'];
			}
			
			return $specialite;
		}
		else
		{
			return false;
		}
	}
	
	/**
	  * getHospitalList - Retourne la liste des hopitaux enregistrés
	  *
	  * @category stageFunction
	  * @param string $order Paramètre selon lequel sont classés les résultats ('id', 'nom', 'alias' ou 'nb')
	  * @param boolean $desc Ordre selon lequel on classe les résultats (TRUE : decroissant, FALSE : croissant)
	  * @return array Array contenant la liste des hopitaux enregistrés
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	[id de l'hopital][] => (array) Array contenant les données relatives à l'hopital, voir {@link getHospitalInfo()}
	  *
	  */
	
	function getHospitalList ($order = 'nom', $desc = false) {
		global $db;
		
		/*
			On met le filtre
		*/
		
		$allowedOrder = array('id', 'nom', 'alias', 'nb');
		if (in_array($order, $allowedOrder))
		{
			$orderSql = $order;
		}
		else
		{
			$orderSql = 'nom';
		}
		
		$sql = 'SELECT h.id id, h.nom nom, h.alias alias, (SELECT count(*) FROM service WHERE hopital = h.id LIMIT 1) nb FROM hopital h ORDER BY '.$orderSql.' ';
		if ($desc) { $sql .= ' DESC'; }
		$res = $db -> query($sql);
		
		$hopitaux = array();
		while($res_f = $res -> fetch())
		{
			$hopitaux[$res_f['id']] = getHospitalInfo($res_f['id']);
		}
		
		return $hopitaux;
	}
	
	/**
	  * getHospitalInfo - Retourne les informations relatives à la spécialité
	  *
	  * @category stageFunction
	  * @param int $id Identifiant de l'hopital
	  * @return array Array contenant les informations relatives à l'hopital
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	['id'] => (int) Identifiant de l'hopital<br>
	  *	['nom'] => (string) Nom de l'hopital<br>
	  *	['alias'] => (string) Alias du nom de l'hopital<br>
	  *	['adresse']['rue'] => (string) Adresse de l'hopital (rue)<br>
	  *	['adresse']['cp'] => (string) Adresse de l'hopital (code postal)<br>
	  *	['adresse']['ville'] => (string) Adresse de l'hopital (ville)<br>
	  *	['nb'] => (int) Nombre de services associés à l'hopital<br>
	  *	['services'][id du service]['id'] => (int) Identifiant des services associés à l'hopital
	  *
	  */
	
	function getHospitalInfo($id)
	{
		/*
			Initialisation des variables
		*/
		global $db; // Permet l'accès à la BDD
		$erreur = array();
		$service = array();
		
		/*
			On vérifie l'existance du service
		*/
		$erreur = checkHopital($id, $erreur);
		if (count($erreur) == 0)
		{

			// Récupérations des données de l'hopital
			$sql = 'SELECT h.id id, h.nom hopitalNom, h.alias hopitalAlias, h.rue hopitalRue, h.cp hopitalCP, h.ville hopitalVille, (SELECT count(*) FROM service WHERE hopital = h.id LIMIT 1) nb
					  FROM hopital h
					  WHERE h.id = ?
					  LIMIT 1';
			$res = $db -> prepare($sql);
			$res -> execute(array($id));
			
			$hopital = array();
			
			// On construit l'array contenant les données de l'hopital
			if ($res_f = $res -> fetch())
			{
				$hopital['id'] = $res_f['id'];
				$hopital['nom'] = $res_f['hopitalNom'];
				$hopital['alias'] = $res_f['hopitalAlias'];
				$hopital['nb'] = $res_f['nb'];
				$hopital['adresse']['rue'] = $res_f['hopitalRue'];
				$hopital['adresse']['cp'] = $res_f['hopitalCP'];
				$hopital['adresse']['ville'] = $res_f['hopitalVille'];
			}
			
			// Liste des services enregistrés dans l'hopital
			
			$sql = 'SELECT s.id id
						FROM service s
						INNER JOIN specialite sp
						ON s.specialite = sp.id
						WHERE s.hopital = ?
						ORDER BY sp.nom  ASC, s.nom ASC';
			$res = $db -> prepare($sql);
			$res -> execute(array($id));
			
			while ($res_f = $res -> fetch())
			{
				$hopital['services'][$res_f['id']]['id'] = $res_f['id'];
			}
			
			return $hopital;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	  * getServiceList - Retourne la liste des services enregistrés
	  *
	  * @category stageFunction
	  * @return array Array contenant la liste des services enregistrés
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	[id du service][] => (array) Array contenant les informations relatives au service, voir {@link getServiceInfo()}
	  *
	  */
	
	function getServiceList () {
		global $db;
		
		$sql = 'SELECT id FROM service ORDER BY id ASC';
		$res = $db -> query($sql);
		
		$services = array();
		while($res_f = $res -> fetch())
		{
			$services[$res_f['id']] = getServiceInfo($res_f['id']);
		}
		
		return $services;
	}
	
	/**
	  * getServiceInfo - Retourne les informations relatives au service
	  *
	  * @category stageFunction
	  * @param int $id Identifiant du service
	  * @return array Array contenant les informations relatives au service
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	['id'] => (int) Identifiant du service<br>
	  *	['nom'] => (string) Nom du service<br>
	  *	['hopital']['alias'] => (string) Alias du nom de l'hopital dont dépend le service<br>
	  *	['hopital']['nom'] => (string) Nom de l'hopital dont dépend le service<br>
	  *	['specialite']['id'] (optionnel) => (int) Identifiant de la spécialité dont dépend le service<br>
	  *	['specialite']['nom'] => (string) Nom de la spécialité dont dépend le service<br>
	  *	['chef']['id'] (optionnel) => (int) Identifiant du chef du service<br>
	  *	['chef']['nom'] => (string)  Nom du chef du service<br>
	  *	['chef']['prenom'] => (string) Prénom du chef du service<br>
	  *	['certificat'][identifiant du certificat]['id'] => (int) Identifiant d'un certificat associé au service<br>
	  *	['certificat'][identifiant du certificat]['idAffectation'] => (int) Identifiant du lien dans la base de donnée entre le service et le certificat<br>
	  *	['certificat'][identifiant du certificat]['nom'] => (string) Nom d'un certificat associé au service<br>
	  *	['FullName'] => (string) Nom complet du service
	  *
	  */
	
	function getServiceInfo($id)
	{
		/*
			Initialisation des variables
		*/
		global $db; // Permet l'accès à la BDD
		$erreur = array();
		$service = array();
		
		/*
			On vérifie l'existance du service
		*/
		$erreur = checkService($id, $erreur);
		if (count($erreur) == 0)
		{

			// Récupérations des données du service
			$sql = 'SELECT s.id serviceId, s.nom serviceNom, h.nom hopitalNom, h.id hopitalId, h.alias hopitalAlias, sp.id specialiteId, sp.nom specialiteNom, u.nom chefNom, u.prenom chefPrenom, u.id chefId
					  FROM service s
					  INNER JOIN hopital h on h.id = s.hopital
					  LEFT JOIN specialite sp on sp.id = s.specialite
					  LEFT JOIN user u ON u.id = s.chef
					  WHERE s.id = ?
					  LIMIT 1';
			$res = $db -> prepare($sql);
			$res -> execute(array($id));
			
			// On construit l'array contenant les données du service
			if ($res_f = $res -> fetch())
			{
				$service['id'] = $res_f['serviceId'];
				$service['nom'] = $res_f['serviceNom'];
				$service['hopital']['id'] = $res_f['hopitalId'];
				$service['hopital']['nom'] = $res_f['hopitalNom'];
				$service['hopital']['alias'] = $res_f['hopitalAlias'];
				
				if (isset($res_f['specialiteNom'])) {
					$service['specialite']['id'] = $res_f['specialiteId']; 
					$service['specialite']['nom'] = $res_f['specialiteNom']; 
				} else {  $service['specialite']['nom'] = ''; }
								
				if (isset($res_f['chefNom'])) {
					$service['chef']['id'] = $res_f['chefId'];
					$service['chef']['nom'] = $res_f['chefNom'];
					$service['chef']['prenom'] = $res_f['chefPrenom'];
				}
				else
				{
					$service['hopital']['chef']['nom'] = '';
					$service['hopital']['chef']['prenom'] = '';
				}
				
				/*
					On récupère la liste des certificats du service
				*/
				$service['certificat'] = array();
				$sql = 'SELECT c.id certificatId, c.nom certificatNom, s.id idAffectation
							FROM servicecertificat s
							INNER JOIN certificat c ON c.id = s.idCertificat
							WHERE s.idService = ?';
				$res2 = $db -> prepare($sql);
				$res2 -> execute(array($res_f['serviceId']));
				while ($res2_f = $res2 -> fetch())
				{
					$service['certificat'][$res2_f['certificatId']]['id'] = $res2_f['certificatId']; 
					$service['certificat'][$res2_f['certificatId']]['idAffectation'] = $res2_f['idAffectation']; 
					$service['certificat'][$res2_f['certificatId']]['nom'] = $res2_f['certificatNom']; 
				}
				
				// String pour la génération du FullName
				$fullNameCertificat = '';
				if (isset($service['certificat']) && count($service['certificat']) != 0)
				{
					foreach ($service['certificat'] AS $certificat)
					{
						$fullNameCertificat .= '['.$certificat['nom'].']';
					}
				}
				
				$firstTerm = TRUE;
				$service['FullName'] = '';
				
				/* On inclut les données une à une dans le champs texte */
				if (isset($fullNameCertificat) && $fullNameCertificat != '')
				{
					if (!$firstTerm) { $service['FullName'] .= ' - '; } else { $firstTerm = FALSE; }
					$service['FullName'] .= $fullNameCertificat;
				}
				
				if (isset($service['specialite']['nom']) && $service['specialite']['nom'] != '')
				{
					if (!$firstTerm) { $service['FullName'] .= ' - '; } else { $firstTerm = FALSE; }
					$service['FullName'] .= $service['specialite']['nom'];
				}
				
				if (isset($service['hopital']['alias']) && $service['hopital']['alias'] != '')
				{
					if (!$firstTerm) { $service['FullName'] .= ' - '; } else { $firstTerm = FALSE; }
					$service['FullName'] .= $service['hopital']['alias'];
				}
				
				if (isset($service['nom']) && $service['nom'] != '')
				{
					if (!$firstTerm) { $service['FullName'] .= ' - '; } else { $firstTerm = FALSE; }
					$service['FullName'] .= $service['nom'];
				}
				
				$service['FullName'] .= ' - '.LANG_ADMIN_SERVICES_NOM_SERVICEOF.' '.$service['chef']['nom'];
			}

			return $service;
		}
		else
		{
			return false;
		}
	}
	
	/**
	  * getAffectationData - Retourne les informations relatives à l'affectation d'un étudiant dans un service
	  *
	  * @category stageFunction
	  * @param int $id Identifiant du lien d'affectation de l'étudiant dans la base de donnée
	  * @return array Array contenant les informations relatives à l'affectation de l'étudiant dans le service
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	['id'] => (int) Identifiant du lien d'affectation de l'étudiant dans la base de donnée<br>
	  *	['service']['id'] => (int) Identifiant du service dans lequel l'étudiant est affecté<br>
	  *	['user']['id'] => (int) Identifiant de l'utilisateur<br>
	  *	['date']['debut'] => (string)  Date du début de la période d'affectation de l'étudiant dans le service sous forme de Timestamp<br>
	  *	['date']['fin'] => (string)  Date du fin de la période d'affectation de l'étudiant dans le service sous forme de Timestamp
	  *
	  */
	
	function getAffectationData($id)
	{
		/*
			Initialisation des variables
		*/
		global $db; // Permet l'accès à la BDD
		$erreur = array();
		
		/*
			On vérifie l'existance de l'affectation
		*/
		$erreur = checkAffectation($id, $erreur);
		if (count($erreur) == 0)
		{

			// Récupérations des données de l'hopital
			$sql = 'SELECT ae.id id, ae.userId userId, ae.service serviceId, ae.dateDebut dateDebut, ae.dateFin dateFin
					  FROM affectationexterne ae
					  WHERE ae.id = ?
					  LIMIT 1';
			$res = $db -> prepare($sql);
			$res -> execute(array($id));
			
			$affectation = array();
			
			// On construit l'array contenant les données de l'hopital
			if ($res_f = $res -> fetch())
			{
				$affectation['id'] = $res_f['id'];
				$affectation['service']['id'] = $res_f['serviceId'];
				$affectation['user']['id'] = $res_f['userId'];
				$affectation['date']['debut'] = DatetimeToTimestamp($res_f['dateDebut']);
				$affectation['date']['fin'] = DatetimeToTimestamp($res_f['dateFin']);				
			}
			
			return $affectation;
		}
		else
		{
			return false;
		}
	}
?>