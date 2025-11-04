# Guide d'installation UptimeCore - Linux vierge (Commandes manuelles)

## 🐳 Installation avec Docker (Recommandé)

### Étape 1 : Mettre à jour le système
```bash
sudo apt update
sudo apt upgrade -y
```

### Étape 2 : Installer les dépendances de base
```bash
sudo apt install -y curl wget git unzip software-properties-common apt-transport-https ca-certificates gnupg lsb-release
```

### Étape 3 : Installer Docker
```bash
# Ajouter la clé GPG Docker
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Ajouter le dépôt Docker
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Installer Docker
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Vérifier l'installation
docker --version
docker compose version
```

### Étape 4 : Créer le dossier et cloner le projet
```bash
# Aller dans /var/www
cd /var/www

# Créer le dossier uptimecore
sudo mkdir -p uptimecore
sudo chown $USER:$USER uptimecore
cd uptimecore

# Si vous avez Git, clonez le projet :
# git clone https://github.com/votre-repo/uptimecore.git .

# OU copiez simplement les fichiers du projet ici
```

### Étape 5 : Configurer le fichier .env
```bash
# Copier le fichier .env.example
cp .env.example .env

# Éditer le fichier .env
nano .env
```

**Modifiez ces lignes dans .env :**
```env
APP_NAME=UptimeCore
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://votre-ip-ou-domaine

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=uptimecore
DB_USERNAME=uptimecore
DB_PASSWORD=changez_moi_par_un_mot_de_passe_securise

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre_email@gmail.com
MAIL_PASSWORD=votre_mot_de_passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@votredomaine.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Étape 6 : Démarrer les conteneurs Docker
```bash
# Démarrer les conteneurs en arrière-plan
docker compose up -d

# Vérifier que tout fonctionne
docker compose ps
```

### Étape 7 : Installer les dépendances Composer
```bash
docker compose exec app composer install --no-dev --optimize-autoloader
```

### Étape 8 : Générer la clé d'application
```bash
docker compose exec app php artisan key:generate
```

### Étape 9 : Exécuter les migrations (créer les tables)
```bash
docker compose exec app php artisan migrate --force
```

### Étape 10 : Créer le compte administrateur
```bash
docker compose exec app php artisan admin:create
```

Vous serez invité à entrer :
- Nom
- Email  
- Mot de passe

### Étape 11 : Vérifier que tout fonctionne
```bash
# Voir les logs
docker compose logs app

# Tester l'accès (remplacez par votre IP)
curl http://localhost
```

### Étape 12 : Configurer le firewall (optionnel)
```bash
# Autoriser HTTP
sudo ufw allow 80/tcp

# Autoriser HTTPS
sudo ufw allow 443/tcp

# Activer le firewall
sudo ufw enable
```

**✅ C'est terminé !** Accédez à `http://votre-ip` dans votre navigateur.

---

## 🛠️ Installation Manuelle (Sans Docker)

### Étape 1 : Mettre à jour le système
```bash
sudo apt update
sudo apt upgrade -y
```

### Étape 2 : Installer PHP 8.2
```bash
# Ajouter le dépôt PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Installer PHP et extensions
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl

# Vérifier
php -v
```

### Étape 3 : Installer Composer
```bash
cd /tmp
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Vérifier
composer --version
```

### Étape 4 : Installer MariaDB
```bash
# Installer MariaDB
sudo apt install -y mariadb-server mariadb-client

# Sécuriser l'installation
sudo mysql_secure_installation
```

### Étape 5 : Créer la base de données
```bash
sudo mysql -u root -p
```

Dans MySQL, exécutez :
```sql
CREATE DATABASE uptimecore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'uptimecore'@'localhost' IDENTIFIED BY 'votre_mot_de_passe_securise';
GRANT ALL PRIVILEGES ON uptimecore.* TO 'uptimecore'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Étape 6 : Installer Nginx
```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

### Étape 7 : Cloner le projet
```bash
cd /var/www
sudo mkdir -p uptimecore
sudo chown $USER:$USER uptimecore
cd uptimecore

# Cloner ou copier les fichiers du projet ici
```

### Étape 8 : Installer les dépendances
```bash
cd /var/www/uptimecore
composer install --no-dev --optimize-autoloader
```

### Étape 9 : Configurer .env
```bash
cp .env.example .env
nano .env
```

**Modifiez ces lignes :**
```env
APP_NAME=UptimeCore
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://votre-ip-ou-domaine

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=uptimecore
DB_USERNAME=uptimecore
DB_PASSWORD=votre_mot_de_passe_securise

QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
```

### Étape 10 : Générer la clé d'application
```bash
php artisan key:generate
```

### Étape 11 : Configurer les permissions
```bash
sudo chown -R www-data:www-data /var/www/uptimecore
sudo chmod -R 755 /var/www/uptimecore
sudo chmod -R 775 /var/www/uptimecore/storage
sudo chmod -R 775 /var/www/uptimecore/bootstrap/cache
```

### Étape 12 : Configurer Nginx
```bash
sudo nano /etc/nginx/sites-available/uptimecore
```

**Collez ce contenu :**
```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/uptimecore/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Activer le site :**
```bash
sudo ln -s /etc/nginx/sites-available/uptimecore /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Étape 13 : Exécuter les migrations
```bash
php artisan migrate --force
```

### Étape 14 : Créer le compte admin
```bash
php artisan admin:create
```

### Étape 15 : Configurer le scheduler (cron)
```bash
sudo crontab -e -u www-data
```

**Ajoutez cette ligne :**
```
* * * * * cd /var/www/uptimecore && php artisan schedule:run >> /dev/null 2>&1
```

### Étape 16 : Installer et configurer Supervisor pour les queues
```bash
# Installer supervisor
sudo apt install -y supervisor

# Créer le fichier de configuration
sudo nano /etc/supervisor/conf.d/uptimecore-worker.conf
```

**Collez ce contenu :**
```ini
[program:uptimecore-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/uptimecore/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/uptimecore/storage/logs/worker.log
stopwaitsecs=3600
```

**Démarrer supervisor :**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start uptimecore-worker:*
```

### Étape 17 : Vérifier que tout fonctionne
```bash
# Vérifier Nginx
sudo systemctl status nginx

# Vérifier PHP-FPM
sudo systemctl status php8.2-fpm

# Vérifier les queues
sudo supervisorctl status

# Tester
curl http://localhost
```

**✅ C'est terminé !** Accédez à `http://votre-ip` dans votre navigateur.

---

## 🔧 Commandes utiles après installation

### Avec Docker
```bash
# Voir les logs
docker compose logs -f app

# Redémarrer
docker compose restart

# Arrêter
docker compose down

# Exécuter une commande artisan
docker compose exec app php artisan [commande]
```

### Sans Docker
```bash
# Voir les logs
tail -f storage/logs/laravel.log

# Redémarrer Nginx
sudo systemctl restart nginx

# Redémarrer PHP-FPM
sudo systemctl restart php8.2-fpm

# Redémarrer les workers
sudo supervisorctl restart uptimecore-worker:*
```

---

## 🐛 Dépannage

### Erreur de permissions
```bash
sudo chown -R www-data:www-data /var/www/uptimecore
sudo chmod -R 775 /var/www/uptimecore/storage
sudo chmod -R 775 /var/www/uptimecore/bootstrap/cache
```

### Les checks ne fonctionnent pas
```bash
# Vérifier le scheduler
php artisan schedule:list

# Exécuter manuellement
php artisan monitors:check

# Vérifier les queues
php artisan queue:work
```

### Problème Docker
```bash
# Reconstruire
docker compose down
docker compose build --no-cache
docker compose up -d

# Logs détaillés
docker compose logs -f
```
