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

	// Requête SQL
	$sql = '';
	
	// affectationexterne
	$sql .= 'CREATE TABLE IF NOT EXISTS `affectationexterne` (
					`id` int(11) NOT NULL,
					`userId` int(11) NOT NULL,
					`service` int(11) NOT NULL,
					`dateDebut` date NOT NULL,
					`dateFin` date NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$sql .= 'ALTER TABLE `affectationexterne`
					ADD PRIMARY KEY (`id`), ADD KEY `service` (`service`), ADD KEY `userId` (`userId`);';
	$sql .= 'ALTER TABLE `affectationexterne`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
					
	// bug
	$sql .= 'CREATE TABLE IF NOT EXISTS `bug` (
					`bugId` int(11) NOT NULL,
					`bugServerData` text NOT NULL,
					`bugSessionVariable` text NOT NULL,
					`bugDescription` text NOT NULL,
					`bugState` int(11) NOT NULL DEFAULT \'0\',
					`bugDate` datetime NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$sql .= 'ALTER TABLE `bug`
					ADD PRIMARY KEY (`bugId`);';
	$sql .= 'ALTER TABLE `bug`
					MODIFY `bugId` int(11) NOT NULL AUTO_INCREMENT;';
					
	// certificat
	$sql .= 'CREATE TABLE IF NOT EXISTS `certificat` (
					`id` int(11) NOT NULL,
					`nom` varchar(255) NOT NULL,
					`promotion` int(11) DEFAULT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$sql .= 'ALTER TABLE `certificat`
					ADD PRIMARY KEY (`id`), ADD KEY `responsable` (`promotion`);';
	$sql .= 'ALTER TABLE `certificat`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
					
	// erreur
	$sql .= 'CREATE TABLE IF NOT EXISTS `erreur` (
					`id` int(11) NOT NULL,
					`msg` varchar(255) NOT NULL
					) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;';
	$sql .= "INSERT INTO `erreur` (`id`, `msg`) VALUES
		(1, 'LANG_ERROR_USER_INVALID'),
		(2, 'LANG_ERROR_SERVICE_INVALID'),
		(4, 'LANG_ERROR_EVALUATION_INVALID'),
		(5, 'LANG_ERROR_EVALUATIONTYPE_INVALID'),
		(6, 'LANG_ERROR_EVALUATION_DONE'),
		(7, 'LANG_ERROR_NAVIGATION_NORIGHT'),
		(8, 'LANG_ERROR_IMPORT_FILE'),
		(9, 'LANG_ERROR_CHECK_NB_ETUDIANT'),
		(10, 'LANG_ERROR_AFFECTATION_SERVICE_ALREADY'),
		(11, 'LANG_ERROR_AFFECTATION_DATE_DEBUT'),
		(12, 'LANG_ERROR_AFFECTATION_DATE_FIN'),
		(13, 'LANG_ERROR_AFFECTATION_DATE_MISMATCH'),
		(14, 'LANG_ERROR_UNKNOWN'),
		(15, 'LANG_ERROR_AFFECTATION_INVALID'),
		(16, 'LANG_ERROR_IMPORT_FILE_INVALID'),
		(17, 'LANG_ERROR_DATA_INVALID_FORMAT'),
		(18, 'LANG_ERROR_DATE_MISMATCH'),
		(19, 'LANG_ERROR_MAIL_INVALID'),
		(20, 'LANG_ERROR_MAIL_NOSETTINGS'),
		(21, 'LANG_ERROR_MAIL_SEND'),
		(22, 'LANG_ERROR_URL_INVALID'),
		(23, 'LANG_ERROR_EVALUATION_NOSETTINGS'),
		(24, 'LANG_ERROR_EVALUATION_WRONGDATE'),
		(25, 'LANG_ERROR_FORM_INCOMPLETE'),
		(26, 'LANG_ERROR_DATA_INCOMPLETE'),
		(27, 'LANG_ERROR_PROMOTION_INEXISTANT'),
		(28, 'LANG_ERROR_RANG_BATCH_INVALID'),
		(29, 'LANG_ERROR_HOSPITAL_INVALID'),
		(30, 'LANG_ERROR_CERTIFICATE_INVALID'),
		(31, 'LANG_ERROR_SPECIALITE_INVALID'),
		(32, 'LANG_ERROR_NOT_REGISTERED'),
		(33, 'LANG_ERROR_EVAL_LOGINAS_FORBIDDEN');";
	$sql .= 'ALTER TABLE `erreur`
					ADD PRIMARY KEY (`id`);';
	$sql .= 'ALTER TABLE `erreur`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;';
	
	// evaluation
	
	$sql .= 'CREATE TABLE IF NOT EXISTS `evaluation` (
				  `id` int(11) NOT NULL,
				  `nom` varchar(255) NOT NULL,
				  `type` int(11) NOT NULL,
				  `dateDebut` datetime NOT NULL,
				  `dateFin` datetime DEFAULT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$sql .= 'ALTER TABLE `evaluation`
					ADD PRIMARY KEY (`id`), ADD KEY `certificat` (`type`), ADD KEY `type` (`type`);';
	$sql .= 'ALTER TABLE `evaluation`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
	
	//evaluationregister
	
	$sql .= 'CREATE TABLE IF NOT EXISTS `evaluationregister` (
				  `id` int(11) NOT NULL,
				  `evaluationId` int(11) NOT NULL,
				  `userId` int(11) NOT NULL,
				  `evaluationData` text NOT NULL,
				  `evaluationStatut` int(11) NOT NULL DEFAULT \'0\',
				  `date` datetime DEFAULT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$sql .= 'ALTER TABLE `evaluationregister`
					ADD PRIMARY KEY (`id`), ADD KEY `evaluationId` (`evaluationId`,`userId`), ADD KEY `userId` (`userId`);';
	$sql .= 'ALTER TABLE `evaluationregister`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
					
	// hopital
	
	$sql .= 'CREATE TABLE IF NOT EXISTS `hopital` (
				  `id` int(11) NOT NULL,
				  `nom` varchar(255) NOT NULL,
				  `alias` varchar(10) NOT NULL,
				  `rue` text,
				  `cp` varchar(5) DEFAULT NULL,
				  `ville` varchar(255) DEFAULT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$sql .= 'ALTER TABLE `hopital`
					ADD PRIMARY KEY (`id`);';
	$sql .= 'ALTER TABLE `hopital`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
					
	// mainMenu
	
	$sql .= 'CREATE TABLE IF NOT EXISTS `mainMenu` (
				  `id` int(11) NOT NULL,
				  `name` varchar(255) NOT NULL,
				  `menuOrder` int(11) NOT NULL
				) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;';
	$sql .= "INSERT INTO `mainMenu` (`id`, `name`, `menuOrder`) VALUES
					(1, 'LANG_MENU_MAIN_MYSERVICE', 2),
					(2, 'LANG_MENU_MAIN_EVALUATION', 3),
					(3, 'LANG_MENU_MAIN_ACCUEIL', 1),
					(4, 'LANG_MENU_MAIN_ADMINISTRATION', 999);";
	$sql .= 'ALTER TABLE `mainMenu`
					ADD PRIMARY KEY (`id`);';
	$sql .= 'ALTER TABLE `mainMenu`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;';
					
	// page
	
	$sql .= 'CREATE TABLE IF NOT EXISTS `page` (
				  `id` int(11) NOT NULL,
				  `name` varchar(255) NOT NULL,
				  `alias` varchar(255) NOT NULL,
				  `file` varchar(255) NOT NULL,
				  `css` varchar(255) NOT NULL,
				  `right0` int(1) NOT NULL,
				  `right1` int(1) NOT NULL,
				  `right2` int(1) NOT NULL,
				  `right3` int(1) NOT NULL,
				  `right4` int(1) NOT NULL
				) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;';
	$sql .= "INSERT INTO `page` (`id`, `name`, `alias`, `file`, `css`, `right0`, `right1`, `right2`, `right3`, `right4`) VALUES
					(1, 'LANG_PAGE_INDEX', 'index', 'index.php', '', 1, 1, 1, 1, 1),
					(2, 'LANG_PAGE_EVAL', 'eval', 'content/evaluation/index.php', '', 0, 1, 1, 1, 1),
					(3, 'LANG_PAGE_LOGIN', 'login', 'login.php', '', 0, 1, 1, 1, 1),
					(4, 'LANG_PAGE_EVALDO', 'evalDo', 'content/evaluation/view.php', '', 0, 1, 1, 1, 1),
					(5, 'LANG_PAGE_EVALVIEW', 'evalView', 'content/evaluation/viewResult.php', 'viewResult.css', 0, 1, 1, 1, 1),
					(7, 'LANG_PAGE_ADMINUTILISATEURS', 'adminUtilisateurs', 'admin/utilisateurs/index.php', 'admin.css', 0, 0, 0, 0, 1),
					(8, 'LANG_PAGE_ADMINSERVICES', 'adminServices', 'admin/services/index.php', 'admin.css', 0, 0, 0, 0, 1),
					(9, 'LANG_PAGE_ADMINEVALUATIONS', 'adminEvaluation', 'admin/evaluations/index.php', 'admin.css', 0, 0, 0, 0, 1),
					(10, 'LANG_PAGE_ADMINUSERLIST', 'ajaxUserList', 'ajax/ajaxUserList.php', '', 0, 0, 1, 1, 1),
					(11, 'LANG_PAGE_MYSTUDENT', 'mystudent', 'content/service/etudiant.php', '', 0, 0, 1, 1, 1),
					(12, 'LANG_PAGE_SETTINGS', 'settings', 'admin/setting.php', 'admin.css', 0, 0, 0, 0, 1),
					(13, 'LANG_PAGE_AJAXBUG', 'ajaxBug', 'ajax/ajaxBug.php', '', 1, 1, 1, 1, 1),
					(14, 'LANG_PAGE_BUG_ADMIN', 'bugAdmin', 'admin/bug.php', '', 0, 0, 0, 0, 1),
					(15, 'LANG_PAGE_ABOUT', 'about', 'about.php', '', '1', '1', '1', '1', '1'),
					(16, 'LANG_PAGE_CHARTE', 'adminChart', 'admin/chart.php', 'admin.css', '0', '0', '0', '1', '1');";
	$sql .= 'ALTER TABLE `page`
					ADD PRIMARY KEY (`id`);';
	$sql .= 'ALTER TABLE `page`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;';
					
	// promotion
	
	$sql .= 'CREATE TABLE IF NOT EXISTS `promotion` (
				  `id` int(11) NOT NULL,
				  `nom` varchar(255) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$sql .= 'ALTER TABLE `promotion`
					ADD PRIMARY KEY (`id`);';
	$sql .= 'ALTER TABLE `promotion`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
					
	// secondaryMenu
	$sql .= 'CREATE TABLE IF NOT EXISTS `secondaryMenu` (
				  `id` int(11) NOT NULL,
				  `mainMenuId` int(11) NOT NULL,
				  `name` varchar(255) NOT NULL,
				  `page` int(11) NOT NULL,
				  `menuOrder` int(11) NOT NULL
				) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;';
	$sql .= "INSERT INTO `secondaryMenu` (`id`, `mainMenuId`, `name`, `page`, `menuOrder`) VALUES
					(1, 1, 'LANG_MENU_SECONDARY_MYSTUDENT', 11, 1),
					(3, 3, 'LANG_MENU_SECONDARY_ACCUEIL', 1, 1),
					(4, 2, 'LANG_MENU_SECONDARY_VIEWEVAL', 5, 2),
					(5, 4, 'LANG_MENU_SECONDARY_USERS', 7, 1),
					(6, 4, 'LANG_MENU_SECONDARY_SERVICES', 8, 2),
					(7, 4, 'LANG_MENU_SECONDARY_EVALUATIONS', 9, 3),
					(8, 2, 'LANG_MENU_SECONDARY_MYEVAL', 2, 1),
					(9, 4, 'LANG_MENU_SECONDARY_SETTINGS', 12, 6),
					(10, 4, 'LANG_MENU_SECONDARY_BUG_MANAGER', 14, 4),
					(11, 4, 'LANG_MENU_SECONDARY_CHARTE', 16, 5);";
	$sql .= 'ALTER TABLE `secondaryMenu`
					ADD PRIMARY KEY (`id`), ADD KEY `mainMenuId` (`mainMenuId`,`page`), ADD KEY `page` (`page`);';
	$sql .= 'ALTER TABLE `secondaryMenu`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;';
	
	// service
	
	$sql .= 'CREATE TABLE IF NOT EXISTS `service` (
				  `id` int(11) NOT NULL,
				  `hopital` int(11) NOT NULL,
				  `specialite` int(11) DEFAULT NULL,
				  `nom` varchar(255) NOT NULL,
				  `chef` int(11) DEFAULT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$sql .= 'ALTER TABLE `service`
					ADD PRIMARY KEY (`id`), ADD KEY `hopital` (`hopital`), ADD KEY `chef` (`chef`), ADD KEY `specialite` (`specialite`);';
	$sql .= 'ALTER TABLE `service`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
	
	// servicecertificat
	
	$sql .= 'CREATE TABLE IF NOT EXISTS `servicecertificat` (
				  `id` int(11) NOT NULL,
				  `idService` int(11) NOT NULL,
				  `idCertificat` int(11) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$sql .= 'ALTER TABLE `servicecertificat`
					ADD PRIMARY KEY (`id`), ADD KEY `idService` (`idService`), ADD KEY `idCertificat` (`idCertificat`);';
	$sql .= 'ALTER TABLE `servicecertificat`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
					
	// setting
	
	$sql .= 'CREATE TABLE IF NOT EXISTS `setting` (
				  `id` int(11) NOT NULL,
				  `alias` varchar(255) NOT NULL,
				  `valeur` TEXT NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$sql .= "INSERT INTO `setting` (`alias`, `valeur`) VALUES
					('CHARTE', '')";
	$sql .= 'ALTER TABLE `setting`
					ADD PRIMARY KEY (`id`);';
	$sql .= 'ALTER TABLE `setting`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
	
	// specialite
	
	$sql .= 'CREATE TABLE IF NOT EXISTS `specialite` (
				  `id` int(11) NOT NULL,
				  `nom` varchar(255) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$sql .= 'ALTER TABLE `specialite`
					ADD PRIMARY KEY (`id`);';
	$sql .= 'ALTER TABLE `specialite`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
					
	// typeevaluation
	
	$sql .= 'CREATE TABLE IF NOT EXISTS `typeevaluation` (
				  `id` int(11) NOT NULL,
				  `nom` varchar(255) NOT NULL,
				  `settings` text NOT NULL,
				  `nomDossier` varchar(255) NOT NULL,
				  `actif` int(11) NOT NULL,
				  `result_access_1` int(11) NOT NULL,
				  `result_access_2` int(11) NOT NULL,
				  `result_access_3` int(11) NOT NULL,
				  `result_access_4` int(11) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$sql .= 'ALTER TABLE `typeevaluation`
					ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `nomDossier` (`nomDossier`);';
	$sql .= 'ALTER TABLE `typeevaluation`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
					
	// user
	
	$sql .= 'CREATE TABLE IF NOT EXISTS `user` (
				  `id` int(11) NOT NULL,
				  `nbEtudiant` varchar(255) DEFAULT NULL,
				  `nom` varchar(255) NOT NULL,
				  `prenom` varchar(255) NOT NULL,
				  `mail` text NOT NULL,
				  `promotion` int(11) DEFAULT NULL,
				  `rang` int(11) NOT NULL DEFAULT \'1\'
				) ENGINE=InnoDB AUTO_INCREMENT=273 DEFAULT CHARSET=latin1;';
	$sql .= 'ALTER TABLE `user`
					ADD PRIMARY KEY (`id`), ADD KEY `promotion` (`promotion`);';
	$sql .= 'ALTER TABLE `user`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
					
	// mail
	
	$sql .= 'CREATE TABLE IF NOT EXISTS `mail` (
				  `id` int(11) NOT NULL,
				  `nom` varchar(255) DEFAULT NULL,
				  `codeCampagne` varchar(255) NOT NULL,
				  `statut` int(11) NOT NULL,
				  `destinataire` int(11) NOT NULL,
				  `objet` text DEFAULT NULL,
				  `message` text DEFAULT NULL,
				  `piecejointes` text DEFAULT NULL,
				  `date` datetime DEFAULT NULL,
				  `erreurs` varchar(255) NOT NULL DEFAULT \'1\'
				) ENGINE=InnoDB AUTO_INCREMENT=273 DEFAULT CHARSET=latin1;';
	$sql .= 'ALTER TABLE `mail`
					ADD PRIMARY KEY (`id`)), ADD KEY `destinataire` (`destinataire`);';
	$sql .= 'ALTER TABLE `mail`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
					
	// On ajoute toutes les foreign key d'un coup
	$sql .= 'ALTER TABLE `user`
					ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`promotion`) REFERENCES `promotion` (`id`) ON DELETE SET NULL;';
	$sql .= 'ALTER TABLE `servicecertificat`
					ADD CONSTRAINT `servicecertificat_ibfk_1` FOREIGN KEY (`idService`) REFERENCES `service` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
					ADD CONSTRAINT `servicecertificat_ibfk_2` FOREIGN KEY (`idCertificat`) REFERENCES `certificat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;';
	$sql .= 'ALTER TABLE `service`
					ADD CONSTRAINT `service_ibfk_1` FOREIGN KEY (`hopital`) REFERENCES `hopital` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
					ADD CONSTRAINT `service_ibfk_2` FOREIGN KEY (`chef`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
					ADD CONSTRAINT `service_ibfk_4` FOREIGN KEY (`specialite`) REFERENCES `specialite` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;';
	$sql .= 'ALTER TABLE `secondaryMenu`
					ADD CONSTRAINT `menu` FOREIGN KEY (`mainMenuId`) REFERENCES `mainMenu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
					ADD CONSTRAINT `secondaryMenu_ibfk_1` FOREIGN KEY (`page`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;';
	$sql .= 'ALTER TABLE `evaluationregister`
					ADD CONSTRAINT `evaluationregister_ibfk_1` FOREIGN KEY (`evaluationId`) REFERENCES `evaluation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
					ADD CONSTRAINT `evaluationregister_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;';
	$sql .= 'ALTER TABLE `evaluation`
					ADD CONSTRAINT `evaluation_ibfk_4` FOREIGN KEY (`type`) REFERENCES `typeevaluation` (`id`);';
	$sql .= 'ALTER TABLE `certificat`
					ADD CONSTRAINT `certificat_ibfk_1` FOREIGN KEY (`promotion`) REFERENCES `promotion` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;';
	$sql .= 'ALTER TABLE `affectationexterne`
					ADD CONSTRAINT `affectationexterne_ibfk_1` FOREIGN KEY (`service`) REFERENCES `service` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
					ADD CONSTRAINT `affectationexterne_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;';
	$sql .= 'ALTER TABLE `mail`
					ADD CONSTRAINT `mail_ibfk_1` FOREIGN KEY (`destinataire`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;';
					
	// $sqlRemove : annule tout ce qui a été installé dans la BDD
	$sqlRemove = '';
	$sqlRemove .= 'SET FOREIGN_KEY_CHECKS=0;';
	$sqlRemove .= 'DROP TABLE IF EXISTS setting;';
	$sqlRemove .= 'DROP TABLE IF EXISTS bug;';
	$sqlRemove .= 'DROP TABLE IF EXISTS erreur;';
	$sqlRemove .= 'DROP TABLE IF EXISTS secondaryMenu;';
	$sqlRemove .= 'DROP TABLE IF EXISTS mainMenu;';
	$sqlRemove .= 'DROP TABLE IF EXISTS page;';
	$sqlRemove .= 'DROP TABLE IF EXISTS affectationexterne;';
	$sqlRemove .= 'DROP TABLE IF EXISTS evaluationregister;';
	$sqlRemove .= 'DROP TABLE IF EXISTS servicecertificat;';
	$sqlRemove .= 'DROP TABLE IF EXISTS evaluation;';
	$sqlRemove .= 'DROP TABLE IF EXISTS typeevaluation;';
	$sqlRemove .= 'DROP TABLE IF EXISTS service;';
	$sqlRemove .= 'DROP TABLE IF EXISTS user;';
	$sqlRemove .= 'DROP TABLE IF EXISTS hopital;';
	$sqlRemove .= 'DROP TABLE IF EXISTS specialite;';
	$sqlRemove .= 'DROP TABLE IF EXISTS certificat;';
	$sqlRemove .= 'DROP TABLE IF EXISTS promotion;';
	$sqlRemove .= 'SET FOREIGN_KEY_CHECKS=1;';
?>
