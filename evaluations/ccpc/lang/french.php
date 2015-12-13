<?php

/**
	Interface
**/
define('LANG_FORM_CCPC_FILTER_TITLE','Filtres');
define('LANG_FORM_CCPC_FILTER_PROMOTION_TITLE','Promotion');
define('LANG_FORM_CCPC_FILTER_PROMOTION_OPTION_ALL','Toutes les promotions');
define('LANG_FORM_CCPC_FILTER_CERTIFICAT_TITLE','Certificat');
define('LANG_FORM_CCPC_FILTER_CERTIFICAT_OPTION_ALL','Tous les certificats');
define('LANG_FORM_CCPC_FILTER_HOPITAL_TITLE','Hopital');
define('LANG_FORM_CCPC_FILTER_HOPITAL_OPTION_ALL','Tous les hopitaux');
define('LANG_FORM_CCPC_FILTER_DATE_TITLE','Date');
define('LANG_FORM_CCPC_FILTER_DATE_FROM','Du');
define('LANG_FORM_CCPC_FILTER_DATE_TO','Au');
define('LANG_FORM_CCPC_FILTER_SEARCH_TITLE','Rechercher');
define('LANG_FORM_CCPC_LISTE_SERVICE_DATEINTERVAL_SELECT','Sélectionner un intervalle temporel');
define('LANG_FORM_CCPC_LISTE_SERVICE_NOSERVICEFOUND','Aucun service ne correspond aux critères sélectionnés');
define('LANG_FORM_CCPC_LISTE_SERVICE_NOEVALPERIOD','Aucune période complète de stage n\'a été retrouvé dans l\'intervalle temporel sélectionné');
define('LANG_FORM_CCPC_FILTER_SERVICE_TITLE','Service');
define('LANG_FORM_CCPC_LISTSERVICE_SERVICE_TITLE','Service');
define('LANG_FORM_CCPC_LISTSERVICE_NBEVAL_TITLE','Nombre d\'évaluations');
define('LANG_FORM_CCPC_EVALUATIONSDATA_SELECT_ALL','Selectionner une évaluation');
define('LANG_FORM_CCPC_EVALUATION_NAME','Evaluation');
define('LANG_FORM_CCPC_ADMIN_SELECT_FILTER','Séléctionner un filtre');
define('LANG_FORM_CCPC_ADMIN_SELECT_FILTER_ADD_HELP_QUERY','Les requêtes contiennent des test de la forme : "\'valeur à tester\' opérateur \'valeur de test\'" : <ul> <li>Valeur à tester : de la forme chemin.fonction : <ul> <li>Le chemin se réfère au champ du formulaire à tester, les sous champs sont séparés par un point.<br /> Les champs interrogeables vont de la catégorie aux valeurs, de la forme categorie (champs "nom" de la catégorie).question (champs "name" de la question) input.valeur (champs "values" de la question)<br /> Par exemple "bilan.noteFinal.-2" se réfère à la valeur -2 à la question noteFinal de la catégorie bilan. </li> <li> La fonction fait référence à la valeur testée, les fonctions disponibles sont : <ul> <li>fnNB : renvoie le nombre de fois que la valeur a été remplis. Elle n\'est valable que pour les champs de type : select, radio et checkbox.<br /> Pour les champs de type TEXT et TEXTAREA, la fonction retourne le nombre de réponses. </li> <li>fnPERCENTAGE : renvoie le pourcentage de réponse, entre 0 et 100. Elle n\'est valable que pour les champs de type : select, radio et checkbox.</li> <li>fnMOYENNE : renvoie la moyenne des notes. Ne fonctionne que avec les catégorie et les select. La moyenne est une valeur numérique entre 0 et 10/</li> </ul> </li> </ul> </li> <li>L\'opérateur est l\'un parmis les suivantes : <=, <, =, !=, >, >=</li> <li>La valeur de test correspond à une valeur numérique.</li> </ul> Plusieurs tests peuvent être effectués, ils doivent alors être séparés d\'un séparateur : "AND" ou "OR".<br /> Il est possible de déterminer la priorité entre les opérateurs en utilisant des parenthèse.<br /><br /> Exemple de requête : "(bilan.noteFinal.-1.fnNB >= 1 OR bilan.fnMOYENNE < 5) AND bilan.fnMOYENNE < 7"<br /> Signifiant : (stages dont la valeur -1 de noteFinal (catégorie Bilan) est rencontré 1 fois ou plus OU stages dont la moyenne de la catégorie bilan est strictement inférieure à 5) ET stage dont la moyenne de la catégorie bilan est strictement inférieure à 7.');

/**
	Génération du PDF
**/

define('LANG_FORM_CCPC_PDF_TITLE','Synthèse des évaluations par les étudiants');
define('LANG_FORM_CCPC_PDF_COMMENT_TITLE','Commentaires');
define('LANG_FORM_CCPC_PDF_FOOTER_FULLRESULT','Résultats complets consultables sur ');
define('LANG_FORM_CCPC_PDF_FOOTER_STRUCTURENAME','Commission des Stages et des Gardes');
define('LANG_FORM_CCPC_PDF_STAGEPERIODE_START','Période du');
define('LANG_FORM_CCPC_PDF_STAGEPERIODE_END','Au');
define('LANG_FORM_CCPC_PDF_STAGEPERIODE_FULLYEAR','Sur les 12 derniers mois');
define('LANG_FORM_CCPC_PDF_STAGEPERIODE','Période du stage');
define('LANG_FORM_CCPC_PDF_STUDENTPROMOTION','Année des étudiants acceuillis');
define('LANG_FORM_CCPC_PDF_STUDENTNB','Nombre d\'étudiants acceuillis');
define('LANG_FORM_CCPC_PDF_EVALUATIONNB','Nombre d\'évaluations remplis');

/**
	Formulaire de l'évaluation
**/

define('LANG_FORM_CCPC_FIELDSET_PEDAGOGIE','Pédagogie');
define('LANG_FORM_CCPC_FIELDSET_INVESTISSEMENT','Investissement personnel');
define('LANG_FORM_CCPC_FIELDSET_AMBIANCE','Ambiance');
define('LANG_FORM_CCPC_FIELDSET_BILAN','Bilan du stage');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE','En stage, j’ai eu l’impression d’apprendre pendant…');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE_SHORT','Temps d\'apprentissage');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE_ITEM_MINUS2','0 - 20% du temps');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE_ITEM_MINUS1','20% - 40% du temps');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE_ITEM_0','40% - 60% du temps');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE_ITEM_PLUS1','60% - 80% du temps');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE_ITEM_PLUS2','80% - 100% du temps');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS','Qui a participé à votre apprentissage ?');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_SHORT','Participant à l\'enseignement');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_PUPH','PU-PH / MCU-PH');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_PUPH_SHORT','PU/MCU-PH');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_CCA','CCA');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_CCA_SHORT','CCA');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_PH','PH');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_PH_SHORT','PH');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_INTERNE','Internes');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_INTERNE_SHORT','Internes');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_MG','Maitres de stage de MG');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_MG_SHORT','MG');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE','Les activités pédagogiques comprenaient');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_SHORT','Activités pédagogiques');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_LITMALADE','Apprentissage au lit du malade');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_LITMALADE_SHORT','Lit du malade');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_COURS','Cours');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_COURS_SHORT','Cours');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_ARC','ARC');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_ARC_SHORT','ARC');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_SALLE','Apprentissage en salle');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_SALLE_SHORT','Salle');
define('LANG_FORM_CCPC_QUESTION_QUALITEENSEIGNEMENTS','Comment qualifiez-vous la qualité des enseignements dans le stage ?');
define('LANG_FORM_CCPC_QUESTION_QUALITEENSEIGNEMENTS_SHORT','Qualité des enseignements');
define('LANG_FORM_CCPC_QUESTION_QUALITEENSEIGNEMENTS_ITEM_MINUS2','Sans intérêt');
define('LANG_FORM_CCPC_QUESTION_QUALITEENSEIGNEMENTS_ITEM_MINUS1','Inégaux ou décevants');
define('LANG_FORM_CCPC_QUESTION_QUALITEENSEIGNEMENTS_ITEM_PLUS1','Bien');
define('LANG_FORM_CCPC_QUESTION_QUALITEENSEIGNEMENTS_ITEM_PLUS2','Excellent et bien ciblés sur la spécialité');
define('LANG_FORM_CCPC_QUESTION_ENCADREMENTEXTERNES','Concernant l\'encadrement et la responsabilité des externes pour la prise en charge des patients, comment qualifiez-vous ce stage ?');
define('LANG_FORM_CCPC_QUESTION_ENCADREMENTEXTERNES_SHORT','Encadrement');
define('LANG_FORM_CCPC_QUESTION_ENCADREMENTEXTERNES_ITEM_MINUS2','Pas du tout formateur, externes abandonnés');
define('LANG_FORM_CCPC_QUESTION_ENCADREMENTEXTERNES_ITEM_MINUS1','Peu formateur, externes peu encadrés');
define('LANG_FORM_CCPC_QUESTION_ENCADREMENTEXTERNES_ITEM_PLUS1','Formateur, externes relativement encadrés');
define('LANG_FORM_CCPC_QUESTION_ENCADREMENTEXTERNES_ITEM_PLUS2','Très formateur, externes bien encadrés');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES','En cas de garde, comment qualifiez-vous l\'apprentissage pendant les gardes ?');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES_SHORT','Apprentissage durant les gardes ?');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES_ITEM_MINUS2','Inexistante');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES_ITEM_MINUS1','Décevant ou inégale');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES_ITEM_0','Sans objet (pas de garde)');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES_ITEM_PLUS1','Bonne');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES_ITEM_PLUS2','Excellente');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE','A combien d\'heures par semaine vous estimez le temps passé dans le service ? (hors garde)');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_SHORT','Temps d\'apprentissage');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_ITEM_MINUS3','Moins de 10h');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_ITEM_MINUS2','Entre 10 h et 15 h');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_ITEM_MINUS1','Entre 15 h et 20 h');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_ITEM_PLUS1','Entre 20 h et 25 h');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_ITEM_PLUS2','Entre 25 h et 30 h');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_ITEM_PLUS3','Supérieur à 30 h');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE','Quelle est le rythme habituel proposé aux externes ?');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_SHORT','Rythme du stage ?');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_5DJ','5 demi-journées par semaine');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_5DJ_SHORT','5 D-J');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_35DJ','Entre 3 et 5 demi-journées par semaine');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_35DJ_SHORT','3-5 D-J');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_3DJ','3 demi-journées par semaine');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_3DJ_SHORT','3 D-J');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_MATIN','Les matins exclusivement');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_MATIN_SHORT','Matins');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_COMPLET','En journées continues exclusivement');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_COMPLET_SHORT','Jours complets');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_ALTERNEE','Semaine alternée 1/2 ou 1/3 (avec journées continues)');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_ALTERNEE_SHORT','Alternée');
define('LANG_FORM_CCPC_QUESTION_AMBIANCESERVICE','L\'ambiance dans le service était');
define('LANG_FORM_CCPC_QUESTION_AMBIANCESERVICE_SHORT','Ambiance dans le service');
define('LANG_FORM_CCPC_QUESTION_AMBIANCESERVICE_ITEM_MINUS2','Inexistante');
define('LANG_FORM_CCPC_QUESTION_AMBIANCESERVICE_ITEM_MINUS1','Tendue');
define('LANG_FORM_CCPC_QUESTION_AMBIANCESERVICE_ITEM_PLUS1','Agréable');
define('LANG_FORM_CCPC_QUESTION_AMBIANCESERVICE_ITEM_PLUS2','Excellente');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL','Au vu de l\'ensemble de cette évaluation, pensez-vous que ce stage est particulièrement formateur pour les étudiants et doit-il être félicité ?');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL_SHORT','Caractère formateur du stage');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL_ITEM_MINUS2','Pas du tout formateur et doit être retiré du choix en l\'absence de modifications');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL_ITEM_MINUS1','Peu formateur et doit être revu en profondeur');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL_ITEM_0','Relativement formateur mais doit être ajusté sur de nombreux détails');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL_ITEM_PLUS1','Formateur mais doit être ajusté sur des détails');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL_ITEM_PLUS2','Très formateur et doit être félicité');
define('LANG_FORM_CCPC_QUESTION_POINTPOSITIFS','Les points positifs à retenir : (soyez brefs, concis et complémentaires des réponses précédentes)');
define('LANG_FORM_CCPC_QUESTION_POINTPOSITIFS_SHORT','Points positifs');
define('LANG_FORM_CCPC_QUESTION_POINTPOSITIF_1','Point positif n°1');
define('LANG_FORM_CCPC_QUESTION_POINTPOSITIF_2','Point positif n°2');
define('LANG_FORM_CCPC_QUESTION_POINTPOSITIF_3','Point positif n°3');
define('LANG_FORM_CCPC_QUESTION_POINTNEGATIFS','Les points négatifs à signaler : (soyez brefs et concis)');
define('LANG_FORM_CCPC_QUESTION_POINTNEGATIFS_SHORT','Points négatifs');
define('LANG_FORM_CCPC_QUESTION_POINTNEGATIF_1','Point négatif n°1');
define('LANG_FORM_CCPC_QUESTION_POINTNEGATIF_2','Point négatif n°2');
define('LANG_FORM_CCPC_QUESTION_POINTNEGATIF_3','Point négatif n°3');
define('LANG_FORM_CCPC_QUESTION_COMMENTAIRELIBRE','Commentaires libres sur le stage');
define('LANG_FORM_CCPC_QUESTION_COMMENTAIRELIBRE_SHORT','Commentaires');
define('LANG_FORM_CCPC_QUESTION_TEXT_MODERATE','<i>LE MESSAGE A FAIT L\'OBJET D\'UNE MODERATION</i>');
define('LANG_FORM_CCPC_QUESTION_TEXT_UNMODERATE','<i>Veuillez actualiser la page afin d\'afficher le commentaire.</i>');
define('LANG_FORM_CCPC_QUESTION_ITEM_0','Non');
define('LANG_FORM_CCPC_QUESTION_ITEM_1','Oui');
define('LANG_FORM_CCPC_SUBMITBUTTON','Enregistrer les réponses');

/* Détection des stages via l'utilisation de filtres */
define('LANG_FORM_CCPC_ADMIN_FILTER_ADD_TITLE','Ajout d\'un nouveau filtre');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_NAME','Nom du filtre');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_QUERY','Règle de filtrage');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_OBJECT','Titre des mails automatiques');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_CONTENT','Contenue des mails automatiques');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_PROMOTION','Restreindre l\'application du filtre à une promotion');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_ICON','Icone');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_SEND','Envoyer les messages');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_RULES','Format PNG, Taille : 128px * 128px');
define('LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_NOCOMMENT','Sans commentaires');
define('LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_MODERATECOMMENT','Avec commentaires (modérés)');
define('LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_UNMODERATECOMMENT','Avec commentaires (non modérés)');

/* Paramètres */
define('LANG_FORM_CCPC_SETTINGS_TITLE','Paramètres de l\'évaluation');
define('LANG_FORM_CCPC_SETTINGS_PERIOD_TITLE','Période à évaluer');
define('LANG_FORM_CCPC_SETTINGS_PERIOD_START','Date de début (JJ/MM/AAAA)');
define('LANG_FORM_CCPC_SETTINGS_PERIOD_END','Date de fin (JJ/MM/AAAA)');
define('LANG_FORM_CCPC_SETTINGS_SUBMIT','Valider');


/* Erreurs  */

define('LANG_ERROR_CCPC_INVALIDDATE','La date de début doit être située avant la date de fin.');
define('LANG_ERROR_CCPC_INCOMPLETEFORM','Veuillez compléter tous les champs du formulaire.');
define('LANG_ERROR_CCPC_NOPROMOTION','Erreur interne - Impossible de récupérer la promotion de l\'utilisateur.');
define('LANG_ERROR_CCPC_NONBEXTERNE','Erreur interne - Impossible de calculer le nombre d\'externes affecté au service.');
define('LANG_ERROR_CCPC_NOSERVICE','Aucuns stage n\'est associé à la période du formulaire.');
define('LANG_ERROR_CCPC_UNKNOWN','Une erreur d\'origine inconnue s\'est déroulée.');
define('LANG_FORM_CCPC_ADMIN_FILTER_ERROR_FORMAT', 'Le format de l\'image est invalide.');
define('LANG_FORM_CCPC_LOGINAS_SUBMITFORBIDDEN', 'Vous ne pouvez pas soumettre le formulaire au nom de l\'utilisateur');
?>