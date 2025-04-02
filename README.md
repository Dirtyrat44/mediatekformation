# Mediatekformation

## Présentation de l'application d'origine

Voici le [lien du dépôt d'origine](https://github.com/CNED-SLAM/mediatekformation) où sont expliqués le contexte et les fonctionnalités initiales du projet.

Ce dépôt présente uniquement les **fonctionnalités ajoutées** à l’application dans le cadre de son évolution.

---

## Fonctionnalités ajoutées

### Ajout de fonctionnalités dans le front-office

- Sur la page des **playlists**, une **nouvelle colonne** affiche le **nombre de formations** associées à chaque playlist.
- Cette colonne permet un **tri ascendant ou descendant**.
- Ce **nombre est également affiché** sur la page de détail de chaque playlist.

---

### Back-office d’administration

Création d’une interface de gestion des contenus, accessible uniquement aux administrateurs authentifiés.

Sections disponibles :

- **Formations** : ajout, modification, suppression.
- **Playlists** : ajout, modification, suppression (uniquement si aucune formation liée).
- **Catégories** : ajout, modification, suppression (uniquement si aucune formation liée).

L’affichage du back-office reprend la structure du front.

---

### Accès administrateur

- Mise en place d’une page de connexion sécurisée pour accéder au **back-office**.
- Une fois connecté, un **bouton "Centre d’administration"** apparaît dans l’interface front pour accéder facilement au back.
- Déconnexion possible à tout moment, depuis n’importe quelle page.

---

### Démo en ligne

L’application peut être consultée à l’adresse suivante :  
👉 [https://mediatekformation.alwaysdata.net](https://mediatekformation.alwaysdata.net)

> Les identifiants d’accès à l’espace administrateur ne sont malheuresement pas disponible.

---

### Documentation intégrée

Un lien vers la **documentation technique de l’application** est disponible dans le site :
👉 [Consulter la documentation](https://mediatekformation.alwaysdata.net/documentation)

---

## Installation et utilisation en local

### Prérequis

- PHP ≥ 8.1  
- Symfony CLI  
- Composer  
- MySQL  
- Serveur local (Wamp, Xampp ou autre)  
- Navigateur web 

---

### Installation avec Git (recommandé)

#### 1. Cloner le dépôt
```bash
git clone https://github.com/votre-utilisateur/mediatekformation.git
cd mediatekformation
```

#### 2. Installer les dépendances PHP
```bash
composer install
```

#### 3. Copier le fichier d’environnement
```bash
cp .env .env.local
```

### Configuration de la base de données

#### 1. Créer une base de données MySQL (ex : mediatekformation).

#### 2. Dans .env.local, configurer la variable DATABASE_URL :
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/mediatekformation"

#### 3. Lancer les commandes suivantes :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### Lancer l'application
```bash
symfony server:start
```

Rendez-vous ensuite sur [https://127.0.0.1:8000](https://127.0.0.1:8000/)

### Installation alternative via fichier ZIP

#### 1. Vérifier que Composer, Git et Wampserver (ou équivalent) sont installés sur votre machine.

#### 2. Télécharger le projet au format ZIP depuis GitHub et le décompresser dans le dossier www de Wamp.

#### 3. Renommer le dossier en mediatekformation.

#### 4. Ouvrir une fenêtre de commande (en tant qu'administrateur), aller dans le dossier du projet :
```bash
cd C:\wamp64\www\mediatekformation
composer install
```

#### 5. Créer la base de données mediatekformation via phpMyAdmin (utilisateur root sans mot de passe par défaut).

#### 6. Importer le fichier mediatekformation.sql présent à la racine du projet.

#### 7. Si nécessaire, configurer les accès dans le fichier .env :
```bash
DATABASE_URL="mysql://root:@127.0.0.1:3306/mediatekformation"
```

#### 8. Ouvrir l'application dans un navigateur à l'adresse suivante :
👉 http://localhost/mediatekformation/public/index.php

