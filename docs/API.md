# Documentation API UptimeCore

Documentation complète de l'API REST pour UptimeCore.

## Base URL

```
https://your-uptimecore.com/api
```

## Authentification

Toutes les requêtes (sauf status public) nécessitent un token Bearer obtenu via `/api/auth/login`:

```bash
Authorization: Bearer YOUR_API_TOKEN
```

Alternativement, vous pouvez utiliser Basic Auth avec email/password pour toutes les routes API.

---

## Endpoints

### 🔐 Auth

#### Login
```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

#### Logout
```bash
POST /api/auth/logout
Authorization: Bearer TOKEN
```

#### Refresh Token
```bash
POST /api/auth/refresh
Authorization: Bearer TOKEN
```

---

### 📡 Monitors

#### Liste des monitors
```bash
GET /api/monitors
GET /api/monitors?status=up
GET /api/monitors?type=http
GET /api/monitors?group=production
```

#### Créer un monitor
```bash
POST /api/monitors/create
Content-Type: application/json

{
  "name": "Mon API",
  "type": "http",
  "url": "https://api.example.com/health",
  "interval": 60,
  "timeout": 10,
  "method": "GET",
  "headers": {
    "Authorization": "Bearer token123"
  },
  "expected_status_code": 200,
  "group": "production",
  "notification_channels": [1, 2]
}
```

#### Détails d'un monitor
```bash
GET /api/monitors/{id}
```

#### Mettre à jour un monitor
```bash
PUT /api/monitors/{id}/update
Content-Type: application/json

{
  "name": "Nouveau nom",
  "interval": 120
}
```

#### Supprimer un monitor
```bash
DELETE /api/monitors/{id}/delete
```

#### Mettre en pause / Reprendre
```bash
POST /api/monitors/{id}/pause
POST /api/monitors/{id}/resume
```

#### Forcer un check
```bash
POST /api/monitors/{id}/check
```

#### Statistiques
```bash
GET /api/monitors/{id}/stats?period=30d
```

#### Historique des heartbeats
```bash
GET /api/monitors/{id}/heartbeats?limit=100
```

---

### 🚨 Incidents

#### Liste des incidents
```bash
GET /api/incidents
GET /api/incidents?status=active
GET /api/incidents?monitor_id=1
```

#### Créer un incident
```bash
POST /api/incidents/create
Content-Type: application/json

{
  "title": "Dégradation de performances",
  "impact": "minor",
  "status": "investigating",
  "monitor_ids": [1, 2, 3],
  "message": "Message initial",
  "notify": true
}
```

#### Mettre à jour un incident
```bash
PUT /api/incidents/{id}/update
Content-Type: application/json

{
  "status": "identified",
  "message": "Cause identifiée"
}
```

#### Résoudre un incident
```bash
POST /api/incidents/{id}/resolve
Content-Type: application/json

{
  "message": "Services restaurés"
}
```

---

### 🛠️ Maintenances

#### Liste des maintenances
```bash
GET /api/maintenances
GET /api/maintenances?status=scheduled
```

#### Créer une maintenance
```bash
POST /api/maintenances/create
Content-Type: application/json

{
  "title": "Maintenance base de données",
  "description": "Optimisation et backup",
  "starts_at": "2025-11-10T22:00:00Z",
  "duration_minutes": 120,
  "monitor_ids": [1, 2, 3],
  "notify": true,
  "disable_alerts": true
}
```

#### Démarrer / Terminer une maintenance
```bash
POST /api/maintenances/{id}/start
POST /api/maintenances/{id}/complete
```

---

### 🔔 Notification Channels

#### Liste des canaux
```bash
GET /api/notification-channels
```

#### Créer un canal
```bash
POST /api/notification-channels/create
Content-Type: application/json

{
  "type": "email",
  "label": "Email Ops",
  "config": {
    "to": "ops@example.com"
  }
}
```

#### Tester un canal
```bash
POST /api/notification-channels/{id}/test
```

---

### 🌐 Status Page

#### Status page publique
```bash
GET /api/status/{slug}
```

#### Mettre à jour la status page
```bash
PUT /api/status-page/update
Content-Type: application/json

{
  "name": "Status - Acme Corp",
  "color": "#3b82f6",
  "show_uptime": true
}
```

---

### 📊 Dashboard

#### Statistiques globales
```bash
GET /api/dashboard/stats
```

---

Pour plus de détails, voir le README.md principal.

