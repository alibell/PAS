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
	Affichage des données
***/
?>
		<!-- Filtres de recherche de service -->
		<div id = "evalccpcFiltres">
			<div id = "evalccpcFiltresTitre"><?php echo LANG_FORM_CCPC_FILTER_TITLE; ?></div>
			
			<form method = 'GET'>
				<div id = "evalccpcFiltresFormContent">
				<input type = 'hidden' name = 'evaluationType' value = '<?php echo $_GET['evaluationType']; ?>' />
			<?php 

				/*
					Rechercher
				*/
				?>
				<span class = "evalccpcFiltreCategorie"><?php echo LANG_FORM_CCPC_FILTER_SEARCH_TITLE; ?></span>

					<input type = "text" name = "FILTER[search]" style = "display: block; margin: auto;" value = "<?php if (isset($_GET['FILTER']['search'])) { echo $_GET['FILTER']['search']; } ?>" />
				<?php
				
				/*
					Accélère la sélection
				*/
				if (count($fastSelectData) > 0)
				{
				?>
				<span class = "evalccpcFiltreCategorie">Sélection rapide</span>
					<div style = "text-align: center;">
						<select id = "fastSelectFilter"> 
							<option></option>
							<?php
							foreach ($fastSelectData AS $key => $value)
							{
							?>
							<option value = "<?php echo $key; ?>"><?php echo $value['promotion']['nom'].' - '.LANG_FORM_CCPC_FILTER_DATE_FROM.' '.date('d/m/Y', $value['dateDebut']).' '.LANG_FORM_CCPC_FILTER_DATE_TO.' '.date('d/m/Y', $value['dateFin']); ?></option>
							<?php
							}
							?>
						</select>
					</div>
				<?php
				}		

				/*
					Date
				*/
				if (isset($filtres['dateMin']) && isset($filtres['dateMax']))
				{				
				?>
				<span class = "evalccpcFiltreCategorie"><?php echo LANG_FORM_CCPC_FILTER_DATE_TITLE; ?></span>

						<!-- Date de filtre actuelle -->
						<div id = "currentDateFilter">
							<span>
							<?php echo LANG_FORM_CCPC_FILTER_DATE_FROM.' '.date('d/m/Y', $filtres['dateMin']).'  '.LANG_FORM_CCPC_FILTER_DATE_TO.' '.date('d/m/Y', $filtres['dateMax']); ?> 

							<?php
								// Création de l'URL
								$copyGET = $_GET;
								unset($copyGET['FILTER']['date']);
								$copyGET['FILTER']['nodateauto'] = true;
								$copyGETURL = http_build_query($copyGET);
							?>
							<a href = "<?php echo ROOT.CURRENT_FILE.'?'.$copyGETURL; ?>"><i class="fa fa-times"></i></a></span>
						</div>

						<input type = "hidden" class = "dateRangeSelector" data-DateType = "min" name = "FILTER[date][min]" value = "" />
						<input type = "hidden" class = "dateRangeSelector" data-DateType = "max" name = "FILTER[date][max]" value = "" />
						<input type = "button" id = "dateRangeSelector" data-dateMin = "<?php echo $filtres['dateMin']; ?>" data-dateMax = "<?php echo $filtres['dateMax']; ?>" value = "<?php echo LANG_FORM_CCPC_LISTE_SERVICE_DATEINTERVAL_SELECT; ?>" />
					<?php
				}
				
				/*
					Promotion
				*/
					?>
					<span class = "evalccpcFiltreCategorie"><?php echo LANG_FORM_CCPC_FILTER_PROMOTION_TITLE; ?></span>
						<label for = "promotionFilterId">
							<input type = "radio" value = "" id = "promotionFilterId" name = "FILTER[promotion]" <?php if (!isset($_GET['FILTER']['promotion']) || $_GET['FILTER']['promotion'] == '') { echo 'checked'; } ?> />
							<?php echo LANG_FORM_CCPC_FILTER_PROMOTION_OPTION_ALL; ?>
						</label>
					<?php
				if (isset($filtres['promotion']) && count ($filtres['promotion'])  > 0)
				{
					foreach ($filtres['promotion'] AS $promotion)
					{
						?>
						<label for = "promotionFilterId<?php echo $promotion['id']; ?>">
							<input type = "radio" value = "<?php echo $promotion['id']; ?>" id = "promotionFilterId<?php echo $promotion['id']; ?>" name = "FILTER[promotion]"  <?php if (isset($_GET['FILTER']['promotion']) && $_GET['FILTER']['promotion'] == $promotion['id']) { echo 'checked'; } ?>/>
							<?php echo $promotion['nom'].' ('.$promotion['nb'].')'; ?>
						</label>
						<?php
					}
				}
				
				/*
					Certificat
				*/
					?>
					<span class = "evalccpcFiltreCategorie"><?php echo LANG_FORM_CCPC_FILTER_CERTIFICAT_TITLE; ?></span>
						<label for = "certificatFilterId">
							<input type = "radio" value = "" id = "certificatFilterId" name = "FILTER[certificat]" <?php if (!isset($_GET['FILTER']['certificat']) || $_GET['FILTER']['certificat'] == '') { echo 'checked'; } ?> />
							<?php echo LANG_FORM_CCPC_FILTER_CERTIFICAT_OPTION_ALL; ?>
						</label>
					<?php
				if (isset($filtres['certificat']) && count ($filtres['certificat'])  > 0)
				{ 
					foreach ($filtres['certificat'] AS $certificat)
					{
						?>
						<label for = "certificatFilterId<?php echo $certificat['id']; ?>">
							<input type = "radio" value = "<?php echo $certificat['id']; ?>" id = "certificatFilterId<?php echo $certificat['id']; ?>" name = "FILTER[certificat]" <?php if (isset($_GET['FILTER']['certificat']) && $_GET['FILTER']['certificat'] == $certificat['id']) { echo 'checked'; } ?> />
							<?php echo $certificat['nom'].' ('.$certificat['nb'].')'; ?>
						</label>
						<?php
					}
				}								

				/*
					Hopitaux
				*/
					?>
					<span class = "evalccpcFiltreCategorie"><?php echo LANG_FORM_CCPC_FILTER_HOPITAL_TITLE; ?></span>
						<label for = "hopitalFilterId">
							<input type = "radio" value = "" id = "hopitalFilterId" name = "FILTER[hopital]" <?php if (!isset($_GET['FILTER']['hopital']) || $_GET['FILTER']['hopital'] == '') { echo 'checked'; } ?> />
							<?php echo LANG_FORM_CCPC_FILTER_HOPITAL_OPTION_ALL; ?>
						</label>
					<?php
				if (isset($filtres['hopital']) && count ($filtres['hopital'])  > 0)
				{ 
					foreach ($filtres['hopital'] AS $hopital)
					{
						?>
						<label for = "hopitalFilterId<?php echo $hopital['id']; ?>">
							<input type = "radio" value = "<?php echo $hopital['id']; ?>" id = "hopitalFilterId<?php echo $hopital['id']; ?>" name = "FILTER[hopital]" <?php if (isset($_GET['FILTER']['hopital']) && $_GET['FILTER']['hopital'] == $hopital['id']) { echo 'checked'; } ?> />
							<?php echo $hopital['nom'].' ('.$hopital['nb'].')'; ?>
						</label>
						<?php
					}
				}								

			?>
				</div>
				<div id = "evalccpcFiltresFormValidate">
					<input type = "submit" value = "Valider" />
				</div>
			</form>
		<div class = "mobileCCPCMenuButtonClose"><i class="fa fa-caret-up"></i></div>
	</div>

	<!-- Bouton permettant d'afficher le menu sur les portables -->
	<div class = "mobileCCPCMenuButton"><i class="fa fa-caret-down"></i></div>
		<div id = "evalccpcContent">
			<?php
				if (isset($evaluationData) && count($evaluationData) > 0)
				{
					?>
						<table>
							<tr class = "headTR">
								<th><?php echo LANG_FORM_CCPC_LISTSERVICE_SERVICE_TITLE; ?></th>
								<th><?php echo LANG_FORM_CCPC_LISTSERVICE_NBEVAL_TITLE; ?></th>
								<?php
									/**
										Affichage des moyennes
									**/
									
										$listeMoyenne = array();
										
										if  ($form = simplexml_load_file(PLUGIN_PATH.'formulaire.xml'))
										{
											foreach ($form -> categorie AS $categorie)
											{
												foreach ($categorie -> input AS $input)
												{
													if ($input['type'] == 'select')
													{
														$listeMoyenne[] = (string) $categorie['nom'];
														?>
														<th><?php echo constant((string) $categorie['legend']); ?> </th>
														<?php
														break;
													}
												}
											}
										}
								?>
								<th></th> <!-- Bouton pour charger le service -->
							</tr>
							<?php
								foreach ($evaluationData AS $evaluation)
								{
									if (isset($evaluation['service']) && count($evaluation['service']) > 0)
									{
										$currentLien = ROOT.CURRENT_FILE.'?'.$urlPage.'&service='.$evaluation['service']['id'];
										if (!isset($_GET['FILTER']['date']['min']) && !isset($_GET['FILTER']['date']['max'])) { $currentLien .= '&'.http_build_query(array('FILTER' => array ('date' => $evaluation['service']['date']))); }
										?>
									<tr class = "bodyTR" data-lien = "<?php echo $currentLien; ?>">
										<td class = "<?php if ($evaluation['service']['hide'] == 1) { echo 'tableHiddenService'; } ?>"><?php echo $evaluation['service']['FullName']; ?></td>
										<td style = "text-align: center;"><?php echo $evaluation['nb']; ?></td>
										<?php
											// Affichage des moyennes
											foreach ($listeMoyenne AS $moyenne)
											{
												?>
													<td style = "text-align: center;"><?php echo ($evaluation[$moyenne]['moyenne']+5).'/10'; makeHorizontalBar(100, 30, -5, 'red', 5, 'green', $evaluation[$moyenne]['moyenne']); ?></td>
												<?php
											}
										?>
										<td><a href = "<?php echo $currentLien; ?>"><i class = "fa fa-bar-chart"></i></a></td> <!-- Bouton pour charger le service -->
									</tr>
										<?php
									}
								}
							?>
						</table>
					<?php
					
					/**
						Affichage de la pagination
					**/
					
					?>
						<div id = "pagination">
							<?php
								foreach ($pagination AS $page => $pageValue)
								{
									?>
										<a href = "<?php echo ROOT.CURRENT_FILE.'?'.$urlPage.'&page='.$page; ?>"><span class = "pageBouton <?php if ($page == $pageActuelle) { echo 'pageActuelle'; } ?>"><?php echo $page; ?></span></a>
									<?php
								}
							?>
						</div>
					<?php
				}
				else
				{
					?>
						<div class = "erreur">
							<?php echo LANG_FORM_CCPC_LISTE_SERVICE_NOSERVICEFOUND; ?>
						</div>
					<?php
				}
			?>
				<!--  Boutons d'export -->
				<div style = "font-size: 2em; float: right;">
					<div id = "downloadCSV"><a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($_GET).'&download=csv'; ?>" target = "_blank"><i class="fa fa-file-excel-o" style = "color: green;"></i></a></div>
				</div>
				
				<!--  Page d'administration -->
				<div style = "font-size: 2em; float: left;">
				<?php
					if ($_SESSION['rang'] >= 3)
					{
				?>
					<div id = "administration" style = "display: inline-block;"><a href = "<?php echo ROOT.CURRENT_FILE.'?evaluationType='.$evaluationTypeData['id'].'&page=admin'; ?>"><i class="fa fa-cogs"></i></a></div>
					<div id = "administrationMail" style = "display: inline-block;"><a href = "<?php echo ROOT.CURRENT_FILE.'?evaluationType='.$evaluationTypeData['id'].'&page=adminMail'; ?>"><i class="fa fa-envelope"></i></a></div>
				<?php
					}
				?>
				</div>
		</div>
		
<script>
	// URL où on ajoute les variable
	var fastSelectURI = "<?php echo ROOT.CURRENT_FILE.'?evaluationType='.$_GET['evaluationType'].'&'; ?>";
	
	// On stock l'array contenant les filtres rapides
	var fastSelectData = <?php echo json_encode($fastSelectData); ?>;
	
	$('select#fastSelectFilter').on('change', function(){
		if ($('select#fastSelectFilter option:selected').val() != '')
		{
			var filtreId = $('select#fastSelectFilter option:selected').val();
			var selectedFastFiltre = fastSelectData[$('select#fastSelectFilter option:selected').val()];
			
			document.location.href = fastSelectURI+'FILTER[promotion]='+selectedFastFiltre['promotion']['id']+'&FILTER[date][min]='+selectedFastFiltre['dateDebut']+'&FILTER[date][max]='+selectedFastFiltre['dateFin'];
		}
	});
</script>