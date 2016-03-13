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

		if (defined('CONFIG_EVAL_CCPC_NBEVALPARPAGE') && is_numeric(constant('CONFIG_EVAL_CCPC_NBEVALPARPAGE')) && constant('CONFIG_EVAL_CCPC_NBEVALPARPAGE') > 0)
		{
			$nbServicesParPage = CONFIG_EVAL_CCPC_NBEVALPARPAGE;
		}
		else
		{
			$nbServicesParPage = 10;
		}

		/**
			Création de la rêquete permettant de récupérer les services
		**/
		
		$filtres = array(); // Array contenant la valeur des filtres de selection
		
		$coreSql = 'SELECT e.id evaluationId, p.id promotionId, p.nom promotionNom, e.debutStage dateDebut, e.finStage dateFin, e.service serviceId,h.id hopitalId, h.nom hopitalNom
							FROM eval_ccpc_resultats e
							INNER JOIN promotion p ON p.id = e.promotion 
							INNER JOIN service s ON s.id = e.service
    					    INNER JOIN hopital h ON h.id = s.hopital
							INNER JOIN specialite sp ON sp.id = s.specialite
							INNER JOIN user c ON c.id = s.chef ';
		$coreSqlCount = 'SELECT count(distinct e.service)
									   FROM eval_ccpc_resultats e
									   INNER JOIN promotion p ON p.id = e.promotion 
									   INNER JOIN service s ON s.id = e.service
									   INNER JOIN hopital h ON h.id = s.hopital
									   INNER JOIN specialite sp ON sp.id = s.specialite
									   INNER JOIN user c ON c.id = s.chef ';
		$coreSqlExport = 'SELECT e.service serviceId FROM eval_ccpc_resultats e INNER JOIN promotion p ON p.id = e.promotion 	';
		$whereSqlFilter = ''; // Utilisé pour déterminer la liste des filtres à proposer
		$whereSqlContent = ''; // Utilisé pour déterminer la liste des évaluations à proposer
		$limitSqlContent = ''; // Utilisé pour déterminer la liste des évaluations à proposer
		$groupbySqlContent = ' GROUP BY e.service';

		// Liste des périodes de stages correspondant au filtre
		$fastSelectSqlCore = 'SELECT DISTINCT e.debutStage debutStage, e.finStage finStage, e.promotion promotionId, p.nom promotionNom FROM eval_ccpc_resultats e INNER JOIN promotion p ON p.id = e.promotion 
									   INNER JOIN service s ON s.id = e.service
									   INNER JOIN hopital h ON h.id = s.hopital
									   INNER JOIN specialite sp ON sp.id = s.specialite
									   INNER JOIN user c ON c.id = s.chef  ';
		
		/*
			Filtres (WHERE)
		*/
		
		$preparedValue = array(); // Array contenant les variables préparées
		
		if (isset($_GET['FILTER']) && count($_GET['FILTER']) > 0)
		{	

			/*
				Rechercher
			*/
			
			if (isset($_GET['FILTER']['search']) && $_GET['FILTER']['search'] != '')
			{
				$preparedValue['search'] = $_GET['FILTER']['search'];
			
				if ($whereSqlFilter == '') { $whereSqlFilter .= 'WHERE '; } else { $whereSqlFilter .= ' AND '; }
				if ($whereSqlContent == '') { $whereSqlContent .= 'WHERE '; } else { $whereSqlContent .= ' AND '; }
				$whereSqlFilter .= ' (h.nom LIKE :search OR h.alias LIKE :search OR sp.nom LIKE :search OR s.nom LIKE :search OR c.nom LIKE :search OR c.prenom LIKE :search OR CONCAT(c.nom, " ", c.prenom) LIKE :search OR CONCAT(c.prenom, " ", c.nom) LIKE :search) ';
				$whereSqlContent .= ' (h.nom LIKE :search OR h.alias LIKE :search OR sp.nom LIKE :search OR s.nom LIKE :search OR c.nom LIKE :search OR c.prenom LIKE :search OR CONCAT(c.nom, " ", c.prenom) LIKE :search OR CONCAT(c.prenom, " ", c.nom) LIKE :search) ';
			}			
		
			/*
				Promotions
			*/
			if (isset($_GET['FILTER']['promotion']) && is_numeric($_GET['FILTER']['promotion']))
			{
				$erreur = checkPromotion($_GET['FILTER']['promotion'], $erreur);
				if (count($erreur) == 0)
				{
					$preparedValue['promotion'] = $_GET['FILTER']['promotion'];

					$filtrePromotion = $_GET['FILTER']['promotion'];
					if ($whereSqlFilter == '') { $whereSqlFilter .= 'WHERE '; } else { $whereSqlFilter .= ' AND '; }
					if ($whereSqlContent == '') { $whereSqlContent .= 'WHERE '; } else { $whereSqlContent .= ' AND '; }
					$whereSqlFilter .= ' p.id = :promotion ';
					$whereSqlContent .= ' p.id = :promotion ';
				}
				else
				{
					$filtrePromotion = false;
				}
			}
			else
			{
				$filtrePromotion = false;
			}
			
			/*
				Dates
			*/

			if (isset($_GET['FILTER']['date']['min']) && isset($_GET['FILTER']['date']['max']) && $_GET['FILTER']['date']['min'] < $_GET['FILTER']['date']['max'])
			{
				$preparedValue['dateMin'] = TimestampToDatetime($_GET['FILTER']['date']['min']);
				$preparedValue['dateMax'] = TimestampToDatetime($_GET['FILTER']['date']['max']);

				if ($whereSqlFilter == '') { $whereSqlFilter .= 'WHERE '; } else { $whereSqlFilter .= ' AND '; }
				if ($whereSqlContent == '') { $whereSqlContent .= 'WHERE '; } else { $whereSqlContent .= ' AND '; }

				$whereSqlFilter .= ' e.finStage > :dateMin AND e.debutStage < :dateMax ';
				$whereSqlContent .= ' e.finStage > :dateMin AND e.debutStage < :dateMax ';
			}
						
			/*
				Certificat
			*/
			if (isset($_GET['FILTER']['certificat']) && is_numeric($_GET['FILTER']['certificat']))
			{
				$erreur = checkCertificat($_GET['FILTER']['certificat'], $erreur);
				if (count($erreur) == 0)
				{
					$preparedValue['certificat'] = $_GET['FILTER']['certificat'];

					if ($whereSqlFilter == '') { $whereSqlFilter .= 'WHERE '; } else { $whereSqlFilter .= ' AND '; }
					if ($whereSqlContent == '') { $whereSqlContent .= 'WHERE '; } else { $whereSqlContent .= ' AND '; }
					$whereSqlFilter .= ' (SELECT count(*) FROM servicecertificat WHERE idService = e.service AND idCertificat =:certificat) != 0 ';
					$whereSqlContent .= ' (SELECT count(*) FROM servicecertificat WHERE idService = e.service AND idCertificat = :certificat) != 0 ';
				}
			}

			/*
				Hopitaux
			*/
			if (isset($_GET['FILTER']['hopital']) && is_numeric($_GET['FILTER']['hopital']))
			{
				$erreur = checkHopital($_GET['FILTER']['hopital'], $erreur);
				if (count($erreur) == 0)
				{
					$preparedValue['hopital'] = $_GET['FILTER']['hopital'];

					if ($whereSqlFilter == '') { $whereSqlFilter .= 'WHERE '; } else { $whereSqlFilter .= ' AND '; }
					if ($whereSqlContent == '') { $whereSqlContent .= 'WHERE '; } else { $whereSqlContent .= ' AND '; }
					$whereSqlFilter .= ' s.hopital = :hopital ';
					$whereSqlContent .= ' s.hopital = :hopital ';
				}
			}			
		}
		else
		{
			$filtrePromotion = false;
		}
		
		/*
			Ne pas afficher les services masqués si il s'agit d'un étudiant
		*/
		
		if ($_SESSION['rang'] <= 1)
		{
			if ($whereSqlFilter == '') { $whereSqlFilter .= 'WHERE '; } else { $whereSqlFilter .= ' AND '; }
			if ($whereSqlContent == '') { $whereSqlContent .= 'WHERE '; } else { $whereSqlContent .= ' AND '; }
			
			$whereSqlFilter .= ' e.hide = 0';
			$whereSqlContent .= ' e.hide = 0';
		}
		
		/*
			Ne pas afficher les evaluations vielles de plus d'un mois aux étudiants
		*/
		
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
			if ($whereSqlFilter == '') { $whereSqlFilter .= 'WHERE '; } else { $whereSqlFilter .= ' AND '; }
			if ($whereSqlContent == '') { $whereSqlContent .= 'WHERE '; } else { $whereSqlContent .= ' AND '; }
			
			$whereSqlFilter .= ' e.date <= "'.$allowedDate.'"';
			$whereSqlContent .= ' e.date <= "'.$allowedDate.'"';
		}
		
		/*
			Pages (LIMIT)
		*/
		
		$sqlContentCount = $coreSqlCount.$whereSqlContent;
		$res = $db -> prepare($sqlContentCount);
		$res -> execute($preparedValue);
		if ($res_f = $res -> fetch())
		{
			$nbResultats = $res_f[0];
			$nbPage = ceil($nbResultats/$nbServicesParPage); // Calcul du nb de pages
			if (isset($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $nbPage)
			{
				$pageActuelle = $_GET['page'];
			}
			else
			{
				$pageActuelle = 1;
			}
				$positionActuelle = ($pageActuelle - 1)*$nbServicesParPage;
				$limitSqlContent  = ' LIMIT '.$nbServicesParPage.' OFFSET '.$positionActuelle;
		}	
		
		/*
			Création de la pagination
		*/

		$pagination = creerPagination(8, 4, $pageActuelle, $nbPage);
				
		// On crée les requêtes SQL
		$sqlFilter = $coreSql.$whereSqlFilter;
		$sqlContent = $coreSql.$whereSqlContent.$groupbySqlContent.$limitSqlContent;
		$fastSelectSql = $fastSelectSqlCore.$whereSqlFilter.' ORDER BY e.finStage DESC, e.debutStage DESC, e.promotion DESC';

		/*
			Création des catégories de filtres
		*/
		$res = $db -> prepare($sqlFilter);
		$res -> execute($preparedValue);
		
		while ($res_f = $res -> fetch())
		{
			/*
				Promotions
			*/
			if (!isset($filtres['promotion'][$res_f['promotionId']]['nom']))
			{
				$filtres['promotion'][$res_f['promotionId']]['id'] = $res_f['promotionId'];
				$filtres['promotion'][$res_f['promotionId']]['nom'] = $res_f['promotionNom'];
			}
			if (!isset($filtres['promotion'][$res_f['promotionId']]['nb'])) { $filtres['promotion'][$res_f['promotionId']]['nb'] = 1; }
			else { $filtres['promotion'][$res_f['promotionId']]['nb']++; }

			/*
				Certificat
			*/
			
			if ($tempService = getServiceInfo($res_f['serviceId']))
			{
				if (isset($tempService['certificat']))
				{
					foreach ($tempService['certificat'] AS $certificat)
					{
						if (!isset($filtres['certificat'][$certificat['id']]))
						{
							$filtres['certificat'][$certificat['id']] = $certificat;
						}
						if (!isset($filtres['certificat'][$certificat['id']]['nb'])) { $filtres['certificat'][$certificat['id']]['nb'] = 1; }
						else { $filtres['certificat'][$certificat['id']]['nb']++; }
					}
				}
			}
			
			/*
				Date
			*/
			
			if (!isset($filtres['dateMin']) || (isset($filtres['dateMin']) && DatetimeToTimestamp($res_f['dateDebut']) < $filtres['dateMin'])) { 
				if (isset($_GET['FILTER']) && count($_GET['FILTER']) > 0)
				{	
					$filtres['dateMin'] = DatetimeToTimestamp($res_f['dateDebut']); 
				}
				else
				{
					$filtres['dateMin'] = time()-31536000; 
				}
			}
			
			if (!isset($filtres['dateMax']) || (isset($filtres['dateMax']) && DatetimeToTimestamp($res_f['dateFin']) > $filtres['dateMax'])) { 
				if (isset($_GET['FILTER']) && count($_GET['FILTER']) > 0)
				{	
					$filtres['dateMax'] = DatetimeToTimestamp($res_f['dateFin']); 
				}
				else
				{
					$filtres['dateMax'] = time(); 
				}
			}

			/*
				Hopitaux
			*/
			if (!isset($filtres['hopital'][$res_f['hopitalId']]['nom']))
			{
				$filtres['hopital'][$res_f['hopitalId']]['id'] = $res_f['hopitalId'];
				$filtres['hopital'][$res_f['hopitalId']]['nom'] = $res_f['hopitalNom'];
			}
			if (!isset($filtres['hopital'][$res_f['hopitalId']]['nb'])) { $filtres['hopital'][$res_f['hopitalId']]['nb'] = 1; }
			else { $filtres['hopital'][$res_f['hopitalId']]['nb']++; }
			
		}
		
		/*
			Récupération des données d'évaluations des stages sélectionnés
		*/

		$evaluationData = array();
		$res = $db -> prepare ($sqlContent);
		$res -> execute($preparedValue);
		while ($res_f = $res -> fetch())
		{
			$evaluationData[$res_f['serviceId']] = getEvaluationCCPCPartialData($res_f['serviceId'], $filtrePromotion, $filtres['dateMin'] ,$filtres['dateMax']);
		}
		
		/*
			Récupération de la liste des périodes de stage correspondant
		*/
		
		$fastSelectData = array();
		$res = $db -> prepare($fastSelectSql);
		$res -> execute($preparedValue);
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
		
		/*
			Exportation de la liste des services
		*/
		
		if (isset($_GET['download']) && $_GET['download'] == 'csv')
		{
			$exportList = array();
		
			// Liste des services dont on veux exporter les données csv
			$sqlExport = $coreSqlExport.$whereSqlContent.$groupbySqlContent;
			$res = $db -> prepare($sqlExport);
			$res -> execute($preparedValue);
			while($res_f = $res -> fetch())
			{
				$exportList[] = $res_f[0];
			}
			
			// On crée le CSV
			if (isset($exportList) && count($exportList) > 0)
			{
				if ($_SESSION['rang'] >= 3) { $moderate = TRUE; }
				else { $moderate = FALSE; }
				
				$csv = generateAllCSV($exportList, $filtres['dateMin'], $filtres['dateMax'], $filtres['promotion'], $moderate);
				downloadFILE($csv['csvPath']);
			}
		}
?>