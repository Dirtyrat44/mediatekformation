# Mediatekformation

## Présentation de l'application d'origine

Voici le [lien du dépôt d'origine](https://github.com/CNED-SLAM/mediatekformation) où sont expliqués le contexte et les fonctionnalités initiales du projet.

Ce dépôt présente uniquement les **fonctionnalités ajoutées** à l’application dans le cadre de son évolution.

---

## Fonctionnalités ajoutées

### Ajout de fonctionnalités dans le front-office

- Sur la page des **playlists**, une **nouvelle colonne** affiche le **nombre de formations** associées à chaque playlist.
- Cette colonne permet un **tri ascendant ou descendant**.

![playlistfront](https://github.com/user-attachments/assets/bf49969e-db32-4ad6-b165-11b8314956cf)
  
- Ce **nombre est également affiché** sur la page de détail de chaque playlist.

![playlistdetailsfrontview](https://github.com/user-attachments/assets/f1d6f3a7-696f-491e-b10c-772cbc8112a2)

---

### Back-office d’administration

Création d’une interface de gestion des contenus, accessible uniquement aux administrateurs authentifiés.

Sections disponibles :

- **Formations** :

- ajout, modification, suppression.
<img src="https://github.com/user-attachments/assets/503ff3e0-ece8-40e6-bbf8-c62d7fc4c6d7" alt="formationbackview" width="33%">
<p align="center">
  <img src="https://github.com/user-attachments/assets/503ff3e0-ece8-40e6-bbf8-c62d7fc4c6d7" alt="formationbackview" width="33%">
  <img src="https://github.com/user-attachments/assets/64391a0a-120a-43bd-8a86-d7095a034963" alt="creeformationback" width="33%">
  <img src="https://github.com/user-attachments/assets/f3c4474f-b614-405d-93c8-84f1825fb647" alt="modifformationback" width="33%">
</p>



- **Playlists** : ajout, modification, suppression (uniquement si aucune formation liée).

-- ***Ajout***
![ajout](https://github.com/user-attachments/assets/8bd14333-baa2-4eda-b363-31791ef7ce27)

-- ***Modification***
![modif](https://github.com/user-attachments/assets/59998baa-7a91-4eb0-aed5-f0b8bd1e5d80)

-- ***Suppression***
![suppr](https://github.com/user-attachments/assets/5c6fd87e-30cd-4634-ac28-612d44a5cbf2" alt="playlistbackmodif)

- **Catégories** : ajout, modification, suppression (uniquement si aucune formation liée).
  
![categorieviewback](https://github.com/user-attachments/assets/159e1622-f8e0-4531-a782-72ada996d285)

L’affichage du back-office reprend la structure du front.

---

### Accès administrateur

- Mise en place d’une page de connexion sécurisée pour accéder au **back-office**.
  
  ![loginbackview](https://github.com/user-attachments/assets/591b460d-800f-480f-a856-74ba23134dfc)

- Une fois connecté, un **bouton "Centre d’administration"** apparaît dans l’interface front pour accéder facilement au back.
- Déconnexion possible à tout moment, depuis n’importe quelle page.
  
  ![frontbuttonsview](https://github.com/user-attachments/assets/65818de9-42e2-4b37-a94b-c0228ea8e6da)


---

### Démo en ligne

L’application peut être consultée à l’adresse suivante :  
👉 [https://mediatekformation.alwaysdata.net](https://mediatekformation.alwaysdata.net)

> Les identifiants d’accès à l’espace administrateur ne sont malheureusement pas disponibles.

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
```bash
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/mediatekformation"
```

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

