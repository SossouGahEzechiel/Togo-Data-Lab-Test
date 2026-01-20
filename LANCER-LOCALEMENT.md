# Installation & Lancement Local – Gestion des Réservations de Véhicules

Ce guide explique comment installer les dépendances et lancer l'application complète (backend Laravel + frontend Next.js) en local.

Date de rédaction : Janvier 2026  
Environnement cible : développement / test (SQLite)

## Prérequis

| Logiciel / Outil          | Version minimale recommandée     | Comment vérifier / installer                          |
|---------------------------|----------------------------------|-------------------------------------------------------|
| PHP                       | 8.2 ou supérieur                 | `php -v`                                              |
| Composer                  | dernière version                 | `composer --version`                                  |
| Node.js                   | 18.x ou supérieur                | `node -v`                                             |
| Yarn                      | 1.x ou 4.x (berry)               | `yarn -v`  (ou `npm install -g yarn`)                 |
| npm                       | 8.x ou supérieur (optionnel)     | `npm -v`                                              |
| Git                       | n'importe quelle version récente | `git --version`                                       |

**Notes :**
- Yarn est utilisé dans les exemples, mais npm fonctionne aussi (remplace `yarn` par `npm` ou `npm run`).
- Aucune base de données externe n’est requise (SQLite est utilisé par défaut).

## Structure attendue du projet
```
application-reservations-vehicules/
├── backend/                # Laravel 12 – API
├── frontend/               # Next.js 14.2.2 – Interface
├── docs/
│   └── diagramme_classes.png
├── README.md
└── LANCER-LOCALEMENT.md    # ← ce fichier
```
## Étape par étape

### 1. Cloner le dépôt (si ce n’est pas déjà fait)

```bash
git clone https://github.com/votre-nom-utilisateur/application-reservations-vehicules.git
cd application-reservations-vehicules
```
### 2. Installer le backend (Laravel)

```bash
cd backend

# Installer les dépendances PHP
composer install

# Créer le fichier .env à partir de l'exemple
cp .env.example .env

# Générer la clé d'application Laravel
php artisan key:generate

# (Optionnel) Vérifiez / ajuster la configuration SQLite dans .env
# Les valeurs par défaut devraient suffire :
# DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite

# Créez le fichier SQLite s'il n'existe pas + migrations
php artisan migrate

# Charger les données de test (utilisateurs, véhicules, missions, réservations)
php artisan db:seed
```

### 3. Installer le frontend (Next.js)

```bash
# Ouvrez un deuxième terminal à la racine du projet :
cd frontend

# Installer les dépendances (Yarn recommandé)
yarn install
# ou avec npm :
npm install

# Créer le fichier .env à partir de l'exemple
cp .env.example .env
```

### 4. Lancer les deux applications

Terminal 1 – Backend (Laravel)
```bash
# Depuis le dossier backend/
php artisan serve
# → API disponible sur http://localhost:8000
```
Terminal 2 – Frontend (Next.js)
```bash
# Depuis le dossier frontend/
yarn dev
# ou
npm run dev

# → Interface disponible sur http://localhost:3000
```

### 5. Accéder à l’application

- Ouvrez votre navigateur : http://localhost:3000
- Connectez-vous avec un compte créé par le seeder
- Vérifiez les identifiants / mots de passe temporaires dans le fichier [UserSeeder.php](backend/database/seeders/UserSeeder.php)
- À la première connexion, vous devrez changer le mot de passe.

### 6. Lancement de tâche de changement de statut des réservations
Dans un autre terminal du dossier backend
```
php artisan reservations:update-expired
