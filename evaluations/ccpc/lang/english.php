<?php

/**
	Interface
**/
define('LANG_FORM_CCPC_FILTER_TITLE','Filters');
define('LANG_FORM_CCPC_FILTER_PROMOTION_TITLE','Classes');
define('LANG_FORM_CCPC_FILTER_PROMOTION_OPTION_ALL','Any classes');
define('LANG_FORM_CCPC_FILTER_CERTIFICAT_TITLE','Certificates');
define('LANG_FORM_CCPC_FILTER_CERTIFICAT_OPTION_ALL','Any certificats');
define('LANG_FORM_CCPC_FILTER_HOPITAL_TITLE','Hospital');
define('LANG_FORM_CCPC_FILTER_HOPITAL_OPTION_ALL','Any hospital');
define('LANG_FORM_CCPC_FILTER_DATE_TITLE','Date');
define('LANG_FORM_CCPC_FILTER_DATE_FROM','From');
define('LANG_FORM_CCPC_FILTER_DATE_TO','To');
define('LANG_FORM_CCPC_FILTER_FAST_FILTER','Fast selection');
define('LANG_FORM_CCPC_FILTER_SEARCH_TITLE','Search');
define('LANG_FORM_CCPC_LISTE_SERVICE_DATEINTERVAL_SELECT','Select an time period');
define('LANG_FORM_CCPC_LISTE_SERVICE_NOSERVICEFOUND','No internship found with the selected options');
define('LANG_FORM_CCPC_LISTE_SERVICE_NOEVALPERIOD','No full stage period found in the selected time period');
define('LANG_FORM_CCPC_FILTER_SERVICE_TITLE','Service');
define('LANG_FORM_CCPC_LISTSERVICE_SERVICE_TITLE','Service');
define('LANG_FORM_CCPC_LISTSERVICE_NBEVAL_TITLE','Evaluations number');
define('LANG_FORM_CCPC_EVALUATIONSDATA_SELECT_ALL','Select an evaluation');
define('LANG_FORM_CCPC_EVALUATION_NAME','Evaluation');
define('LANG_FORM_CCPC_ADMIN_SELECT_FILTER','Select an filter');
define('LANG_FORM_CCPC_ADMIN_SELECT_FILTER_ADD_HELP_QUERY','Request respect the structure : "\'Variable to test\' Operator \'Value to test\'" : <ul> <li>Variable to test : specified by her path : <ul> <li>The path refer to the form inputs, subinput are specified by a ".".<br /> Input deapth goes from categories to values, the structure is : categorie (attribute named "nom" of the categorie).question (attribute "name" of the question) .input.value (attribute "value" of the question)<br /> Example : "bilan.noteFinal.-2" refer to the value -2 of the question noteFinal in the categorie "bilan". </li> <li> The function refer to the tested value, avalaible functions are  : <ul> <li>fnNB : return the number of occurence of the selected value. The function is avalaible only in theses inputs type : select, radio and checkbox.<br /> For TEXT and TEXTAREA inputs, the function returns number of answer. </li> <li>fnPERCENTAGE : return the percentage matche by the answer, it returns a number between 0 and 100. It is only avalaible in theses input types : select, radio and checkbox.</li> <li>fnMOYENNE : return the mean of the rates. It is only avalaible for categories and SELECT inputs. The mean is a numeric value betweaan 0 and 10/</li> </ul> </li> </ul> </li> <li>The operator must be one of these : <=, <, =, !=, >, >=</li> <li>The test value must be a numeric value.</li> </ul> You can combine test, these must be seperate by a separator : "AND" ou "OR".<br /> It it possible to determine test priority using parenthesis.<br /><br /> Request example : "(bilan.noteFinal.-1.fnNB >= 1 OR bilan.fnMOYENNE < 5) AND bilan.fnMOYENNE < 7"<br /> Meaning : (Internship which have >= than 1 occurence of the value -1 of the noteFinal (in categorie Bilan) input OR internship which have a "bilan" categorie\'s mean < 5) AND internshit which have a "bilan" categorie\'s mean < 7.');

/**
	Génération du PDF
**/

define('LANG_FORM_CCPC_PDF_TITLE','Student evaluations summary');
define('LANG_FORM_CCPC_PDF_COMMENT_TITLE','Comments');
define('LANG_FORM_CCPC_PDF_FOOTER_FULLRESULT','Full results avalaible on  ');
define('LANG_FORM_CCPC_PDF_FOOTER_STRUCTURENAME','Commission des Stages et des Gardes');
define('LANG_FORM_CCPC_PDF_STAGEPERIODE_START','From ');
define('LANG_FORM_CCPC_PDF_STAGEPERIODE_END','To');
define('LANG_FORM_CCPC_PDF_STAGEPERIODE_FULLYEAR','Past 12 months');
define('LANG_FORM_CCPC_PDF_STAGEPERIODE','Stage period');
define('LANG_FORM_CCPC_PDF_STUDENTPROMOTION','Classes');
define('LANG_FORM_CCPC_PDF_STUDENTNB','Number of students');
define('LANG_FORM_CCPC_PDF_EVALUATIONNB','Number of evaluations');

/**
	Formulaire de l'évaluation
**/

define('LANG_FORM_CCPC_VALIDATE_WARNING','Be careful, you are attempting to validate the form. Once the form is validate, you cannot edit is content. Do you confirm your action ?');
define('LANG_FORM_CCPC_FIELDSET_PEDAGOGIE','Pedagogy');
define('LANG_FORM_CCPC_FIELDSET_INVESTISSEMENT','Personal Investissement');
define('LANG_FORM_CCPC_FIELDSET_AMBIANCE','Atmosphere');
define('LANG_FORM_CCPC_FIELDSET_BILAN','Review');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE','Time learning things…');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE_SHORT','Time learning');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE_ITEM_MINUS2','0 - 20%');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE_ITEM_MINUS1','20% - 40%');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE_ITEM_0','40% - 60%');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE_ITEM_PLUS1','60% - 80%');
define('LANG_FORM_CCPC_QUESTION_TEMPSAPPRENTISSAGE_ITEM_PLUS2','80% - 100%');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS','Who participate to your formation ?');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_SHORT','Formation actors');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_PUPH','PU-PH / MCU-PH');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_PUPH_SHORT','PU/MCU-PH');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_CCA','CCA');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_CCA_SHORT','CCA');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_PH','PH');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_PH_SHORT','PH');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_INTERNE','Resident');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_INTERNE_SHORT','Resident');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_MG','Generalist internship teacher');
define('LANG_FORM_CCPC_QUESTION_PARTICIPATIONENSEIGNANTS_ITEM_MG_SHORT','Generalist');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE','Pedagogic activities contains');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_SHORT','Pedagogic activities');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_LITMALADE','Learning at patient\'s bed');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_LITMALADE_SHORT','Patient\s bed');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_COURS','Classes');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_COURS_SHORT','Classes');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_ARC','ARC');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_ARC_SHORT','ARC');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_SALLE','Teaching in service');
define('LANG_FORM_CCPC_QUESTION_ACTIVITEPEDAGOGIQUE_ITEM_SALLE_SHORT','Service');
define('LANG_FORM_CCPC_QUESTION_QUALITEENSEIGNEMENTS','How would you describe formation\'s quality in this internship ?');
define('LANG_FORM_CCPC_QUESTION_QUALITEENSEIGNEMENTS_SHORT','Formation\'s quality');
define('LANG_FORM_CCPC_QUESTION_QUALITEENSEIGNEMENTS_ITEM_MINUS2','Useless');
define('LANG_FORM_CCPC_QUESTION_QUALITEENSEIGNEMENTS_ITEM_MINUS1','Disapointing');
define('LANG_FORM_CCPC_QUESTION_QUALITEENSEIGNEMENTS_ITEM_PLUS1','Good');
define('LANG_FORM_CCPC_QUESTION_QUALITEENSEIGNEMENTS_ITEM_PLUS2','Excellent');
define('LANG_FORM_CCPC_QUESTION_ENCADREMENTEXTERNES','About responsibility and managment, how would you describe this internship ?');
define('LANG_FORM_CCPC_QUESTION_ENCADREMENTEXTERNES_SHORT','Managment');
define('LANG_FORM_CCPC_QUESTION_ENCADREMENTEXTERNES_ITEM_MINUS2','No managment');
define('LANG_FORM_CCPC_QUESTION_ENCADREMENTEXTERNES_ITEM_MINUS1','Low managment');
define('LANG_FORM_CCPC_QUESTION_ENCADREMENTEXTERNES_ITEM_PLUS1','Good managment');
define('LANG_FORM_CCPC_QUESTION_ENCADREMENTEXTERNES_ITEM_PLUS2','Excellent managment');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES','During on-duty service, how would you qualify the formation ?');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES_SHORT','On duty formation ?');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES_ITEM_MINUS2','None');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES_ITEM_MINUS1','Disapointing');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES_ITEM_0','No on-duty service');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES_ITEM_PLUS1','Good');
define('LANG_FORM_CCPC_QUESTION_APPRENTISSAGEGARDES_ITEM_PLUS2','Excellent');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE','How meny time do you spend in the service in a week (on-duty service excludes)');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_SHORT','Formation time');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_ITEM_MINUS3','Less than 10h');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_ITEM_MINUS2','Between 10 h and 15 h');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_ITEM_MINUS1','Between 15 h and 20 h');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_ITEM_PLUS1','Between 20 h and 25 h');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_ITEM_PLUS2','Between 25 h and 30 h');
define('LANG_FORM_CCPC_QUESTION_TEMPSSERVICE_ITEM_PLUS3','More than 30 h');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE','What is the habituel time schedule of students ?');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_SHORT','Time Schedule');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_5DJ','5 half day per week');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_5DJ_SHORT','5 H-D');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_35DJ','Between 3 and 5 half day per week');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_35DJ_SHORT','3-5 H-D');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_3DJ','3 half day per semaine');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_3DJ_SHORT','3 H-D');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_MATIN','Morning only');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_MATIN_SHORT','Morning');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_COMPLET','Full Day only');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_COMPLET_SHORT','Full days');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_ALTERNEE','Alternate week 1/2 or 1/3 (with full days)');
define('LANG_FORM_CCPC_QUESTION_RYTHMESERVICE_ITEM_ALTERNEE_SHORT','Alternate');
define('LANG_FORM_CCPC_QUESTION_AMBIANCESERVICE','In the service, the atmosphere is ?');
define('LANG_FORM_CCPC_QUESTION_AMBIANCESERVICE_SHORT','Atmosphere in service');
define('LANG_FORM_CCPC_QUESTION_AMBIANCESERVICE_ITEM_MINUS2','Inexistant');
define('LANG_FORM_CCPC_QUESTION_AMBIANCESERVICE_ITEM_MINUS1','Strained');
define('LANG_FORM_CCPC_QUESTION_AMBIANCESERVICE_ITEM_PLUS1','Plaisant');
define('LANG_FORM_CCPC_QUESTION_AMBIANCESERVICE_ITEM_PLUS2','Excellent');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL','According to this evaluation, do you think the intership is formative and do you that it should be congratulate ?');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL_SHORT','Formative caracter of the internshit');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL_ITEM_MINUS2','No formative, it should be removed it it doesn\'t improve');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL_ITEM_MINUS1','Low formative, it should be improved in huge details');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL_ITEM_0','Relative formative, but can be improved in many points');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL_ITEM_PLUS1','Formative but can still be improved');
define('LANG_FORM_CCPC_QUESTION_NOTEFINAL_ITEM_PLUS2','Very formative, it should be congratulate');
define('LANG_FORM_CCPC_QUESTION_POINTPOSITIFS','Positives aspects : (short answer)');
define('LANG_FORM_CCPC_QUESTION_POINTPOSITIFS_SHORT','Positives aspects');
define('LANG_FORM_CCPC_QUESTION_POINTPOSITIF_1','Positive aspect n°1');
define('LANG_FORM_CCPC_QUESTION_POINTPOSITIF_2','Positive aspect n°2');
define('LANG_FORM_CCPC_QUESTION_POINTPOSITIF_3','Positive aspectn°3');
define('LANG_FORM_CCPC_QUESTION_POINTNEGATIFS','Negative aspects : (short answer)');
define('LANG_FORM_CCPC_QUESTION_POINTNEGATIFS_SHORT','Negative aspects');
define('LANG_FORM_CCPC_QUESTION_POINTNEGATIF_1','Negative aspect n°1');
define('LANG_FORM_CCPC_QUESTION_POINTNEGATIF_2','Negative aspect n°2');
define('LANG_FORM_CCPC_QUESTION_POINTNEGATIF_3','Negative aspect n°3');
define('LANG_FORM_CCPC_QUESTION_COMMENTAIRELIBRE','Free comment');
define('LANG_FORM_CCPC_QUESTION_COMMENTAIRELIBRE_SHORT','Comment');
define('LANG_FORM_CCPC_QUESTION_TEXT_MODERATE','<i>THIS MESSAGE HAS BEEN REMOVED BY AN MODERATOR INTERVENTION</i>');
define('LANG_FORM_CCPC_QUESTION_TEXT_UNMODERATE','<i>Reload the page to display this comment.</i>');
define('LANG_FORM_CCPC_QUESTION_ITEM_0','No');
define('LANG_FORM_CCPC_QUESTION_ITEM_1','Yes');
define('LANG_FORM_CCPC_SUBMITBUTTON','Save answers');

/* Détection des stages via l'utilisation de filtres */
define('LANG_FORM_CCPC_ADMIN_FILTER_ADD_TITLE','Add a new filter');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_NAME','Filter\'s name');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_QUERY','Filter\'s rules');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_OBJECT','Automatic mail object');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_CONTENT','Automatic mail content');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_PROMOTION','Restreint the analyze at promotion\'s data');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_ICON','Icon');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_SEND','Send messages');
define('LANG_FORM_CCPC_ADMIN_FILTER_FILTER_MAIL_RULES','PNG, Size : 128px * 128px');
define('LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_NOCOMMENT','Without comments');
define('LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_MODERATECOMMENT','With comments (moderate)');
define('LANG_FORM_CCPC_ADMIN_FILTER_TABLE_DOWNLOAD_UNMODERATECOMMENT','With commentaires (no moderate)');

/* Envoie de campagnes d'emails */
define('LANG_FORM_CCPC_ADMIN_MAILER_NOYEARSELECTED','No year selected');
define('LANG_FORM_CCPC_ADMIN_MAILER_NOCAMPAIGN','There are no mail campaign for this period.');
define('LANG_FORM_CCPC_ADMIN_MAILER_TABLE_FROM','Beginning of the intership');
define('LANG_FORM_CCPC_ADMIN_MAILER_TABLE_TO','End of the internshit');
define('LANG_FORM_CCPC_ADMIN_MAILER_TABLE_PROMOTION','Classes');
define('LANG_FORM_CCPC_ADMIN_MAILER_TABLE_CAMPAIGNSTART','Beginning of the mail campaign');
define('LANG_FORM_CCPC_ADMIN_MAILER_TABLE_STATE','State');
define('LANG_FORM_CCPC_ADMIN_MAILER_TABLE_CREATECAMPAIGN','Create a mail campaign');

/* Paramètres */
define('LANG_FORM_CCPC_SETTINGS_TITLE','Evaluation settings');
define('LANG_FORM_CCPC_SETTINGS_PERIOD_TITLE','Time interval');
define('LANG_FORM_CCPC_SETTINGS_PERIOD_START','From (DD/MM/YYYY)');
define('LANG_FORM_CCPC_SETTINGS_PERIOD_END','To (DD/MM/YYYY)');
define('LANG_FORM_CCPC_SETTINGS_SUBMIT','Valider');


/* Erreurs  */

define('LANG_ERROR_CCPC_INVALIDDATE','Invalid date.');
define('LANG_ERROR_CCPC_INCOMPLETEFORM','Please complete all form inputs.');
define('LANG_ERROR_CCPC_NOPROMOTION','Internal error - Could not get user\'s class.');
define('LANG_ERROR_CCPC_NONBEXTERNE','Internal error - Could not determine the number of student affected to the service.');
define('LANG_ERROR_CCPC_NOSERVICE','No internshit is associated with the form time interval.');
define('LANG_ERROR_CCPC_UNKNOWN','Unknown error.');
define('LANG_FORM_CCPC_ADMIN_FILTER_ERROR_FORMAT', 'Picture format is invalid.');
define('LANG_FORM_CCPC_LOGINAS_SUBMITFORBIDDEN', 'You cannot submit the form when logged as an user');
?>