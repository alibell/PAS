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
		Variables
	*/
	
	/*
		eval_initTable()
		Fonction chargée de créer les tables dans la BDD si elles n'existent pas
	*/
	
	function eval_initTable()
	{
		global $db;
		
		// Paramètres de l'évaluation
		$sql = 'CREATE TABLE IF NOT EXISTS `eval_survey_settings` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `evaluation` int(11) NOT NULL,
				  `surveyLink` text NOT NULL,
				  `surveyCode` varchar(255) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `evaluation` (`evaluation`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
				ALTER TABLE `eval_survey_settings`
				ADD CONSTRAINT `eval_survey_ibfk_1` FOREIGN KEY (`evaluation`) REFERENCES `evaluation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE';
					
		$db -> query($sql);
	}
	
	/*
		eval_getSurveyData
		Récupère les informations propres à un formulaire
		
		Input : id de l'évaluation
		Output : array contenant les données sur le formulaire
	*/
	
	function eval_getSurveyData ($evaluationId) {

		global $db; // permet l'accès à la BDD
		$surveyData = array(); // Array contenant les données
		
		// On récupère les données
		$sql = 'SELECT id id, surveyLink link, surveyCode code FROM eval_survey_settings WHERE evaluation = ? LIMIT 1';
		$res = $db -> prepare($sql);
		$res -> execute(array($evaluationId));
		
		if ($res_f = $res -> fetch())
		{
			$surveyData['id'] = $res_f['id'];
			$surveyData['link'] = $res_f['link'];
			$surveyData['code'] = $res_f['code'];
		}
		
		return $surveyData;
	}
	
	//Générer une chaine de caractère unique et aléatoire

	function eval_random($nbcar) {
		$str = "";
		$chaine = "abcdefghijklmnpqrstuvwxyABCDEFGHIJKLMNOPQRSUTVWXYZ0123456789";
		$nb_chars = strlen($chaine);

		for($i=0; $i<$nbcar; $i++)
		{
			$str .= $chaine[ rand(0, ($nb_chars-1)) ];
		}

		return $str;
	}
?>