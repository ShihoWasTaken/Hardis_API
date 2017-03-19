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

# On définit le bon chmod pour les fichiers
$ find . -type d -exec chmod 0755 {} \;
$ find . -type f -exec chmod 0644 {} \;

# On bouge le repertoire vers le dossier /var/www/html
$ cd .. && sudo mv Hardis_API/ /var/www/html/hardis_api_prod/ && cd /var/www/html/hardis_api_prod/

# On donne les bonnes permissions aux dossiers de logs et de cache
$ HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
$ sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var
$ sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var
``` 
Description | URL
--- | ---
Liste des adhérents | http://localhost/hardis_api_prod/web/app.php/adherents
Adhérent trouvé | http://localhost/hardis_api_prod/web/app.php/adherents/2
Adhérent non trouvé | http://localhost/hardis_api_prod/web/app.php/adherents/99
Page 404 | http://localhost/hardis_api_prod/web/app.php/toto
