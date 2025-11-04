# UptimeCore 🚀

> **Monitoring simple et efficace pour usage personnel et petits hébergeurs**

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![Laravel](https://img.shields.io/badge/Laravel-11-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-purple)

**Créé par** [Orion](https://theorion.ovh)

---

## 🎯 Pourquoi UptimeCore ?

UptimeCore est une solution de monitoring **auto-hébergée**, **simple** et **bien codée** pour :

* ✅ Particuliers avec plusieurs services
* ✅ Associations avec infrastructure simple
* ✅ Petits hébergeurs (<100 clients)
* ✅ Intégration facile via API REST

### Ce qui fait la différence

* 🚀 **Installation en 5 minutes** avec Docker
* 🎨 **Interface épurée et moderne**
* 🔌 **API REST complète avec Basic Auth** pour toutes les routes pour intégrations
* 📊 **Système d'incidents** intégré
* 🛠️ **Maintenances planifiées**
* 💚 **100% gratuit et open-source**
* 🧹 **Code propre Laravel 11**

---

## ⚡ Installation Rapide

### Docker (Recommandé) 🐳

```bash
# 1. Cloner le projet
git clone https://github.com/votre-repo/uptimecore.git
cd uptimecore

# 2. Configuration
cp .env.example .env
# Éditer .env avec vos paramètres

# 3. Démarrer
docker-compose up -d

# 4. Créer votre compte admin
docker-compose exec app php artisan admin:create
```

**C'est tout !** Accédez à `http://localhost` 🎉

### Installation Manuelle

```bash
# Prérequis: PHP 8.2+, Composer, MariaDB/SQLite, Nginx
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan admin:create

# Démarrer le scheduler (checks automatiques)
php artisan schedule:work

# Démarrer le serveur
php artisan serve
```

---

## 🎯 Fonctionnalités

### 1. Types de Monitoring 📡

| Type | Description | Exemple |
|------|-------------|---------|
| **HTTP/HTTPS** | Sites web, API REST | `https://api.example.com/health` |
| **Ping (ICMP)** | Serveurs, routeurs | `192.168.1.1` |
| **Port TCP** | Services spécifiques | `smtp.example.com:587` |
| **DNS** | Résolution de domaines | `example.com → A → 1.2.3.4` |
| **SSL/TLS** | Expiration certificats | `https://example.com` |

**Configuration par monitor:**
- Intervalle: 30s, 60s, 5min, 10min, 30min
- Timeout: 5-60 secondes
- Retries: 1-5 tentatives
- Méthode HTTP: GET, POST, PUT, DELETE, PATCH
- Headers customs: Authorization, User-Agent, etc.
- Vérifier code statut: 200, 201, 204, etc.

### 2. Notifications 🔔

**Canaux supportés:**
* 📧 **Email (SMTP)** - Gmail, Outlook, SMTP custom
* 🎮 **Discord Webhook** - Avec embeds colorés
* 🔗 **Webhook Custom** - Pour Slack, Mattermost, etc.

### 3. Dashboard 📊

* ✅ Vue temps réel de tous les monitors
* ✅ Graphiques historiques (7j, 30j, 90j)
* ✅ Temps de réponse moyens
* ✅ Logs des checks en direct
* ✅ Filtres par statut/type/groupe

### 4. Gestion des Incidents 🚨

* Création automatique lors des pannes
* Création manuelle avec timeline
* Impact: Mineur / Majeur / Critique
* Statut: En investigation / Identifié / Résolu

### 5. Maintenances Planifiées 🛠️

* Planification avec dates
* Désactivation automatique des alertes
* Notification avant/pendant/après
* Affichage sur status page

### 6. Status Page Publique 🌐

* URL personnalisée
* Personnalisation complète (logo, couleurs)
* Historique des incidents
* Abonnements (Email, RSS, Webhook)

### 7. API REST Complète 🔌

Voir la [documentation API complète](docs/API.md)

---

## 🏗️ Architecture

### Stack Technique

```yaml
Backend:
  - Laravel 11
  - MariaDB
  - Redis (queues)

Frontend:
  - Blade
  - Tailwind CSS
  - Alpine.js
  - Backpack for Laravel

Monitoring:
  - Laravel Scheduler
  - Laravel Queues
  - Guzzle HTTP Client
  - Spatie Uptime Monitor

Alertes:
  - Laravel Notifications
  - Email SMTP
  - Discord Webhooks
  - Custom Webhooks
```

---

## 📡 Documentation API

Voir [docs/API.md](docs/API.md) pour la documentation complète de l'API REST.

---

## 📝 License

MIT License - Voir [LICENSE](LICENSE) pour plus de détails.

---

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou une pull request.

---

## 📧 Support

Pour toute question ou problème :
- 📧 Email: support@theorion.ovh
- 🐛 Issues: [GitHub Issues](https://github.com/votre-repo/uptimecore/issues)

