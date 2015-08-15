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
		22/02/15 - fnEvaluation.php - Ali Bellamine
		Fonctions en rapport avec les évaluations
	*/
	
	/**
	  * getEvaluationTypeList - Retourne la liste des modules d'évaluation installés
	  *
	  * @category : evaluationFunction
	  * @return array Array contenant la liste des modules d'évaluations installés
	  *
	  * @Author Ali Bellamine
	  *
	  *	 Contenu de l'array retourné :<br>
	  *		[id du module d'évaluation]['id'] => (int) id du module d'évaluation<br>
	  *		[id du module d'évaluation]['actif'] => (int) 0 si le module est inactif, 1 si il est actif<br>
	  *		[id du module d'évaluation]['nom'] => (string) nom du module d'évaluation<br>
	  *		[id du module d'évaluation]['dossier'] => (string) dossier d'installation du module d'évaluation<br>
	  *		[id du module d'évaluation]['resultRight'] => (int) array contenant les droits d'accès (0 ou 1) aux résultats d'évaluation selon le rang de l'utilisateur (de 0 à 4)<br>
	  *		[id du module d'évaluation]['nbInstances'] => (int) nombre d'instances du module d'évaluation
	  */
	
	function getEvaluationTypeList ()
	{
		global $db;
		$evaluationTypeList = array();
		
		$sql = 'SELECT t.id typeevaluationId, t.nom typeevaluationNom, t.nomDossier typeevaluationDossier, t.actif typeevaluationActif, t.result_access_1 access1, t.result_access_2 access2, t.result_access_3 access3, t.result_access_4 access4, (SELECT count(*) FROM evaluation e WHERE e.type = t.id) typeevaluationNbInstances
					FROM typeevaluation t';
		$res = $db -> query($sql);
		while ($res_f = $res -> fetch())
		{
			$evaluationTypeList[$res_f['typeevaluationId']]['id'] = $res_f['typeevaluationId'];
			$evaluationTypeList[$res_f['typeevaluationId']]['actif'] = $res_f['typeevaluationActif'];
			$evaluationTypeList[$res_f['typeevaluationId']]['nom'] = $res_f['typeevaluationNom'];
			$evaluationTypeList[$res_f['typeevaluationId']]['dossier'] = $res_f['typeevaluationDossier'];
			for ($n = 1; $n <= 4; $n++)
			{
				if (isset($res_f['access'.$n]) && $res_f['access'.$n] == 1)
				{
					$evaluationTypeList[$res_f['typeevaluationId']]['resultRight'][$n] = 1;
				}
				else
				{
					$evaluationTypeList[$res_f['typeevaluationId']]['resultRight'][$n] = 0;
				}
			}
			$evaluationTypeList[$res_f['typeevaluationId']]['nbInstances'] = $res_f['typeevaluationNbInstances'];
		}
		
		return $evaluationTypeList;
	}
	
	/**
	  * updateEvaluationsTypes - Vérifie la validité des modules d'évaluation installés et met à jours leurs données d'installation
	  *
	  * @category : evaluationFunction
	  *
	  * @Author Ali Bellamine
	  */
	
	function updateEvaluationsTypes()
	{
		/*
			Préparation des variables
		*/
		global $db; // Base de donnée
		$listEvaluationsType = array(); // Array contenant la liste des évaluations

		// On charge la liste des plugins contenues dans le dossier evaluations
		if ($pluginsEvaluations = scandir($_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/evaluations'))	
		{
			foreach ($pluginsEvaluations AS $file)
			{
				if (is_dir($_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/evaluations/'.$file) && $file != '..' && $file != '.')
				{
					/**
						Pour chaque évaluation, on l'enregistre dans la variable $listEvaluationsType et on enregistre ses informations
					**/
					
					$listEvaluationsType[$file]['dossier'] = $file; // dossier de l'évaluation
					
					/*
						Récupèration du nom de l'évaluation dans le fichier XML
					*/
					
					if (is_file($_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/evaluations/'.$file.'/MANIFEST.xml'))
					{
						if  ($manifest = simplexml_load_file($_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/evaluations/'.$file.'/MANIFEST.xml'))
						{
							$listEvaluationsType[$file]['nom']  = $manifest -> nom -> __toString();					
						}
					}
					else
					{
						$listEvaluationsType[$file]['nom']  = false;
					}
					
					/*
						On vérifie que le type d'évaluation contient les fichiers nécessaire à son fonctionnement, si les 2 conditions suivantes sont validées on met la variable "valid" sur 1 :
							-  Présence des fichiers : displayEvaluation.php, displayEvaluationResult.php
							- Présence de la variable $listEvaluationsType[$file]['nom']
					*/
					
					if (is_file($_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/evaluations/'.$file.'/displayEvaluation.php') && is_file($_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/evaluations/'.$file.'/displayEvaluationResult.php') && (isset($listEvaluationsType[$file]['nom']) && $listEvaluationsType[$file]['nom'] != false))
					{
						$listEvaluationsType[$file]['valid'] = 1;
					}
					else
					{
						$listEvaluationsType[$file]['valid']  = 0;
					}
				}
			}
		}
		
		/*
			On charge la liste des évaluations déjà enregistrées
		*/
		
		$bddEvaluationTypeList = getEvaluationTypeList();
		foreach ($bddEvaluationTypeList  AS $bddEvaluationType)
		{
			/*
				Pour chaque plugin :
					- Soit elle existe dans le dossiers evaluations :
						* Elle est identique en tout point: on la supprime de l'array $listEvaluationsType
						* Elle présente une différence : on met à jour les données de la BDD puis on la supprime de l'array $listEvaluationsType
					- Soit elle n'existe pas :  on met la variable valid sur 0
			*/

			if (isset($bddEvaluationType['dossier']) && isset($listEvaluationsType[$bddEvaluationType['dossier']]))
			{
				if ($listEvaluationsType[$bddEvaluationType['dossier']]['valid'] == $bddEvaluationType['actif'] && $listEvaluationsType[$bddEvaluationType['dossier']]['nom'] == $bddEvaluationType['nom'] && $listEvaluationsType[$bddEvaluationType['dossier']]['dossier'] == $bddEvaluationType['dossier'])
				{
					// On supprime le type de l'array $listEvaluationsType
					unset($listEvaluationsType[$bddEvaluationType['dossier']]);
				}
				else
				{
					// On met à jour le type d'évaluation dans la BDD
					
					if ($listEvaluationsType[$bddEvaluationType['dossier']]['nom'])
					{
						$sql = 'UPDATE typeevaluation SET nom =  "'.$listEvaluationsType[$bddEvaluationType['dossier']]['nom'].'", actif = "'.$listEvaluationsType[$bddEvaluationType['dossier']]['valid'].'" WHERE nomDossier = "'.$listEvaluationsType[$bddEvaluationType['dossier']]['dossier'].'" LIMIT 1';
					}
					else
					{
						$sql = 'UPDATE typeevaluation SET actif = "'.$listEvaluationsType[$bddEvaluationType['dossier']]['valid'].'" WHERE nomDossier = "'.$listEvaluationsType[$bddEvaluationType['dossier']]['dossier'].'" LIMIT 1';
					} 
					// On stock la requête dans l'array $listEvaluationsType
					$listEvaluationsType[$bddEvaluationType['dossier']]['sql'] = $sql;
				}
			}
			else // On le rajoute dans $listEvaluationsType afin que la requête sql soit executée ensuite
			{
				$listEvaluationsType[$bddEvaluationType['dossier']]['dossier'] = $res_f['dossier'];
				$listEvaluationsType[$bddEvaluationType['dossier']]['valid'] = 0;
				$listEvaluationsType[$bddEvaluationType['dossier']]['sql'] = 'UPDATE typeevaluation SET actif = 0 WHERE nomDossier = "'.$bddEvaluationType['dossier'].'" LIMIT 1';
			}
		}
		
		// On parcours l'array, si des requêtes sont en attente, on les execute, sinon on réalise des insert
		if (isset($listEvaluationsType) && count($listEvaluationsType) > 0)
		{
			foreach ($listEvaluationsType AS $evaluationType)
			{
				if (isset($evaluationType['sql']))
				{
					$res = $db -> query($evaluationType['sql']);
					unset($listEvaluationsType[$evaluationType['dossier']]);
				}
				else if ($evaluationType['valid'] == 1)
				{
					$sql = 'INSERT INTO typeevaluation (nom, nomDossier, actif) VALUES (
								"'.$evaluationType['nom'].'",
								"'.$evaluationType['dossier'].'",
								"'.$evaluationType['valid'].'"
							)';				
							echo $sql;
					$db -> query($sql);
					unset($listEvaluationsType[ $evaluationType['dossier']]);
				}
			}
		}
	}
	
	/**
	  * getEvalList - Retourne la liste des instances de campagnes d'évaluations disponibles pour un utilisateur donné
	  *
	  * @category : evaluationFunction
	  * @param int $id Identifiant de l'utilisateur
	  * @return array Array contenant la liste des instances de campagne d'évaluation disponible pour l'utilisateur
	  *
	  * @Author Ali Bellamine
	  *
	  *	 Contenu de l'array retourné :<br>
	  *		[id de la campagne d'évaluation] => (array) données relative à une évaluation voir {@link getEvalData()}<br>
	  *		[id de la campagne d'évaluation]['registerId'] => (int) id de l'instance de la campagne d'évaluation pour l'utilisateur<br>
	  *		[id de la campagne d'évaluation]['data'] => (string) informations optionelles relatives à l'instance de la campagne d'évaluation<br>
	  *		[id de la campagne d'évaluation]['remplissage']['valeur'] => (int) statut de remplissage de l'instance de la campagne d'évaluation (0 ou 1)<br>
	  *		[id de la campagne d'évaluation]['remplissage']['date'] => (string) date du remplissage de l'instance de la campagne d'évaluation sous forme de timestamp
	  *
	  */
	
	function getEvalList($id)
	{
		/*
			Initialisation des variables
		*/
		global $db; // Permet l'accès à la BDD
		$erreur = array();
		$evaluations = array();
		
		/*
			On vérifie l'existance de l'utilisateur
		*/
		$erreur = checkUser($id, $erreur);
		
		if (count($erreur) == 0)
		{
			/*
				Récupèration informations contenant l'utilisateur
			*/
			
			$sql = 'SELECT e.id evaluationId, er.id registerId, er.date dateRemplissage, er.evaluationData evaluationData, er.evaluationStatut remplissageStatut FROM evaluationregister er INNER JOIN evaluation e ON e.id = er.evaluationId WHERE userId = ?';
			$res = $db -> prepare($sql);
			if ($res -> execute(array($id)))
			{
				while ($res_f = $res -> fetch())
				{
					$evaluations[$res_f['evaluationId']] = getEvalData($res_f['evaluationId']);
					$evaluations[$res_f['evaluationId']]['registerId'] = $res_f['registerId'];
					$evaluations[$res_f['evaluationId']]['data'] = $res_f['evaluationData'];
					
					// On enregistre que l'évaluation a été faite si elle a été faite
					if (isset($res_f['dateRemplissage']) && $res_f['remplissageStatut'] == 1)
					{
						$evaluations[$res_f['evaluationId']]['remplissage']['valeur'] = true;					
						$evaluations[$res_f['evaluationId']]['remplissage']['date'] = DatetimeToTimestamp($res_f['dateRemplissage']);					
					}
					else
					{
						$evaluations[$res_f['evaluationId']]['remplissage']['valeur'] = false;	
					}
				}
			}
				
			return $evaluations;
		}
		else 
		{
			return false;
		}
	}
	
	/**
	  * getEvalCampaignList - Retourne la liste des campagnes d'évaluations
	  *
	  * @category : evaluationFunction
	  * @return array Array contenant la liste des campagnes d'évaluations
	  *
	  * @Author Ali Bellamine
	  *
	  *	 Contenu de l'array retourné :<br>
	  *		[id de la campagne d'évaluation] => (array) données relative à une évaluation voir {@link getEvalData()}
	  *
	  */
	
	function getEvalCampaignList ()
	{
		/*
			Initialisation des variables
		*/
			global $db;
			$evalList = array();
			
		/*
			On récupère la liste des campagnes d'évaluation
		*/
		$sql = 'SELECT e.id evaluationId FROM evaluation e ORDER BY e.dateDebut DESC, e.dateFin DESC, e.nom ASC';
		if ($res = $db -> query($sql))
		{
			while ($res_f = $res -> fetch())
			{
				$evalList[$res_f['evaluationId']] = getEvalData($res_f['evaluationId']);
			}
			
			return $evalList;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	  * getEvalRegisterData - Retourne les informations relative à une instance de campagne d'évaluation
	  *
	  * @category : evaluationFunction
	  * @param int $id Identifiant de l'instance de campagne d'évaluation
	  * @return array Array contenant les données relative à l'instance de campagne d'évaluation
	  *
	  * @Author Ali Bellamine
	  *
	  *	 Contenu de l'array retourné :<br>
	  *		['id'] => (int) identifiant de l'instance de campagne d'évaluation<br>
	  *		['evaluation']['id'] => (int) identifiant de la campagne d'évaluation<br>
	  *		['evaluation']['data'] => (string) informations optionelles relatives à l'instance de la campagne d'évaluation<br>
	  *		['statut'] => (int) statut de remplissage de l'instance de la campagne d'évaluation (0 ou 1)<br>
	  *		['date'] => (string) date du remplissage de l'instance de la campagne d'évaluation sous forme de timestamp<br>
	  *		['user']['id'] => (int) identifiant de l'utilisateur concerné par l'instance de campagne d'évaluation
	  *
	  */
	
	function getEvalRegisterData($id)
	{
		/*
			Initialisation des variables
		*/
		global $db; // Permet l'accès à la BDD
		$erreur = array();
		$evaluation = array();
		
		/*
			On vérifie l'existance de l'id
		*/
		$erreur = checkRegisterEvaluation($id, $erreur);
		
		if (count($erreur) == 0)
		{

			/*
				Récupération des informations sur l'évaluation
			*/
			
			$sql = 'SELECT er.id evaluationRegisterId, er.evaluationId evaluationId, er.userId userId, er.evaluationData evaluationData, er.date evaluationRegisterDate, er.evaluationStatut registerStatut
						FROM evaluationregister er
						WHERE er.id = ?
						LIMIT 1';
			$res = $db -> prepare ($sql);
			$res -> execute(array($id));
			
			if ($res_f = $res -> fetch())
			{
				$evaluation['id'] = $res_f['evaluationRegisterId'];
				$evaluation['evaluation']['id'] = $res_f['evaluationId'];
				$evaluation['evaluation']['data'] = $res_f['evaluationData'];
				$evaluation['statut'] = $res_f['registerStatut'];
				if (isset($evaluation['date']) && $evaluation['date'] != 0 && $evaluation['date'] != NULL)
				{
					$evaluation['date'] = DatetimeToTimestamp($res_f['evaluationRegisterDate']);
				}
				else
				{
					$evaluation['date'] = FALSE;
				}
				$evaluation['user']['id'] = $res_f['userId'];
			}
			
			return $evaluation;
		}
		else 
		{
			return false;
		}
	}
	
	/**
	  * getEvalData - Retourne les informations relative à une campagne d'évaluation
	  *
	  * @category : evaluationFunction
	  * @param int $id Identifiant de la campagne d'évaluation
	  * @return array Array contenant les données relative à la campagne d'évaluation
	  *
	  * @Author Ali Bellamine
	  *
	  *	 Contenu de l'array retourné :<br>
	  *		['id'] => (int) identifiant de la campagne d'évaluation<br>
	  *		['nom'] => (string) nom de la campagne d'évaluation<br>
	  *		['date'][debut'] => (string) Date de début de la campagne d'évaluation sous forme de Timestamp<br>
	  *		['date'][fin'] => (string) Date de fin de la campagne d'évaluation sous forme de Timestamp<br>
	  *		['type'][id'] => (int) Identifiant du module de la campagne d'évaluation<br>
	  *		['type'][nom'] => (string) Nom du module de la campagne d'évaluation<br>
	  *		['type'][dossier'] => (string) Dossier d'installation du module de la campagne d'évaluation<br>
	  *		['type'][data'] => (array) Contient les données relatives au type de module d'évaluation, voir {@link getEvalTypeData()}<br>
	  *		['type'][statut'] => (int) Si 0, le module d'évaluation est actuellement inactif, si 1 il est actuellement actif<br>
	  *		['users'][id de l'utilisateur] => (array) Contient les informations relatives à l'utilisateur : nom, prenom, promotion, nbEtudiant, voir {@link: getUserData()}<br>
	  *		['orderedUsers'][] => (array) Même informations que ci dessus, mais celles-ci sont classé par ordre de résultats de la requête SQL et non plus par id (évaluations non remplis en premier)<br>
	  *		['orderedUsers']['nb'] => (int) Nombre total d'utilisateurs<br>
	  *		['nb']['remplis'] => (int) nombre d'évaluations remplis<br>
	  *		['nb']['total'] => (int) nombre total d'utilisateurs
	  */

  function getEvalData($id)
	{
		/*
			Initialisation des variables
		*/
		global $db; // Permet l'accès à la BDD
		$erreur = array();
		$evaluation = array();
		
		/*
			On vérifie l'existance de l'évaluation
		*/
		$erreur = checkEvaluation($id, $erreur);
		
		if (count($erreur) == 0)
		{

			/*
				Récupération des informations sur l'évaluation
			*/
			
			$sql = 'SELECT e.id evaluationId, e.nom evaluationNom, e.dateDebut evaluationDateDebut, e.dateFin evaluationDateFin, t.id evaluationTypeId, t.nom evaluationTypeNom, t.nomDossier evaluationTypeDossier, t.actif evaluationTypeStatut
						FROM evaluation e
						INNER JOIN typeevaluation t ON t.id = e.type
						WHERE e.id = ?
						LIMIT 1';
			$res = $db -> prepare ($sql);
			$res -> execute(array($id));
			
			while ($res_f = $res -> fetch())
			{
				$evaluation['id'] = $res_f['evaluationId'];
				$evaluation['nom'] = $res_f['evaluationNom'];
				$evaluation['date']['debut'] = DatetimeToTimestamp($res_f['evaluationDateDebut']);
				$evaluation['date']['fin'] = DatetimeToTimestamp($res_f['evaluationDateFin']);
				$evaluation['type']['id'] = $res_f['evaluationTypeId'];
				$evaluation['type']['nom'] = $res_f['evaluationTypeNom'];
				$evaluation['type']['dossier'] = $res_f['evaluationTypeDossier'];
				$evaluation['type']['data'] = getEvalTypeData($res_f['evaluationTypeId']);
				$evaluation['type']['statut'] = $res_f['evaluationTypeStatut'];
			}
			
			/*
				Récupèration de la liste des personne assignés à l'évaluation
			*/
			
			$sql = 'SELECT er.id idRegister, er.evaluationStatut statut, er.date date, er.userId userId, u.nom userNom, u.prenom userPrenom, u.mail userMail, p.id promotionId, p.nom promotionNom, u.nbEtudiant nbEtudiant
						FROM evaluationregister er
						INNER JOIN user u ON er.userId = u.id
						LEFT JOIN promotion p ON p.id = u.promotion
						WHERE er.evaluationId = ?
						ORDER BY er.evaluationStatut ASC, u.nom ASC, u.prenom ASC';
			$res = $db -> prepare($sql);
			if ($res -> execute(array($evaluation['id'])))
			{
				$nbEval = 0;
				$evaluation['orderedUsers'] = array();
				
				while ($res_f = $res -> fetch())
				{
					if ($res_f['statut'] == 1) {
						$nbEval++;
					}
					
					$evaluation['users'][$res_f['userId']]['id'] = $res_f['userId'];
					$evaluation['users'][$res_f['userId']]['registerId'] = $res_f['idRegister'];
					$evaluation['users'][$res_f['userId']]['statut'] = $res_f['statut'];
					
					$evaluation['users'][$res_f['userId']]['mail'] = array();
					if (isSerialized($res_f['userMail']) && $tempMail = unserialize($res_f['userMail']))
					{
						$firstLoop = TRUE;
						
						foreach ($tempMail AS $email)
						{
							$evaluation['users'][$res_f['userId']]['mail'][] = $email;
						}
					}
					$evaluation['users'][$res_f['userId']]['nom'] = $res_f['userNom'];
					$evaluation['users'][$res_f['userId']]['prenom'] = $res_f['userPrenom'];
					if (isset($res_f['promotionNom']))
					{
						$evaluation['users'][$res_f['userId']]['promotion']['id'] = $res_f['promotionId'];						
						$evaluation['users'][$res_f['userId']]['promotion']['nom'] = $res_f['promotionNom'];						
					}
					$evaluation['users'][$res_f['userId']]['nbEtudiant'] = $res_f['nbEtudiant'];
					$evaluation['orderedUsers'][] = $evaluation['users'][$res_f['userId']]; // Même liste mais ordonée
				}
				
				if (isset($evaluation['users']))
				{
					$evaluation['nb']['total'] = count($evaluation['users']);
				}
				else
				{
					$evaluation['nb']['total'] = 0;
				}
				$evaluation['nb']['remplis'] = $nbEval;
				$evaluation['orderedUsers']['nb'] = $evaluation['nb'];
			}

			return $evaluation;
		}
		else 
		{
			return false;
		}
	}
	
	/**
	  * getEvalTypeData - Retourne les informations relative à un module d'évaluation
	  *
	  * @category : evaluationFunction
	  * @param int $id Identifiant du module d'évaluation
	  * @return array Array contenant les données relative au module d'évaluation
	  *
	  * @Author Ali Bellamine
	  *
	  *	 Contenu de l'array retourné :<br>
	  *		['id'] => (int) identifiant du module d'évaluation<br>
	  *		['nom'] => (string) nom du module d'évaluation<br>
	  *		[dossier'] => (string) Dossier d'installation du module d'évaluation<br>
	  *		[statut'] => (int) Si 0, le module d'évaluation est actuellement inactif, si 1 il est actuellement actif<br>
	  *		['optionnel']['js'] => (array) Fichiers javascript optionnels présentent dans le module (voir documentation relative à la création de module d'évaluation)<br>
	  *		['optionnel']['php'] => (array) Fichiers PHP optionnels présentent dans le module (voir documentation relative à la création de module d'évaluation)
	  */
	
	function getEvalTypeData($id)
	{
		/*
			Initialisation des variables
		*/
		global $db; // Permet l'accès à la BDD
		$erreur = array();
		$evaluationType = array();
		
		/*
			On vérifie l'existance de l'évaluation
		*/
		$erreur = checkEvaluationType($id, $erreur);
		
		if (count($erreur) == 0)
		{

			/*
				Récupération des informations sur le type d'évaluation dans la BDD
			*/
			
			$sql = 'SELECT t.id evaluationTypeId, t.nom evaluationTypeNom, t.nomDossier evaluationTypeDossier, t.actif evaluationTypeStatut
						FROM typeevaluation t
						WHERE t.id = ?
						LIMIT 1';
			$res = $db -> prepare ($sql);
			$res -> execute(array($id));
			
			while ($res_f = $res -> fetch())
			{
				$evaluationType['id'] = $res_f['evaluationTypeId'];
				$evaluationType['nom'] = $res_f['evaluationTypeNom'];
				$evaluationType['dossier'] = $res_f['evaluationTypeDossier'];
				$evaluationType['statut'] = $res_f['evaluationTypeStatut'];
			}
			
			/*
				Recherche des fichiers optionnels
			*/
			if (is_file($_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/evaluations/'.$evaluationType['dossier'].'/js/main.js'))
			{
				$evaluationType['optionnel']['js'][] = 'main';
			}
			if (is_file($_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/evaluations/'.$evaluationType['dossier'].'/js/displayEvaluation.js'))
			{
				$evaluationType['optionnel']['js'][] = 'displayEvaluation';
			}
			if (is_file($_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/evaluations/'.$evaluationType['dossier'].'/js/displayEvaluationResult.js'))
			{
				$evaluationType['optionnel']['js'][] = 'displayEvaluationResult';
			}
			if (is_file($_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/evaluations/'.$evaluationType['dossier'].'/js/configEvaluation.js'))
			{
				$evaluationType['optionnel']['js'][] = 'configEvaluation';
			}
			if (is_file($_SERVER['DOCUMENT_ROOT'].LOCAL_PATH.'/evaluations/'.$evaluationType['dossier'].'/configEvaluation.php'))
			{
				$evaluationType['optionnel']['php'][] = 'configEvaluation';
			}
			
			return $evaluationType;
		}
		else 
		{
			return false;
		}
	}
	
	/**
	  * setEvaluationRegisterData - Enregistre les paramètres optionnels d'une instance de campagne d'évaluation
	  *
	  * @category : evaluationFunction
	  * @param string $data Valeurs à enregistrer comme paramètres optionnels de l'instance de campagne d'évaluation en cours
	  * @return boolean Succès (TRUE) ou non (FALSE) dans l'execution de la fonction
	  *
	  * @Author Ali Bellamine
	  *
	  * /!\ Cette fonction ne fonctionne que depuis une page de module d'évaluation /!\
	  *
	  */
	
	function setEvaluationRegisterData ($data) {
		
		// Récupère les données d'évaluations
		global $evaluationRegisterData;
		global $db;
		
		if (isset($evaluationRegisterData) && count($evaluationRegisterData) > 0)
		{
			$sql = 'UPDATE evaluationregister SET evaluationData = ? WHERE id = ?';
			$res = $db -> prepare($sql);
			if ($res -> execute(array($data, $evaluationRegisterData['id'])))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	  * getEvaluationRegisterData - Récupère les paramètres optionnels d'une instance de campagne d'évaluation
	  *
	  * @category : evaluationFunction
	  * @return string|boolean  Contenu du paramètre optionnel de l'instance de campagne d'évaluation en cours, si echec de récupération des données la fonction retourne FALSE
	  *
	  * @Author Ali Bellamine
	  *
	  * /!\ Cette fonction ne fonctionne que depuis une page de module d'évaluation /!\
	  *
	  */
	
	function getEvaluationRegisterData () {
		
		// Récupère les données d'évaluations
		global $evaluationRegisterData;
		global $db;
		
		if (isset($evaluationRegisterData) && count($evaluationRegisterData) > 0)
		{
			$sql = 'SELECT evaluationData FROM evaluationregister WHERE id = ?';
			$res = $db -> prepare($sql);
			$res -> execute(array($evaluationRegisterData['id']));
			if ($res_f = $res -> fetch())
			{
				return $res_f[0];
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	  * validateEvaluation - Valide l'évaluation en cours, l'enregistre comme remplis et redirige l'utilisateur vers la liste des évaluations
	  *
	  * @category : evaluationFunction
	  * @return boolean FALSE si echec lors de l'execution de la fonction
	  *
	  * @Author Ali Bellamine
	  *
	  * /!\ Cette fonction ne fonctionne que depuis une page de module d'évaluation /!\
	  */
	
	function validateEvaluation () {
		global $evaluationData; // informations concernant l'évaluation qui a été remplie
		global $db;
		
		/*
			On enregistre l'évaluation
		*/
		
		if (isset($evaluationData) && isset($_SESSION['id']))
		{
			/*
				Détermination de la date : on enregistre la date du premier jour de la semaine et non pas la date actuelle, afin de garantir l'anonymat
			*/
			$jour_actuel = date('d'); // On récupère le numéro du jour
			$numero_jour = date('w'); // On récupère le numéro du jour de la semaine (0 = dimanche)
			$date_lundi = $jour_actuel - $numero_jour + 1; // On fait le calcul
			$timestamp = mktime(0,0,0,date('m'),$date_lundi,date('Y'));
			
			/*
				Array contenant les données à envoyer dans la BDD
			*/
			$queryArray = array(
									'registerId' => $evaluationData['register']['id'],
									'date' => TimestampToDatetime($timestamp)
									);
			
			$sql = 'UPDATE evaluationregister SET date = :date, evaluationStatut = 1 WHERE id = :registerId';
			$res = $db -> prepare($sql);
			if ($res -> execute($queryArray))
			{
				header('Location: '.ROOT.'content/evaluation/index.php?msg=LANG_SUCCESS_EVALUATION_FORM');
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	  * validateEvaluationSettings - Valide le bon enregistrement des paramètres relatifs à la campagne d'évaluation et autorise la suite de l'execution du code
	  *
	  * @category : evaluationFunction
	  *
	  * @Author Ali Bellamine
	  *
	  * /!\ Cette fonction ne fonctionne que depuis une page de module d'évaluation /!\
	  */
	
	function validateEvaluationSettings () {
		global $evaluationData;
		
		 // Si il s'agit d'un nouvelle évaluation, on redirige vers la page d'ajout des utilisateurs

		 if (isset($_SESSION['evaluationAdd']))
		 {
			header('Location: '.getPageUrl('adminEvaluation').'page=liste&action=userList&id='.$evaluationData['id']);
		 }
		 else
		 {
			header('Location: '.getPageUrl('adminEvaluation').'page=liste&action=liste&msg=LANG_SUCCESS_EVALUATION_SETTING');
		 }			
	}
?>