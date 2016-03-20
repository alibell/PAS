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
		31/01/2016 - fnMail.php - Ali Bellamine
		Fonctions relatives au système d'envoi de mails
	*/
	
	/**
	  * getMailCampagnList - Retourne la liste des campagnes d'envoi de mail
	  *
	  * @category mailFunction
	  * @param string $order Paramètre selon lequel sont classés les résultats ('id', 'nom', 'date' ou 'remplissage')
	  * @param boolean $desc Ordre selon lequel on classe les résultats (TRUE : decroissant, FALSE : croissant)
	  * @return array Array contenant la liste des campagne de mails enregistrés
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	[id de la campagne de mail]['id'] => (int) Identifiant du certificat<br>
	  *	[id de la campagne de mail]['nom'] => (string) Nom de la campagne de mail<br>
	  *	[id de la campagne de mail]['codecampagne'] => (string) Identifiant unique de la campagne<br>
	  *	[id de la campagne de mail]['statut'] => (int) 0 : incomplète | 1 : complète<br>
	  *	[id de la campagne de mail]['date'] => (timestamp) Date de la campagne de mail<br>
	  *	[id de la campagne de mail]['nb']['0'] => (int) Nombre de mails non envoyés<br>
	  *	[id de la campagne de mail]['nb']['1'] => (int) Nombre de mails envoyés
	  *
	  */
	
	function getMailCampagnList ($order = 'nom', $desc = false) {
		global $db;
		
		$allowedOrder = array('id', 'nom', 'date', 'remplissage');
		if (in_array($order, $allowedOrder))
		{
			$orderSql = $order;
		}
		else
		{
			$orderSql = 'nom';
		}
		
		
		$sql = 'SELECT m.id id, m.nom nom, m.date date, m.codeCampagne codeCampagne, (SELECT count(*) FROM mail WHERE codeCampagne = m.codeCampagne AND statut = 1 LIMIT 1) nbEnvoye, (SELECT count(*) FROM mail WHERE codeCampagne = m.codeCampagne AND statut = 0 LIMIT 1) nbNonEnvoye, (SELECT count(*) FROM mail WHERE codeCampagne = m.codeCampagne AND statut = 1 LIMIT 1) / ((SELECT count(*) FROM mail WHERE codeCampagne = m.codeCampagne AND statut = 1 LIMIT 1) + (SELECT count(*) FROM mail WHERE codeCampagne = m.codeCampagne AND statut = 0 LIMIT 1)) remplissage FROM mail m GROUP BY m.codeCampagne ORDER BY '.$orderSql.' ';;
		if ($desc) { $sql .= ' DESC'; }
		$res = $db -> query($sql);
		
		$mail = array();
		while($res_f = $res -> fetch())
		{
			$mail[$res_f['id']]['id'] = $res_f['id'];
			$mail[$res_f['id']]['nom'] = $res_f['nom'];
			$mail[$res_f['id']]['codeCampagne'] = $res_f['codeCampagne'];
			$mail[$res_f['id']]['date'] = DatetimeToTimestamp($res_f['date']);
			if ($res_f['remplissage'] < 1) { $mail[$res_f['id']]['statut'] = 0; } else { $mail[$res_f['id']]['statut'] = 1; }
			$mail[$res_f['id']]['nb'][0] = $res_f['nbNonEnvoye'];
			$mail[$res_f['id']]['nb'][1] = $res_f['nbEnvoye'];
		}
		
		return $mail;
	}
	
	/**
	  * getMailCampaignData - Retourne les informations relatives à une campagne d'envoi de mail
	  *
	  * @category mailFunction
	  * @param string $id Code unique permettant d'identifier la campagne
	  * @return array Array contenant les informations relative à la campagne
	  *
	  * @Author Ali Bellamine
	  *
	  * Contenu de l'array retourné :<br>
	  *	['id'] => (int) Identifiant du certificat<br>
	  *	['nom'] => (string) Nom de la campagne de mail<br>
	  *	['date'] => (timestamp) Date de la campagne de mail<br>
	  *	['statut'] => (int) 0 : incomplète | 1 : complète<br>
	  *	['nb']['0'] => (int) Nombre de mails non envoyés<br>
	  *	['nb']['1'] => (int) Nombre de mails envoyés<br> 
	  *	['destinataires'][id du destinataire]['id'] => (id) Identifiant du destinataire<br>
	  *	['destinataires'][id du destinataire]['objet'] => (string) Objet du mail<br>
	  *	['destinataires'][id du destinataire]['message'] => (string) Contenu du mail<br>
	  *	['destinataires'][id du destinataire]['statut'] => (int) Statut du mail (envoyé / non envoyé)<br>
	  *	['destinataires'][id du destinataire]['attachments'] => (array) Array contenant les informations relatives aux pièces jointes<br>
	  *	['destinataires'][id du destinataire]['erreurs'] => (array) Array contenant la liste des codes d'erreurs rencontrés lors de l'envoie de mails
	  *
	  */
	
	function getMailCampaignData ($id) {

		global $db;

		$sql = 'SELECT m.id id, m.codeCampagne codeCampagne, m.nom nom, m.destinataire destinataire, m.objet objet, m.message message, m.piecejointes piecejointes, m.erreurs erreurs, m.statut statut, m.date date FROM mail m WHERE m.codeCampagne = ?';
		$res = $db -> prepare ($sql);
		$res -> execute(array($id));

		$mail = array();
		$mail['nb'][0] = 0; $mail['nb'][1] = 0; // On incrémente le statut des mails
		$mail['statut'] = 1; // Vrai jusqu'à preuve du contraire
		$listeConstante = array('id', 'codeCampagne', 'nom', 'date'); // Liste des variables identiques pour tous les mails
		
		$sqlResult = FALSE;

		while ($res_f = $res -> fetch())
		{
			$sqlResult = TRUE; // Indique que la requête a donné un résultat

			// On remplit ce qui est déjà constant pour toutes les campagnes de mail
			foreach ($listeConstante AS $constante)
			{
				if (!isset($mail[$constante])) { $mail[$constante] = $res_f[$constante]; }
			}
			
			$mail['nb'][$res_f['statut']] ++;
			if ($res_f['statut'] == 0) { $mail['statut'] = 0; }
			
			$mail['destinataires'][$res_f['destinataire']]['messageId'] = $res_f['id'];
			$mail['destinataires'][$res_f['destinataire']]['id'] = $res_f['destinataire'];
			$mail['destinataires'][$res_f['destinataire']]['objet'] = $res_f['objet'];
			$mail['destinataires'][$res_f['destinataire']]['message'] = $res_f['message'];
			$mail['destinataires'][$res_f['destinataire']]['statut'] = $res_f['statut'];
			if (isset($res_f['piecejointes']) && $res_f['piecejointes'] != '')
			{
				$mail['destinataires'][$res_f['destinataire']]['attachments'] = unserialize($res_f['piecejointes']);
			}
			else
			{
				$mail['destinataires'][$res_f['destinataire']]['attachments'] = array();
			}
			if (isset($res_f['erreurs']) && $res_f['erreurs'] != '')
			{
				$mail['destinataires'][$res_f['destinataire']]['erreurs'] = unserialize($res_f['erreurs']);
			}
			else
			{
				$mail['destinataires'][$res_f['destinataire']]['erreurs'] = array();
			}
		}
		
		if ($sqlResult)
		{
			return $mail;			
		}
		else
		{
			return FALSE;
		}
	}	
	
	/**
	  * sendMailFromCampaign - Envoie un mail à partir de son ID
	  *
	  * @category mailFunction
	  * @param string $id Code unique permettant d'identifier le mail
	  * @return boolean
	  *
	  * @Author Ali Bellamine
	  *
	  *
	  */
	
	function sendMailFromCampaign ($id) {

		global $db;
		if (isset($id) && is_numeric($id))
		{
			$sql = 'SELECT m.destinataire destinataire, m.objet objet, m.message message, m.piecejointes piecejointes, u.mail mail FROM mail m INNER JOIN user u ON u.id = m.destinataire WHERE m.id = ?';
			$res = $db -> prepare ($sql);
			$res -> execute(array($id));
			
			if ($res) {
				while ($res_f = $res -> fetch())
				{
					
					// On stocke les emails
					$destinataire = unserialize($res_f['mail']);
					
					// Ajout des pièces jointes
					$attachment = array();
					if (isset($res_f['piecejointes']) && is_array(unserialize($res_f['piecejointes'])))
					{
						$tempAttachment = unserialize($res_f['piecejointes']);
						foreach ($tempAttachment AS $key => $value)
						{
							$attachment[] = array('name' => $key, 'path' => $value['path']);
						}
					}

					
					// Envoie de l'email
					$erreur = sendMail($destinataire, $res_f['objet'], $res_f['message'], array(), $attachment);
					print_r($erreur);
					// Traitement des erreurs
					if (count($erreur) > 0)
					{
						$res2 = $db -> prepare('UPDATE mail SET erreurs = ? WHERE id = ?');
						$res2 -> execute(array(serialize($erreur), $id));
						return FALSE;
					}
					else
					{
						$res2 = $db -> prepare('UPDATE mail SET statut = 1, erreurs = ? WHERE id = ?');
						$res2 -> execute(array(serialize(array()), $id));
						return TRUE;
					}
				}
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
	  * addMailCampagne - Ajoute un mail dans une campagne
	  *
	  * @category mailFunction
	  * @param string $code Code unique de la campagne de mails
	  * @param string $nom Nom de la campagne de mails	  
	  * @param int $destinataire ID du destinataire
	  * @param string $objet Objet du mail
	  * @param string $message Contenu du mail
	  * @param array $pj Array contenant les données de pièce jointe
	  * @param array $erreur Array contenant la liste des codes d'erreurs
	  * @return array Liste des codes d'erreurs
	  *
	  * @Author Ali Bellamine
	  *
	  *
	  */
	
	function addMailCampagne ($code, $nom, $destinataire, $objet, $message, $pj = array(), $erreur = array()) {

		global $db;
		
		/*
			1. On verifie les données
		*/
		
		$check = true;
		
		// Verification de l'utilisateur
		if (isset($destinataire))
		{
			$erreur = checkUser($destinataire, $erreur);
		}
		else
		{
			$erreur[1] = true;
		}
		
		// On vérifie que pièce jointe est un array
		if (!isset($pj) || !is_array($pj))
		{
			$check = false;
		}
		
		if ($check && count($erreur) == 0)
		{
			$sql = 'INSERT INTO mail (nom, codeCampagne, statut, destinataire, objet, message, piecejointes, date, erreurs) VALUES (?, ?, 0, ?, ?, ?, ?, NOW(), ?)';
			$res = $db -> prepare ($sql);
			$res -> execute(array($nom, $code, $destinataire, $objet, $message, serialize($pj), serialize(array())));
			
			return TRUE;
		}
		else
		{
			return($erreur);
		}
	}	
?>