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
		01/07/2015 - graphGen.php
		Page chargée de la génération des graphiques via la librairie SVG Graph
	**/

	/**
	  * eval_ccpc_genGraphPie - Génère un graphique de type camenbert sous forme d'image au format PNG
	  *
	  * @category : eval_ccpc_functions
	  * @param array $data Données à partir desquelles le graphique est généré
	  * @return string URL de l'image générée
	  *
	  * @Author Ali Bellamine
	  *
	  * Structure de $data :<br>
	  * 	['data'][nom du label] => (int) Valeur liée au label<br>
	  * 	['settings']['height'] => (int) Hauteur du graphique (en px)<br>
	  * 	['settings']['width'] => (int) Largeur du graphique (en px)
	  *
	  */

	function eval_ccpc_genGraphPie ($data) {

		// On vérifie les données fournit
		if (isset($data) && isset($data['data']) && count ($data['data']) > 0)
		{
			// On récupère le hash de $data
			$hash = md5(json_encode($data));

			// Chemin du fichier			
			$filePath = PLUGIN_PATH.'cache/'.$hash.'.png';
			$filePathURI = ROOT.'evaluations/ccpc/cache/'.$hash.'.png';

			// Si le hash existe déjà : on renvoie le lien de l'image // sinon en crée le graphique
			if (is_file($filePath)) 
			{
				return $filePathURI; 
			}
			else 
			{
				// On crée l'image
				
					/* On inclut la librairie */
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pData.class.php');
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pDraw.class.php');
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pImage.class.php');
					
					/* On crée l'objet pData */
						// Préparation des données
						
							$tempDataArray = array(); // Contient les données chiffrés de chaque part du camenbert
							$tempLegendArray = array(); // Contient la légend chaque part de camenbert
							
							foreach ($data['data'] AS $tempDataLegend => $tempDataValue)
							{
								$tempLegendArray[] = $tempDataLegend;
								if (isset($tempDataValue) && is_numeric($tempDataValue)) { 
									$tempDataArray[] = $tempDataValue;
								}
								else {
									return FALSE;
								}
							}
							
						$MyData = new pData();
						$MyData->addPoints($tempDataArray, 'Values');  
						$MyData->addPoints($tempLegendArray, 'Label');  
						$MyData->setAbscissa("Label"); 

					/* On crée l'objet pChart */
					
					if (isset($data['settings']['width'])) { $width = $data['settings']['width']; } else { $width = 600; }
					if (isset($data['settings']['height'])) { $height = $data['settings']['height']; } else { $height = 300; }
					
					$myPicture = new pImage($width,$height,$MyData);				
										
					$myPicture->setFontProperties(array("FontName"=> PLUGIN_PATH.'core/pChart2.1.4/fonts/verdana.ttf',"FontSize"=>8,"R"=>223,"G"=>223,"B"=>223));
						
					 /* Set the graph area */  
					 $myPicture->setGraphArea(10,20,$width-20,$height-20); 
					 $myPicture->drawGradientArea(10,20,$width-20,$height-20,DIRECTION_HORIZONTAL,array("StartR"=>200,"StartG"=>200,"StartB"=>200,"EndR"=>255,"EndG"=>255,"EndB"=>255,"Alpha"=>30)); 

					 /* Draw the chart scale */  
					 $scaleSettings = array("RemoveXAxis" => TRUE, "AxisAlpha"=>10,"TickAlpha"=>10,"DrawXLines"=>FALSE,"Mode"=>SCALE_MODE_START0,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10,"Pos"=>SCALE_POS_TOPBOTTOM); 
					 $myPicture->drawScale($scaleSettings);  

					 /* Turn on shadow computing */  
					 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10)); 

					 /* Draw the chart */  
					 $myPicture->drawBarChartAli(array("DisplayValues"=>TRUE,"DisplayShadow"=>TRUE,"DisplayPos"=>LABEL_POS_INSIDE,"Rounded"=>TRUE,"Surrounding"=>30)); 
					$myPicture->render($filePath);

					return $filePathURI;
			}
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	  * eval_ccpc_genGraphBar - Génère un graphique de type barre sous forme d'image au format PNG
	  *
	  * @category : eval_ccpc_functions
	  * @param array $data Données à partir desquelles le graphique est généré
	  * @return string URL de l'image générée
	  *
	  * @Author Ali Bellamine
	  *
	  * Structure de $data :<br>
	  * 	['option'] => (array) Liste de label possibles<br>
	  * 	['data'][nom de la catégorie][nom du label] => (int) Valeur du label<br>
	  * 	['settings']['height'] => (int) Hauteur du graphique (en px)<br>
	  * 	['settings']['width'] => (int) Largeur du graphique (en px)
	  *
	  */

	function eval_ccpc_genGraphBar ($data) {
		// On vérifie les données fournit
		if (isset($data) && isset($data['data']) && count ($data['data']) > 0 && isset($data['option']) && count($data['option']) > 0)
		{
			// On récupère le hash de $data
			$hash = md5(json_encode($data));

			// Chemin du fichier
			$filePath = PLUGIN_PATH.'cache/'.$hash.'.png';
			$filePathURI = ROOT.'evaluations/ccpc/cache/'.$hash.'.png';

			// Si le hash existe déjà : on renvoie le lien de l'image // sinon en crée le graphique
			if (is_file($filePath)) 
			{
				return $filePathURI; 
			}
			else 
			{
				// On crée l'image
				
					/* On inclut la librairie */
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pData.class.php');
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pDraw.class.php');
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pImage.class.php');
					
					/* On crée l'objet pData */
						// Préparation des données
						
							$tempDataArray = array(); // Contient les données chiffrés
							$tempLegendArray = array(); // Contient la légende affichée en bas
							
							// On prépare $tempDataArray :
							foreach ($data['option'] AS $option)
							{
								if ($option != '') {
									$tempDataArray[$option] = array();
								}
							}

							foreach ($data['data'] AS $tempDataLegend => $tempDataValue)
							{
								$tempLegendArray[] = $tempDataLegend;
								if (is_array($tempDataValue)) {
									foreach ($tempDataArray AS $key => $value)
									{
										if (isset($tempDataValue[$key]) && is_numeric($tempDataValue[$key]))
										{
											$tempDataArray[$key][] = $tempDataValue[$key];
										}
										else
										{
											$tempDataArray[$key][] = 0;
										}
									}
								}
								else {	
									return FALSE;
								}
							}

						$MyData = new pData();
						foreach ($tempDataArray AS $key => $value)
						{
							$MyData->addPoints($value, $key); 
						}					
						$MyData->addPoints($tempLegendArray, 'Label');  
						
						$MyData->setAbscissa("Label");
						$MyData->setSerieDescription("Label","Label");
					
					/* On crée l'objet pChart */
					
					if (isset($data['settings']['width'])) { $width = $data['settings']['width']; } else { $width = 600; }
					if (isset($data['settings']['height'])) { $height = $data['settings']['height']; } else { $height = 300; }
					
					$myPicture = new pImage($width,$height-20,$MyData, TRUE);				
					$myPicture->setFontProperties(array("FontName"=> PLUGIN_PATH.'core/pChart2.1.4/fonts/MankSans.ttf',"FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));
										
					// On détermine les limites
					
					$myPicture->setGraphArea(0,0,$width,$height-60);
					$scaleSettings = array("DrawSubTicks"=>TRUE,"CycleBackground"=>FALSE, "RemoveYAxis" => TRUE);
					$myPicture->drawScale($scaleSettings);
					
					$myPicture->drawLegend(10, $height-30,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));
					
					$settings = array("Surrounding"=>-30,"InnerSurrounding"=>30, "DisplayValues"=>TRUE, "ORIENTATION_HORIZONTAL" => TRUE);
					$myPicture->drawBarChart($settings);
					$myPicture->render($filePath);

					return $filePathURI;
			}
		}
		else
		{
			return FALSE;
		}
	}

	/**
	  * eval_ccpc_genGraphSimpleBar - Génère un graphique de type barre simple sous forme d'image au format PNG
	  *
	  * @category : eval_ccpc_functions
	  * @param array $data Données à partir desquelles le graphique est généré
	  * @return string URL de l'image générée
	  *
	  * @Author Ali Bellamine
	  *
	  * Structure de $data :<br>
	  * 	['data'][nom du label] => (int) Valeur du label<br>
	  * 	['settings']['height'] => (int) Hauteur du graphique (en px)<br>
	  * 	['settings']['width'] => (int) Largeur du graphique (en px)
	  *
	  */

	function eval_ccpc_genGraphSimpleBar ($data) {
		// On vérifie les données fournit
		if (isset($data) && isset($data['data']) && count ($data['data']) > 0)
		{
			// On récupère le hash de $data
			$hash = md5(json_encode($data));

			// Chemin du fichier
			$filePath = PLUGIN_PATH.'cache/'.$hash.'.png';
			$filePathURI = ROOT.'evaluations/ccpc/cache/'.$hash.'.png';

			// Si le hash existe déjà : on renvoie le lien de l'image // sinon en crée le graphique
			if (is_file($filePath)) 
			{
				return $filePathURI; 
			}
			else 
			{
				// On crée l'image
				
					/* On inclut la librairie */
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pData.class.php');
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pDraw.class.php');
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pImage.class.php');
					
					/* On crée l'objet pData */
						// Préparation des données
						
							$tempDataArray = array(); // Contient les données chiffrés
							$tempLegendArray = array(); // Contient la légende affichée en bas
							
							// On prépare $tempDataArray :
							foreach ($data['data'] AS $tempDataLegend => $tempDataValue)
							{
								$tempLegendArray[] = $tempDataLegend;
								if (is_numeric($tempDataValue)) {
									$tempDataArray[] = $tempDataValue;
								}
								else {	
									return FALSE;
								}
							}

						$MyData = new pData();
						
						$MyData->addPoints($tempDataArray, 'Valeur'); 					
						$MyData->addPoints($tempLegendArray, 'Label');  
						
						$MyData->setAbscissa("Label");
						$MyData->setSerieDescription("Label","Label");
					
					/* On crée l'objet pChart */
					
					if (isset($data['settings']['width'])) { $width = $data['settings']['width']; } else { $width = 600; }
					if (isset($data['settings']['height'])) { $height = $data['settings']['height']; } else { $height = 300; }
					
					$myPicture = new pImage($width,$height-20,$MyData, TRUE);				
					$myPicture->setFontProperties(array("FontName"=> PLUGIN_PATH.'core/pChart2.1.4/fonts/MankSans.ttf',"FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));
										
					// On détermine les limites
					
					$myPicture->setGraphArea(0,0,$width,$height-40);
					$scaleSettings = array("DrawSubTicks"=>TRUE,"CycleBackground"=>FALSE, "RemoveYAxis" => TRUE);
					$myPicture->drawScale($scaleSettings);
										
					$settings = array("Surrounding"=>-30,"InnerSurrounding"=>30, "DisplayValues"=>TRUE, "ORIENTATION_HORIZONTAL" => TRUE, "DisplayPos"=>LABEL_POS_INSIDE);
					$myPicture->drawBarChart($settings);
					$myPicture->render($filePath);

					return $filePathURI;
			}
		}
		else
		{
			return FALSE;
		}
	}	
	
	/**
	  * eval_ccpc_genGraphLine - Génère un graphique de type Line simple sous forme d'image au format PNG
	  *
	  * @category : eval_ccpc_functions
	  * @param array $data Données à partir desquelles le graphique est généré
	  * @return string URL de l'image générée
	  *
	  * @Author Ali Bellamine
	  *
	  * Structure de $data :<br>
	  * 	['option'] => (array) Liste des labels disponibles<br>
	  * 	['data'][nom de la catégorie][nom du label] => (int) Valeur du label<br>
	  * 	['settings']['min'] => (int) Valeur (ordonnée) minimale<br>
	  * 	['settings']['max'] => (int) Valeur (ordonnée) maximale<br>
	  * 	['settings']['height'] => (int) Hauteur du graphique (en px)<br>
	  * 	['settings']['width'] => (int) Largeur du graphique (en px)
	  *
	  */

	function eval_ccpc_genGraphLine ($data) {
	
		// On vérifie les données fournit
		if (isset($data) && isset($data['data']) && count ($data['data']) > 0 && isset($data['option']) && count($data['option']) > 0)
		{
			// On récupère le hash de $data
			$hash = md5(json_encode($data));

			// Chemin du fichier
			$filePath = PLUGIN_PATH.'cache/'.$hash.'.png'; 
			$filePathURI = ROOT.'evaluations/ccpc/cache/'.$hash.'.png';

			// Si le hash existe déjà : on renvoie le lien de l'image // sinon en crée le graphique
			if (is_file($filePath)) 
			{ 
				return $filePathURI; 
			}
			else 
			{ 
				// On crée l'image
				
					/* On inclut la librairie */
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pData.class.php');
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pDraw.class.php');
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pImage.class.php');
					
					/* On crée l'objet pData */
						// Préparation des données
						
							$tempDataArray = array(); // Contient les données chiffrés
							
							foreach ($data['data'] AS $tempDataLegend => $tempDataValue)
							{

								if (is_array($tempDataValue)) {
									foreach ($data['option'] AS $key)
									{
										if (isset($tempDataValue[$key]) && is_numeric($tempDataValue[$key]))
										{
											$tempDataArray[$tempDataLegend][$key] = $tempDataValue[$key];
										}
										else
										{
											$tempDataArray[$tempDataLegend][$key] = VOID;
										}
									}
								}
								else {	
									return FALSE;
								}
							}
							
						$MyData = new pData();
						foreach ($tempDataArray AS $key => $value)
						{
							$MyData->addPoints($value, $key); 
						}					
						$MyData->addPoints($data['option'], 'Label');  

						$MyData->setAbscissa("Label");
						$MyData->setSerieDescription("Label","Label");
					
					/* On crée l'objet pChart */
					
					if (isset($data['settings']['width'])) { $width = $data['settings']['width']; } else { $width = 600; }
					if (isset($data['settings']['height'])) { $height = $data['settings']['height']; } else { $height = 300; }
					
					$myPicture = new pImage($width,$height,$MyData, TRUE);				
					$myPicture->setFontProperties(array("FontName"=>PLUGIN_PATH."core/pChart2.1.4/fonts/MankSans.ttf","FontSize"=>14,"R"=>80,"G"=>80,"B"=>80));
										
					// On détermine les limites
					$myPicture->setGraphArea(0,0,$width,$height-30);
										
					$scaleSettings = array("DrawSubTicks"=>TRUE);
					if (isset($data['settings']['min']) && is_numeric($data['settings']['min']) && isset($data['settings']['max']) && is_numeric($data['settings']['max']) && $data['settings']['min'] < $data['settings']['max']) { // Prise en charge de la possibilité d'imposer le min et le max
						$scaleSettings['Mode'] = SCALE_MODE_MANUAL;
						$scaleSettings['ManualScale'][0] = array('Min' => $data['settings']['min'], 'Max' => $data['settings']['max']); // Min et Max sur l'axe des ordonnées
					}

					$myPicture->drawScale($scaleSettings);
					
					$myPicture->drawLegend(10, 10,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));
					
					$settings = array("Surrounding"=>-30,"InnerSurrounding"=>30, "DisplayValues"=>TRUE, "ORIENTATION_HORIZONTAL" => TRUE);
					$myPicture->drawLineChart($settings);
					$myPicture->render($filePath);

					return $filePathURI;
			}
		}
		else
		{
			return FALSE;
		}
	}

	/**
	  * eval_ccpc_genGraphRadar - Génère un graphique de type Radar simple sous forme d'image au format PNG
	  *
	  * @category : eval_ccpc_functions
	  * @param array $data Données à partir desquelles le graphique est généré
	  * @return string URL de l'image générée
	  *
	  * @Author Ali Bellamine
	  *
	  * Structure de $data :<br>
	  * 	['data'][nom du label] => (int) Valeur du label<br>
	  * 	['settings']['max'] => (int) Valeur maximale<br>
	  * 	['settings']['height'] => (int) Hauteur du graphique (en px)<br>
	  * 	['settings']['width'] => (int) Largeur du graphique (en px)
	  *
	  */

	function eval_ccpc_genGraphRadar ($data) {
		// On vérifie les données fournit 

		if (isset($data) && isset($data['data']) && count ($data['data']) > 0)
		{
			// On récupère le hash de $data
			$hash = md5(json_encode($data));
			// Chemin du fichier
			$filePath = PLUGIN_PATH.'cache/'.$hash.'.png';
			$filePathURI = ROOT.'evaluations/ccpc/cache/'.$hash.'.png';

			if (is_file($filePath)) 
			// Si le hash existe déjà : on renvoie le lien de l'image // sinon en crée le graphique
			{
				return $filePathURI; 
			}
			else 
			{
				// On crée l'image
				
					/* On inclut la librairie */
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pData.class.php');
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pRadar.class.php');
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pDraw.class.php');
					require_once(PLUGIN_PATH.'core/pChart2.1.4/class/pImage.class.php');
					
					/* On crée l'objet pData */
						// Préparation des données
						
							$tempLegendArray = array(); // Contient les légendes
							$tempDataArray = array(); // Contient les données chiffrés
							
							foreach ($data['data'] AS $tempDataLegend => $tempDataValue)
							{
								$tempLegendArray[] = $tempDataLegend;
								if (is_numeric($tempDataValue)) {
									$tempDataArray[] = $tempDataValue;
								}
								else {	
									return FALSE;
								}
							}

						$MyData = new pData();
						$MyData->addPoints($tempDataArray, 'Valeurs'); 
						$MyData->setPalette("Valeurs",array("R"=>150,"G"=>5,"B"=>217));
						$MyData->addPoints($tempLegendArray, 'Label');  
						
						$MyData->setAbscissa("Label");
						$MyData->setSerieDescription("Label","Label");
					
					/* On crée l'objet pChart */
					
					if (isset($data['settings']['width'])) { $width = $data['settings']['width']; } else { $width = 600; }
					if (isset($data['settings']['height'])) { $height = $data['settings']['height']; } else { $height = 300; }
					
					$myPicture = new pImage($width,$height,$MyData, TRUE);				

					$myPicture->setFontProperties(array("FontName"=>PLUGIN_PATH.'core/pChart2.1.4/fonts/verdana.ttf',"FontSize"=>10,"R"=>0,"G"=>0,"B"=>0));
										
					// On détermine les limites
					$myPicture->setGraphArea(0,0,$width,$height-30);
					
					/* On crée l'objet pRadar */
					$SplitChart = new pRadar();
					$Options = array("DrawPoly"=>TRUE, "LabelPos" => RADAR_LABELS_HORIZONTAL, "WriteValues"=>TRUE, "WriteValuesInBubble " => TRUE, "InnerBubbleR" => 255, "InnerBubbleG" => 255, "InnerBubbleB" => 255, "ValueFontSize"=>20, "ValuePadding" => 10, "Layout"=>RADAR_LAYOUT_CIRCLE,"BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>100,"EndR"=>207,"EndG"=>227,"EndB"=>125,"EndAlpha"=>50));
					if (isset($data['settings']['max']) && is_numeric($data['settings']['max'])) { // Prise en charge de la possibilité d'imposer le min et le max
						$Options['FixedMax'] = $data['settings']['max'];
					}

					$SplitChart->drawRadar($myPicture,$MyData,$Options);

					
					$myPicture->render($filePath);

					return $filePathURI;
			}
		}
		else
		{
			return FALSE;
		}
	}