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
		fnAdmin - 09/07/2015
		Ali Bellamine
		
		Contient les fonctions relatives à l'administration des évaluations
	**/
	
	/**
	  * eval_ccpc_exploreQuery - Teste une requête pour un service
	  *
	  * @category : eval_ccpc_functions
	  * @param string $query Requête à tester
	  * @param XMLObject $xmlObject Objet XML du formulaire d'évaluation
	  * @param array $data Array contenant les résultats d'évaluation du service, voir {@link getEvaluationCCPCFullData()}
	  * @return boolean TRUE sur la requête est vérifiée, FALSE si elle n'est pas vérifiée
	  * 
	  * @Author Ali Bellamine
	  *
	  */

	function eval_ccpc_exploreQuery ($query, $xmlObject, $data) {

		// On extrait les données de query
		$startWord = FALSE;
		$parenthese = 0;
		$tempArray = array();
		$query = preg_replace('/\s+/', ' ',$query);
		

		for ($i = 0; $i <= (strlen($query)-1); $i++)
		{
			if ($query[$i] == '(')
			{
				$parenthese++;
			}
			else if ($query[$i] == ')')
			{
				$parenthese--;
			}
		
			// On extrait les mots
			if ($query[$i] != ' ')
			{
				if ($startWord)
				{
					$theWord .= $query[$i];;
				}
				else
				{
					$startWord = TRUE;
					$theWord = $query[$i];;
				}
			}
			
			// On stocke les mots dans un array
			if (($query[$i] == ' ' || !isset($query[$i+1])) && $startWord)
			{
				if ($parenthese == 0)
				{
					$startWord = FALSE;
					$tempArray[] = $theWord;
				}
				else
				{
					$theWord .= $query[$i];
				}
			}
		}

		// Liste des opérateurs reconnus
		$allowedOperator = array('<=' => '<=', '<' => '<', '=' => '==', '>=' => '>=', '>' => '>', '!=' => '!=');
		// Liste des séparateurs reconnus
		$allowedSeparator = array('AND', 'OR');
		
		// On récupère les terme de gauche et de droite
		$queryArray = array();
		$delimiters = array();

		$loop = 1;
		$n = 0;
		
		foreach ($tempArray AS $key => $value)
		{
			// Il s'agit du terme de gauche
			if ($loop == 1)
			{				
				// Si il s'agit d'une expression de type TRUE or FALSE --> on ne récupère pas d'opérateur
				if (in_array($tempArray[$key+1], $allowedSeparator))
				{
					$loop = 4;
				}
				// Sinon on récupère l'opérateur au prochain tour de boucle
				else if (in_array($tempArray[$key+1], $allowedOperator))
				{
					$loop = 2;
				}
				else
				{
					return 'error';
				}
				
				$queryArray['left'][$n] = $value;
			}
			else if ($loop == 2)
			{
				if (in_array($value, $allowedOperator))
				{
					$loop = 3;
				}
				else
				{
					return 'error';
				}
				$queryArray['operator'][$n] = $value;
			}
			// Récupère la valeur de test
			else if ($loop == 3)
			{
				$loop = 4;
				$queryArray['right'][$n] = $value;
			}
			// Récupère le séparateur
			else if ($loop == 4)
			{
				$loop = 1;
				$n++;
				$delimiters[$n] = $value;
			}
		}

                // On parcours toutes les conditions
		for($i = 0; $i <= $n; $i++)
		{
			if (isset($delimiters[$i]) && $delimiters[$i] == 'OR' && isset($treatOR) && $treatOR == FALSE) { break; } // On zappe les OR lorsque $treatOR est sur false
			
			// On vérifie la condition, par défaut elle est considérée comme fausse
			$theCheck = FALSE;
		
			// On vérifie si il s'agit d'une condition composée (présence de parenthèse)
			$composedValue = array();
			if (preg_match('#\((.*?)\)#', $queryArray['left'][$i], $composedValue))
			{
				$theCheck = eval_ccpc_exploreQuery($composedValue[1], $xmlObject, $data);
			}
			else
			{
				// On vérifie l'égalité
					
					// On décompose le premier terme
					$leftTerm = explode('.',$queryArray['left'][$i]);
					if (count($leftTerm) < 0) { return 'error'; }
					else if (substr($leftTerm[count($leftTerm)-1], 0, 2) != 'fn') { return 'error'; } // La fonction appliqué doit commencer par fn
				
					// On récupère la valeur du terme de gauche
						
					$valueToTest = FALSE; // Valeur à tester, si elle reste à false à la fin, on considère qu'il y a une erreur dans la redaction de la requête
					$nbr = count($leftTerm)-1; // Profondeur d'exploration dans le xml
					$function = $leftTerm[count($leftTerm)-1]; // Fonction appliquer
                                        
					if ($nbr > 0)
					{
						if ($nbr == 1)
						{
							if ($xmlObject -> xpath('categorie[@nom="'.$leftTerm[0].'"]'))
							{
								$objectToTest = $xmlObject -> xpath('categorie[@nom="'.$leftTerm[0].'"]')[0];
								
								if ($function == 'fnMOYENNE')
								{
									if (isset($data[(string) $objectToTest['nom']]['moyenne']))
									{
										$valueToTest = $data[(string) $objectToTest['nom']]['moyenne'];
									}
								}
							}
						}
						else if ($nbr == 2)
						{
							if ($xmlObject -> xpath('categorie[@nom="'.$leftTerm[0].'"]/input[@name="'.$leftTerm[1].'"]'))
							{
								$objectToTest = $xmlObject -> xpath('categorie[@nom="'.$leftTerm[0].'"]/input[@name="'.$leftTerm[1].'"]')[0];
								
								$type = (string) $objectToTest['type'];
								
								if ($type == 'select')
								{
									if ($function == 'fnMOYENNE')
									{
										if (isset($data[(string) $objectToTest -> xpath('..')[0]['nom']][(string) $objectToTest['nomBDD']]['moyenne']))
										{
											$valueToTest = $data[(string) $objectToTest -> xpath('..')[0]['nom']][(string) $objectToTest['nomBDD']]['moyenne'];
										}
									}
								}
								else if ($type == 'text' || $type == 'textarea')
								{
									if ($function == 'fnNB')
									{
										if ($type == 'text')
										{
											$valueToTest = 0;
											
											foreach ($objectToTest AS $text)
											{
												if (isset($data[(string) $objectToTest -> xpath('..')[0]['nom']][(string) $text['nomBDD']]))
												{
													$valueToTest += count($data[(string) $objectToTest -> xpath('..')[0]['nom']][(string) $text['nomBDD']]);
												}
											}
										}
										else if ($type == 'textarea')
										{
											if (isset($data[(string) $objectToTest -> xpath('..')[0]['nom']][(string) $objectToTest['nomBDD']]))
											{
												$valueToTest += count($data[(string) $objectToTest -> xpath('..')[0]['nom']][(string) $objectToTest['nomBDD']]);
											}
										}
									}
								}
							}
						}
						else if ($nbr == 3)
						{
							if ($xmlObject -> xpath('categorie[@nom="'.$leftTerm[0].'"]/input[@name="'.$leftTerm[1].'"]/*[@value="'.$leftTerm[2].'"]'))
							{
								$objectToTest =  $xmlObject -> xpath('categorie[@nom="'.$leftTerm[0].'"]/input[@name="'.$leftTerm[1].'"]/*[@value="'.$leftTerm[2].'"]')[0];
								
								$typeObject = $objectToTest -> xpath ('..')[0];
								$type = (string) $typeObject['type'];
								
								if ($type != 'text' && $type != 'textarea')
								{
									if ($function == 'fnNB')
									{
										if ($type == 'select')
										{
											if (isset($data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $typeObject['nomBDD']]['nb'][(string) $objectToTest['value']]))
											{
												$valueToTest = $data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $typeObject['nomBDD']]['nb'][(string) $objectToTest['value']];
											}
										}
										else if ($type == 'checkbox')
										{
											if (isset($data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $objectToTest['nomBDD']]))
											{
												$valueToTest = $data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $objectToTest['nomBDD']]['nb'][1];
											}
										}
										else if ($type == 'radio')
										{
											if (isset($data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $typeObject['nomBDD']]['nb'][(string) $objectToTest['value']]))
											{ 
												$valueToTest = $data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $typeObject['nomBDD']]['nb'][(string) $objectToTest['value']];
											}
										}
									}
									else if ($function == 'fnPERCENTAGE')
									{
										if ($type == 'select')
										{
                                                                                    if (isset($data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $typeObject['nomBDD']]['nb'][(string) $objectToTest['value']]))
                                                                                    {
                                                                                        $valeur = $data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $typeObject['nomBDD']]['nb'][(string) $objectToTest['value']];
                                                                                    } else {
                                                                                        $valeur = 0;
                                                                                    }

                                                                                    $total = 0;

                                                                                    foreach ($data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $typeObject['nomBDD']]['nb'] AS $value => $nb)
                                                                                    {
                                                                                            $total += $nb;
                                                                                    }
												
                                                                                    if ($total != 0)
                                                                                    {
                                                                                            $valueToTest = ceil(($valeur/$total)*100);
                                                                                    } else {
                                                                                        $valueToTest = 0;
                                                                                    }
										}
										else if ($type == 'checkbox')
										{
											if (isset($data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $objectToTest['nomBDD']]))
											{
												$valueToTest = ceil(($data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $objectToTest['nomBDD']]['nb'][1]/$data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $objectToTest['nomBDD']]['nbTotal'])*100);
											}
										}
										else if ($type == 'radio')
										{
											if (isset($data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $typeObject['nomBDD']]['nb'][(string) $objectToTest['value']]))
											{ 
												$valueToTest = ceil(($data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $typeObject['nomBDD']]['nb'][(string) $objectToTest['value']]/$data[(string) $typeObject -> xpath('..')[0]['nom']][(string) $typeObject['nomBDD']]['nbTotal'])*100);
											}
										}
									}
								}
							}
						}
					}
			if (!isset($valueToTest)) { return 'error'; }
			
			/**
				On test la valeur
			**/
			
			$theCheck = eval_ccpc_exploreQuery_operatorAction($valueToTest, $queryArray['right'][$i], $queryArray['operator'][$i]);
			}
			
			/**
				Analyse du résultat
			**/
			
			// Si TRUE --> Test positif
			if ($theCheck)
			{
				$treatOR = FALSE; // On zappe les occurence avec 'OR'
			}
			// Si FALSE --> Test négatif
			else 
			{
				$treatOR = TRUE; // On traite les occurence contenant un 'OR' --> Sauvetage du test
				
				// On vérifie si la prochaine occurence contient un OR : si non on retourne FALSE, si oui on ne retourne rien et on laisse la boucle continuer
				if (!isset($delimiters[$i+1]) || $delimiters[$i+1] != 'OR')
				{
					return FALSE;
				}
			}
		}
		
		// Si les tours de boucles ont été effectués sans problème : on retourne TRUE
		return 'success';
	}
	
	/**
	  * eval_ccpc_exploreQuery_operatorAction - Teste une égalité
	  *
	  * @category : eval_ccpc_functions
	  * @param int $value1 Terme de gauche
	  * @param int $value2 Terme de droite
	  * @param string $operator Opérateur parmis : <=, <, ==, >, >=, !=
	  * @return boolean TRUE sur l'égalité est vérifiée, FALSE si elle n'est pas vérifiée
	  * 
	  * @Author Ali Bellamine
	  *
	  */
	
	function eval_ccpc_exploreQuery_operatorAction ($value1, $value2, $operator) 
	{
		if ($operator == '<=') { return ($value1 <= $value2); }
		if ($operator == '<') { return ($value1 < $value2); }
		if ($operator == '==' || $operator == '=') { return ($value1 == $value2); }
		if ($operator == '>') { return ($value1 > $value2); }
		if ($operator == '>=') { return ($value1 >= $value2); }
		if ($operator == '!=') { return ($value1 != $value2); }		
	}
	
	/***
		Gestion des filtres
	***/
	
		// Vérification de l'existence d'un filtre

		/**
		  * eval_ccpc_checkFiltre - Vérifie l'existence d'un filtre à partir de son identifiant
		  *
		  * @category : eval_ccpc_functions
		  * @param int $id Identifiant du filtre
		  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
		  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
		  * 
		  * @Author Ali Bellamine
		  *
		  */

		  function eval_ccpc_checkFiltre ($id, $erreur)
		{
			if (is_numeric($id))
			{
				global $db; // On récupère l'accès à la BDD
				
				$sql = 'SELECT count(*) FROM eval_ccpc_filtres WHERE id = ? LIMIT 1';
				$res = $db -> prepare($sql);
				$res -> execute(array($id));
				
				$res_f = $res -> fetch();
				if ($res_f[0] != 1) { $erreur[14] = TRUE; }
			}
			else
			{
				$erreur[14] = TRUE;
			}
			
			return $erreur;
		}
		
		// Récupération de la liste des filtres

		/**
		  * eval_ccpc_getFilterList - Retourne la liste des filtres existants
		  *
		  * @category : eval_ccpc_functions
		  * @return array Array contenant la liste des filtres existants
		  * 
		  * @Author Ali Bellamine
		  * @param int $dateMin Date en dessous de laquelle on refuse certains résultat (stages détectés)
		  * @param int $dateMax Date au dessus de laquelle on refuse certains résultat (stages détectés)	  *
                  * 
		  * Contenu de l'array retourné :<br>
		  *	[identifiant du filtre][] => (array) Array contenant les informations relatives au filtre, voir {@link eval_ccpc_getFilterDetails}
		  *
		  */
			
			function eval_ccpc_getFilterList ($dateMin = '', $dateMax = '') {
                            
                                if (!isset($dateMin) || $dateMin == '') { $dateMin = TimestampToDatetime(0); }
                                if (!isset($dateMax) || $dateMax == '') { $dateMax = TimestampToDatetime(time()); }
				
				// Récupère l'accès à la BDD
				global $db;
				
				$filtres = array();
				
				// Récupère la liste des filtres
				$sql = 'SELECT id FROM eval_ccpc_filtres ORDER BY nom ASC';
				$res = $db -> query($sql);
				
				while ($res_f = $res -> fetch())
				{
					$filtres[$res_f['id']] = eval_ccpc_getFilterDetails($res_f['id'], $dateMin, $dateMax);
				}
				
				return $filtres;
			}
                        
                  /**
		  * eval_ccpc_getFilterYearList - Retourne la liste des années pour lesquelles il y a un résultat dans la liste de filtres
		  *
		  * @category : eval_ccpc_functions
		  * @param int $id Identifiant du filtre
		  * @return array Array contenant la liste des années pour lesquelles il y a un résultat dans la liste de filtres
		  * 
		  * @Author Ali Bellamine
		  *
		  * Contenu de l'array retourné :<br>
		  *	[année] => (array) Array contenant la liste des années
		  *
		  */
			
			function eval_ccpc_getFilterYearList ($id) {

                                // Récupère l'accès à la BDD
				global $db;
				
				$annee = array();

                                if (count(eval_ccpc_checkFiltre($id, array())) == 0)
				{
                                       $sql = 'SELECT YEAR(debutStage) AS annee FROM eval_ccpc_filtres_detected WHERE id_filtre = '.$id;
                                       $res = $db -> query($sql);

                                       while($res_f = $res -> fetch()) {
                                          if (!isset($annee[$res_f['annee']])) { $annee[$res_f['annee']] = TRUE; }
                                       }
                                       
                                       $sql = 'SELECT YEAR(finStage) AS annee FROM eval_ccpc_filtres_detected WHERE id_filtre = '.$id;
                                       $res = $db -> query($sql);
                                       
                                       while($res_f = $res -> fetch()) {
                                           if (!isset($annee[$res_f['annee']])) { $annee[$res_f['annee']] = TRUE; }
                                       }
                                       
                                       // On tri par ordre décroissant
                                       krsort($annee);
                                       
                                       return($annee);
                                } else {
                                    return false;
                                }
			}
			
		 /**
		  * eval_ccpc_getFilterDetails - Retourne les informations relatives à un filtre à partir de son identifiant
		  *
		  * @category : eval_ccpc_functions
		  * @param int $id Identifiant du filtre
		  * @param int $dateMin Date en dessous de laquelle on refuse le résultat
		  * @param int $dateMax Date au dessus de laquelle on refuse le résultat
		  * @return array Array contenant les informations relatives au filtre
		  * 
		  * @Author Ali Bellamine
		  *
		  * Contenu de l'array retourné :<br>
		  *	['id'] => (int) Identifiant du filtre<br>
		  *	['nom'] => (string) Nom du filtre<br>
		  *	['query'] => (string) Requête relative au filtre<br>
		  *	['promotion'] => (int) 1 si le filtre prend en compte la promotion de l'utilisateur, 0 si il ne le prend pas en compte<br>
		  *	['mail']['titre'] => (string) Objet des mails automatiques envoyés à partir du filtre<br>
		  *	['mail']['objet'] => (string) Contenu des mails automatiques envoyés à partir du filtre<br>
		  *	['icone'] (optionnel) => (string) Chemin vers l'icone du filtre
		  *	['detected'][timestamp de la borne supérieure de l'intervalle temporel de la période détectée][timestamp de la borne inférieure de l'intervalle temporel de la période détectée][id du service][] (optionnel) => (array) informations relatives au service détecté
		  *
		  */
			
			function eval_ccpc_getFilterDetails ($id, $dateMin, $dateMax) {
				if (count(eval_ccpc_checkFiltre($id, array())) == 0)
				{
					global $db;
					$filtre = array();
					if (!isset($dateMin)) { $dateMin = TimestampToDatetime(0); }
                                        if (!isset($dateMax)) { $dateMax = TimestampToDatetime(time()); }
					
					/**
						Informations relative aux réglages du filtre
					**/
					$sql = 'SELECT id filtreId,nom filtreNom, query filtreQuery, promotion promotion, mail_titre filtreMailTitre, mail_objet filtreMailObjet, icone icone FROM eval_ccpc_filtres WHERE id = ? LIMIT 1';
					
					$res = $db -> prepare($sql);
					$res -> execute(array($id));
					if ($res_f = $res -> fetch())
					{
						$filtre['id'] = $res_f['filtreId'];
						$filtre['nom'] = $res_f['filtreNom'];
						$filtre['query'] = $res_f['filtreQuery'];
						$filtre['promotion'] = $res_f['promotion'];
						$filtre['mail']['titre'] = $res_f['filtreMailTitre'];
						$filtre['mail']['objet'] = $res_f['filtreMailObjet'];
						if (isset($res_f['icone']))
						{
							$filtre['icone'] = $res_f['icone'];
						}
						else
						{
							$filtre['icone'] = '';
						}
					}
					
					/**
						Liste des stages détectés par le filtre
					**/
					
					$sql = 'SELECT id, id_service serviceId, debutStage dateDebut, finStage dateFin, promotion promotion FROM eval_ccpc_filtres_detected WHERE id_filtre = :id AND finStage >= :dateMin AND debutStage <= :dateMax';
                                        $res = $db -> prepare($sql);
					$res -> execute(array('id' => $filtre['id'], 'dateMin' => $dateMin, 'dateMax' => $dateMax));
					// De la forme : $filtre['detected'][timestamp supérieur de l'intervale][timestamp inférieur de l'intervale][id du service]
					while ($res_f = $res -> fetch())
					{
						$filtre['detected'][DatetimeToTimestamp($res_f['dateFin'])][DatetimeToTimestamp($res_f['dateDebut'])][$res_f['serviceId']]['id'] = $res_f['id'];
						$filtre['detected'][DatetimeToTimestamp($res_f['dateFin'])][DatetimeToTimestamp($res_f['dateDebut'])][$res_f['serviceId']]['filtre']['id'] = $filtre['id'];
						$filtre['detected'][DatetimeToTimestamp($res_f['dateFin'])][DatetimeToTimestamp($res_f['dateDebut'])][$res_f['serviceId']]['service']['id'] = $res_f['serviceId'];
						$filtre['detected'][DatetimeToTimestamp($res_f['dateFin'])][DatetimeToTimestamp($res_f['dateDebut'])][$res_f['serviceId']]['date']['debut'] = DatetimeToTimestamp($res_f['dateDebut']);
						$filtre['detected'][DatetimeToTimestamp($res_f['dateFin'])][DatetimeToTimestamp($res_f['dateDebut'])][$res_f['serviceId']]['date']['fin'] = DatetimeToTimestamp($res_f['dateFin']);
						$filtre['detected'][DatetimeToTimestamp($res_f['dateFin'])][DatetimeToTimestamp($res_f['dateDebut'])][$res_f['serviceId']]['promotion'] = $res_f['promotion'];
					}
					
					// On trie les services par ordre décroissant
					if (isset($filtre['detected']))
					{
						krsort($filtre['detected']);
						foreach ($filtre['detected'] AS $key => $value)
						{
							krsort($filtre['detected'][$key]);
						}
					}
					
					return($filtre);
				}
				else
				{
					return FALSE;
				}
			}
			
		/**
		  * eval_ccpc_checkFilterExistence - Récupère les filtres appliqués à un service sur une période donnée
		  *
		  * @category : eval_ccpc_functions
		  * @param int $service Identifiant du service
		  * @param string $debutStage Borne inférieur de l'intervalle temporel considéré, sous forme de timestamp
		  * @param string $finStage Borne supérieure de l'intervalle temporel considéré, sous forme de timestamp
		  * @param int|boolean $promotion Promotion pour laquelle on s'intéresse aux filtres détectés, FALSE si pas de promotion particulière
		  * @return array Array contenant la liste des filtres s'appliquant au service
		  * 
		  * @Author Ali Bellamine
		  *
		  * Contenu de l'array retourné :<br>
		  *	[Identifiant du filtre][] => (array) Informations relatives au filtre, voir {@link eval_ccpc_getFilterDetails()}<br>
		  *
		  */
			
			function eval_ccpc_checkFilterExistence ($service, $debutStage, $finStage, $promotion = FALSE) {
				if (isset($service) && count(checkService($service, array())) == 0 && isset($debutStage) && isset($finStage))
				{
					global $db;
					$filtres = array(); // Contient la liste des filtres
					
					$sqlData = array('service' => $service, 'debutStage' => TimestampToDatetime($debutStage), 'finStage' => TimestampToDatetime($finStage)); // Array utilisé dans la requête préparée
				
					if (isset($promotion) && is_numeric($promotion) && count(checkPromotion($promotion, array())) == 0)
					{
						$sqlData['promotion'] = $promotion;
						$sql = 'SELECT id_filtre filtre FROM eval_ccpc_filtres_detected WHERE id_service = :service AND debutStage = :debutStage AND finStage = :finStage AND (promotion = :promotion OR promotion IS NULL)';
					}
					else
					{
						$sql = 'SELECT id_filtre filtre FROM eval_ccpc_filtres_detected WHERE id_service = :service AND debutStage = :debutStage AND finStage = :finStage';
					}
					$res = $db -> prepare($sql);
					$res -> execute($sqlData);

					if ($res) {
						while ($res_f = $res -> fetch())
						{
							$filtres[$res_f['filtre']] = eval_ccpc_getFilterDetails($res_f['filtre']);
						}
					}
					return $filtres;
				}
				else
				{
					return FALSE;
				}
			}
			
		/**
		  * eval_ccpc_applyFilter - Test l'ensemble des requêtes pour un service, et enregistre les filtres qui s'y appliquent dans la base de donnée
		  *
		  * @category : eval_ccpc_functions
		  * @param int $id Identifiant du service
		  * @param int $promotion Identifiant de la promotion pour laquelle les données seront restreinte si le filtre presente une restriction de promotion
		  * @param string $debutStage Borne inférieur de l'intervalle temporel considéré, sous forme de timestamp
		  * @param string $finStage Borne supérieure de l'intervalle temporel considéré, sous forme de timestamp
		  * @return boolean TRUE si l'opération s'est déroulée avec succès, FALSE sinon
		  * 
		  * @Author Ali Bellamine
		  *
		  */
			
			function eval_ccpc_applyFilter ($id, $promotion, $dateDebut, $dateFin)
			{
				/**
					Prépare les variables
				**/
				
					// Base de donnée
					global $db;
					
					// Objet XML du formulaire d'évaluation
					if (is_file(PLUGIN_PATH.'formulaire.xml'))
					{
						$form = simplexml_load_file(PLUGIN_PATH.'formulaire.xml');
					}

					// Liste des filtres
					$filtres = eval_ccpc_getFilterList(TimestampToDatetime($dateDebut), TimestampToDatetime($dateFin));
					
					// Données d'évaluation

						// On récupère toutes les informations, même temporaires
						$bypasslimit = TRUE;
						
						// Pour la promotion
						$dataPromo = getEvaluationCCPCFullData($id, $promotion, $dateDebut, $dateFin, FALSE);
						
						// Sans la promotion (toutes les promotions)
						$dataAllPromo = getEvaluationCCPCFullData($id, FALSE, $dateDebut, $dateFin, FALSE);

						// On ferme la dérogation à l'accès aux données
						$bypasslimit = FALSE;
						
					if (!isset($form)) { return FALSE; }
				
				/**
					Effectue les test
				**/
				
				foreach($filtres AS $filtre)
				{
					$res = FALSE;

					if ($filtre['promotion'] == 0)
					{
						$res = eval_ccpc_exploreQuery($filtre['query'], $form, $dataAllPromo);
					}
					else
					{
						$res = eval_ccpc_exploreQuery($filtre['query'], $form, $dataPromo);
					}

                                        // Si le filtre est vérifié
					
					if ($res == 'success')
					{
						/*
							On enregistre le succès dans la BDD si il n'est pas déjà présent
						*/
							$sqlData = array('service' => $id, 'filtre' => $filtre['id'], 'dateDebut' => TimestampToDatetime($dateDebut), 'dateFin' => TimestampToDatetime($dateFin));
							
							// On vérifie si il est déjà présent
							if ($filtre['promotion'] == 0)
							{
								$sql = 'SELECT count(*) FROM eval_ccpc_filtres_detected WHERE id_service = :service AND id_filtre = :filtre AND debutStage = :dateDebut AND finStage = :dateFin LIMIT 1';
							}
							else
							{
								$sql = 'SELECT count(*) FROM eval_ccpc_filtres_detected WHERE id_service = :service AND id_filtre = :filtre AND debutStage = :dateDebut AND finStage = :dateFin AND promotion = :promotion LIMIT 1';
								$sqlData['promotion'] = $promotion;
							}
							
							$res = $db -> prepare($sql);
							$res -> execute($sqlData);
							
							if ($res_f = $res -> fetch())
							{
								if ($res_f[0] == 0)
								{
									// On insert dans la base de donnée
									$sqlData['promotion'] = $promotion; // Dans tous les cas on enregistre la promotion

									$sql = 'INSERT INTO eval_ccpc_filtres_detected (id_service, id_filtre, debutStage, finStage, promotion) VALUES (:service, :filtre, :dateDebut, :dateFin, :promotion)';
									$res = $db -> prepare($sql);
									$res -> execute($sqlData);
								}
							}
					}
					else {
						// On le supprime de la BDD si il est présent						
						$sqlData = array('service' => $id, 'filtre' => $filtre['id'], 'dateDebut' => TimestampToDatetime($dateDebut), 'dateFin' => TimestampToDatetime($dateFin));

						if ($filtre['promotion'] == 0)
						{
							$sql = 'DELETE FROM eval_ccpc_filtres_detected WHERE id_service = :service AND id_filtre = :filtre AND debutStage = :dateDebut AND finStage = :dateFin LIMIT 1';
						}
						else
						{
							$sql = 'DELETE FROM eval_ccpc_filtres_detected WHERE id_service = :service AND id_filtre = :filtre AND debutStage = :dateDebut AND finStage = :dateFin AND promotion = :promotion LIMIT 1';
							$sqlData['promotion'] = $promotion;
						}
						
						$res = $db -> prepare($sql);
						$res -> execute($sqlData);
					}
				}
			}
?>