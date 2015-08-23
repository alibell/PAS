PAS - Plateforme d'Appréciation des Stages
=======

PAS est un logiciel destiné à l'évaluation des stages effectués durant la formation universitaire en médecine.  
Suffisament souple, le programme peut rapidement être adapté à d'autres contextes d'utilisation.  

FONCTIONNALITES
-------

PAS présente les fonctionnalités suivante :

- Enregistrement d'étudiants pouvant être classés par promotion
- Enregistrement des enseignants
- Possibilité d'ajouter des utilisateurs à l'aide d'un fichier au format CSV
- Authentification à l'aide du système d'authentification unique (CAS) : http://jasig.github.io/
- Gestion de l'affectation des étudiants dans un service, possibilité d'importer des données d'affectation à l'aide d'un fichier au format CSV
- Gestion des services avec possibilité d'y renseigner l'hopital, la spécialité, l'enseignement dans le cadre duquel l'étudiant est amené à passer dans le service
- Possibilité de créer des campagnes d'évaluation et de cibler les étudiants / enseignants par promotion et / ou enseignement
- Envoi de mail de rappel aux étudiants n'ayant pas remplis les évaluations
- Possibilité pour les administrateurs de naviguer sur PAS comme si ils étaient connectés au nom d'un autre utilisateur. Il n'est alors pas possible d'effectuer des actions à la place de l'utilisateur. Lorsqu'un module d'évaluation est chargé, les droits de l'administrateur sont alors laissés à la discretion du créateur du module, par défaut la fonctionnalité est désactivé au sein des modules.

Les campagnes d'évaluation sont des instances d'un module d'évaluation.  
Les modules d'évaluation doivent être installés dans le dossier "evaluations" situé à la racine de PAS.

Les modules d'évaluation peuvent être réalisés de façon indépendante sans avoir à modifier le code de PAS.  
Pour plus d'informations consulter le fichier "CREER UN MODULE"

PAS est fournit avec 2 modules :
- Le module CCPC permettant de créer des campagnes d'évaluations à destination des étudiants propre aux services acceuillant ces derniers
- Le module survey permettant de créer des campagnes d'évaluation à partir de formulaires extérieurs, tel que Surveymonkey...
Pour plus d'information consulter le fichier LISEZMOI situé à la racine de chaque module d'évaluation.

PRE-REQUIS
-------

Pour fonctionner correctement, PAS doit être installé sur un serveur contenant une version de PHP >= 5.4 et d'une base de donnée Mysql.  
Les extensions suivantes doivent être installés et activés :
- GD avec FreeType
- zlib
- curl
- DOM
- openssl
- zip
- PDO avec pdo_mysql

INSTALLATION
-------

Un script d'installation est fournit avec PAS.  
Pour installer PAS, effectuez les étapes suivantes :

- Copiez l'intégralité des fichiers de PAS dans votre serveur
- Assurez-vous que votre serveur dispose des droits d'écritures dans le dossier "core" (CHMOD 755)
- Pour chaque module d'évaluation, vous êtes inviter à vérifier dans leurs fichiers LISEZMOI si ce dernier nécessite des réglages particuliers
- Rendez-vous ensuite avec votre navigateur sur la page d'installation à l'adresse suivante : http://www.votredomaine/install/
- Suivez les instructions
- Une fois l'installation terminé, supprimez le dossier "install" de votre serveur

LICENCE
-------

Auteur : Ali BELLAMINE  
Ce programme est sous licence GPL v3  
Pour plus d'informations consulter le fichier LICENCE  
Pour plus d'informations sur la licence GPL, rendez-vous à l'adresse suivante : http://www.gnu.org/licences/gpl-3.0.en.html  

Le programme inclut les librairies suivantes :

- Librairies PHP :
phpCAS 1.3.3 : sous licence Apache 2.0, disponible à l'adresse suivante : https://github.com/Jasig/phpCAS  
swiftMailer 5.4.1 : sous licence Creative Commons Attribution-Share Alike 3.0, disponible à l'adresse suivante : http://swiftmailer.org/  
htmLawed 1.1.20 : sous licence LGPL, disponible à l'adresse suivante : http://www.bioinformatics.org/phplabware/internal_utilities/htmLawed/  

- Librairies Javascript :
jQuery 2.1.3 : sous licence MIT, disponible à l'adresse suivante : https://jquery.com/  
glDatePicker 2.0 : sous licence MIT, disponible à l'adresse suivante : http://glad.github.io/glDatePicker/  
featherlight 1.3.1 : sous licence MIT, disponible à l'adresse suivante : http://noelboss.github.io/featherlight/  
tinyMCE 4.2.1 : sous licence LGPL, disponible à l'adresse suivante : http://www.tinymce.com/  
moment 2.10.2: sous licence MIT, disponible à l'adresse suivante : http://momentjs.com/  
daterangepicker 0.0.7 : sous licence MIT, disponible à l'adresse suivante : http://www.daterangepicker.com/  
stacktable : sous licence MIT, disponible à l'adresse suivante : http://johnpolacek.github.io/stacktable.js/  

- Divers :
Icones originaires de la police d'écriture font-awesome, sous licence SIL OFL 1.1 (police d'écriture) et MIT (CSS), disponible à l'adresse suivante : http://fortawesome.github.io/

AUTEUR
-------

Ali BELLAMINE  
Vous pouvez me contacter à l'adresse suivante : contact@alibellamine  
Vous pouvez supporter le projet PAS à travers un don Paypal du montant de votre choix à contact@alibellamine.me
