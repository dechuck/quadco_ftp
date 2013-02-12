Quadco FTP
==========
Gestionnaire FTP pour Quadco


Installation :

1. Ouvrir includes/config/config.general.php en mode d'édition. Un section, bien indiqué ressemblera à ceci :
	
	/*********** CONFIGURATION A MODIFIER *************/
	
	// Informations de connexion à la DB
	
	$_CONFIG['DB_HOST']		= '';
	$_CONFIG['DB_USER']		= '';
	$_CONFIG['DB_PASS']		= '';
	$_CONFIG['DB_NAME']		= '';
	$_CONFIG['DB_PORT']		= '3306';
	
	// Path setup
	
	/**** Le path de la racine ftp du site au dossier ****/
	
	/* 'PATH JUSQUA LA RACINE FTP' . 'DOSSIER_ROOT' */
	$_CONFIG['BASE_FOLDER'] 	=  '/' . 'quadco_ftp/';
	
	
	/************* RIEN D'AUTRE A MODIFIER **************/
	
2. Entrer les informations de connexion à la base de donnée dans les $_CONFIG correspondant

3. $_CONFIG['BASE_FOLDER'] => C'est le chemin de la racine du ftp jusqu'au dossier contenant les dossies /modules /includes /templates, etc.
	Simplement mettre à cette endroit le path approprié.

4. Uploader le dossier racine à l'endroit désiré sur le serveur

5. Effectué le dump dans la base de donnée (fichier DB_SQL.sql, le dump a été fait à partir de MySQL)

6. Par défaut, un utilisateur est créé. User : Admin, pass : admin.

7. Contactez-moi si une quelconque étape pose problème.

8. Utilisez l'application et dressez la liste des fonctionnalités supplémentaires désirées.

9. Bonne utilisation!