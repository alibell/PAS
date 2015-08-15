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
		21/04/15 - liste.php - Ali Bellamine
		Affiche la liste des utilisateurs
	*/

	/**
		1. Récupération de la liste des utilisateurs
	**/
	
		// Variables nécessaire à la récupération des données
			
			$nbUtilisateurParPage = 20;
			$listeUtilisateurs = array(); // array qui contiendra la liste des utilisateur
			
		// Création des requêtes
			$sql = 'SELECT id FROM user '; // Permet de récupérer la liste des id des utilisateurs concernés
			$sqlCount = 'SELECT count(*) nb FROM `user` '; // Permet de récupérer le nombre total de résultat
			
		// On complète la requête
		
			// WHERE : prise en compte des filtres
			
				$rechercheFiltre = array(); // On y stocke les variables utilisés dans la requête SQL
				$whereSQL = ' WHERE 1 ';
			
				// De recherche
				if (isset($_GET['search']))
				{
					$rechercheFiltre['search'] = '%'.$_GET['search'].'%'; // On l'ajoute aux filtres utilisés
					
					$whereSQL .= ' AND (nbEtudiant LIKE :search OR nom LIKE :search OR prenom LIKE :search OR CONCAT(nom, \' \', prenom) LIKE :search OR CONCAT(prenom, \' \', nom) LIKE :search) ';
				}
				
			// LIMIT : prise en compte des limites de page
				
				// Nombre total de pages
				$res = $db -> prepare($sqlCount.$whereSQL);
				$res -> execute($rechercheFiltre);
				$res_f = $res -> fetch();
				
				$nbResultats = $res_f['nb'];
				$nbTotalPages = ceil($nbResultats/$nbUtilisateurParPage);
				
				// Détermination de la page en cours
				if (isset($_GET['pageNb']) && $_GET['pageNb'] > 0 && $_GET['pageNb'] <= $nbTotalPages)
				{
					$pageActuelle = $_GET['pageNb'];
				}
				else
				{
					$pageActuelle = 1;
				}
				
				$positionActuelle = ($pageActuelle - 1)*$nbUtilisateurParPage;
				$limitSQL  = ' LIMIT '.$nbUtilisateurParPage.' OFFSET '.$positionActuelle;
				
			// ORDER : si non spécifié, il s'agit du nom
						
			$allowedORDER = array('prenom', 'nom', 'rang', 'nbEtudiant', 'promotion', 'mail'); // Liste des order autorisés
			if (isset($_GET['order']) && in_array($_GET['order'], $allowedORDER))
			{
				$orderSQL = ' ORDER BY '.$_GET['order'].' ';
			}
			else
			{
				$orderSQL = ' ORDER BY nom ';
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
				$listeUtilisateurs[$res_f['id']] = getUserData($res_f['id']);
			}
			
	/**
		2. Création de la pagination
	**/
	
	$pagination = creerPagination(8, 4, $pageActuelle, $nbTotalPages);
			
	/**
		Affichage des résultats
	**/
	
		/*
			Liste des utilisateurs
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
					
					<input name = "search" type = "text" style = "width: 90%;" value = "<?php if (isset($_GET['search'])) { echo $_GET['search']; } ?>" placeholder = "<?php echo LANG_ADMIN_UTILISATEURS_LISTE_FILTER_SEARCHBAR; ?>" />
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
			
			<table  style = "margin-top: 10px;">
				<tr class = "headTR">
					<td><a href = "<?php echo $url.'&order=nom'; if (isset($_GET['order']) && $_GET['order'] == 'nom' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NOM; ?></a></td>
					<td><a href = "<?php echo $url.'&order=prenom'; if (isset($_GET['order']) && $_GET['order'] == 'prenom' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PRENOM; ?></a></td>
					<td><a href = "<?php echo $url.'&order=mail'; if (isset($_GET['order']) && $_GET['order'] == 'mail' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_MAIL; ?></a></td>
					<td><a href = "<?php echo $url.'&order=nbEtudiant'; if (isset($_GET['order']) && $_GET['order'] == 'nbEtudiant' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_NBETUDIANT; ?></a></td>
					<td><a href = "<?php echo $url.'&order=promotion'; if (isset($_GET['order']) && $_GET['order'] == 'promotion' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_PROMOTION; ?></a></td>
					<td><a href = "<?php echo $url.'&order=rang'; if (isset($_GET['order']) && $_GET['order'] == 'rang' && !isset($_GET['desc'])) { echo '&desc'; } ?>"><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_RANG; ?></td>
					<td><?php echo LANG_ADMIN_UTILISATEURS_LISTE_TABLE_TITLE_ADMIN; ?></td>
				</tr>
				<?php
					foreach($listeUtilisateurs AS $utilisateur)
					{
						?>
						<tr class = "bodyTR">
							<td><?php echo $utilisateur['nom']; ?></td>
							<td><?php echo $utilisateur['prenom']; ?></td>
							<td>
							<?php
								if (is_array($utilisateur['mail']))
								{
									$firstLoop = TRUE;
									foreach ($utilisateur['mail'] AS $mail)
									{
										if ($firstLoop) { $firstLoop = FALSE; } else { echo '<br />'; }
										echo $mail;
									}
								}
								else
								{
									echo $utilisateur['mail']; 
								}
							?>
							</td>
							<td><?php echo $utilisateur['nbEtudiant']; ?></td>
							<td><?php if (isset($utilisateur['promotion']['nom'])) { echo $utilisateur['promotion']['nom']; } ?></td>
							<td><?php echo constant('LANG_RANG_VALUE_'.$utilisateur['rang']); ?></td>
							<td>
								<a href = "<?php echo ROOT.CURRENT_FILE.'?page=profil&action=view&id='.$utilisateur['id']; ?>"><i class="fa fa-user"></i></a>
								<a href = "<?php echo ROOT.CURRENT_FILE.'?page=profil&action=edit&id='.$utilisateur['id']; ?>"><i class="fa fa-pencil"></i></a>
								<?php if ($_SESSION['rang'] >= $utilisateur['rang']) { ?><a href = "<?php echo getPageUrl('index', array('loginAS' => 1, 'loginASId' => $utilisateur['id'])); ?>"><i class="fa fa-user-secret"></i></a>	 <?php } ?> <!-- Se connecter en tant que l'utilisateur -->
								<?php if ($_SESSION['rang'] >= $utilisateur['rang']) { ?><a href = "<?php echo ROOT.CURRENT_FILE.'?page=profil&action=delete&id='.$utilisateur['id']; ?>"><i class="fa fa-trash-o"></i></a>	 <?php } ?>
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