Test technique pour Hardis - Création d'une API Rest
=======

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/21be9ed2-ea96-4120-b96e-593a73ab9db2/big.png)](https://insight.sensiolabs.com/projects/21be9ed2-ea96-4120-b96e-593a73ab9db2)

Prérequis
======
* Avoir accès au bash et au sudo
*	Avoir composer d’installé en global (ou avec un alias composer vers le chemin de l’éxécutable) afin de gérer les dépendances de librairies
*	PHP 5.5.9 au minimum
*	Extension PHP JSON
*	Extension PHP ctype
*	Extension PHP mbstring
*	Le paramètre datetime.zone du fichier php.ini de Apache doit être renseigné par exemple datetime.zone = Europe/Paris


Installation (en mode production)
======

```bash
$ git clone https://github.com/ShihoWasTaken/Hardis_API.git
$ cd Hardis_API/
$ export SYMFONY_ENV=prod
# Laissez les paramètres par défaut en appuyant sur entrée quand ils sont demandés
$ composer install --no-dev --optimize-autoloader

# On bouge le repertoire vers le dossier /var/www/html
$ cd .. && sudo mv Hardis_API/ /var/www/html/hardis_api_prod/ && cd /var/www/html/hardis_api_prod/

# On donne les bonnes permissions aux dossiers de logs et de cache
$ HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
$ sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var
$ sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var
``` 

Configuration Apache (VirtualHost)
======

On va rajouter un nom de domaine pour notre appli qui pointera sur localhost, lancez la commande suivante :
```bash
$ gksudo gedit /etc/hosts
``` 
Et rajoutez cette ligne dans le fichier puis sauvergardez :
```txt
127.0.0.1   hardis-api.com
``` 

Rendez vous de le repertoire **/etc/apache2/sites-available/**, créez un fichier hardis.api.conf et rajoutez le contenu suivant dedans puis sauvegardez le fichier:

```
<VirtualHost *:80>
    ServerName hardis-api.com
    ServerAlias hardis-api.com

    DocumentRoot /var/www/html/hardis_api_prod/web/
    DirectoryIndex /var/www/html/hardis_api_prod/web/app.php/

	<Directory />
	  Order Deny,Allow
	  Deny from all
	  Options None
	  AllowOverride None
	</Directory>
    <Directory /var/www/html/hardis_api_prod/web>
	  Order Allow,Deny
	  Allow from all

		 <IfModule mod_rewrite.c>
	            Options -MultiViews
	            RewriteEngine On
	            RewriteCond %{REQUEST_FILENAME} !-f
	            RewriteRule ^(.*)$ app.php [QSA,L]
	        </IfModule>
    </Directory>


    <Directory /var/www/html/hardis_api_prod/web/bundles>
        <IfModule mod_rewrite.c>
            RewriteEngine Off
        </IfModule>
    </Directory>

    ErrorLog /var/log/apache2/project_error.log
    CustomLog /var/log/apache2/project_access.log combined
</VirtualHost>
``` 
Ensuite exécutez les commandes suivantes :
``` bash
# On active le virtualhost
$ sudo a2ensite hardis-api.conf
# Active le module d'URL rewriting s'il est installé
$ sudo a2enmod rewrite
# On rédémarre Apache pour prendre en compte la nouvelle configuration
$ sudo /etc/init.d/apache2 restart
``` 
L'application est maintenant accessible à l'adresse http://hardis-api.com/ (Vous devriez avoir une page d'erreur 404 car la route ne corresponds à aucune action)
* Si mod_rewrite est installé est activé dans Apache, utilisez les URLs de la dernière colonne du tableau.
* Si mod_rewrite n'est pas installé ou n'est pas activé, utilisez les URLs de la 3ème colonne du tableau.
* Enfin que vous ayez effectué la configuration des virtualhosts ou non, les URLs de la 2ème colonne reste accessibles également.

Description | URL sans configuration Apache | URL avec configuration Apache | URL configuration Apache + mod_rewrite
--- | --- | --- | ---
Liste des adhérents | http://localhost/hardis_api_prod/web/app.php/adherents | http://hardis-api.com/app.php/adherents | http://hardis-api.com/adherents
Adhérent trouvé | http://localhost/hardis_api_prod/web/app.php/adherents/2  | http://hardis-api.com/app.php/adherents/2 | http://hardis-api.com/adherents/2
Adhérent non trouvé | http://localhost/hardis_api_prod/web/app.php/adherents/99 | http://hardis-api.com/app.php/adherents/99 | http://hardis-api.com/adherents/99
Page 404 | http://localhost/hardis_api_prod/web/app.php/toto | http://hardis-api.com/app.php/toto | http://hardis-api.com/toto
