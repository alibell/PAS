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
			Inclusion des fichiers nécessaires au bon fonctionnement de la page
		**/
		
		if (is_file(PLUGIN_PATH.'lang/'.LANG.'.php'))
		{
			require PLUGIN_PATH.'lang/'.LANG.'.php'; // Fichier de langue
		}
		else
		{
			require PLUGIN_PATH.'lang/'.DEFAULT_LANG.'.php';
		}
		
		/*
			Fichiers de fonctions
		*/
		
		$bypasslimit = false; // Variable permettant de bypasser les limites d'accès aux évaluations (chef de service, étudiants ...)
		require(PLUGIN_PATH.'core/fnDisplayEvaluationResult.php'); // Fonctions propres à l'affichage des résultats d'épreuves
		require(PLUGIN_PATH.'core/fnDisplayEvaluation.php'); // Fonctions propres à l'affichage des formulaires d'évaluation
		require(PLUGIN_PATH.'core/fnGraphGen.php'); // Affichage des graphiques
		include(PLUGIN_PATH.'core/fnAdmin.php'); // Administration du module	
                
                /*
                 *  Action Ajax : Simple routage pour chargement d'une page Ajax
                 */
                
                $allowedAjax = array('ajaxContentEvaluation');
                if (isset($_GET['ajax']) && is_file (PLUGIN_PATH.'ajax/'.$_GET['ajax'].'.php') && in_array($_GET['ajax'], $allowedAjax)) {
                    include(PLUGIN_PATH.'ajax/'.$_GET['ajax'].'.php'); // On charge la page
                }
		
		/*
			On récupère la liste des services à évaluer
		*/
		
		$nonEvaluationData = eval_ccpc_getNoFormData($evaluationData,array());

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
		
		if ((!isset($nonEvaluationData['data']) || count($nonEvaluationData['data']) == 0) && $isLogedAs == FALSE)
		{
			validateEvaluation();				
		}
				
		/**
			Traitement des données lors de la réponse au formulaire
		**/
		
		if (isset($_POST) && count($_POST) > 0)
		{
			$formData = processCCPCformData($_POST, $evaluationData);
			$erreur = $formData['erreur'];
			
			// On interdit les administrateurs connectés au nom d'un utilisateur de répondre au formulaire
			if ($isLogedAs)
			{
				$erreur['LANG_FORM_CCPC_LOGINAS_SUBMITFORBIDDEN'] = TRUE;
			}
			
			unset($formData['erreur']);
			/*
				En l'absence d'erreur dans le traitement des données --> on lance l'enregistrement dans la base de donnée
			*/
			if (count($erreur) == 0)
			{
				if (registerCCPCformData($formData))
				{
					// Le formulaire a été correcterment enregistré --> on enregistre cela dans les réglages du service
					if (isSerialized(getEvaluationRegisterData()))
					{
						$evaluateServiceTemp = unserialize(getEvaluationRegisterData());
					}
					else
					{
						$evaluateServiceTemp = array();
					}
					
					if (!in_array($formData['service'], $evaluateServiceTemp))
					{
                                                /**
                                                 *  On supprime les données temporaires pour garantir l'anonymat
                                                 */
                                            
                                                if (isset($evaluateServiceTemp['data'])) {
                                                    $tempData = json_decode($evaluateServiceTemp['data'], true);
                                                    if (isset($tempData[$formData['service']])) {
                                                        unset($tempData[$formData['service']]);
                                                        $evaluateServiceTemp['data'] = json_encode($tempData);
                                                    }
                                                }
                                                
                                                $evaluateServiceTemp[] = $formData['service'];
						setEvaluationRegisterData(serialize($evaluateServiceTemp));
						header('Location: '.ROOT.CURRENT_FILE.'?'.http_build_query($_GET));
					}
				}
				else
				{
					// Une erreur s'est déroulé lors de l'enregistrement
					$erreur['LANG_ERROR_CCPC_UNKNOWN'] = true;
				}
			}
		}
		
		/**
			Affichage des erreurs
		**/
		
		if (count($erreur) > 0)
		{
			?>
			<ul class = "erreur">
				<?php
					foreach ($erreur AS $msgErreur => $valeurErreur)
					{
						?>
						<li><?php echo constant($msgErreur); ?></li>
						<?php
					}
				?>
			</ul>
			<?php
		}
		
		/**
			Affichage du formulaire
		**/
		require PLUGIN_PATH.'displayEvaluation/form.php';
	?>
	
	<script>
	$('form').on('submit', function(e){
		if (!confirm("<?php echo LANG_FORM_CCPC_VALIDATE_WARNING; ?>"))
		{
			e.preventDefault();
		}
	});

        ajaxURI = "<?php echo ROOT.CURRENT_FILE.'?id='.$evaluationData['register']['id'].'&ajax=ajaxContentEvaluation'; ?>";

        // Variable contenant les données de formulaires remplies
        tempFormData = {};
        
        // On récupère les données du serveur
        <?php        
        if (isset($evaluateService['data'])) { 
        ?>
            tempFormData = JSON.parse('<?php echo $evaluateService['data']; ?>');
        <?php
        }
        ?>
                
        // Enregistrement à la volée des changement
        $('select, input, textarea').on('change', function(e){    
           // Service concerné
           var service = $('select[name = "service"] option:selected').val();
           
           // Type de case
           if ($(this).attr('name') != 'service') {
            var nodeName = e.target.nodeName.toLowerCase();
            if (nodeName == 'input') {
                nodeName = $(this).attr('type');
            }

           // Nom du champs
           if (nodeName == 'radio' || nodeName == 'checkbox' || nodeName == 'select') {
               var inputId = $(this).attr('id');
           } else {
               var inputId = $(this).attr('name');
           }

            // Valeur du champs
            if (nodeName == 'radio' || nodeName == 'checkbox') {
                var value = $(this).attr('value');
            } else if (nodeName == 'text' || nodeName == 'textarea') {
                var value = $(this).val();
            } else if (nodeName == 'select') {
                var value = $(this).children('option:selected').val();
            }
           }
           
           // On enregistre le tout dans la variable tempFormData
           if (typeof(tempFormData[service]) == 'undefined') { tempFormData[service] = {}; }
           if (typeof(tempFormData[service][inputId]) == 'undefined') { tempFormData[service][inputId] = {}; }
           tempFormData[service][inputId]['Type'] = nodeName;
           if (nodeName)
           tempFormData[service][inputId]['Value'] = value;
           
           // On envoie le tout au serveur
           $.post(ajaxURI, {action: 'saveDraftEval', draft: JSON.stringify(tempFormData)});
        });
        
        // Fonction rétablissant l'état du formulaire
        function loadFormData (service) {
            $('form').get(0).reset();
            $('input[type = "checkbox"]').prop('checked', false);
            $('select[name = "service"]').val(service);
                
            if (typeof(tempFormData[service]) !== 'undefined') {
                $.each(tempFormData[service], function(id, data) {;
                    if (data['Type'] == 'radio' || data['Type'] == 'checkbox') {
                        $('#'+id).prop('checked',true);
                    } else if (data['Type'] == 'select') {
                        $('#'+id).val(data['Value']);
                    } else if (data['Type'] == 'text') {
                        $('input[name = "'+id+'"]').val(data['Value']);
                    } else if (data['Type'] == 'textarea') {
                        $('textarea[name = "'+id+'"]').val(data['Value']);
                    }
                });
            }
        }
        
        // Au chargement de la page --> On charge les données en mémoire
        var service = $('select[name = "service"] option:selected').val();
        loadFormData(service);
        
        // Au changement de service
        $('select[name = "service"]').on('change', function(){
            var service = $('select[name = "service"] option:selected').val();
            loadFormData(service);
        });
        
	</script>