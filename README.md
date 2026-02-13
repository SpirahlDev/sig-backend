# SIG - Plateforme de Patrimoine (Backend)

API backend du projet de géolocalisation de patrimoine. Cette application, développée avec Laravel, assure la gestion des sites historiques, des types de lieux, des photographies et les services de recherche géographique par proximité.

## Choix techniques
Nous avons utilisé Laravel car il permet de développer rapidement tout en gardant une assez bonne organisation du code. Il propose une architecture MVC claire, comme demandé dans les spécifications.
Ayant déjà développé auparavant des outils pour mettre en place rapidement des contrôleurs CRUD et gérer la pagination, utiliser Laravel m’a permis d’avancer efficacement sur le backend.

## Installation

Procédure de configuration pour un nouvel environnement de développement.

### 1. Prérequis

L'environnement doit disposer des composants suivants :

- **PHP** : version 8.2 ou supérieure
- **Composer** : gestionnaire de dépendances PHP
- **Node.js & NPM** : compilation des assets (si applicable)
- **MySQL** ou driver compatible Laravel

### 2. Configuration automatique

Un script automatise les étapes de configuration initiales (dépendances, fichier `.env`, clé d'application et migrations) :

```bash
composer setup
```

### 3. Configuration manuelle (Alternative)

Étapes pour une installation manuelle :

1.  **Installation des dépendances** :
    ```bash
    composer install
    npm install
    ```
2.  **Configuration de l'environnement** :
    ```bash
    cp .env.example .env
    ```
    _Note : Configurer les accès à la base de données dans le fichier `.env`._
3.  **Généréation de la clé d'application** :
    ```bash
    php artisan key:generate
    ```
4.  **Exécution des migrations** :
    ```bash
    php artisan migrate
    ```

---

## Lancement du projet

Démarrage du serveur de développement Laravel :

```bash
php artisan serve
```

L'API est accessible sur : [http://localhost:8000](http://localhost:8000)

---

## Points de terminaison principaux (API)

| Méthode | Route               | Description                                    |
| :------ | :------------------ | :--------------------------------------------- |
| `GET`   | `/api/sites`        | Liste des sites enregistrés                    |
| `POST`  | `/api/sites`        | Enregistrement d'un nouveau site (avec photos) |
| `GET`   | `/api/sites/nearby` | Recherche de sites par proximité géographique  |

---

## Gestion des médias

Les images sont stockées localement. Pour assurer l'accessibilité des fichiers via le client, générer le lien symbolique si nécessaire :

```bash
php artisan storage:link
```
