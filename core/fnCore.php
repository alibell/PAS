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
		25/02/15 - fnCore.php - Ali Bellamine
		Fonctions diverses
	*/

	/**
	  * displayErreur - Affiche les erreurs à partir d'un array contenant les codes d'erreur
	  *
	  * @category : coreFunction
	  * @param array $erreur Array contenant la liste des codes d'erreur à afficher
	  *
	  * @Author Ali Bellamine
	  */

function displayErreur($erreur)
{
	global $db;
	$n = 0;
	
	if (count($erreur) > 0) {
	foreach ($erreur AS $codeErreur => $valeurErreur)
	{
		if (is_numeric($codeErreur))
		{
			$sql = 'SELECT msg
						FROM erreur WHERE id = ?';
			$res = $db -> prepare($sql);
			$res -> execute(array($codeErreur));
			if ($res_f = $res -> fetch())
			{
				// Si $n = 0 on crée le <ul>
				if ($n == 0)
				{
					?>
					<ul class = "erreur">
					<?php
				}
				?>
					<li><?php echo constant($res_f['msg']); ?></li>
				<?php
					$n++; // On incrémente $n
			}
			}
	} }
	
	// Si on a écrit au moins un message d'erreur : on ferme le <ul>
	if ($n != 0)
	{
		?>
			</ul>
		<?php
	}
}

	/**
	  * SendMail - Envoie un email
	  *
	  * @category : coreFunction
	  * @param array|string $destinataire Destinataire du message, si string : fournir l'adresse email, si array : key = adresse email, value : nom du destinataire 
	  * @param string $titre Objet du mail
	  * @param string $corps Contenu du mail
	  * @param array $erreur Array contenant la liste des erreurs rencontrées avant execution de la fonction.
	  * @param array $attach Array contenant la liste des pièces jointe à ajouter au mail, de la forme : [] => array('name' => nom de la pièce jointe, 'path' => chemin local du fichier)
	  * @return array Array contenant la liste des erreurs rencontrées après execution de la fonction
	  *
	  * @Author Ali Bellamine
	  */

function sendMail ($destinataire, $titre, $corps, $erreur = array(), $attach = array()) {
	if (isset($destinataire) && isset($titre) && isset($corps))
	{
		if (defined('MAIL_SMTP_HOST') && constant('MAIL_SMTP_HOST') != '' && defined('MAIL_SMTP_LOGIN') && constant('MAIL_SMTP_LOGIN') != '' && defined('MAIL_SMTP_PORT') && defined('MAIL_SMTP_PASSWORD') && constant('MAIL_SMTP_PASSWORD') != '')
		{
			// Connexion au SMTP
			if (MAIL_SMTP_PORT == 587 || MAIL_SMTP_PORT == 465) { $ssl = 'ssl'; } else { $ssl = ''; }
			if ($transport = Swift_SmtpTransport::newInstance(MAIL_SMTP_HOST, MAIL_SMTP_PORT, $ssl) ->setUsername(MAIL_SMTP_LOGIN) ->setPassword(MAIL_SMTP_PASSWORD))
			{
				// Si le destinataire n'est pas un array
				if (!is_array($destinataire))
				{
					$destinataire = array($destinataire);
				}
				
				// On écrit le message
				$message = Swift_Message::newInstance()
					->setSubject($titre)
					->setFrom(MAIL_SMTP_LOGIN)
					->setTo($destinataire)
					->setBody($corps, 'text/html');
				
				// Pièces jointes
				if (isset($attach) && count ($attach) > 0)
				{
					foreach ($attach AS $attachement)
					{
						if (isset($attachement['name']) && isset($attachement['path']) && is_file($attachement['path']))
						{
							$message->attach(
								Swift_Attachment::fromPath($attachement['path'])->setFilename($attachement['name'])
							);
						}
					}
				}
					
				 $mailer = Swift_Mailer::newInstance($transport);
				 $mailer->send($message);
			}
			else
			{
				$erreur[21];
			}
		}
		else
		{
			$erreur[20] = true;
		}
	}
	
	return $erreur;
}


	/**
	  * getCurrentPageData - Information concernant la page chargée par l'utilisateur
	  *
	  * @category : coreFunction
	  * @return array Array contenant les informations contenant la chargée par l'utilisateur
	  *
	  * @Author Ali Bellamine
	  *
	  * Informations contenues dans l'array :<br>
	  *  	'id' => (int) Identifiant de la page<br>
	  *		'file' => (string) Nom du fichier<br>
	  *		'name' => (string) Nom de la page<br>
	  * 	'css' => (string) Nom du fichier CSS spécifique à la page<br>
	  *		'right' => (int) 0 ou 1, traduit si l'utilisateur a droit d'accèder à la page (1) ou non (0)<br>
	  *		'fullRight' => array contenant les droits pour chaque rang d'utilisation (0 à 4)
	  */

function getCurrentPageData () {
	global $db; // Accès à la BDD
	$page = array();
	
	$sql = 'SELECT * 
				FROM page
				WHERE file = :currentFile
				LIMIT 1';
	$res = $db -> prepare($sql);
	$res -> execute(array('currentFile' => CURRENT_FILE));
	if ($res_f = $res -> fetch())
	{
		$page['id'] = $res_f['id'];
		$page['file'] = $res_f['file'];
		if (defined($res_f['name']))
		{
			$page['name'] = constant($res_f['name']);
		}
		else
		{
			$page['name'] = $res_f['name'];
		}
		$page['css'] = $res_f['css'];
        if (isset($_SESSION['rang']))
        {
            $page['right'] = $res_f['right'.$_SESSION['rang']];
        }

        for($n = 0; $n <= 4; $n++)
        {
            $page['fullRight'][$n] = $res_f['right'.$n];
        }

		return $page;
	}
	else
	{
		return false;
	}
}



	/**
	  * isSerialized - Vérifie si une variable est une version serializé d'un array
	  *
	  * @category : coreFunction
	  * @param string $string Variable à tester
	  * @return boolean Résultat du test
	  *
	  * @Author Ali Bellamine
	  */

 function isSerialized($string) {
    return (@unserialize($string) !== false || $string == 'b:0;');
}
	
	/**
	  * DatetimeToTimestamp - Convertit une date du format Datetime au format Timestamp
	  *
	  * @todo Remplacer par class Timestamp de PHP
	  * @category : coreFunction
	  * @param string $date Date au format datetime
	  * @return string Date au format timestamp
	  *
	  * @Author Ali Bellamine
	  */

function DatetimeToTimestamp($date)
{
	$date = str_replace(array(' ', ':'), '-', $date);
	$c    = explode('-', $date);
	$c    = array_pad($c, 6, 0);
	array_walk($c, 'intval');
 
	return mktime($c[3], $c[4], $c[5], $c[1], $c[2], $c[0]);
}

	/**
	  * TimestampToDatetime - Convertit une date du format Timestamp au format Datetime
	  *
	  * @todo Remplacer par class Timestamp de PHP
	  * @category : coreFunction
	  * @param string $date Date au format timestamp
	  * @return string Date au format datetime
	  *
	  * @Author Ali Bellamine
	  */

function TimestampToDatetime($date)
{
	$datetime = date("Y-m-d H:i:s", $date);
	return $datetime;
}

	/**
	  * FrenchdateToDatetime - Convertit une date du format JJ/MM/AAAA au format Datetime
	  *
	  * @todo Remplacer par class Timestamp de PHP
	  * @category : coreFunction
	  * @param string $date Date au format JJ/MM/AAAA
	  * @return string Date au format datetime
	  *
	  * @Author Ali Bellamine
	  */

function FrenchdateToDatetime($date)
{
	$temp = explode('/', $date);
	$datetime = $temp[2].'-'.$temp[1].'-'.$temp[0];
	return $datetime;
}

	/**
	  * listeMoisEntreDeuxDates - Retourne la liste des mois et des années entre 2 dates
	  *
	  * @todo Remplacer par class Timestamp de PHP
	  * @category : coreFunction
	  * @param string $date1 Marge inférieure de l'intervalle, date au format Timestamp
	  * @param string $date2 Marge supérieure de l'intervalle, date au format Timestamp
	  * @return array Array contenant la liste des mois et année entre les 2 dates fournies
	  *
	  * @Author Ali Bellamine
	  */

function listeMoisEntreDeuxDates ($date1, $date2) {
	$my = date('mY', $date2);

	$months[date('nY', $date1)] = array('MoisNb' => date('n', $date1), 'Mois' => constant('LANG_MONTH_'.date('n', $date1)), 'Annee' => date('Y', $date1));

	while($date1 < $date2) {
		$date1 = strtotime(date('Y-m-d', $date1).' +1 month');
		if(date('mY', $date1) != $my && ($date1 < $date2))
		{
			if (!isset($months[date('nY', $date1)]))
			{
				$months[date('nY', $date1)] = array('MoisNb' => date('n', $date1), 'Mois' => constant('LANG_MONTH_'.date('n', $date1)), 'Annee' => date('Y', $date1));
			}	
		}
	}

	if (!isset($months[date('nY', $date2)]))
	{
		$months[date('nY', $date2)] = array('MoisNb' => date('n', $date2), 'Mois' => constant('LANG_MONTH_'.date('n', $date2)), 'Annee' => date('Y', $date2));
	}	

	return $months;
}

	/**
	  * getPageUrl - Retourne l'url d'une page à partir de son alias
	  *
	  * @category : coreFunction
	  * @param string $alias Alias de la page dont on souhaite récupérer l'URL
	  * @param array $get Array de valeurs à rajouter en variables $_GET à la fin de l'URL
	  * @return string URL de la page demandée accompagné des variables $_GET fournies
	  *
	  * @Author Ali Bellamine
	  */

function getPageUrl ($alias, $get = array()) {
	global $db;
	
	if (isset($alias) && $alias != '')
	{
		$sql = 'SELECT file FROM page WHERE alias = ? LIMIT 1';
		$res = $db -> prepare($sql);
		$res -> execute(array($alias));
		if ($res_f = $res -> fetch())
		{
			$url = ROOT.$res_f['file'].'?';
			if (isset($get) && is_array($get) && count($get) > 0)
			{
				$url .= http_build_query($get);
			}
			
			return $url;
		}
		else
		{
			return FALSE;
		}
	}
	else
	{
		return FALSE;
	}
}

	/**
	  * creerPagination - Crée une pagination
	  *
	  * @category : coreFunction
	  * @param int $nbDePage Nombre de page souhaité
	  * @param int $nbPageAutours Nombre de page à afficher autours de la page actuelle
	  * @param int $pageActuelle Page actuellement chargée
	  * @param int $nbMaxPage Nombre maximal de pages disponibles
	  * @return array Array contenant la liste des pages à proposer à l'utilisateur
	  *
	  * @Author Ali Bellamine
	  */

function creerPagination ($nbDePage, $nbPageAutours, $pageActuelle, $nbMaxPage) {
	$pagination = array();
	
	if ($nbMaxPage <= $nbDePage) // Dans ce cas on affiche toutes les pages
	{
		for ($i = 1; $i <= $nbMaxPage; $i++)
		{
			$pagination[$i] = true;
		}
	}
	else
	{
		// On affiche la première page
		$pagination[1] = true;
		
		// On affiche les pages autours de la page actuelle
		$i = $pageActuelle - $nbPageAutours;
		if ($i < 1)  { $i = 1; }
		$borneSup = $pageActuelle + $nbPageAutours;
		if ($borneSup > $nbMaxPage) { $borneSup = $nbMaxPage; }
		
		for ($i; $i <= $borneSup; $i++)
		{
			$pagination[$i] = true;
		}
		
		// On affiche la dernière page
		$pagination[$nbMaxPage] = true;
	}
	
	return $pagination;
}

	/**
	  * downloadCSV - Crée et retourne dans le navigateur de l'utilisateur un fichier CSV à partir d'un array de valeurs à inclure dans le fichier
	  *
	  * @category : coreFunction
	  * @param array $array Array contenant les valeurs à inclure dans le fichier CSV
	  * @param string $file Nom du fichier CSV
	  *
	  * @Author Ali Bellamine
	  */

	function downloadCSV ($array, $file = "file.csv") {
		ob_get_clean(); // On efface le output actuel
			
		// Création du CSV
		$csv = fopen("php://memory", "w");
		foreach ($array as $line) {
			fputcsv($csv, $line, ';');
		}
			
		fseek($csv, 0);
		header('Content-Type: application/csv; charset=UTF-8');
		header('Content-Encoding: UTF-8');
		header('Content-Disposition: attachement; filename="'.$file.'";');
		echo "\xEF\xBB\xBF"; // UTF-8 BOM
		fpassthru($csv);
		exit (); // Empêche l'execution du reste du script
	}
	
	/**
	  * readCSV - Lit un fichier CSV et retourne son contenu dans un Array
	  *
	  * @category : coreFunction
	  * @param string $file Chemin local vers le fichier CSV
	  * @param string $separateur Séparateur utilisé dans le fichier CSV, par défaut ';'
	  * @param boolean $first Si FALSE, la première ligne du fichier CSV est ignorée
	  * @return array Array contenant le contenu du fichier CSV
	  *
	  * @Author Ali Bellamine
	  */

	  
	function readCSV ($file, $separateur = ';', $first = TRUE) {			
		$arrayCSV = array();
		$n = 1;
		
		if (is_file($file) && $csv = fopen($file, 'r'))
		{
			while ($line = fgetcsv($csv, 0, $separateur))
			{
				if (($first && $n == 1) || $n > 1) 
				{
					$arrayCSV[] = $line;
				}
				$n++;
			}
		}
		
		return $arrayCSV;
	}
	
	/**
	  * downloadFILE - Force le télécharge d'un fichier dans le navigateur de l'utilisateur
	  *
	  * @category : coreFunction
	  * @param string $path Chemin local vers le fichier à faire télécharger
	  * @param string $name Nom du fichier souhaité
	  * @return boolean FALSE si l'opération a échoué
	  *
	  * @Author Ali Bellamine
	  */

	function downloadFILE ($path, $name = FALSE) {
		ob_end_clean();
		
		if (is_file($path))
		{
			// On récupère les infos sur le fichier
			$file = new finfo();
			$mime = $file -> file ($path, FILEINFO_MIME);
			$fileName = pathinfo($path);
			
			if (isset($name) && $name != '')
			{
				$thefile = $name;
			}
			else
			{
				$thefile = $fileName['basename'];
			}
			
			header('Content-Type: '.$mime.'; charset=UTF-8');
			header('Content-Encoding: UTF-8');
			header('Content-Disposition: attachement; filename="'.$thefile.'";');
			
			readfile($path);

			exit();
		}
		else { return FALSE; }
		exit();
	}
?>