Quadco FTP
==========
Gestionnaire FTP pour Quadco


Installation :

1. Ouvrir includes/config/config.general.php en mode d'�dition. Un section, bien indiqu� ressemblera � ceci :
	
	/*********** CONFIGURATION A MODIFIER *************/
	
	// Informations de connexion � la DB
	
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
	
2. Entrer les informations de connexion � la base de donn�e dans les $_CONFIG correspondant

3. $_CONFIG['BASE_FOLDER'] => C'est le chemin de la racine du ftp jusqu'au dossier contenant les dossies /modules /includes /templates, etc.
	Simplement mettre � cette endroit le path appropri�.

4. Uploader le dossier racine � l'endroit d�sir� sur le serveur

5. Effectu� le dump dans la base de donn�e (fichier DB_SQL.sql, le dump a �t� fait � partir de MySQL)

6. Par d�faut, un utilisateur est cr��. User : Admin, pass : admin.

7. Contactez-moi si une quelconque �tape pose probl�me.

8. Utilisez l'application et dressez la liste des fonctionnalit�s suppl�mentaires d�sir�es.

9. Bonne utilisation!