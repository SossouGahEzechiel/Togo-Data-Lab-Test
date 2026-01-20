# Gestion des Réservations de Véhicules

Application web full-stack de gestion des réservations de véhicules pour une organisation gouvernementale togolaise.

## Contexte de l’application

Cette application a été développée dans le cadre d’un test technique pour le Togo Data Lab.  
Elle permet de gérer la réservation de véhicules de service pour les missions professionnelles tout en évitant les conflits de planning.

Objectif principal :  
Permettre aux utilisateurs authentifiés de réserver un véhicule pour une mission, tout en garantissant qu’aucun chevauchement n’est possible sur un même véhicule.

## Choix Techniques

Architecture full-stack moderne, légère et adaptée à un environnement de test / local :

- **Backend** : Laravel 12 (PHP)  
  → Authentification intégrée, API RESTful, jobs asynchrones, planification de tâches (Scheduler)

- **Frontend** : Next.js 14.2.2 (React)  
  → Rendu hybride (SSR + CSR), interface réactive et fluide

- **Base de données** : SQLite  
  → Aucun serveur externe requis, configuration ultra-minimale, parfait pour tests et portabilité

- **Sécurité**  
  → Authentification email + mot de passe, pour générer un Bearer token  
  → Hashage des mots de passe  
  → Règles de complexité strictes  
  → Contrôle d’accès par rôle

- **Envoi d’emails** : File d’attente asynchrone (Laravel Queue)  
  → Non bloquant, fonctionne même sans serveur mail (Vu que nous sommes en phase de test, les mots de passe seront mis dans le fichier [BACKEND/storage/logs/laravel.log](BACKEND/storage/logs/laravel.log))

- **Tâches planifiées** : Laravel Scheduler  
  → Mise à jour automatique des statuts (réservations et missions terminées)

- **Données de test** : Seeders des utilisateurs, des véhicules et des missions.

## Principales Fonctionnalités Implémentées
Toutes les fonctionnalités ne sont accessibles qu'aux administrateurs. Si ce n'est pas le cas, ce sera précisé.
### 1. Gestion des Utilisateurs
- Création – modification – suppression  
- Génération et envoi **asynchrone** d’un mot de passe temporaire par email lors de la création  
- Obligation de changer le mot de passe à la **première connexion**  
  (≥8 caractères, majuscule, minuscule, caractère spécial) vérifié côté client et serveur  
- Activation / désactivation d’un compte  
- Liste des utilisateurs

### 2. Gestion des Véhicules
- Ajout – modification – suppression  
- Consultation détaillée (modèle, plaque, catégorie, état)  
- Historique complet des réservations par véhicule

### 3. Gestion des Missions
- Création – modification – suppression  
- Statuts : en attente | en cours | terminée | annulée  

### 4. Gestion des Réservations
- Création : choix véhicule + mission + dates début/fin  
- Vérification **temps réel** de la disponibilité (avant soumission)  
- Blocage automatique en cas de chevauchement  
- **Règles d’annulation / suppression** :  
  → **Seul l’auteur** de la réservation peut l’annuler ou la supprimer  
  → Les administrateurs peuvent **uniquement valider / refuser**  
- Historique des réservations :  
  → Utilisateur → voit **ses propres** réservations  
  → Administrateur → voit **toutes** les réservations  
- **Mise à jour automatique** du statut « passée » via tâche planifiée

### 5. Tableau de bord Administrateur
Accessible uniquement aux administrateurs, affiche en temps réel :

- Nombre de réservations en attente de validation  
- Nombre de véhicules actuellement disponibles  
- Nombre de missions en cours  
- Nombre d’utilisateurs actifs  
- Liste des réservations en attente + actions rapides (valider / refuser)  
- Top 3 des véhicules les plus utilisés  
- Récapitulatif / graphique des activités du mois
