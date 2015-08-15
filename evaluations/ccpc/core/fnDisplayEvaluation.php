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
	  * eval_ccpc_getNoFormData - Récupère les informations non contenues dans le formulaire au cours d'une évaluation
	  *
	  * @category : eval_ccpc_functions
	  * @param array $evaluationData Array contenant les informations relatives à l'évaluation
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction
	  * @return array Array contenant les informations non contenues dans le formulaire et les erreurs rencontrés lors de l'execution de la fonction
	  * 
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	['data'][identifiant du service où est affecté l'utilisateur]['date'] => (string) Date de remplissage de l'évaluation, sous forme de timestamp<br>
	  *	['data'][identifiant du service où est affecté l'utilisateur]['promotion'] => (int) Identifiant de la promotion de l'utilisateur<br>
	  *	['data'][identifiant du service où est affecté l'utilisateur]['debutStage'] => (string) Date de début de l'affectation de l'étudiant dans le service<br>
	  *	['data'][identifiant du service où est affecté l'utilisateur]['finStage'] => (string) Date de fin de l'affectation de l'étudiant dans le service<br>
	  *	['data'][identifiant du service où est affecté l'utilisateur]['service'] => (int) Identifiant du service<br>
	  *	['data'][identifiant du service où est affecté l'utilisateur]['nbExternesPeriode'] => (int) Nombre d'étudiants actuellement enregistrés dans le service<br>
	  *	['erreur'][identifiant du service où est affecté l'utilisateur][] => (array) Array contenant la liste des erreurs rencontrés
	  *
	  */
	
	function eval_ccpc_getNoFormData ($evaluationData, $erreur) {
		
		global $db;
		$data = array();
		
		/**
			On remplit l'array $data['data']
		**/
		
			// Date
			$tempData['date'] = TimestampToDatetime(time());
		
			// Promotion de l'utilisateur
			if ($userData = getUserData($_SESSION['id']) && isset($tempData['promotion']))
			{
				$tempData['promotion'] = $userData['promotion']['id'];
			}
			else if ($userData = getUserData($_SESSION['id']) && !isset($tempData['promotion']))
			{
				$tempData['promotion'] = 'NULL';
			}
			else {
				$tempErreur['LANG_ERROR_CCPC_NOPROMOTION'] = true;
			}
		
			// Informations concernant le service et la durée du stage
			$sql = 'SELECT ae.service service, ae.dateDebut dateDebut, ae.dateFin dateFin
						FROM affectationexterne ae
						WHERE ae.dateDebut <= :dateDebutEval AND ae.dateFin >= :dateDebutEval AND userId = :id';
			$res = $db -> prepare($sql);
			$res -> execute(array(
										'dateDebutEval' => TimestampToDatetime($evaluationData['date']['debut']),
										'id' => $_SESSION['id']
									));
			if ($res)
			{
				while ($res_f = $res -> fetch())
				{
					$data['data'][$res_f['service']]['debutStage'] = $res_f['dateDebut'];
					$data['data'][$res_f['service']]['finStage'] = $res_f['dateFin'];
					$data['data'][$res_f['service']]['service'] = $res_f['service'];
					$data['data'][$res_f['service']]['promotion'] = $tempData['promotion'];
					$data['data'][$res_f['service']]['date'] = $tempData['date'];
											
					$sql = 'SELECT count(*) nbExterne
								FROM affectationexterne
								WHERE (dateDebut >=  :dateDebutUser AND dateDebut <= :dateFinUser ) AND (dateDebut <= :dateDebutUser AND dateFin >= :dateDebutUser)';
					$res2 = $db -> prepare($sql);
					$res2 -> execute(array(
													'dateDebutUser' => $data['data'][$res_f['service']]['debutStage'],
													'dateFinUser' => $data['data'][$res_f['service']]['finStage']
												));
					if ($res2_f = $res2 -> fetch())
					{
						$data['data'][$res_f['service']]['nbExternesPeriode'] = $res2_f['nbExterne'];
					}
					else
					{
						$data['erreur'][$res_f['service']]['LANG_ERROR_CCPC_NONBEXTERNE'] = true;
					}
				}
			}
			
		return $data;
	}
	
	/**
	  * processCCPCformData - Vérifie et traite les données retournées par le formulaire 
	  *
	  * @category : eval_ccpc_functions
	  * @param array $formData Array contenant les données à traiter
	  * @param array $evaluationData Array contenant les informations relatives à l'évaluation
	  * @return array Array contenant les informations de formData après qu'elles aient été traités et les erreurs rencontrés lors de l'execution de la fonction
	  * 
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	['erreur'][] => (array) Array contenant les erreurs<br>
	  *	[nom du champs dans la base de donnée] => valeur fournie par l'utilisateur
	  */
	
	function processCCPCformData($formData, $evaluationData)
	{
	
	global $db;
	
	$formResult = array();	
	$erreur = array();
	
	// On parcours le fichier XML
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
						$allowedValue[] = array();
						
						foreach ($input -> option AS $option)
						{
							$allowedValue[]	= $option['value'];
						}
						
						if (isset($formData[(string) $input['name']]) && in_array($formData[(string) $input['name']], $allowedValue))
						{
							$formResult[(string) $input['nomBDD']] = $formData[(string) $input['name']];
						}
						else if (isset($input['required']) && $input['required'] == 1)
						{
							$erreur['LANG_ERROR_CCPC_INCOMPLETEFORM'] = true;
						}
						else
						{
							$formResult[(string) $input['nomBDD']] = '';
						}
					}
					else if ($input['type'] == 'checkbox')
					{
						foreach($input -> checkbox AS $checkbox)
						{
							if (isset($formData[(string) $input['name']]) && in_array((string) $checkbox["value"], $formData[(string) $input['name']]))
							{
								$formResult[(string) $checkbox['nomBDD']] = 1;
							}
							else
							{
								$formResult[(string) $checkbox['nomBDD']] = 0;
							}
						}
					}
					else if ($input['type'] == 'radio')
					{
						$allowedValue[] = array();
						
						foreach ($input -> radio AS $radio)
						{
							$allowedValue[]	= $radio['value'];
						}
						
						if (isset($formData[(string) $input['name']]) && in_array($formData[(string) $input['name']], $allowedValue))
						{
							$formResult[(string) $input['nomBDD']] = $formData[(string) $input['name']];
						}
						else if (isset($input['required']) && $input['required'] == 1)
						{
							$erreur['LANG_ERROR_CCPC_INCOMPLETEFORM'] = true;
						}
						else
						{
							$formResult[(string) $input['nomBDD']] = '';
						}
					}
					else if ($input['type'] == 'text')
					{
						foreach ($input -> text AS $text)
						if (isset($text['required']) && $text['required'] == 1 && (!isset($formData[(string) $text['name']]) || $formData[(string) $text['name']] == ''))
						{
							$erreur['LANG_ERROR_CCPC_INCOMPLETEFORM'] = true;
						}
						else if (isset($formData[(string) $text['name']]))
						{
							$formResult[(string) $text['nomBDD']] = htmLawed($formData[(string) $text['name']]);
						}
						else 
						{
							$formResult[(string) $text['nomBDD']] = '';
						}
					}
					else if ($input['type'] == 'textarea')
					{
						if (isset($input['required']) && $input['required'] == 1 && (!isset($formData[(string) $input['name']]) || $formData[(string) $input['name']] == ''))
						{
							$erreur['LANG_ERROR_CCPC_INCOMPLETEFORM'] = true;
						}
						else if (isset($formData[(string) $input['name']]))
						{
							$formResult[(string) $input['nomBDD']] = htmLawed($formData[(string) $input['name']]);
						}
						else 
						{
							$formResult[(string) $input['nomBDD']] = '';
						}
					}
				}
			}
		}
	}

	/**
		Récupération des données non incluses dans le formulaire (promotion, nb d'externe, service, etc...)
	**/
	
	if (count($erreur) == 0)
	{
		$nonEvaluationData = eval_ccpc_getNoFormData($evaluationData,$erreur);
		
			/*
				On récupère la liste des services déjà évalués
			*/
			
			if (getEvaluationRegisterData() != '')
			{
				$evaluateService = unserialize(getEvaluationRegisterData());
			}
			else
			{
				$evaluateService = array();
			}
			
			/*
				On retire les services déjà évalués de la liste des services à évaluer
			*/
			
			foreach ($evaluateService AS $service)
			{
				if (isset($nonEvaluationData['data'][$service]))
				{
					unset($nonEvaluationData['data'][$service]);
				}
			}
		
		// On récupère les données qui ne sont pas d'évaluation
		if (isset($nonEvaluationData['data']) && count($nonEvaluationData['data']) > 0)
		{
			if (isset($formData['service']) && isset($nonEvaluationData['data'][$formData['service']]))
			{
				$formResult = array_merge($formResult, $nonEvaluationData['data'][$formData['service']]); // On récupère les données d'évaluation
			}
			else
			{
				$erreur['LANG_ERROR_CCPC_INCOMPLETEFORM'] = true;
			}
		}
		
		// On récupère les erreurs
		if (isset($nonEvaluationData['erreur']) && count($nonEvaluationData['erreur']) > 0)
		{
			if (isset($formData['service']) && isset($nonEvaluationData['erreur'][$formData['service']]))
			{
				$erreur = array_merge($erreur, $nonEvaluationData['erreur'][$formData['service']]); // On récupère les données d'évaluation
			}
		}
	}
	$formResult['erreur'] = $erreur;
	
	return $formResult;
	}

	/**
	  * registerCCPCformData - Enregistre les données du formulaire d'évaluation dans la base de donnée, et appelle la fonction {@link eval_ccpc_applyFilter} chargée de détecter les filtres s'appliquant au service
	  *
	  * @category : eval_ccpc_functions
	  * @param array $formData Array contenant les données à enregistrer dans la base de données, fournis par la fonction {@link processCCPCformData}
	  * @return boolean TRUE si l'enregistrement a été effectué avec succès, FALSE sinon
	  * 
	  * @Author Ali Bellamine
	  *
	  */
	
	function registerCCPCformData($formData)
	{
		initTable(); // S'assure de l'existence de la table dans la BDD
		global $db;
		
		// On enregistre le formulaire dans la base de donnée
		$listeSQL = array();
		
		if (is_file(PLUGIN_PATH.'formulaire.xml'))
		{
			if  ($form = simplexml_load_file(PLUGIN_PATH.'formulaire.xml'))
			{
				foreach ($form -> categorie AS $categorie)
				{
					foreach($categorie -> input AS $input)
					{
						if ($input['type'] == 'select' || $input['type'] == 'radio' || $input['type'] == 'textarea')
						{
							$listeSQL[] = $input['nomBDD'];
						}
						else if ($input['type'] == 'checkbox')
						{
							foreach($input -> checkbox AS $checkbox)
							{
								$listeSQL[] = $checkbox['nomBDD'];
							}
						}
						else if ($input['type'] == 'text')
						{
							foreach($input -> text AS $text)
							{
								$listeSQL[] = $text['nomBDD'];
							}
						}
					}
				}
			}
		}
		
		/*
			On crée la requête SQL
		*/
		
		$SQLColumn = '(promotion, service, debutStage, finStage, nbExternesPeriode, date';
		$SQLValues = '(:promotion, :service, :debutStage, :finStage, :nbExternesPeriode, :date';
		
		foreach ($listeSQL AS $itemSQL)
		{
			$SQLColumn .= ', ';
			$SQLValues .= ', ';
			
			$SQLColumn .= $itemSQL;
			$SQLValues .= ':'.$itemSQL;
		}
		
		$SQLColumn .= ')';
		$SQLValues .= ')';
		
		$sql = 'INSERT INTO eval_ccpc_resultats'.$SQLColumn.' VALUES '.$SQLValues;
						
		$res = $db -> prepare($sql);
		if ($res -> execute($formData))
		{
			// On met à jour les filtres pour le stage sur le période donnée
			eval_ccpc_applyFilter($formData['service'], $formData['promotion'], DatetimeToTimestamp($formData['debutStage']), DatetimeToTimestamp($formData['finStage']));
			
			return true;
		}
		else
		{
			return false;
		}
	}
?>