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

?>

<div id = "evalccpcContentUnique">
<?php
	if (isset($evaluationData) && count($evaluationData) > 0)
	{
		
	/**
		On charge le fichier XML du formulaire
	**/
	
	if (is_file(PLUGIN_PATH.'formulaire.xml'))
	{
		$form = simplexml_load_file(PLUGIN_PATH.'formulaire.xml');
	}

	/**
		1. Données nécessaires à la génération des graphiques
	**/

	if ($evaluationContentType == 'history')
	{

		/**
			On crée un array allant des catégories aux dates (actuellement : va des dates aux catégories)
		**/
		$evaluationDataHistory = array();
		
		foreach ($evaluationData['evaluations'] AS $evaluation)
		{
			if (isset($evaluation['stat']) && count($evaluation['stat']) > 0)
			{
				foreach ($evaluation['stat'] AS $dataName => $data)
				{
					if (isset($form -> xpath('categorie[@nom="'.$dataName.'"]')[0]))
					{
						$evaluationDataHistory[$dataName][$evaluation['date']['MoisNb'].$evaluation['date']['Annee']]['date'] = $evaluation['date'];
						$evaluationDataHistory[$dataName][$evaluation['date']['MoisNb'].$evaluation['date']['Annee']]['moyenne'] = $data['moyenne'];
					}
				}
			}
		}		
	}
		
		/**
			2. Affichage des données concernant le service
		**/
		
		$checkBoxList = array(); // Contient la liste des checkbox déjà crée
		$textList = array(); // Liste des textBox déjà crée
		
		/**
			2.1 Donnée générales
		**/

				/*
					Nom du service
				*/
				
				?>
				<div id = "evalccpcContentUniqueNom">
					<?php
					echo $evaluationData['service']['FullName'];
					?>
				</div>
				<?php
				
				/*
					Bouton retour
				*/
				
				// Création de l'URL
				$tempGET = $_GET;
				unset($tempGET['service']);
				unset($tempGET['evaluationContentType']);
				unset($tempGET['hideService']);
				$urlPage = http_build_query($tempGET);
				?>
				<a href = "<?php echo ROOT.CURRENT_FILE.'?'.$urlPage; ?>">
				<div id = "evalccpcContentUniqueRetour">
					<i class="fa fa-angle-left"></i>
				</div>
				</a>
				
				<?php
				/*
					Formulaire de tri des données
				*/
				
				if (count($filtres) > 0) 
				{
				?>
				<div id = "evalccpcFiltresContent">
				<form method = "GET" action = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($_GET); ?>">
					<input type = "hidden" name = "service" value = '<?php echo $_GET['service']; ?>' />
					<input type = "hidden" name = "evaluationType" value = '<?php echo $_GET['evaluationType']; ?>' />
					<input type = "hidden" name = "evaluationContentType" value = '<?php if (isset($_GET['evaluationContentType'])) { echo $_GET['evaluationContentType']; } ?>' />
					<?php
					if (isset($_GET['FILTER']['service']) && count($_GET['FILTER']['service']) > 0)
					{
						foreach ($_GET['FILTER']['service'] AS $serviceId => $serviceValue)
						{
							?>
								<input type = "hidden" name = "FILTER[service][<?php echo $serviceId; ?>]" value = '<?php echo $serviceValue; ?>' />
							<?php
						}
					}
					
					if (isset($_GET['FILTER']['certificat']))
					{
						?>
						<input type = "hidden" name = "FILTER[certificat]" value = '<?php echo $_GET['FILTER']['certificat']; ?>' />
						<?php
					}
					
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
						Promotion
					*/
					if (isset($filtres['promotion']) && count ($filtres['promotion'])  > 0)
					{
						?>
						<div class = "evalccpcFiltresContentItem">
						<span class = "evalccpcFiltreCategorie"><?php echo LANG_FORM_CCPC_FILTER_PROMOTION_TITLE; ?></span>
						<select name = "FILTER[promotion]">
							<option value = ""><?php echo LANG_FORM_CCPC_FILTER_PROMOTION_OPTION_ALL; ?></option>
						<?php
						foreach ($filtres['promotion'] AS $promotionId => $promotionNom)
						{
							?>
							<option value = "<?php echo $promotionId; ?>" <?php if (isset($_GET['FILTER']['promotion']) && $promotionId == $_GET['FILTER']['promotion']) { echo 'selected'; } ?>><?php echo $promotionNom; ?></option>
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
					
					?>
						<div class = "evalccpcFiltresContentItem">		
						<span class = "evalccpcFiltreCategorie"><?php echo LANG_FORM_CCPC_FILTER_DATE_TITLE; ?></span>
						
						<input type = "hidden" class = "dateRangeSelector" data-DateType = "min" name = "FILTER[date][min]" value = "" />
						<input type = "hidden" class = "dateRangeSelector" data-DateType = "max" name = "FILTER[date][max]" value = "" />
						<input type = "button" id = "dateRangeSelector" data-dateMin = "<?php echo $filtres['dateMin']; ?>" data-dateMax = "<?php echo $filtres['dateMax']; ?>" value = "<?php echo date('d/m/Y',$dateDebut).' - '.date('d/m/Y', $dateFin); ?>" />						
						</div>
					
					<input type = "submit" value = "Valider" />
				</form>
				</div>
				<?php
				}
				
				/**
					Menu
				**/
				
				?>
				<div class = "barreNavigation">
					<?php
						$tempGET = $_GET;
						unset($tempGET['evaluationContentType']);
						unset($tempGET['hideService']);
					?>
					<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&evaluationContentType=stat'; ?>"><i class="fa fa-bar-chart barreNavigationBouton"></i></a>
					<?php
						if ((isset($evaluationData['service']['nbDate']) && $evaluationData['service']['nbDate'] > 1) || $evaluationContentType == 'history')
						{
							?>
								<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&evaluationContentType=history'; ?>"><i class="fa fa-history barreNavigationBouton"></i></a>
							<?php
						}
					?>
					<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&evaluationContentType=data'; ?>"><i class="fa fa-database barreNavigationBouton"></i></a>
				</div>
				<?php
			
				/**
					Informations concernant le service
				**/
				
				/*
					Statistiques
				*/
				
				if ($evaluationContentType == 'stat')
				{
					/**
						Notes du service, nombre d'évaluations
					**/
					
					?>
					
					<div class = "catEvaluationData" style = "padding: 5px;">
						<?php	
						// Date des données d'évaluation
						echo '<div style = "padding-bottom: 5px;"><b>'.date('d/m/Y', $evaluationData['service']['date']['min']).' - '.date('d/m/Y', $evaluationData['service']['date']['max']).'</b></div>';
						
						// Nombre d'évaluations
						echo '<div><b>'.LANG_FORM_CCPC_LISTSERVICE_NBEVAL_TITLE.' : </b>'.$evaluationData['nb'].'</div>';
						
						// Notes du service
						if  ($form = simplexml_load_file(PLUGIN_PATH.'formulaire.xml'))
						{
							foreach ($form -> categorie AS $categorie)
							{
								foreach ($categorie -> input AS $input)
								{
									if ($input['type'] == 'select')
									{
										$moyenne = (string) $categorie['nom'];
										if (isset($evaluationData[$moyenne]))
										{
											?>
											<div style = "display: inline-block; vertical-align: bottom; padding: 10px; font-weight: bold;">
												<?php
												echo constant((string) $categorie['legend']).' : '.($evaluationData[$moyenne]['moyenne']+5).'/10';
												?>
												<?php makeHorizontalBar(100, 30, -5, 'red', 5, 'green', $evaluationData[$moyenne]['moyenne']); ?>
											</div>
											<?php
										}
										break;
									}
								}
							}
						}
						?>
					</div>
					
					<?php

					/**
						Bouton de téléchargement
					**/
					
					if (isset($_GET['download']))
					{
						if ($_GET['download'] == 'csv')
						{
							downloadFILE(generateCSV($evaluationData, TRUE)['csvPath'], $evaluationData['service']['FullName'].'.csv');
						}
						else if ($_GET['download'] == 'pdf')
						{
							downloadFILE(generatePDF($evaluationData, TRUE, TRUE)['pdfPath'], $evaluationData['service']['FullName'].'.pdf');							
						}
					}
					
					/**
						Masquage du service
					**/
					
					if (isset($_GET['hideService']) && $_SESSION['rang'] >= 3)
					{
						unset($_GET['hideService']);
						
						if ($evaluationData['service']['hide'] == 1) { $setHideValue = 0; }
						else { $setHideValue = 1; }
						
						$sql = 'UPDATE eval_ccpc_resultats SET hide = '.$setHideValue.' WHERE service = ?';
						$res = $db -> prepare($sql);
						if ($res -> execute(array($evaluationData['service']['id'])))
						{
							$evaluationData['service']['hide'] = $setHideValue;							
						}
					}					
					?>
					
					<?php
					if ($_SESSION['rang'] >= 3) {?>
					<div id = "hideService">
						<div><a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($_GET).'&hideService'; ?>"><i class="fa fa-ban barreNavigationBouton <?php if ($evaluationData['service']['hide'] == 1) { echo 'serviceHidden'; } ?>"></i></a></div>
					</div>
					<?php } ?>
					
					<div id = "download">
						<div id = "downloadCSV"><a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($_GET).'&download=csv'; ?>" target = "_blank"><i class="fa fa-file-excel-o" style = "color: green;"></i></a></div>
						<div id = "downloadPDF"><a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($_GET).'&download=pdf'; ?>" target = "_blank"><i class="fa fa-file-pdf-o"  style = "color: rgb(166, 0, 0);"></i></a></div>
					</div>
					
					<div style = "width: 100%; text-align: center;">
					<?php
						
					?>
					</div>

					<?php
					if (isset($evaluationData['service']) && count($evaluationData['service']) > 0)
					{
					foreach ($evaluationData AS $dataName => $data)
					{
					
						/**
							On crée la div de catégorie
						**/
						
						if (count($data) > 0 && isset($form -> xpath('categorie[@nom="'.$dataName.'"]')[0]) && $categorie = $form -> xpath('categorie[@nom="'.$dataName.'"]')[0])
						{
							?>
							<div class = "catEvaluationData">
								<h1><?php echo constant($categorie['legend']); ?></h1>
								<?php
								foreach($data AS $evaluationName => $evaluation)
								{
									$tempData = array();
									
									if (count($evaluation) > 0)
									{
										// Tout ce qui n'est pas une checkbox ou un text
										if (isset($categorie -> xpath('input[@nomBDD="'.$evaluationName.'"]')[0]) && $input = $categorie -> xpath('input[@nomBDD="'.$evaluationName.'"]')[0])
										{
											$inputName = (string) $input['type'];
											if ($inputName == 'select')
											{
												$tempData = array();

												// Création de l'array permettant la génération du graphique
												
													// Ajout des valeurs remplis
													foreach ($input -> option AS $key => $value)
													{
														if (isset($evaluation['nb'][(string) $value['value']]))
														{
															$tempData['data'][constant((string) $value['text'])] = $evaluation['nb'][(string) $value['value']];
														}
														else
														{
															$tempData['data'][constant((string) $value['text'])] = 0;
														}
													}
												$tempData['settings'] = array('height' => 220, 'width' => 300);
												?>
												<div class = "EvaluationData">
													<h2><?php echo constant($input['label'].'_SHORT'); ?></h2>
													<img src = "<?php echo eval_ccpc_genGraphPie($tempData); ?>" />
												</div>
												<?php
											}
											else if ($inputName == 'radio')
											{
												$tempData = array();

												// Création de l'array permettant la génération du graphique
												$tempData['settings'] = array('height' => 210, 'width' => 300);
												
												?>
												<div class = "EvaluationData">
													<?php
														if (isset($input['label']))
														{
													?>
														<h2><?php echo constant($input['label'].'_SHORT'); ?></h2>
													<?php
														}
													?>											
													<div class = "listItem">
													<?php
														foreach ($input -> radio AS $value)
														{
															// On remplit $tempData
															$tempData['data'][constant((string) $value['text'].'_SHORT')] = $evaluationData[$dataName][$evaluationName]['nb'][(string) $value['value']];
														
															/*
																Couleur de chaque selon la fréquence
															*/

															if (isset($evaluationData[$dataName][$evaluationName]['nbTotal']) && $evaluationData[$dataName][$evaluationName]['nbTotal'] > 0)
															{
																$valuePercentage = round(100*$evaluationData[$dataName][$evaluationName]['nb'][(string) $value['value']]/$evaluationData[$dataName][$evaluationName]['nbTotal'],1);
															}
															else
															{
																$valuePercentage = 0;
															}
															$cssValue = getCssPercentageValue($valuePercentage);
															
															if (isset($cssValue) && $cssValue != '')
															{
															?>
															<span class = "<?php echo  $cssValue; ?>"><?php echo constant($value['text']); ?></span>
															<?php
															}
														}
														?>
													</div>
													<img src = "<?php echo eval_ccpc_genGraphSimpleBar($tempData); ?>" />
												</div>
												<?php
											}
											// Pour les textarea
											else if ($inputName == 'textarea')
											{
												$tempGET = $_GET;
												$tempGET['evaluationContentType'] = 'data';
												
												// Création de l'array contenant les données à afficher sous forme [timestamp fin][timestamp début][timestamp commantaire][idCommentaire] => commentaire
												$tempData = array();
												foreach ($evaluationData[$dataName][$evaluationName] AS $commentId => $commentData)
												{
													if (isset($evaluationData['donnees'][$commentId]['infos']))
													{
														$tempData[$evaluationData['donnees'][$commentId]['infos']['dateFin']][$evaluationData['donnees'][$commentId]['infos']['dateDebut']][$evaluationData['donnees'][$commentId]['infos']['date']][$commentId] = $commentData;
													}
												}
												?>
												<div class = "EvaluationData EvaluationDataText EvaluationDataTextFullWidth">
													
													<h2>
													<!-- Bouton de plein écran pour les mobiles --><i class="fa fa-arrows-alt mobileCommentFullscreenButton"></i>
													<?php echo constant($input['label'].'_SHORT'); ?>
													</h2>
													
													<?php
														// On affiche les commentaires, pas ordre chronologique décroissant
														krsort($tempData);
														foreach ($tempData AS $dateFin => $value)
														{
															krsort($value);
															foreach ($value AS $dateDebut => $value2)
															{
																?>
																<div class = "EvaluationDataTextPediod">
																	<span><?php echo date('d/m/y', $dateDebut).' - '.date('d/m/y', $dateFin); ?></span>
																	<?php
																		krsort($value2);
																		foreach ($value2 AS $key => $value3)
																		{
																			foreach ($value3 AS $commentId => $commentData)
																			{
																			?>
																				<div class = "EvaluationDataTextDiv <?php if (isset($evaluationData['donnees'][$commentId]['Moderation'][(string) $input['nomBDD']])) { echo 'moderateText'; } ?>" data-nomBDD = "<?php echo (string) $input['nomBDD']; ?>" data-evaluationId = "<?php echo $commentId; ?>"><?php if ($_SESSION['rang'] >= 3) { ?><i class="modereComment fa fa-ban barreNavigationBouton"></i><?php } ?><span><?php echo $commentData; ?></span></div>
																				<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&id='.$commentId; ?>"><?php echo '#'.$commentId; ?></a> - <?php echo date('d/m/Y',$evaluationData['donnees'][$commentId]['infos']['date']); ?>
																			<?php
																			}
																		}
																	?>
																</div>
																<?php
															}
														}
													?>
												</div>
												<?php
											}
										}
										// Pour les checkbox
										if (isset($categorie -> xpath('input/checkbox[@nomBDD="'.$evaluationName.'"]/..')[0]) && $input = $categorie -> xpath('input/checkbox[@nomBDD="'.$evaluationName.'"]/..')[0])
										{
											$inputName = (string) $input['name'];
											
											// On vérifie qu'on a pas déjà traité cet input
											if (!in_array($inputName, $checkBoxList))
											{
												$checkBoxList[] = (string) $input['name'];

												// Création de l'array permettant la génération du graphique
												$tempData = array();
												$tempData['settings'] = array('height' => 210, 'width' => 350);
												$tempData['option'] = array();
												
												?>
												<div class = "EvaluationData">
													<?php
														if (isset($input['label']))
														{
													?>
														<h2><?php echo constant($input['label'].'_SHORT'); ?></h2>
													<?php
														}
													?>		
													<div class = "listItem">
													<?php
														foreach ($input -> checkbox AS $value)
														{
															/*
																On remplit $tempData
															*/
															
															krsort($evaluationData[$dataName][(string) $value['nomBDD']]['nb']); // Pour avoir les valeur dans l'ordre décroissant (oui puis non)
															foreach ($evaluationData[$dataName][(string) $value['nomBDD']]['nb'] AS $key2 => $value2)
															{
																if (!in_array(constant('LANG_FORM_CCPC_QUESTION_ITEM_'.$key2), $tempData['option']))
																{
																	$tempData['option'][] = constant('LANG_FORM_CCPC_QUESTION_ITEM_'.$key2);
																}
																
																$tempData['data'] [constant($value['text'].'_SHORT')][constant('LANG_FORM_CCPC_QUESTION_ITEM_'.$key2)] = $value2;
															}
														
															/*
																Couleur de chaque selon la fréquence
															*/

															if (isset($evaluationData[$dataName][$evaluationName]))
															{
																if (isset($evaluationData[$dataName][(string) $value['nomBDD']]['nb'][1]) && is_numeric($evaluationData[$dataName][(string) $value['nomBDD']]['nb'][1]) && $evaluationData[$dataName][(string) $value['nomBDD']]['nbTotal'] > 0)
																{
																	$valuePercentage = round(100*$evaluationData[$dataName][(string) $value['nomBDD']]['nb'][1]/$evaluationData[$dataName][(string) $value['nomBDD']]['nbTotal'],1);
																}
																else
																{
																	$valuePercentage = 0;
																}
															}
															
															$cssValue = getCssPercentageValue($valuePercentage);
															if (isset($cssValue) && $cssValue != '')
															{
															?>
															<span class = "<?php echo  $cssValue; ?>"><?php echo constant($value['text']); ?></span>
															<?php
															}
														}
													?>
													</div>
													<img src = "<?php echo eval_ccpc_genGraphBar($tempData); ?>" />
												</div>
												<?php
											}
										}
										// Pour les text
										if (isset($categorie -> xpath('input/text[@nomBDD="'.$evaluationName.'"]/..')[0]) && $input = $categorie -> xpath('input/text[@nomBDD="'.$evaluationName.'"]/..')[0])
										{
											$tempGET = $_GET;
											$tempGET['evaluationContentType'] = 'data';
											
											$inputName = (string) $input['name'];
											// On vérifie qu'on a pas déjà traité cet input
											if (!in_array($inputName, $textList))
											{
												$textList[] = (string) $input['name'];
											?>
											
												<?php
												// Création de l'array contenant les données à afficher sous forme [timestamp fin][timestamp début][timestamp commantaire][idMessage][] => message
												$tempData = array();
												foreach ($input -> text AS $value)
												{
													if (isset($evaluationData[$dataName][(string) $value['nomBDD']])) {
														foreach ($evaluationData[$dataName][(string) $value['nomBDD']] AS $idEval => $textValue)
														{
															if (isset($evaluationData['donnees'][$idEval]['infos']))
															{
																$tempData[$evaluationData['donnees'][$idEval]['infos']['dateFin']][$evaluationData['donnees'][$idEval]['infos']['dateDebut']][$evaluationData['donnees'][$idEval]['infos']['date']][$idEval][] = $textValue;
															}
														}
													}
												}
												?>
												<div class = "EvaluationData EvaluationDataText">
													<h2>
														<!-- Bouton de plein écran pour les mobiles --><i class="fa fa-arrows-alt mobileCommentFullscreenButton"></i>
														<?php echo constant($input['label'].'_SHORT'); ?>
													</h2>
													<?php
														// On affiche les commentaires, pas ordre chronologique décroissant
														krsort($tempData);
														foreach ($tempData AS $dateFin => $tempvalue)
														{
															krsort($tempvalue);
															foreach ($tempvalue AS $dateDebut => $value2)
															{
																?>
																<div class = "EvaluationDataTextPediod">
																	<span><?php echo date('d/m/y', $dateDebut).' - '.date('d/m/y', $dateFin); ?></span>
																	<?php
																		krsort($value2);
																		foreach ($value2 AS $key => $value3)
																		{
																			foreach ($value3 AS $commentId => $comments)
																			{
																				?>
																				<div class = "EvaluationDataTextPediodStudent">
																				<?php
																				foreach ($comments AS $comment)
																				{
																			?>
																				<div class = "EvaluationDataTextDiv <?php if (isset($evaluationData['donnees'][$idEval]['Moderation'][(string) $value['nomBDD']])) { echo 'moderateText'; } ?>" data-nomBDD = "<?php echo (string) $value['nomBDD']; ?>" data-evaluationId = "<?php echo $idEval; ?>"><?php if ($_SESSION['rang'] >= 3) { ?><i class="modereComment fa fa-ban barreNavigationBouton"></i><?php } ?><span><?php echo $comment; ?></span> </div>
																			<?php
																				}
																				?>
																				</div>
																				<a href = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET).'&id='.$idEval; ?>"><?php echo '#'.$idEval; ?></a> - <?php echo date('d/m/Y',$evaluationData['donnees'][$idEval]['infos']['date']); ?>
																				<?php
																			}
																		}
																	?>
																</div>
																<?php
															}
														}
													?>
												</div>
												<?php
											}
										}
									}
								}
								?>
							</div>
							<?php
						}
					}
					}
					else
					{
						// Si aucune évaluation trouvé
						?>
							<div class = "erreur">
								<?php echo LANG_FORM_CCPC_LISTE_SERVICE_NOEVALPERIOD; ?>
							</div>
						<?php
					}
				}
				else if ($evaluationContentType == 'history')
				{
					foreach ($evaluationDataHistory AS $evaluationDataHistoryCategorie => $evaluationDataHistoryData)
					{
						/**
							On crée l'array contenant les données de graphique
						**/
						
						$tempData = array();
						$tempData['option'] = array();
						$tempData['settings'] = array('height' => 200, 'width' => 600, 'min' => 0, 'max' => 11); // 11 pour laisser une marge à l'affichage graphique
						
						foreach ($evaluationDataHistoryData AS $value)
						{
							$tempData['option'][] = $value['date']['Mois'].' '.$value['date']['Annee'];
							$tempData['data']['Evolution'][$value['date']['Mois'].' '.$value['date']['Annee']] = $value['moyenne']+5;
						}
												
						/**
							On crée la div de catégorie
						**/
						
						if (count($data) > 0 && isset($form -> xpath('categorie[@nom="'.$evaluationDataHistoryCategorie.'"]')[0]) && $categorie = $form -> xpath('categorie[@nom="'.$evaluationDataHistoryCategorie.'"]')[0])
						{
							?>
							<div class = "catEvaluationData">
								<h1><?php echo constant($categorie['legend']); ?></h1>
								<div class = "EvaluationData EvaluationDataHistory" style = "display: block; width: 90%; margin: auto;">
									<img src = "<?php echo eval_ccpc_genGraphLine($tempData); ?>" />
								</div>
							</div>
						<?php
						}
					}
					if (!isset($evaluationData['service']) || count($evaluationData['service']) == 0)
					{
						// Si aucune évaluation trouvé
						?>
							<div class = "erreur">
								<?php echo LANG_FORM_CCPC_LISTE_SERVICE_NOEVALPERIOD; ?>
							</div>
						<?php
					}
				}
				else if ($evaluationContentType == 'data')
				{
					?>
					<div class = "catEvaluationData" style = "background-color: inherit; box-shadow: inherit;">
						<select style = "width: 100%">
							<option value = ""><?php echo LANG_FORM_CCPC_EVALUATIONSDATA_SELECT_ALL; ?></option>
							<?php
								$evaluationList = array(); // liste plus légère qu'on pourra parcourir lors de la génération des informations
							
								foreach ($evaluationData['donnees'] AS $evaluationId => $evaluation)
								{
									$evaluationList[] = $evaluationId;
									?>
										<option value = "<?php echo $evaluationId; ?>" <?php if (isset($_GET['id']) && $_GET['id'] == $evaluationId) { echo 'selected'; } ?>><?php echo LANG_FORM_CCPC_EVALUATION_NAME; ?> #<?php echo $evaluationId; ?> - <?php echo $evaluation['infos']['promotion']['nom']; ?> - <?php echo date('d/m/Y', $evaluation['infos']['date']); ?></option>
									<?php
								}
							?>
						</select>
						
						<?php
							/**
								Affichage des données
							**/
						?>
							<!-- On crée la structure du formulaire -->
							<div id = "evaluationDataDisplayUserReponse">
							<?php
							foreach ($form -> categorie AS $categorie)
							{
								/**
									On crée la catégorie
								**/
								?>
								
								<div class = "catEvaluationData">
								<h1><?php if (isset($categorie['legend'])) { echo constant($categorie['legend']); } ?></h1>
									<?php
										// On crée les input de la catégorie
										foreach($categorie -> input AS $input)
										{
											/**
												On crée l'input du formulaire
											**/
											
											if (isset($input['label']))
											{
												/*
													On affiche le label
												*/
												?>
												<label class = "titleLabel" for "<?php echo $input['name']; ?>">
													<?php echo constant($input['label']); ?>
												</label>
												<?php
											}
											
											if ($input['type'] == 'select')
											{
												?>
												<select id = "<?php echo $input['name']; ?>" name = "<?php echo $input['name']; ?>" disabled>
													<?php
														foreach($evaluationList AS $evaluationId)
														{
															foreach($input -> option AS $option)
															{
																if (isset($evaluationData['donnees'][$evaluationId][(string) $categorie['nom']][(string) $input['nomBDD']]) && $evaluationData['donnees'][$evaluationId][(string) $categorie['nom']][(string) $input['nomBDD']] == $option['value'])
																{
																?>
																<option class = "evaluationDataDisplay" data-evaluationId = "<?php echo $evaluationId; ?>" value = "<?php echo $option['value']; ?>" selected>
																	<?php echo constant($option['text']); ?>
																</option>
																<?php
																}
															}
														}
													?>
												</select>
												<?php
											}
											else if ($input['type'] == 'checkbox')
											{
												foreach($evaluationList AS $evaluationId)
												{
													foreach($input -> checkbox AS $checkbox)
													{
														if (isset($evaluationData['donnees'][$evaluationId][(string) $categorie['nom']][(string) $checkbox['nomBDD']]) && $evaluationData['donnees'][$evaluationId][(string) $categorie['nom']][(string) $checkbox['nomBDD']] == 1)
														{
															$checked = true;
														}
														else
														{
															$checked = false;
														}
													?>
													<div class = "evaluationDataDisplay <?php if ($checked) { echo 'evaluationDataDisplaySelected'; } ?>" data-evaluationId = "<?php echo $evaluationId; ?>">
														<label for = "<?php echo $input['name'].'_'.$checkbox['value']; ?>"><input type="checkbox" name="<?php echo $input['name'].'[]'; ?>" id = "<?php echo $input['name'].'_'.$checkbox['value']; ?>" value="<?php echo $checkbox['value']; ?>" <?php if ($checked) { echo 'checked'; } ?> disabled>
															<?php echo constant($checkbox['text']); ?>
														</label>
													</div>
													<?php
													}
												}
											}
											else if ($input['type'] == 'radio')
											{
												foreach($evaluationList AS $evaluationId)
												{
													foreach($input -> radio AS $radio)
													{
														if (isset($evaluationData['donnees'][$evaluationId][(string) $categorie['nom']][(string) $input['nomBDD']]) && $evaluationData['donnees'][$evaluationId][(string) $categorie['nom']][(string) $input['nomBDD']] == $radio['value'])
														{
															$checked = true;
														}
														else
														{
															$checked = false;
														}
													?>
													<div class = "evaluationDataDisplay <?php if ($checked) { echo 'evaluationDataDisplaySelected'; } ?>" data-evaluationId = "<?php echo $evaluationId; ?>">
													<label for = "<?php echo $input['name'].'_'.$radio['value']; ?>"><input type="checkbox" name="<?php echo $input['name']; ?>" id = "<?php echo $input['name'].'_'.$radio['value']; ?>" value="<?php echo $radio['value']; ?>" <?php if ($checked) { echo 'checked'; } ?> disabled>
														<?php echo constant($radio['text']); ?>
													</label>
													</div>
													<?php
													}
												}
											}
											else if ($input['type'] == 'text')
											{ 
												foreach($evaluationList AS $evaluationId)
												{
													foreach($input -> text AS $text)
													{
														if (isset($evaluationData['donnees'][$evaluationId][(string) $categorie['nom']][(string) $text['nomBDD']]))
														{
														?>
														<div class = "evaluationDataDisplay evaluationDataDisplayText" data-evaluationId = "<?php echo $evaluationId; ?>">
															<?php echo $evaluationData['donnees'][$evaluationId][(string) $categorie['nom']][(string) $text['nomBDD']]; ?>
														</div>
														<?php
														}
													}
												}
											}
											else if ($input['type'] == 'textarea')
											{
												foreach($evaluationList AS $evaluationId)
												{
													if (isset($evaluationData['donnees'][$evaluationId][(string) $categorie['nom']][(string) $input['nomBDD']]) && isset($evaluationData['donnees'][$evaluationId][(string) $categorie['nom']][(string) $input['nomBDD']]) != '')
													{
													?>
														<div class = "evaluationDataDisplay evaluationDataDisplayText">
															<?php echo $evaluationData['donnees'][$evaluationId][(string) $categorie['nom']][(string) $input['nomBDD']]; ?>
														</div>
													<?php
													}
												}
											}
											?>
												<div class = "smallSeparator"></div>
											<?php
										}
									?>
								</div>
								<?php
							}
							?>
							</div>
							<!-- Fermeture du formulaire -->
					</div>
					<?php
				}
	}
?>
<script>
<?php
	// Variables nécessaires au JS
	?>
	moderateText = "<?php echo LANG_FORM_CCPC_QUESTION_TEXT_MODERATE; ?>";
	unmoderateText = "<?php echo LANG_FORM_CCPC_QUESTION_TEXT_UNMODERATE; ?>";
	ajaxURI = "<?php echo ROOT.CURRENT_FILE.'?evaluationType='.$evaluationTypeData['id'].'&ajax=ajaxContentEvaluationDisplay'; ?>";
	<?php 
	
	/**
		Charge automatique une évaluation si evaluationContentType = data et qu'une id est fournit
	**/
	
	if ($evaluationContentType == 'data' && isset($_GET['id']) && is_numeric($_GET['id']))
	{
		?>
			$(document).ready(function() { eval_ccpc_DisplayEvaluation(<?php echo $_GET['id']; ?>); });
		<?php
	}
?>

	// URL où on ajoute les variable
	<?php $tempGET = $_GET; unset($tempGET['FILTER']['promotion']); unset($tempGET['FILTER']['date']); ?>
	var fastSelectURI = "<?php echo ROOT.CURRENT_FILE.'?'.http_build_query($tempGET); ?>";
	
	// On stock l'array contenant les filtres rapides
	var fastSelectData = <?php echo json_encode($fastSelectData); ?>;
	
	$('select#fastSelectFilter').on('change', function(){
		if ($('select#fastSelectFilter option:selected').val() != '')
		{
			var filtreId = $('select#fastSelectFilter option:selected').val();
			var selectedFastFiltre = fastSelectData[$('select#fastSelectFilter option:selected').val()];
			
			document.location.href = fastSelectURI+'&FILTER[promotion]='+selectedFastFiltre['promotion']['id']+'&FILTER[date][min]='+selectedFastFiltre['dateDebut']+'&FILTER[date][max]='+selectedFastFiltre['dateFin'];
		}
	});
</script>