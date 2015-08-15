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
		Contient les réglages du module
	**/
	
	define('ALLOW_LOGIN_AS', TRUE); // Autorise les connexion "en tant que" à charger le plugin, FALSE par défaut, l'administrateur ne pourra pas dans tout les cas valider l'évaluation à la place de l'utilisateur
	define('CONFIG_EVAL_CCPC_DELAIDISPOEVAL', 30); // Délai en jours avant de permettre l'accès aux évaluations pour les étudiants, 30 jours par défaut
	define('CONFIG_EVAL_CCPC_NBEVALPARPAGE', 10); // Nombre d'évaluations à afficher par page, 10 par défaut
	
?>