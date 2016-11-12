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
	  * getEvaluationCCPCPartialData - Récupère les données d'évaluation de stage, récupération partielles des données
	  *
	  * @category : eval_ccpc_functions
	  * @param int $id Identifiant du service pour lequel on récupère les données
	  * @param int|boolean $promotion Identifiant de la promotion pour laquelle on récupère les données, FALSE si elles sont récupérés indifférement de la promotion
	  * @param string $dateMin Borne inférieure de la période pour laquelle on récupère les données d'évaluation, sous forme de Timestamp
	  * @param string $dateMax Borne supérieure de la période pour laquelle on récupère les données d'évaluation, sous forme de Timestamp
	  * @return array Array contenant les résultats d'évaluation pour un service durant une période donnée et pour une promotion donnée
	  * 
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	['service'][] => (array) Array contenant les données relatives au service (voir getServiceInfo())<br>
	  *	['service']['nbDate'] => (int) Nombre de dates d'évaluation différentes<br>
	  *	['service']['date']['min'] => (string) Borne inférieure de l'intervalle sur lequel on a récupéré les évaluations<br>
	  *	['service']['date']['max'] => (string) Borne supérieure de l'intervalle sur lequel on a récupéré les évaluations<br>
	  *	['service']['nbEvaluation'] => (int) Nombre total d'évaluations<br>
	  *	['service']['promotion'][id de la promotion][] => (int) Promotions représentés dans les résultats d'évaluations<br>
	  * ['service']['hide'] => (int) 1 si le service est masqué de la liste pour les utilisateurs, 0 sinon
	  *   	['donnees'] => (array) Contient les données d'évaluation individuelles<br>
	  * 	['donnees'][identifiant de l'évaluation]['infos']['date'] => (string) Date de l'évaluation<br>
	  * 	['donnees'][identifiant de l'évaluation]['infos']['dateDebut'] => (string) Date de début de la période de stage évaluée<br>
	  * 	['donnees'][identifiant de l'évaluation]['infos']['dateFin'] => (string) Date de fin de la période de stage évaluée<br>
	  * 	['donnees'][identifiant de l'évaluation]['infos']['promotion']['id'] => (int) Identifiant de la promotion de l'utilisateur ayant remplis l'évaluation<br>
	  * 	['donnees'][identifiant de l'évaluation]['infos']['promotion']['nom'] => (string) Nom de la promotion de l'utilisateur ayant remplis l'évaluation<br>
	  * 	['donnees'][identifiant de l'évaluation][categorie de la question][nom du champs dans la BDD] => (int) Valeur de la réponse à la question (n'apparaissent que les questions ayant une valeur numérique en réponse)<br>
	  * 	['donnees'][categorie de la question]['moyenne'] => (int) Moyenne des réponses aux questions de la catégorie<br>
	  * 	['donnees'][categorie de la question]['sommeCoefficients'] => (int) Somme des coefficients des questions de la catégorie<br>
	  * 	['donnees'][categorie de la question][nom du champs dans la BDD]['moyenne'] => (int) Moyenne des réponses aux questions du champs<br>
	  * 	['donnees']['nb'] => (int) Nombre total d'évaluations
	  *
	  */
	
	function getEvaluationCCPCPartialData ($id, $promotion, $dateMin, $dateMax) 
	{
		global $db;
		global $bypasslimit;
		
		/**
			Vérification de l'id et des dates
		**/
		if (count(checkService($id, array())) > 0 || !is_numeric($dateMin) || !is_numeric($dateMax))
		{
			return FALSE;
		}

		/**
			Récupération des toutes les évaluations  de type select concernant le service dans la base de donnée
		**/
		
		$listEvaluationItems = array();
		$listCat = array();
		if (is_file(PLUGIN_PATH.'formulaire.xml'))
		{
			if  ($form = simplexml_load_file(PLUGIN_PATH.'formulaire.xml'))
			{
				foreach ($form -> categorie AS $categorie)
				{					
					foreach($categorie -> input AS $input)
					{
						if ($input['type'] == 'select')
						{
							$listCat[(string) $categorie['nom']] = (string) $categorie['nom']; // liste des catégories disponible
							$listEvaluationItems[(string) $input['nomBDD']]['type'] = (string) $categorie['nom']; // Liste des items disponibles
							$listEvaluationItems[(string) $input['nomBDD']]['nb'] = 0; // Nombre de fois où l'item a été évalué
							if (isset($input['coefficient']) && $input['coefficient'] > 0) // On enregistre le coefficient attribué à chaque item
							{
								$listEvaluationItems[(string) $input['nomBDD']]['coefficient'] = (string) $input['coefficient'];
							}
							else
							{
								$listEvaluationItems[(string) $input['nomBDD']]['coefficient'] = 0;
							}

							$max = 0;
							foreach ($input -> option AS $option)
							{
								if ((int) $option['value'] > $max)
								{
									$max = (int) $option['value'];
								}
							}
							$listEvaluationItems[(string) $input['nomBDD']]['max'] = $max; // On enregistre la valeur max que peux obtenir un item
						}
					}
				}
			}
		}

		/*
			Récupération des résultats dans la BDD
		*/
	
		$sqlData = array('id' => $id);
		$sqlNbDate = 'SELECT COUNT(DISTINCT e.date) nombreDate FROM eval_ccpc_resultats e INNER JOIN service s ON e.service = s.id WHERE e.service = :id '; // Permet de calculer le nombre de date d'évaluations différentes dispo
		$sqlNbStudent = 'SELECT COUNT(DISTINCT u.id) nombreEtudiant FROM affectationexterne ae INNER JOIN user u ON u.id = ae.userId WHERE ae.service = :id '; // Permet de calculer le nombre d'étudiants en stage sur la période considérée
		$sql = 'SELECT e.hide hide, e.id evaluationId, e.service serviceId, e.date evaluationDate, e.debutStage dateDebut, e.finStage dateFin, p.nom promotionNom, e.promotion promotionId';
		foreach ($listEvaluationItems AS $key => $value)
		{
			$sql .= ', e.'.$key.' '.$key.' ';
		}
		$sql .= 'FROM eval_ccpc_resultats e
					 INNER JOIN promotion p ON e.promotion = p.id
					 INNER JOIN service s ON e.service = s.id
					 WHERE e.service = :id ';
		
		if ($dateMin != 0 && $dateMax != 0)
		{
			$sql .= 'AND e.debutStage >= :dateMin AND e.finStage <= :dateMax ';
			$sqlNbDate .= 'AND e.debutStage >= :dateMin AND e.finStage <= :dateMax ';
			$sqlNbStudent .= 'AND ae.dateDebut >= :dateMin AND ae.dateFin <= :dateMax ';
			$sqlData['dateMin'] = TimestampToDatetime($dateMin);
			$sqlData['dateMax'] = TimestampToDatetime($dateMax);
		}
		
		if (isset($promotion) && is_numeric($promotion) && count(checkPromotion($promotion, array())) == 0)
		{
			$sql .= 'AND e.promotion = :promotion ';
			$sqlNbDate .= 'AND e.promotion = :promotion ';
			$sqlNbStudent .= 'AND u.promotion = :promotion ';
			$sqlData['promotion'] = $promotion;
		}
		
		// Si il s'agit d'un étudiant, on affiche que les évaluations vielles de + de 30 jours
		if ($_SESSION['rang'] <= 1 && $bypasslimit == FALSE)
		{
			if (defined('CONFIG_EVAL_CCPC_DELAIDISPOEVAL') && is_numeric(constant('CONFIG_EVAL_CCPC_DELAIDISPOEVAL')) && constant('CONFIG_EVAL_CCPC_DELAIDISPOEVAL') >= 0)
			{
				$nbJourAllowedDate = CONFIG_EVAL_CCPC_DELAIDISPOEVAL;
			}
			else
			{
				$nbJourAllowedDate = 30;
			}

			$allowedDate = TimestampToDatetime(time()-$nbJourAllowedDate*24*3600);
			$sql .= ' AND e.date <= "'.$allowedDate.'" ';
			$sqlNbDate .= ' AND e.date <= "'.$allowedDate.'" ';
		}
		
		/*
			Ne pas afficher les évaluations des autres services aux chef de service
		*/
		
		if ($_SESSION['rang'] == 2 && defined('CONFIG_EVAL_CCPC_RESTRICTEVALUATIONACCESSSERVICE') && CONFIG_EVAL_CCPC_RESTRICTEVALUATIONACCESSSERVICE == TRUE && $bypasslimit == FALSE)
		{			
			$sql .= ' AND s.chef = "'.$_SESSION['id'].'"';
			$sqlNbDate .= ' AND s.chef = "'.$_SESSION['id'].'"';
		}
		
		$sql .= 'ORDER BY e.date DESC';
		$res = $db -> prepare($sql);
		$res -> execute($sqlData);
		$res_fall = $res -> fetchAll();
		$res2 = $db -> prepare($sqlNbDate);
		$res2 -> execute($sqlData);
		$res2_f = $res2 -> fetch();
		$res3 = $db -> prepare($sqlNbStudent);
		$res3 -> execute($sqlData);
		$res3_f = $res3 -> fetch();

		/*
			Array contenant les résultats
		*/
		$serviceEvaluation = array();
		
		// Chemin du fichier temporaire		
		$hash = md5(serialize($res_fall));
		$filePath = PLUGIN_PATH.'cache/'.$hash.'.txt';

		// Si le hash existe déjà : on récupère les calculs stockés dans le cache
		if (is_file($filePath)) 
		{
			$file = fopen($filePath, 'r');
			$serviceEvaluation = (unserialize(fread($file, filesize($filePath))));
			fclose($file);
			
			return $serviceEvaluation;
		} // Sinon on recalcule tout
		else
		{
			$serviceEvaluation['service'] = getServiceInfo($id);
			$serviceEvaluation['service']['nbDate'] = $res2_f['nombreDate'];
			$serviceEvaluation['service']['date']['min'] = $dateMin;
			$serviceEvaluation['service']['date']['max'] = $dateMax;
			
			// On ajoute le nombre total d'étudiants pendant la période de stage considéré
			$serviceEvaluation['service']['nbEvaluation'] = $res3_f['nombreEtudiant'];		
			
			foreach ($res_fall AS $res_f)
			{
			
				$serviceEvaluation['service']['hide'] = $res_f['hide'];

				/*
					On enregistre les données de l'évaluation
				*/
				$serviceEvaluation['donnees'][$res_f['evaluationId']]['infos']['date'] = DatetimeToTimestamp($res_f['evaluationDate']);
				$serviceEvaluation['donnees'][$res_f['evaluationId']]['infos']['dateDebut'] = DatetimeToTimestamp($res_f['dateDebut']);
				$serviceEvaluation['donnees'][$res_f['evaluationId']]['infos']['dateFin'] = DatetimeToTimestamp($res_f['dateFin']);
				$serviceEvaluation['donnees'][$res_f['evaluationId']]['infos']['promotion']['id'] = $res_f['promotionId'];
				$serviceEvaluation['donnees'][$res_f['evaluationId']]['infos']['promotion']['nom'] = $res_f['promotionNom'];
				
					// On stocke la liste des promotions rencontrées dans $serviceEvaluation['service']['promotion']
					if (!isset($serviceEvaluation['service']['promotion'][$res_f['promotionId']]))
					{
						$serviceEvaluation['service']['promotion'][$res_f['promotionId']]['id'] = $res_f['promotionId'];
						$serviceEvaluation['service']['promotion'][$res_f['promotionId']]['nom'] = $res_f['promotionNom'];					
						$serviceEvaluation['service']['promotion'][$res_f['promotionId']]['nb'] = 1;					
					}
					else
					{
						$serviceEvaluation['service']['promotion'][$res_f['promotionId']]['nb']++; // On compte le nombre de fois que chaque promotion apparait
					}

				/*
					On récupère les données d'évaluation
				*/
				foreach ($res_f AS $key => $value)
				{
					if (isset($listEvaluationItems[$key]))
					{
						// On incrémente pour le calcul de moyenne
						if (isset($serviceEvaluation[$listEvaluationItems[$key]['type']][$key]['moyenne']))
						{
							$serviceEvaluation[$listEvaluationItems[$key]['type']][$key]['moyenne'] = $serviceEvaluation[$listEvaluationItems[$key]['type']][$key]['moyenne'] + $value;
						}
						else
						{
							$serviceEvaluation[$listEvaluationItems[$key]['type']][$key]['moyenne'] = $value;
						}
						
						// On dénombre le nombre d'évaluation pour l'item (permettant de calculer la moyenne)
						$listEvaluationItems[$key]['nb']++;
						
						// On enregistre la valeur
						$serviceEvaluation['donnees'][$res_f['evaluationId']][$listEvaluationItems[$key]['type']][$key] = $value;
					}
				}			
			}

			/*
				On calcule les moyennes
					D'abord des item
					Puis des catégories rapporté sur 5
			*/
			
			if (isset($serviceEvaluation['donnees'])) 
			{
				$serviceEvaluation['nb'] = count($serviceEvaluation['donnees']);
				
				foreach($listEvaluationItems AS $key => $value)
				{
					if (isset($serviceEvaluation[$value['type']][$key]['moyenne']))
					{
						if (!isset($serviceEvaluation[$value['type']]['sommeCoefficients']))
						{
							$serviceEvaluation[$value['type']]['sommeCoefficients'] = 0; // Somme des coefficients permettant le calcul de la note						
						}
						
						if ($listEvaluationItems[$key]['nb'] > 0)
						{
							$serviceEvaluation[$value['type']][$key]['moyenne'] = round(5*$serviceEvaluation[$value['type']][$key]['moyenne']/($listEvaluationItems[$key]['nb']*$listEvaluationItems[$key]['max']),2);						
						}
						else
						{
							$serviceEvaluation[$value['type']][$key]['moyenne'] = 0;
						}
						
						if (isset($serviceEvaluation[$listEvaluationItems[$key]['type']]['moyenne']))
						{
							$serviceEvaluation[$listEvaluationItems[$key]['type']]['moyenne'] = $serviceEvaluation[$listEvaluationItems[$key]['type']]['moyenne'] + round(($serviceEvaluation[$value['type']][$key]['moyenne']*$listEvaluationItems[$key]['coefficient']),1);
						}
						else
						{
							$serviceEvaluation[$listEvaluationItems[$key]['type']]['moyenne'] = round(($serviceEvaluation[$value['type']][$key]['moyenne']*$listEvaluationItems[$key]['coefficient']),1);
						}

						$serviceEvaluation[$value['type']]['sommeCoefficients'] = $serviceEvaluation[$value['type']]['sommeCoefficients'] + $listEvaluationItems[$key]['coefficient'];
					}
				}
				
				foreach ($listCat AS $value)
				{
					if (isset($serviceEvaluation[$value]['moyenne']))
					{
						if ($serviceEvaluation[$value]['sommeCoefficients'] > 0)
						{
							$serviceEvaluation[$value]['moyenne'] = round($serviceEvaluation[$value]['moyenne']/$serviceEvaluation[$value]['sommeCoefficients'],2);						
						}
						else
						{
							$serviceEvaluation[$value]['moyenne'] = 0;
						}
					}
				}

				// On enregistre le calcul dans le cache
				$file = fopen($filePath, 'w+');
				fputs($file, serialize($serviceEvaluation));
				fclose($file);
				
				return $serviceEvaluation;
			}			
		}
	}
	

	/**
	  * getEvaluationCCPCFullData - Récupère l'intégralité des données d'évaluation de stage
	  *
	  * @category : eval_ccpc_functions
	  * @param int $id Identifiant du service pour lequel on récupère les données
	  * @param int|boolean $promotion Identifiant de la promotion pour laquelle on récupère les données, FALSE si elles sont récupérés indifférement de la promotion
	  * @param string $dateMin Borne inférieure de la période pour laquelle on récupère les données d'évaluation, sous forme de Timestamp
	  * @param string $dateMax Borne supérieure de la période pour laquelle on récupère les données d'évaluation, sous forme de Timestamp
	  * @param boolean $modere si TRUE on affiche les commentaires modérés, si FALSE on ne les affiche pas
	  * @return array Array contenant les résultats d'évaluation pour un service durant une période donnée et pour une promotion donnée
	  * 
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *     Contient l'intégralité des données retournés par {@link getEvaluationCCPCPartialData()}<br>
	  * 	['donnees'][identifiant de l'évaluation][categorie de la question][nom du champs dans la BDD] => (int) Valeur de la réponse à la question, toutes les données y apparaissent<br>
	  * 	[Catégorie des données][Nom du champs dans la BDD]['nb'][valeur] => (int) Nombre d'occurence de chaque valeur<br>
	  * 	[Catégorie des données][Nom du champs dans la BDD]['nbTotal'] => (int) Nombre total de réponses pour le champs donné<br>
	  * 	[Catégorie des données][Nom du champs dans la BDD][] => (string) Pour champs texte uniquement, contient toutes les réponses données dans le champs
	  *
	  */
	
	function getEvaluationCCPCFullData ($id, $promotion,$dateMin,$dateMax, $modere = FALSE) 
	{	
		global $db;
		global $bypasslimit;

		// On récupère des données de la page d'accueil
		$evaluationData = getEvaluationCCPCPartialData($id,$promotion,$dateMin,$dateMax);
		
		if (!isset($evaluationData) || $evaluationData == FALSE)
		{
			return FALSE;
		}

		/**
			Récupération des toutes les évaluations  de type différent de select concernant le service dans la base de donnée
		**/
		
		$listEvaluationItems = array();
		$listTextItems = array(); // Liste des champs à ne pas dénombrer
		$listChamp = array();
		if (is_file(PLUGIN_PATH.'formulaire.xml'))
		{
			if  ($form = simplexml_load_file(PLUGIN_PATH.'formulaire.xml'))
			{
				foreach ($form -> categorie AS $categorie)
				{					
				
				$listChamp[(string) $categorie['nom']] = array();
				
					foreach($categorie -> input AS $input)
					{
						if ($input['type'] == 'select')
						{
							$listChamp[(string) $categorie['nom']][] = (string) $input['nomBDD'];
						}
						
						if ($input['type'] == 'radio' || $input['type'] == 'textarea' )
						{
			
							$listChamp[(string) $categorie['nom']][] = (string) $input['nomBDD'];
							$listEvaluationItems[(string) $input['nomBDD']]['type'] = (string) $categorie['nom'];
							if ($input['type'] == 'radio')
							{
								$listEvaluationItems[(string) $input['nomBDD']]['value'] = array();
								foreach ($input -> radio AS $radio)
								{
									$listEvaluationItems[(string) $input['nomBDD']]['value'][] = (string) $radio['value'];
								}
							}
							if ($input['type'] == 'textarea')
							{
								$listTextItems[(string) $input['nomBDD']] = (string) $categorie['nom'];
							}
						}
						else if ($input['type'] == 'checkbox')
						{
							foreach ($input -> checkbox AS $checkbox)
							{
								$listChamp[(string) $categorie['nom']][] = (string) $checkbox['nomBDD'];
								$listEvaluationItems[(string) $checkbox['nomBDD']]['type'] = (string) $categorie['nom'];
								$listEvaluationItems[(string) $checkbox['nomBDD']]['value'] = array(0,1);
							}
						}
						else if ($input['type'] == 'text')
						{
							foreach ($input -> text AS $text)
							{
								$listChamp[(string) $categorie['nom']][] = (string) $text['nomBDD'];
								$listTextItems[(string) $text['nomBDD']] = (string) $categorie['nom'];
								$listEvaluationItems[(string) $text['nomBDD']]['type'] = (string) $categorie['nom'];
							}
						}
					}
				}
			}
		}		
		
		/**
			On récupère les données non récupérés dans getEvaluationCCPCPartialData
		**/
		
		$sqlData = array('id' => $id);
		$sql = 'SELECT e.id evaluationId, e.moderation moderation';
		foreach ($listEvaluationItems AS $key => $value)
		{
			$sql .= ', e.'.$key.' '.$key.' ';
		}
		
		$sql .= 'FROM eval_ccpc_resultats e
					INNER JOIN service s ON e.service = s.id
					 WHERE e.service = :id ';
		
		if ($dateMin != 0 && $dateMax != 0)
		{
			$sql .= 'AND e.debutStage >= :dateMin AND e.finStage <= :dateMax ';
			$sqlData['dateMin'] = TimestampToDatetime($dateMin);
			$sqlData['dateMax'] = TimestampToDatetime($dateMax);
		}
		
		if (isset($promotion) && is_numeric($promotion) && count(checkPromotion($promotion, array())) == 0)
		{
			$sql .= 'AND e.promotion = :promotion ';
			$sqlData['promotion'] = $promotion;
		}

		// Si il s'agit d'un étudiant, on affiche que les évaluations vielles de + de 30 jours
		if ($_SESSION['rang'] <= 1 && $bypasslimit == FALSE)
		{
			if (defined('CONFIG_EVAL_CCPC_DELAIDISPOEVAL') && is_numeric(constant('CONFIG_EVAL_CCPC_DELAIDISPOEVAL')) && constant('CONFIG_EVAL_CCPC_DELAIDISPOEVAL') >= 0)
			{
				$nbJourAllowedDate = CONFIG_EVAL_CCPC_DELAIDISPOEVAL;
			}
			else
			{
				$nbJourAllowedDate = 30;
			}
			
			$allowedDate = TimestampToDatetime(time()-$nbJourAllowedDate*24*3600);
			$sql .= ' AND e.date <= "'.$allowedDate.'" ';
		}

		/*
			Ne pas afficher les évaluations des autres services aux chef de service
		*/
		
		if ($_SESSION['rang'] == 2 && defined('CONFIG_EVAL_CCPC_RESTRICTEVALUATIONACCESSSERVICE') && CONFIG_EVAL_CCPC_RESTRICTEVALUATIONACCESSSERVICE == TRUE && $bypasslimit == FALSE)
		{			
			$sql .= ' AND s.chef = "'.$_SESSION['id'].'"';
		}		
		
		$res = $db -> prepare($sql);
		$res -> execute($sqlData);
		
		while ($res_f = $res -> fetch())
		{
			// On récupére la liste des champs textes modérés
			if (isset($res_f['moderation']) && unserialize($res_f['moderation']))
			{
				$moderationArray = unserialize($res_f['moderation']);
			}
			else
			{
				$moderationArray = array();
			}
		
			// On remplit l'array de résultats
			
			foreach ($res_f AS $key => $value)
			{
				// On enregistre l'évaluation
				if (isset($listEvaluationItems[$key]) && $value != '')
				{	
					$evaluationData['donnees'][$res_f['evaluationId']][$listEvaluationItems[$key]['type']][$key] = $value;
				}
				
				// On stocke à part les évaluation de type text
				if (isset($listTextItems[$key]))
				{
					if ($value != '')
					{
						// On enregistre le fait que ça soit modéré
						if (isset($moderationArray[$key]))
						{
							$evaluationData['donnees'][$res_f['evaluationId']]['Moderation'][$key] = TRUE;
						}
						
						if (!isset($moderationArray[$key]) || $modere)
						{
							$evaluationData[$listTextItems[$key]][$key][$res_f['evaluationId']] = $value;
						}
						else
						{
							$evaluationData[$listTextItems[$key]][$key][$res_f['evaluationId']] = LANG_FORM_CCPC_QUESTION_TEXT_MODERATE;
						}
					}
				}
			}
		}

		/**
			On compte les réponses et on enregistre combien il y a de chaque réponse
		**/
		foreach ($evaluationData['donnees'] AS $id => $valeur)
		{
			foreach ($listChamp AS $champType => $champValeur)
			{
				foreach ($champValeur AS $champ)
				{
					if (!isset($listTextItems[$champ]) && isset($valeur[$champType][$champ]))
					{
						/*
							On compte l'item
						*/
						if (!isset($evaluationData[$champType][$champ]['nb'][$valeur[$champType][$champ]]) || !is_numeric($evaluationData[$champType][$champ]['nb'][$valeur[$champType][$champ]]))
						{
							$evaluationData[$champType][$champ]['nb'][$valeur[$champType][$champ]] = 1;
						}
						else
						{
							$evaluationData[$champType][$champ]['nb'][$valeur[$champType][$champ]]++;
						}
						
						/*
							On calcul le total
						*/
						if (!isset($evaluationData[$champType][$champ]['nbTotal']) || !is_numeric($evaluationData[$champType][$champ]['nbTotal']))
						{
							$evaluationData[$champType][$champ]['nbTotal'] = 1;
						}
						else
						{
							$evaluationData[$champType][$champ]['nbTotal']++;
						}						
					}
				}
			}
		}
		
		/**
			On corrige met 0 aux valeurs non cochés pour les questions où les réponses possibles sont exhaustives
		**/
		foreach ($listEvaluationItems AS $listFixQuestionName => $listFixQuestionValue)
		{
			if (isset($listFixQuestionValue['value']))
			{
				foreach($listFixQuestionValue['value'] AS $listFixQuestionPossibilite)
				{
					if (!isset($evaluationData[$listFixQuestionValue['type']][$listFixQuestionName]['nb'][$listFixQuestionPossibilite]))
					{
						$evaluationData[$listFixQuestionValue['type']][$listFixQuestionName]['nb'][$listFixQuestionPossibilite] = 0;
					}
				}
			}
		}
		
		return $evaluationData;
	}

	/**
	  * makeHorizontalBar - Barre horizontales permettant d'afficher les notes des services
	  *
	  * @category : eval_ccpc_functions
	  * @param int $taille Longueur de la barre en pixels
	  * @param int $hauteur Hauteur de la barre en pixels
	  * @param int $borneInferieur Borne inférieure du graphique
	  * @param string $couleurInferieur Couleurs de la partie gauche du graphique
	  * @param int $borneSuperieur Borne supérieure du graphique
	  * @param string $couleurSuperieur Couleurs de la partie droite du graphique
	  * @param int $valeur Valeur à représenter sur le graphique
	  * 
	  * @Author Ali Bellamine
	  *
	  */
	
	function makeHorizontalBar($taille, $hauteur, $borneInferieur, $couleurInferieur, $borneSuperieur, $couleurSuperieur, $valeur)
	{
		// Verification des variables
		
		if (!is_numeric($taille) || !is_numeric($hauteur) || !is_numeric($borneInferieur) || $borneSuperieur <= 0 || $borneInferieur >= 0 || !is_numeric($borneSuperieur) || !is_numeric($valeur))
		{
			return FALSE;
		}
		
		// Calcul des longueurs
		$totalGauche = round(abs($borneInferieur)/(abs($borneInferieur) + abs($borneSuperieur)),1);
		$totalDroite = 1 - $totalGauche;
		if ($valeur > 0)
		{
			$position = round((abs($valeur)/abs($borneSuperieur))*$totalGauche,1)*$taille;
			$couleur = $couleurSuperieur;
			$positionDepart = $totalGauche*$taille;
		}
		else if ($valeur < 0)
		{
			$position = round((abs($valeur)/abs($borneInferieur))*$totalDroite,1)*$taille;		
			$couleur = $couleurInferieur;
			$positionDepart = ($totalGauche*$taille)-$position;
		}
		else
		{
			$position = 0;
			$couleur = $couleurSuperieur;
			$positionDepart = $totalGauche*$taille;
		}
		?>
			<div class = "horizontalBarMain" style = "width: <?php echo $taille.'px'; ?>; height: <?php echo $hauteur.'px'; ?>;">
				<div class = "horizontalBar" style = "left: <?php echo $positionDepart.'px'; ?>; padding-right: <?php echo $position.'px'; ?>; background-color: <?php echo $couleur; ?>;">
				</div>
			</div>
		<?php
	}
	
	/**
	  * getCssPercentageValue - Retourne une classe CSS selon le pourcentage d'une valeur
	  *
	  * @category : eval_ccpc_functions
	  * @param int $pencentage Pourcentage de la valeur (entre 0 et 100)
	  * @return string Classe CSS correspondant à la couleur du pourcentage à afficher
	  *
	  * @Author Ali Bellamine
	  *
	  */
	
	function getCssPercentageValue ($percentage) {
		if (!is_numeric($percentage) || $percentage < 0 || $percentage > 100)
		{
			return FALSE;
		}
		
		// >= 75% : on considère qu'elle se produit de façon sure
		if ($percentage >= 75)
		{
			$cssValue = 'high';
		}
		// [50;75[ : on considère qu'elle se produit de façon moyennement sûre
		else if ($percentage < 75 && $percentage >= 50)
		{
			$cssValue = 'medium';
		}
		// ]25;50[ : on considère qu'elle se produit de façon faiblement sûre (25% non compris)
		else if ($percentage < 50 && $percentage > 25)
		{
			$cssValue = 'low';
		}
		
		else 
		{
			$cssValue = '';
		}
		
		return $cssValue;
	}
	
	/**
	  * initTable - Crée les table eval_ccpc_resultats, eval_ccpc_settings, eval_ccpc_filtres et eval_ccpc_filtres_detected dans la base de donnée si ces derniers n'existent pas. Table nécessaires au fonctionnement du module d'évaluation.
	  *
	  * @category : eval_ccpc_functions
	  * @return boolean TRUE si l'opération s'est effectué avec succès, FALSE si une erreur a été rencontrée
	  *
	  * @Author Ali Bellamine
	  *
	  */
	
	function initTable()
	{
		global $db;
					
		// Structure minimale
		$sql = 'CREATE TABLE IF NOT EXISTS `eval_ccpc_resultats` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `promotion` int(11) NOT NULL,
					  `service` int(11) NOT NULL,
					  `debutStage` datetime NOT NULL,
					  `finStage` datetime NOT NULL,
					  `nbExternesPeriode` int(3) NOT NULL,
					  `date` datetime NOT NULL,
					  `moderation` text NOT NULL,
					  `hide` int(11) NOT NULL DEFAULT 0,
					  PRIMARY KEY (`id`),
					  KEY `promotion` (`promotion`),
					  KEY `service` (`service`)
					) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;';
					
		// Ajout des clés étrangères
		$sql .= 'ALTER TABLE `eval_ccpc_resultats`
						ADD CONSTRAINT `eval_ccpc_resultats_ibfk_1` FOREIGN KEY (`promotion`) REFERENCES `promotion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
						ADD CONSTRAINT `eval_ccpc_resultats_ibfk_2` FOREIGN KEY (`service`) REFERENCES `service` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
					SET FOREIGN_KEY_CHECKS=1;';
		$db -> query($sql);
		
		// On récupère la liste des colonnes
		$listeColonnes = array();
		
		$sql = 'show columns from eval_ccpc_resultats';
		$res = $db -> query($sql);
		while($res_f = $res -> fetch())
		{
			$listeColonnes[] = $res_f[0];
		}
					
		// On ajoute les champs liés au formulaire
		// On parcours le fichier XML
		$sql = '';
		if (is_file(PLUGIN_PATH.'formulaire.xml'))
		{
			if  ($form = simplexml_load_file(PLUGIN_PATH.'formulaire.xml'))
			{
				foreach ($form -> categorie AS $categorie)
				{
					foreach ($categorie -> input AS $input)
					{
						if ($input['type'] == 'select')
						{
							if (!in_array((string) $input['nomBDD'], $listeColonnes))
							{
								$sql .= 'ALTER TABLE eval_ccpc_resultats ADD '.(string) $input['nomBDD'].' INT NULL;';
							}
						}
						else if ($input['type'] == 'checkbox')
						{
							foreach($input -> checkbox AS $checkbox)
							{
								if (!in_array((string) $checkbox['nomBDD'], $listeColonnes))
								{
									$sql .= 'ALTER TABLE eval_ccpc_resultats ADD '.(string) $checkbox['nomBDD'].' INT NULL;';
								}
							}
						}
						else if ($input['type'] == 'radio')
						{
							if (!in_array((string) $input['nomBDD'], $listeColonnes))
							{
								$sql .= 'ALTER TABLE eval_ccpc_resultats ADD '.(string) $input['nomBDD'].' INT NULL;';
							}
						}
						else if ($input['type'] == 'text')
						{
							foreach ($input -> text AS $text)
							{
								if (!in_array((string) $text['nomBDD'], $listeColonnes))
								{
									$sql .= 'ALTER TABLE eval_ccpc_resultats ADD '.(string) $text['nomBDD'].' TEXT NULL;';
								}
							}
						}
						else if ($input['type'] == 'textarea')
						{
							if (!in_array((string) $input['nomBDD'], $listeColonnes))
							{
								$sql .= 'ALTER TABLE eval_ccpc_resultats ADD '.(string) $input['nomBDD'].' TEXT NULL;';
							}
						}
					}
				}
			}
		}
		if ($sql != '')
		{
			$db -> query($sql);
		}
		
		/**
			On crée eval_ccpc_filtres et eval_ccpc_filtres_detected
		**/
		
		$sql = 'CREATE TABLE IF NOT EXISTS `eval_ccpc_filtres` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `nom` varchar(255) NOT NULL,
					  `query` text NOT NULL,
					  `mail_titre` varchar(255) NOT NULL,
					  `mail_objet` varchar(255) NOT NULL,
					  `promotion` int(11) NOT NULL,
					  `icone` varchar(255) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;';
					
		$sql .= 'CREATE TABLE IF NOT EXISTS `eval_ccpc_filtres_detected` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `id_filtre` int(11) NOT NULL,
					  `id_service` int(11) NOT NULL,
					  `debutStage` datetime NOT NULL,
					  `finStage` datetime NOT NULL,
					  `promotion` int(11) DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `id_filtre` (`id_filtre`),
					  KEY `id_service` (`id_service`),				  
					  KEY `promotion` (`promotion`)					  
					) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;';
					
		// Clés étrangères
		$sql .= 'ALTER TABLE `eval_ccpc_filtres_detected`
						ADD CONSTRAINT `eval_ccpc_filtres_detected_ibfk_3` FOREIGN KEY (`promotion`) REFERENCES `promotion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
						ADD CONSTRAINT `eval_ccpc_filtres_detected_ibfk_2` FOREIGN KEY (`id_service`) REFERENCES `service` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
						ADD CONSTRAINT `eval_ccpc_filtres_detected_ibfk_1` FOREIGN KEY (`id_filtre`) REFERENCES `eval_ccpc_filtres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
					SET FOREIGN_KEY_CHECKS=1;';
					
		$db -> query($sql);
		
		// On crée eval_ccpc_settings
		$sql = 'CREATE TABLE IF NOT EXISTS `eval_ccpc_settings` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `id_evaluation` INT NOT NULL,
					  `dateDebut` datetime NOT NULL,
					  `dateFin` datetime NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `id_evaluation` (`id_evaluation`)
					) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;';
					
		// Clé étrangère
		$sql .= 'ALTER TABLE `eval_ccpc_settings`
						ADD CONSTRAINT `eval_ccpc_settings_ibfk_1` FOREIGN KEY (`id_evaluation`) REFERENCES `evaluation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
					SET FOREIGN_KEY_CHECKS=1;';

		$db -> query($sql);
	}
	
	/**
	  * eval_ccpc_clearCache - Efface automatiquement les fichiers du cache vieux de plus de 30 jours.
	  *
	  * @category : eval_ccpc_functions
	  *
	  * @Author Ali Bellamine
	  *
	  */
	
	function eval_ccpc_clearCache () {
		$tempsVieFichier = 30*24*3600;
		$path = PLUGIN_PATH.'cache';
		
		$cacheDir = opendir($path);
		while ($file = readdir($cacheDir))
		{
			// Plus de 30 jours
			if (filemtime($path.'/'.$file) < time() - $tempsVieFichier && $file != '.' && $file != '..')
			{
				unlink($path.'/'.$file);
			}
		}
	}
	
	/**
	  * generateCSV - Génère un fichier CSV à partir des données d'évaluation d'un service
	  *
	  * @category : eval_ccpc_functions
	  * @param array $data Données d'évaluation récupérées à partir de la fonction {@link getEvaluationCCPCFullData()}
	  * @return array Array contenant les informations du fichier généré
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  * 	['csvPath'] => (string) Chemin local vers le fichier généré<br>
	  * 	['csvURI'] => (string) URI pointant vers le fichier généré
	  *
	  */
	
	function generateCSV ($data, $header = TRUE) {
		// Array contenant les résultats
		$output = array();
	
		// On vérifie l'existence des données
		if (isset($data['donnees']) && count($data['donnees']) > 0)
		{
			/* 
				Mise en cache
			*/
			
				// Calcul du md5
				$hashData = $data;
				$hashData['header'] = $header;
				
				$hash = md5(json_encode($hashData));
				$csvPath = PLUGIN_PATH.'cache/'.$hash.'.csv';
				$csvPathURI = ROOT.'evaluations/ccpc/cache/'.$hash.'.csv';
				
				if (is_file($csvPath))
				{
					$output['csvPath'] = $csvPath;
					$output['csvURI'] = $csvPathURI;
					return $output;
				}
				// On génère le CSV
				else
				{
					if ($csv = fopen($csvPath, 'w'))
					{
						$firstLoop = true;
						foreach ($data['donnees'] AS $donnees)
						{
							$tempTitle = array(LANG_FORM_CCPC_FILTER_SERVICE_TITLE, LANG_FORM_CCPC_FILTER_DATE_TITLE, LANG_FORM_CCPC_FILTER_PROMOTION_TITLE); // Contient les données Service, Date, Promotion
							$tempDonnee = array($data['service']['FullName'], date('d/m/Y', $donnees['infos']['date']), $donnees['infos']['promotion']['nom']);
							foreach ($donnees AS $cat => $catDonnees) {
								if ($cat != 'infos')
								{
									foreach ($catDonnees AS $title => $value)
									{
										if ($firstLoop)
										{
											$tempTitle[] = $title;
										}
										$tempDonnee[] = $value;
									}
								}
							}

							if ($firstLoop && $header) {
								$tempTitle = array_map("utf8_decode", $tempTitle);
								fputcsv($csv, $tempTitle, ';'); // On envoie les titres
							}
	
							$tempDonnee = array_map("utf8_decode", $tempDonnee);
							fputcsv($csv, $tempDonnee, ';'); // On envoie les données

							$firstLoop = false;
						}
						
						// On enregistre le fichier et on retourne les liens
						if (fclose($csv))
						{
							$output['csvPath'] = $csvPath;
							$output['csvURI'] = $csvPathURI;
							return $output;
						}
						
					}
					else
					{
						return FALSE;
					}
				}
		}
		else
		{
			return FALSE;
		}
	}

	/**
	  * generateAllCSV - Génère un fichier CSV à partir d'une liste de services (plusieurs services en même temps)
	  *
	  * @category : eval_ccpc_functions
	  * @param array $list Array contenant la liste des identifiants des services
	  * @param string $dateMin Borne inférieure de l'intervalle temporel sur lequel on extrait les données
	  * @param string $dateMax Borne supérieure de l'intervalle temporel sur lequel on extrait les données
	  * @param int|boolean $promotion Promotion pour laquelle on extrait les données, FALSE si on extrait indifféremment de la promotion
	  * @param boolean $moderate TRUE si on affiche les messages modérés, FALSE sinon
	  * @return array Array contenant les informations du fichier généré
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  * 	['csvPath'] => (string) Chemin local vers le fichier généré<br>
	  * 	['csvURI'] => (string) URI pointant vers le fichier généré
	  *
	  */
	
	function generateAllCSV ($list, $dateMin, $dateMax, $promotion = FALSE, $moderate = FALSE) {
		// Array contenant les résultats
		$output = array();
	
		// On vérifie l'existence des données
		if (isset($list) && count($list) > 0 && isset($dateMin) && isset($dateMax) && $dateMin <= $dateMax)
		{
			// Calcul du md5
			$hash = md5(json_encode($list));
			$csvPath = PLUGIN_PATH.'cache/'.$hash.'.csv';
			$csvPathURI = ROOT.'evaluations/ccpc/cache/'.$hash.'.csv';
			
			if ($csv = fopen($csvPath, 'w'))
			{
				$firstLoop = TRUE;
				
				foreach ($list AS $service)
				{
					// Si le service existe
					if (count(checkService($service, array())) == 0)
					{
						// On affiche la liste des catégorie uniquement au premier tour de boucle
						if ($firstLoop)
						{
							$serviceCSV = generateCSV(getEvaluationCCPCFullData($service, $promotion, $dateMin, $dateMax, $moderate), TRUE);	
						}
						else
						{
							$serviceCSV = generateCSV(getEvaluationCCPCFullData($service, $promotion, $dateMin, $dateMax, $moderate), FALSE);	
						}
						
						// On lit le fichier CSV généré
						if ($tempCSV = fopen($serviceCSV['csvPath'], 'r'))
						{
							$tempCSVContent = fread($tempCSV, filesize($serviceCSV['csvPath']));
						}
						
						// On copie son contenue à la fin du fichier CSV final
						fwrite($csv, $tempCSVContent);
						unset($tempCSVContent);
						
						$firstLoop = FALSE;
					}				
				}
				
				fclose($csv);
				$output['csvPath'] = $csvPath;
				$output['csvURI'] = $csvPathURI;
				
				return ($output);
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
	  * generatePDF - Génère un fichier PDF à partir des données d'évaluation d'un service
	  *
	  * @category : eval_ccpc_functions
	  * @param array $data Données d'évaluation récupérées à partir de la fonction {@link getEvaluationCCPCFullData()}
	  * @param boolean $comment TRUE si on incut les commentaire, FALSE si on ne les inclut pas
	  * @param boolean $commentMSG TRUE si on incut un message concernant la CSG, FALSE si on ne l'inclut pas
	  * @return array Array contenant les informations du fichier généré
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  * 	['pdfPath'] => (string) Chemin local vers le fichier généré<br>
	  * 	['pdfURI'] => (string) URI pointant vers le fichier généré
	  *
	  */
	
	function generatePDF ($data, $comment = FALSE, $commentMSG = FALSE) {
		
		// Accès à la BDD
		global $db;
		
		// Array contenant les résultats
		$output = array();
	
		// On vérifie l'existence des données
		if (isset($data) && count($data) > 0)
		{
			
			/* 
				Mise en cache
			*/
			
				// Calcul du md5
				$hashdata = $data;
				$hashdata['optionsPDF'] = array('comment' => $comment, 'commentMSG' =>  $commentMSG);
				
				$hash = md5(json_encode($hashdata));
				$pdfPath = PLUGIN_PATH.'cache/'.$hash.'.pdf';
				$pdfPathURI = ROOT.'evaluations/ccpc/cache/'.$hash.'.pdf';
				
				if (is_file($pdfPath))
				{
					$output['pdfPath'] = $pdfPath;
					$output['pdfURI'] = $pdfPathURI;
					return $output;
				}
				// On génère le PDF
				else
				{
					// On charge la librairie
					require_once(PLUGIN_PATH.'core/fpdf17/fpdf.php');
					
					// On charge le fichier XML
					if (is_file(PLUGIN_PATH.'formulaire.xml'))
					{
						$form = simplexml_load_file(PLUGIN_PATH.'formulaire.xml');
					}
					
					// Promotion
					if (count($data['service']['promotion']) > 1)
					{
						$promotion = false;
					}
					else
					{
						foreach ($data['service']['promotion'] AS $promotionData)
						{
							$promotion = $promotionData['id'];
						}
					}
					
					try {
						ob_end_clean();
					} catch (Exception $e)
					{
						
					}
					
					// On crée le PDF
					$A4Height = 842;
					$A4Width = 595;
					$titleSize = 15;
					$textSize = 11;
					
					$pdf = new FPDF('L', 'pt', 'A4');
					$pdf -> SetTopMargin(10);
					$pdf -> SetLeftMargin(15);
					$pdf -> SetAutoPageBreak(TRUE, 10);
					
					/**
						Page contenant le résumé des données d'évaluation
					**/
					
						$pdf->AddPage();
						$pdf->SetFont('Arial','B',$titleSize);
					
						// On affiche le titre
						$pdf -> SetFillColor(70, 70, 242);
						$pdf -> SetTextColor(255, 255, 255);
						$pdf -> SetX(floor(0.1*$A4Height));
						$pdf -> Cell(floor(0.8*$A4Height),$titleSize+5,utf8_decode(LANG_FORM_CCPC_PDF_TITLE), 'LRTB', 0, 'C', TRUE);
					
						// Première ligne
						$pdf -> Ln(2*$titleSize);
						$pdf->SetFont('Arial','',$textSize);
					
							// On affiche les informations concernant le service
						
								// Récupération des données
								$textToDisplay = LANG_FORM_CCPC_FILTER_SERVICE_TITLE.' : '.$data['service']['FullName'].PHP_EOL.LANG_FORM_CCPC_PDF_STAGEPERIODE.' : '.date('d/m/Y', $data['service']['date']['min']).' - '.date('d/m/Y', $data['service']['date']['max']);
							
									// Nombre d'étudiants par promotion
										$nbEtudiantsService = array();
										
										$sql = 'SELECT p.nom promotion, COUNT( ae.userId ) nombre
												FROM `affectationexterne` ae
												INNER JOIN user u ON u.id = ae.userId
												INNER JOIN promotion p ON p.id = u.promotion
												WHERE `dateDebut` >= "'.TimestampToDatetime($data['service']['date']['min']).'" AND `dateFin` <= "'.TimestampToDatetime($data['service']['date']['max']).'" AND service = '.$data['service']['id'].'
												GROUP BY u.promotion';

										$res = $db -> query($sql);
										while($res_f = $res -> fetch())
										{
											$nbEtudiantsService[$res_f['promotion']] = $res_f['nombre'];
										}

								$firstLoop = true;
								if (count($nbEtudiantsService) > 0)
								{
									$textToDisplay .= PHP_EOL.LANG_FORM_CCPC_PDF_STUDENTPROMOTION.' : ';
									foreach ($nbEtudiantsService AS $promotionNom => $promotionNombre) { 
										if (!$firstLoop) { $textToDisplay .= ', '; } else { $firstLoop = FALSE; } 
										$textToDisplay .= $promotionNom.' ('.$promotionNombre.')'; 
									}
								}
							
								$textToDisplay .= PHP_EOL.LANG_FORM_CCPC_PDF_STUDENTNB.' : '.$data['service']['nbEvaluation'].PHP_EOL.LANG_FORM_CCPC_PDF_EVALUATIONNB.' : '.$data['nb'];
								$textToDisplay = utf8_decode($textToDisplay);
								
								// Affichage
								$pdf -> SetFillColor(231, 231, 231);
								$pdf -> SetTextColor(0, 0, 0);
								$pdf -> MultiCell(floor(0.35*$A4Height),$textSize+5,$textToDisplay, 'LRTB', 'L', TRUE);
								
							// On affiche les graphiques : mainGraphPDF
							
								// Récupération des données
								// Liste des graphiques à afficher
									$input = $form -> xpath('categorie/input[@mainPDFGraph="1"]');
									
									$nbGraph = count($input); // Nombre de graphiques à intégrer

									// On génère $tempData, contenant les données utilisées pour génération du png
									foreach ($input AS $select)
									{
										if ($select['type'] == 'select')
										{
											$categorie = $select -> xpath('..')[0]; // Catégorie du graphique
										
											$tempData = array();
											$tempData['settings'] = array('width' => 450, 'height' => 230);

											foreach ($select -> option AS $option)
											{
												if (isset($data[(string) $categorie['nom']][(string) $select['nomBDD']]['nb'][(string) $option['value']]))
												{
													$value = $data[(string) $categorie['nom']][(string) $select['nomBDD']]['nb'][(string) $option['value']];
													if (is_numeric($value))
													{
														$tempData['data'][constant((string) $option['text'])] = $value;
													}
													else
													{
														$tempData['data'][constant((string) $option['text'])] = 0;
													}
												}
												else
												{
													$tempData['data'][constant((string) $option['text'])] = 0;
												}
											}
											
											// On inclut l'image
											$pdf -> Image(eval_ccpc_genGraphPie($tempData), 0.4*$A4Height, 3*$titleSize, floor(0.4*$A4Height), 0, 'PNG');
											break;
										}
									}
									
							// On affiche l'icone des filtres : maximum 4
							$filtres = eval_ccpc_checkFilterExistence($data['service']['id'], $data['service']['date']['min'], $data['service']['date']['max'], $promotion); 
							$numberOfIcons = 0; // Compte le nombre d'icones ajoutées
							$leftCornerX = 0.8*$A4Height-5;
							$leftCornerY = 3*$titleSize-5;
							
							if (is_array($filtres))
							{
								foreach ($filtres AS $filtre)
								{
									if (isset($filtre['icone']) && strlen($filtre['icone']) > 1 && $numberOfIcons < 4)
									{
										$pdf -> Image($filtre['icone'], $leftCornerX, $leftCornerY, floor(0.1*$A4Height), 0, 'PNG');								
										$numberOfIcons++;
										
										if ($numberOfIcons == 1) { $leftCornerX = 0.9*$A4Height-3; }
										else if ($numberOfIcons == 2) { $leftCornerX = 0.8*$A4Height-5; $leftCornerY += 0.1*$A4Height+1; }
										else if ($numberOfIcons == 3) { $leftCornerX = 0.9*$A4Height-3; }
										break;
									}
								}
							}
							
							if ($numberOfIcons == 0)
							{
								// On ajoute l'icone neutre si aucune icone n'est présente
								$pdf -> Image(PLUGIN_PATH.'/css/img/neutral.png', $leftCornerX, $leftCornerY, floor(0.1*$A4Height), 0, 'PNG');
							}
									
						// Deuxième ligne
						$pdf -> Ln(8*$titleSize);
						
							// On affiche le radar sur 1 an de données
								$fullYearData = getEvaluationCCPCFullData($data['service']['id'], $promotion, $data['service']['date']['max']-31536000, $data['service']['date']['max'], FALSE); // Récupération des données
							
								// Titre
								$pdf -> Cell(floor(0.4*$A4Height),$titleSize+5,utf8_decode(LANG_FORM_CCPC_PDF_STAGEPERIODE_FULLYEAR.' ('.date('d/m/Y', $fullYearData['service']['date']['min']).' '.LANG_FORM_CCPC_PDF_STAGEPERIODE_END.' '.date('d/m/Y', $fullYearData['service']['date']['max']).')'), 0, 0, 'C', FALSE);
								
								// On affiche l'image
									
									// Liste des valeurs à afficher
									$input = $form -> xpath('categorie/input[@radarPDFGraph="1"]');
									
									// Préparation des données
									$tempData = array();
									$tempData['settings'] = array('height' => 380, 'width' => 680, 'max' => 10); 
																			
									foreach ($input AS $theinput)
									{
										// Récupération du parent
										$categorie = $theinput -> xpath('..')[0]; // Catégorie du graphique
									
										if (isset($data[(string) $categorie['nom']][(string) $theinput['nomBDD']]['moyenne']))
										{
											$tempData['data'][constant($theinput['label'].'_SHORT')] = $fullYearData[(string) $categorie['nom']][(string) $theinput['nomBDD']]['moyenne']+5;
										}
									}
									
									// Affichage de l'image
									$pdf -> Image(eval_ccpc_genGraphRadar($tempData), 10, $pdf -> getY() + 40, floor(0.4*$A4Height), 0, 'PNG');
									
							// On affiche le radar sur la période du stage
							
								// On décale du 0.05*largeur
								$pdf -> Cell(floor(0.05*$A4Height));
								
								// On crée un rectangle contenant les données
								$pdf -> Rect($pdf -> getX(), $pdf -> getY()-10, 0.5*$A4Height, 0.3*$A4Height, 'F');
							
								// Titre
								$pdf -> Cell(floor(0.5*$A4Height),$titleSize+5,utf8_decode(LANG_FORM_CCPC_PDF_STAGEPERIODE_START.' '.date('d/m/Y', $data['service']['date']['min']).' '.LANG_FORM_CCPC_PDF_STAGEPERIODE_END.' '.date('d/m/Y', $data['service']['date']['max'])), 0, 0, 'C', FALSE);
								
								// On ajoute l'image
								
									// Liste des valeurs à afficher
									$input = $form -> xpath('categorie/input[@radarPDFGraph="1"]');
									
									// Préparation des données
									$tempData = array();
									$tempData['settings'] = array('height' => 380, 'width' => 650, 'max' => 10); 
																			
									foreach ($input AS $theinput)
									{
										// Récupération du parent
										$categorie = $theinput -> xpath('..')[0]; // Catégorie du graphique
									
										if (isset($data[(string) $categorie['nom']][(string) $theinput['nomBDD']]['moyenne']))
										{
											$tempData['data'][constant($theinput['label'].'_SHORT')] = $data[(string) $categorie['nom']][(string) $theinput['nomBDD']]['moyenne']+5;
										}
									}
									
									// Affichage de l'image
									$pdf -> Image(eval_ccpc_genGraphRadar($tempData), $pdf -> getX()  - 0.45*$A4Height, $pdf -> getY() + 40, floor(0.4*$A4Height), 0, 'PNG');
									
						// Affiche du logo

						$pdf -> Image(ROOT.'theme/img/logo.png', 10, $A4Width - 100, 0, 50, 'PNG');				

					
						// Pied de Page
						$pdf -> SetX(0.5*$A4Height);
						$pdf -> SetY($A4Width - 40);
						$textSize = 10;
						$pdf -> SetFont('Arial', 'I', $textSize);
						
							// Ligne de demarcation
							$pdf -> Line(15, $pdf -> getY(), $A4Height - 15, $pdf -> getY());
							
							// Accès aux résultats
							$pdf -> SetX(0.5*$A4Height);
							$pdf -> Cell(0.5*$A4Height-15, $textSize + 5, utf8_decode(LANG_FORM_CCPC_PDF_FOOTER_FULLRESULT.' '.getPageUrl('evalView', array('evaluationType' => 1, 'service' => $data['service']['id']))), 0, 1, 'R', 0);
							
							// Coordonées CSG
							if ($commentMSG) {
								$pdf -> SetFont('Arial', 'B', $textSize);
								$pdf -> SetX(15);
								$pdf -> Cell($A4Height-15, $textSize + 5, utf8_decode(LANG_FORM_CCPC_PDF_FOOTER_STRUCTURENAME.' - '.CONTACT_STAGE_MAIL), 0, 0, 'C', 0);
							}
						
						/**
							Commentaires
						**/
						
						// Ajout des commentaires, points positifs et points négatifs : 'pdfComment'
						if ($comment) {
							$pdf -> addPage('P', 'A4');
							
							// Titre
							$pdf -> SetFont('Arial','B',$titleSize);
							$pdf -> SetFillColor(70, 70, 242);
							$pdf -> SetTextColor(255, 255, 255);
							$pdf -> SetX(floor(0.1*$A4Width));
							$pdf -> Cell(floor(0.8*$A4Width),$titleSize+5,utf8_decode(LANG_FORM_CCPC_PDF_COMMENT_TITLE), 'LRTB', 1, 'C', TRUE);
							$pdf -> SetTextColor(0,0,0);
							$pdf -> SetFillColor(245, 245, 245);
							
							// Les commentaires
							
								$input = $form -> xpath('categorie/input[@pdfComment="1"]');
																
								foreach ($input AS $theinput)
								{
									$categorie = $theinput -> xpath('..')[0];

									if ($theinput['type'] == 'text')
									{
										// Création de l'array contenant les données à afficher sous forme [timestamp fin][timestamp début][timestamp commantaire][idMessage][] => message
										$tempData = array();
										foreach ($theinput -> text AS $value)
										{
											if (isset($data[(string) $categorie['nom']][(string) $value['nomBDD']])) {
												foreach ($data[(string) $categorie['nom']][(string) $value['nomBDD']] AS $idEval => $textValue)
												{
													if (isset($data['donnees'][$idEval]['infos']))
													{
														$tempData[$data['donnees'][$idEval]['infos']['dateFin']][$data['donnees'][$idEval]['infos']['dateDebut']][$data['donnees'][$idEval]['infos']['date']][$idEval][] = $textValue;
													}
												}
											}
										}
										
										$textArea = '';
										$firstLoop = TRUE;
										
										// On affiche les commentaires
										krsort($tempData);
										foreach ($tempData AS $dateFin => $tempvalue)
										{
											krsort($tempvalue);
											foreach ($tempvalue AS $dateDebut => $value2)
											{
												krsort($value2);
												foreach ($value2 AS $date => $value3)
												{
													foreach ($value3 AS $commentId => $comments)
													{
														foreach ($comments AS $comment)
														{
															if ($comment != '')
															{
																if (!$firstLoop) { $textArea .= PHP_EOL.PHP_EOL; } else { $firstLoop = FALSE; } // Saut de ligne
																$textArea .= $comment.' - '.date('d/m/Y', $date).' #'.$commentId;
															}
														}
													}
												}
											}
										}
										
										if ($textArea != '')
										{
											// On affiche les textes dans le PDF
											$pdf -> Ln(20);
											$pdf -> SetX(20);
											$pdf -> setFont ('Arial','', $titleSize);
											$pdf -> Cell(0, $titleSize+5, utf8_decode(constant($theinput['label'].'_SHORT')), 0, 1, 'L', FALSE);
											$pdf -> setFont ('Arial','', $textSize);
											$pdf -> SetX(20);
											$pdf -> MultiCell ($A4Width - 40, $textSize + 5, utf8_decode($textArea), 'LTRB', 'L', TRUE);
										}
									}
									else if ($theinput['type'] == 'textarea')
									{
										// Création de l'array contenant les données à afficher sous forme [timestamp fin][timestamp début][timestamp commantaire][idCommentaire] => commentaire
										$tempData = array();
										foreach ($data[(string) $categorie['nom']][(string) $theinput['nomBDD']] AS $commentId => $commentData)
										{
											if (isset($data['donnees'][$commentId]['infos']))
											{
												$tempData[$data['donnees'][$commentId]['infos']['dateFin']][$data['donnees'][$commentId]['infos']['dateDebut']][$data['donnees'][$commentId]['infos']['date']][$commentId] = $commentData;
											}
										}
										
										$textArea = '';
										$firstLoop = TRUE;

										// On affiche les commentaires
										krsort($tempData);
										foreach ($tempData AS $dateFin => $tempvalue)
										{
											krsort($tempvalue);
											foreach ($tempvalue AS $dateDebut => $value2)
											{
												krsort($value2);
												foreach ($value2 AS $date => $value3)
												{
													foreach ($value3 AS $commentId => $comment)
													{
														if ($comment != '')
														{
															if (!$firstLoop) { $textArea .= PHP_EOL.PHP_EOL; } else { $firstLoop = FALSE; } // Saut de ligne
															$textArea .= $comment.' - '.date('d/m/Y', $date).' #'.$commentId;
														}
													}
												}
											}
										}
										
										if ($textArea != '')
										{
										
											// On affiche les textes dans le PDF
														
											$pdf -> Ln(20);
											$pdf -> SetX(20);
											$pdf -> setFont ('Arial','', $titleSize);											
											$pdf -> Cell(0*$A4Width, $titleSize+5, utf8_decode(constant($theinput['label'].'_SHORT')), 0, 1, 'L', FALSE);
											$pdf -> setFont ('Arial','', $textSize);
											$pdf -> SetX(20);
											$pdf -> MultiCell ($A4Width - 40, $textSize + 5, utf8_decode($textArea), 'LTRB', 'L', TRUE);
										}
									}
								}
						}

					// On retourne le fichier PDF
					$pdf -> Output($pdfPath, 'F');
					$output['pdfPath'] = $pdfPath;
					$output['pdfURI'] = $pdfPathURI;
					return $output;
				}
					
			exit();
		}
		else
		{
			return FALSE;
		}
	}
?>