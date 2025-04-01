# Mediatekformation
## Présentation de l'application d'origine
Voici le [lien du dépôt d'origine](https://github.com/CNED-SLAM/mediatekformation) où est expliqué plus en détails le contexte et la présentation de l'application dans le readme.
Ce dépôt présente les fonctionnalités ajoutées au projet, notamment la création d’un back-office et les évolutions apportées à la partie front.
## Fonctionnalités ajoutées
### Back-office d'administration
Ajout d'une interface sécurisée permettant la gestion des contenus depuis un espace administrateur.
Trois sections administrables :

    Formations : ajout, modification, suppression, tri, filtrage.

    Playlists : idem.

    Catégories : idem.
Les pages de gestion sont accessibles uniquement après connexion à l’espace admin.
L’interface suit l’architecture visuelle du front (bannière, structure générale).
### Sécurité et bonnes pratiques
Formulaires sécurisés avec protection CSRF.

Requêtes paramétrées via Doctrine (pas de requêtes SQL brutes).

Contrôle des saisies : validation côté serveur.

Accès restreint aux routes back-office par système de rôles.
### Mutualisation de composants Twig
Création de partials pour mutualiser les éléments réutilisables :

    Boutons d’action (modifier/supprimer)

    Messages flash

    Filtres de tri et de recherche
Les partials sont organisés dans templates/admin/partial/ pour une meilleure lisibilité et réutilisabilité.
### Refactorisation du code
  Respect de l’architecture MVC Symfony

  Nettoyage des fichiers inutiles ou redondants

  Amélioration de la lisibilité du code et des templates
## Installation et utilisation en local
### Prérequis
    PHP ≥ 8.1
  
    Symfony CLI
  
    Composer
  
    Serveur local (Wamp, Xampp ou autre)
  
    MySQL
  
  Un navigateur moderne
  ### Étapes d'installation
  #### Cloner le dépôt :
    git clone https://github.com/votre-utilisateur/mediatekformation.git
    cd mediatekformation
  #### Installer les dépendances :
    composer install
  #### Configurer la base de données :
