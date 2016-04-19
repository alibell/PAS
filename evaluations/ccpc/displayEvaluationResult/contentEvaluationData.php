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

/***
	Récupération des données
***/

		/***
			On vérifie la présence d'évaluations -> si non on redirige l'utilisateur
		***/
		
		$sql = 'SELECT count(*) FROM eval_ccpc_resultats WHERE service = ?';
		$res = $db -> prepare($sql);
		$res -> execute(array($_GET['service']));
		$res_f = $res -> fetch();
		if (!isset($res_f[0]) || $res_f[0] == 0)
		{
			header('Location: '.ROOT.CURRENT_FILE.'?evaluationType='.$evaluationTypeData['id']);
		}

		/***
			Type de données affichées
		***/
		
		$allowedEvaluationContentType = array('stat', 'history', 'data');
		if (isset($_GET['evaluationContentType']) && in_array($_GET['evaluationContentType'], $allowedEvaluationContentType))
		{
			$evaluationContentType = $_GET['evaluationContentType'];
		}
		else
		{
			$evaluationContentType = 'stat';
		}

		/***
			Liste des filtres existants
		***/
		
		$filtres = array(); // Array contenant la valeur des filtres de selection
		
		$sql = 'SELECT e.promotion promotionId, p.nom promotionNom, e.debutStage dateDebut, e.finStage dateFin
					FROM eval_ccpc_resultats e
					INNER JOIN promotion p ON p.id = e.promotion
					INNER JOIN service s ON e.service = s.id
					WHERE e.service = ?';
					
			// Liste des périodes de stages correspondant au filtre
			$fastSelectSqlCore = 'SELECT DISTINCT e.debutStage debutStage, e.finStage finStage, e.promotion promotionId, p.nom promotionNom FROM eval_ccpc_resultats e INNER JOIN promotion p ON p.id = e.promotion 
							   INNER JOIN service s ON s.id = e.service
							   INNER JOIN hopital h ON h.id = s.hopital
							   INNER JOIN specialite sp ON sp.id = s.specialite
							   INNER JOIN user c ON c.id = s.chef WHERE e.service = ?';
		
		if ($_SESSION['rang'] <= 1)
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
			$fastSelectSqlCore .= ' AND e.date <= "'.$allowedDate.'" ';
		}
		
		/*
			Ne pas afficher les évaluations des autres services aux chef de service
		*/
		
		if ($_SESSION['rang'] == 2 && defined('CONFIG_EVAL_CCPC_RESTRICTEVALUATIONACCESSSERVICE') && CONFIG_EVAL_CCPC_RESTRICTEVALUATIONACCESSSERVICE == TRUE)
		{			
			$sql .= ' AND s.chef = "'.$_SESSION['id'].'"';
			$fastSelectSqlCore .= ' AND s.chef = "'.$_SESSION['id'].'"';
		}

		$res = $db -> prepare($sql);
		$res -> execute(array($_GET['service']));
		
		while($res_f = $res -> fetch())
		{
			if (!isset($filtres['promotion'][$res_f['promotionId']]))
			{
				/**
					Liste des promotions
				**/
				$filtres['promotion'][$res_f['promotionId']] = $res_f['promotionNom'];
			}
				
			/*
				Date
			*/
			if (!isset($filtres['dateMin']) || DatetimeToTimestamp($res_f['dateDebut']) < $filtres['dateMin'])
			{
				$filtres['dateMin'] = DatetimeToTimestamp($res_f['dateDebut']);
			}
			
			if (!isset($filtres['dateMax']) || DatetimeToTimestamp($res_f['dateFin']) > $filtres['dateMax'])
			{
				$filtres['dateMax'] = DatetimeToTimestamp($res_f['dateFin']);
			}
		}
		
		// Récupération de la liste des périodes de stages acceptées
		$fastSelectData = array();
		$res = $db -> prepare($fastSelectSqlCore);
		$res -> execute(array($_GET['service']));
		while ($res_f = $res -> fetch())
		{
			$fastSelectData[] = array(
				'dateDebut' => DatetimeToTimestamp($res_f['debutStage']),
				'dateFin' => DatetimeToTimestamp($res_f['finStage']),
				'promotion' => array(
					'id' => $res_f['promotionId'],
					'nom' => $res_f['promotionNom']
				)
			);
		}
		
		/***
			Application des filtres
		***/
		
		if (isset($_GET['FILTER']) && count($_GET['FILTER']) > 0)
		{	
			/*
				Promotions
			*/
			if (isset($_GET['FILTER']['promotion']) && is_numeric($_GET['FILTER']['promotion']))
			{
				$erreur = checkPromotion($_GET['FILTER']['promotion'], $erreur);
				if (count($erreur) == 0)
				{
					$promotion = $_GET['FILTER']['promotion'];
				}
				else
				{
					$promotion = false;
				}
			}
			else
			{
				$promotion = false;
			}
			
			/*
				Date
			*/
			if (isset($_GET['FILTER']['date']['min']) && is_numeric($_GET['FILTER']['date']['min']))
			{
				$dateDebut = $_GET['FILTER']['date']['min'];
			}
			else
			{
				$dateDebut = $filtres['dateMin'];
			}
			
			if (isset($_GET['FILTER']['date']['max']) && is_numeric($_GET['FILTER']['date']['max']))
			{
				$dateFin = $_GET['FILTER']['date']['max'];
			}
			else
			{
				$dateFin = $filtres['dateMax'];				
			}
		}
		else
		{
			$dateDebut = $filtres['dateMin'];
			$dateFin = $filtres['dateMax'];
			$promotion = false;
		}

		if ($_SESSION['rang'] >= 3)
		{
			$moderate = TRUE; // Les administrateurs peuvent voir les messages modérés
		}
		else
		{
			$moderate = FALSE;
		}
		
		/**
		Récupération des informations concernant le service
		**/
		
		if ($evaluationContentType == 'stat' || $evaluationContentType == 'data')
		{
			$evaluationData = getEvaluationCCPCFullData($_GET['service'],$promotion, $dateDebut, $dateFin, $moderate);
		}
		else if ($evaluationContentType == 'history')
		{
			$evaluationData['evaluations'] = array(); // Array qui contiendra toutes les données
			$listeDate = listeMoisEntreDeuxDates($dateDebut, $dateFin); // Liste des dates d'évaluations dispo dans l'intervalle temporel choisit par l'utilisateur
			
			foreach ($listeDate AS $dateKey => $date)
			{
				$tempEvaluationData = getEvaluationCCPCPartialData($_GET['service'],$promotion, $dateDebut, strtotime($date['Annee'].'-'.$date['MoisNb'].'-01 +1 month')-1); // On récupère les données à la date choisie
				
				/**
					On stocke les informations concernant le service
				**/
				if (!isset($evaluationData['service']))
				{
					$evaluationData['service'] = $tempEvaluationData['service'];
				}
				unset($tempEvaluationData['service']);
				
				/**
					On enregistre les données lorsqu'elles sont différentes des précédentes
				**/
				if ((count($evaluationData['evaluations']) > 0 && $evaluationData['evaluations'][count($evaluationData['evaluations'])-1]['stat'] !== $tempEvaluationData) || count($evaluationData['evaluations']) == 0)
				{
					$evaluationData['evaluations'][] = array('date' => $date, 'stat' => $tempEvaluationData);
				}
			}
		}
?>