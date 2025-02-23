# SneakerShop

**SneakerShop** est une boutique en ligne spécialisée dans la vente de sneakers, conçue pour offrir une expérience utilisateur fluide et moderne. Le site se distingue par une interface responsive, une gestion efficace des produits et une section dédiée à la vente entre particuliers.

---

## Table des matières

- [Fonctionnalités](#fonctionnalités)
- [Technologies utilisées](#technologies-utilisées)
- [Structure du projet](#structure-du-projet)
- [Installation et configuration](#installation-et-configuration)
- [Utilisation](#utilisation)
- [Accès administrateur](#accès-administrateur)
- [Contributeurs](#contributeurs)
- [Lien vers le repo](#lien-vers-le-repo)

---

## Fonctionnalités

- **Gestion des articles** :
  - Création, modification et suppression d'articles via une interface administrateur.
  - Mise en vente de sneakers par l'administration et par des utilisateurs (section "Vente entre particuliers").
- **Recherche en temps réel** :

  - Recherche dynamique des articles à l'aide d'une barre de recherche live.

- **Interface moderne et responsive** :

  - Conception avec Twig et TailwindCSS pour une expérience utilisateur optimale sur mobile et desktop.

- **Panier et commandes** :
  - Ajout d’articles au panier, gestion des quantités et affichage d’un résumé de commande.

---

## Technologies utilisées

- **Backend** : Symfony (PHP)
- **Frontend** : Twig, TailwindCSS, JavaScript (Fetch API pour les appels AJAX)
- **Base de données** : MySQL

---

## Structure du projet

- `/src` : Code source de l’application (contrôleurs, entités, formulaires, sécurité, etc.)
- `/templates` : Fichiers de templates Twig
- `/public` : Fichiers accessibles au public (images, assets CSS/JS, favicon, etc.)
- `/config` : Configuration du projet (services, sécurité, routes, etc.)
- `/migrations` : Scripts de migration de la base de données (si applicable)

---

## Installation et configuration

1. **Cloner le dépôt** :

   ```bash
   git clone https://github.com/EnzoTurpin/SneakerShop.git
   cd SneakerShop
   ```

2. **Configurer l'environnement** :  
   Copier le fichier `.env` en `.env.local` et adapter les variables (connexion à la base de données, etc.).

3. **Mettre à jour la base de données** :

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

4. **Lancer le serveur** :

   ```bash
   symfony serve
   ```

   ou

   ```bash
   php -S localhost:8000 -t public
   ```

---

## Utilisation

- **Accès public** :  
  Le site est accessible pour tous les utilisateurs avec une navigation intuitive et une recherche en temps réel.

- **Section administration** :  
  Les articles du site (sneakers) sont gérés par l'administration via l'interface dédiée accessible à l'URL `/admin/add-article`.

- **Vente entre particuliers** :  
  Une section dédiée permet aux utilisateurs de mettre en vente leurs sneakers.

  - Listing des annonces : `/vente/articles`
  - Ajout d’une annonce utilisateur : `/vente/articles/utilisateur/add`
  - Détail d'une annonce : `/vente/article/{id}`

- **Recherche** :  
  La barre de recherche (disponible dans le header) permet d'effectuer une recherche en temps réel des articles en fonction du nom ou de la description.

---

## Accès administrateur

Pour tester le compte administrateur, utilisez les identifiants suivants :

- **Email** : enzo@enzo.fr
- **Mot de passe** : enzo

---

## Contributeurs

- **Turpin Enzo**
- **Bouchene Maria**
- **Matro Daryl**
- **Hardy Guillaume**

---

## Lien vers le repo

[https://github.com/EnzoTurpin/SneakerShop.git](https://github.com/EnzoTurpin/SneakerShop.git)
