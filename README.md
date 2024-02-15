# Gestionnaire de Catalogue de Produits

Ce projet implémente une application web permettant de gérer un catalogue de produits. L'application offre plusieurs fonctionnalités telles que l'importation de produits depuis une source externe, l'affichage paginé des produits avec des filtres de recherche, la visualisation détaillée d'un produit, l'insertion de nouveaux produits et la suppression de produits existants.

## Fonctionnalités

- Importation des produits depuis une source externe via une routine CLI/API.
- Affichage paginé des produits avec des filtres de recherche textuelle par nom, catégorie, prix et note moyenne des reviews.
- Affichage de la fiche détaillée d'un produit, incluant tous les champs de la base de données et la note moyenne des reviews.
- Insertion d'un nouveau produit en respectant les critères de validation.
- Suppression d'un produit existant.
- Utilisation d'une couche applicative de cache pour minimiser les appels à la base de données, avec rafraîchissement du cache lors de l'insertion ou de la suppression d'un produit.

## Installation

1. Clonez ce dépôt sur votre machine locale.
2. Assurez-vous d'avoir PHP (version > 8.1) et Composer installés.
3. Exécutez 'composer install' pour installer les dépendances PHP.
4. Avant de configurer les variables d'environnement, assurez-vous d'avoir créé la base de données MongoDB.
5. Créez la base de données MongoDB à l'aide de MongoDB Compass ou de toute autre méthode.
6. Dans le fichier '.env' et configurez les variables d'environnement selon votre environnement. Assurez-vous de configurer 'MONGODB_DB' et 'MONGODB_URL' avec les informations de connexion à votre base de données MongoDB. Par exemple :
'MONGODB_URL=mongodb://localhost:27017'
'MONGODB_DB=ATS'

7. Exécutez 'php bin/console doctrine:mongodb:schema:create' pour créer le schéma de la base de données.

## Importation des Données

Pour importer les données depuis une URL externe dans la base de données MongoDB, suivez les étapes ci-dessous :

1. Assurez-vous que votre base de données MongoDB est correctement configurée dans le fichier '.env', en particulier la variable 'API_URL'.
2. Exécutez la commande suivante pour importer les données :

'''bash
php bin/console import-products


## Utilisation

1. Accédez à l'application dans votre navigateur à l'adresse 'http://localhost:8000'.
2. Explorez les différentes fonctionnalités de l'application.

## Auteurs

- [Essalah Hassen]
