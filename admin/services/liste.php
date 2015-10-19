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
		26/04/15 - liste.php - Ali Bellamine
		Affiche la liste des services
	*/

	/**
		0. Routage
	**/
	
	$allowedAction = array('list', 'dl');
	if (isset($_GET['action']) && in_array($_GET['action'], $allowedAction))
	{
		$action = $_GET['action'];
	}
	else
	{
		$action = 'list';
	}
	
	/**
		1. Récupération de la liste des services
	**/
	
		// Variables nécessaire à la récupération des données
			
			$nbServicesParPage = 20;
			$listeServices = array(); // array qui contiendra la liste des services
			
		// Création des requêtes
			$sql = 'SELECT s.id id FROM service s INNER JOIN hopital h ON s.hopital = h.id INNER JOIN specialite sp ON sp.id = s.specialite INNER JOIN user u ON u.id = s.chef '; // Permet de récupérer la liste des id des services concernés
			$sqlCount = 'SELECT count(*) nb FROM `service` s INNER JOIN hopital h ON s.hopital = h.id INNER JOIN specialite sp ON sp.id = s.specialite INNER JOIN user u ON u.id = s.chef'; // Permet de récupérer le nombre total de résultat
			
		// On complète la requête
		
			// WHERE : prise en compte des filtres
			
				$rechercheFiltre = array(); // On y stocke les variables utilisés dans la requête SQL
				$whereSQL = ' WHERE 1 ';
			
				// De recherche
				if (isset($_GET['search']))
				{
					$rechercheFiltre['search'] = '%'.$_GET['search'].'%'; // On l'ajoute aux filtres utilisés
					
					$whereSQL .= ' AND (s.nom LIKE :search OR h.nom LIKE :search OR h.alias LIKE :search OR sp.nom LIKE :search OR u.nom LIKE :search OR u.prenom LIKE :search OR CONCAT(u.nom, \' \', u.prenom) LIKE :search OR CONCAT(u.prenom, \' \', u.nom) LIKE :search) ';
				}
				
			// LIMIT : prise en compte des limites de page
				
				// Nombre total de pages
				$res = $db -> prepare($sqlCount.$whereSQL);
				$res -> execute($rechercheFiltre);
				$res_f = $res -> fetch();
				
				$nbResultats = $res_f['nb'];
				$nbTotalPages = ceil($nbResultats/$nbServicesParPage);
				
				// Détermination de la page en cours
				if (isset($_GET['pageNb']) && $_GET['pageNb'] > 0 && $_GET['pageNb'] <= $nbTotalPages)
				{
					$pageActuelle = $_GET['pageNb'];
				}
				else
				{
					$pageActuelle = 1;
				}
				
				$positionActuelle = ($pageActuelle - 1)*$nbServicesParPage;
				$limitSQL  = ' LIMIT '.$nbServicesParPage.' OFFSET '.$positionActuelle;
				
			// ORDER : si non spécifié, il s'agit du nom
						
			$allowedORDER = array('nom' => 's.nom', 'hopital' => 'h.nom', 'chef' => 'u.nom', 'specialite' => 'sp.nom', 'id' => 's.id'); // Liste des order autorisés
			if (isset($_GET['order']) && isset($allowedORDER[$_GET['order']]))
			{
				$orderSQL = ' ORDER BY '.$allowedORDER[$_GET['order']].' ';
			}
			else
			{
				$orderSQL = ' ORDER BY  '.$allowedORDER['nom'];
			}
			
			if (isset($_GET['desc']))
			{
				$orderSQL .= ' DESC';
			}
			else
			{
				$orderSQL .= ' ASC';
			}

				
		// On récupére la liste des id
			$res = $db -> prepare($sql.$whereSQL.$orderSQL.$limitSQL);
			$res -> execute($rechercheFiltre);
			while($res_f = $res -> fetch())
			{
				$listeServices[$res_f['id']] = getServiceInfo($res_f['id']);
			}
			
			
		/*
			Téléchargement de la liste des services au format CSV
		*/
		
		if ($action == 'dl')
		{
			/*
				Création de l'array à retourner
			*/
			
			$servicesCSV = array(array('id', LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_HOPITAL, LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_SPECIALITE, LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_CHEF, LANG_ADMIN_SERVICES_NOM));
			
			foreach (getServiceList() AS $service)
			{
				$servicesCSV[] = array($service['id'], $service['hopital']['nom'], $service['specialite']['nom'], $service['chef']['nom'], $service['nom']);
			}
			// Téléchargement du CSV
			downloadCSV($servicesCSV, 'services.csv');
		}
		
	if ($action == 'list')
	{
		
	/**
		2. Création de la pagination
	**/
	
	$pagination = creerPagination(8, 4, $pageActuelle, $nbTotalPages);
			
	/**
		Affichage des résultats
	**/
	
		/*
			Liste des services
		*/
		
			/*
				Filtres de sélection
			*/
			
			?>
			<div id = "filtres">
				<form method = "GET">
					<?php
					if (isset($_GET['page']))
					{
						?>
						<input type = "hidden" name = "page" value = "<?php echo $_GET['page']; ?>" />
						<?php
					}
					
					if (isset($_GET['pageNb']))
					{
						?>
						<input type = "hidden" name = "pageNb" value = "<?php echo $_GET['pageNb']; ?>" />
						<?php
					}
					?>
					
					<input name = "search" type = "text" style = "width: 90%;" value = "<?php if (isset($_GET['search'])) { echo $_GET['search']; } ?>" placeholder = "<?php echo LANG_ADMIN_SERVICES_LISTE_FILTER_SEARCHBAR; ?>" />
				</form>
			</div>
			
			<div id = "donnees">
			
			<?php

			/*
				Données
			*/
		
			// Création des liens
			$tempGET = $_GET;
			unset($tempGET['order']);
			unset($tempGET['desc']);
			$url = ROOT.CURRENT_FILE.'?'.http_build_query($tempGET);
			?>
			<a class = "bouton" href = "<?php echo ROOT.CURRENT_FILE.'?page=service&action=add'; ?>"><i class="fa fa-plus-circle"></i></a>

			<table  style = "margin-top: 10px;">
				<tr class = "headTR">
					<td><a href = "<?php echo $url.'&order=nom'; if (isset($_GET['order']) && $_GET['order'] == 'nom' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_NOM; ?></a></td>
					<td><a href = "<?php echo $url.'&order=hopital'; if (isset($_GET['order']) && $_GET['order'] == 'hopital' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_HOPITAL; ?></a></td>
					<td><a href = "<?php echo $url.'&order=chef'; if (isset($_GET['order']) && $_GET['order'] == 'chef' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_CHEF; ?></a></td>
					<td><a href = "<?php echo $url.'&order=specialite'; if (isset($_GET['order']) && $_GET['order'] == 'specialite' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_SERVICES_LISTE_TABLE_TITLE_SPECIALITE; ?></a></td>
					<td><?php echo LANG_ADMIN_TABLE_TITLE_ADMIN; ?></td>
				</tr>
				<?php
					foreach($listeServices AS $service)
					{
						?>
						<tr class = "bodyTR">
							<td><?php echo $service['nom']; ?></td>
							<td><?php echo $service['hopital']['nom']; ?></td>
							<td><?php echo $service['chef']['nom'].' '.$service['chef']['prenom']; ?></td>
							<td><?php echo $service['specialite']['nom']; ?></td>
							<td>
								<a href = "<?php echo ROOT.CURRENT_FILE.'?page=service&action=view&id='.$service['id']; ?>"><i class="fa fa-info"></i></a>
								<a href = "<?php echo ROOT.CURRENT_FILE.'?page=service&action=edit&id='.$service['id']; ?>"><i class="fa fa-pencil"></i></a>
								<a href = "<?php echo ROOT.CURRENT_FILE.'?page=service&action=delete&id='.$service['id']; ?>"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>
						<?php
					}
				?>
			</table>
			
			<?php
		
				/*
					Pagination
				*/
			?>		
				<div id = "pagination">
					<?php
					
						$tempGET = $_GET;
						unset($tempGET['pageNb']);
						$urlPage = http_build_query($tempGET);
					
						foreach ($pagination AS $page => $pageValue)
						{
							?>
								<a href = "<?php echo ROOT.CURRENT_FILE.'?'.$urlPage.'&pageNb='.$page; ?>"><span class = "pageBouton <?php if ($page == $pageActuelle) { echo 'pageActuelle'; } ?>"><?php echo $page; ?></span></a>
							<?php
						}
					?>
				</div>
			</div>
			
	<?php
	}
	?>