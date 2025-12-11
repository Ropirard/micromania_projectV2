# Micromania - Projet PHP Objet

## Avant-propos

Le style chaotique et en manque de beauté est totalement prévu. J'avais comme intention de créer une interface disgracieuse, bien qu'elle peut nuire à l'immersion utilisateur (je ne compte pas recréer un tel monstre plus tard). 

## A NOTER

Il manque quelques fonctionnalités pourtant affichées dans les views tels que la wishlist ou les logs utilisateurs utilisées par les admins. Ces dernières seront probablement mises en fonction plus tard. (Il faut aussi que j'ajoute un système de filtres..)

De plus, les Repository sont quasiment vide, tout est dans les Controller, ça aussi je dois m'en charger.

## ✨ Fonctionnalités

### Pour les Utilisateurs

- **Authentification** : Inscription et connexion sécurisée
- **Panier d'achat** : 
  - Ajout/suppression de jeux
  - Gestion des quantités (permet les doublons)
  - Validation du panier
- **Historique des commandes** :
  - Consultation de toutes les commandes validées
  - Suivi du statut de livraison (En cours de préparation, Expédiée, Livrée)
- **Catalogue** : Navigation dans le catalogue de jeux

### Pour les Administrateurs

- **Dashboard Admin** : Vue d'ensemble de la plateforme
- **Gestion du Catalogue** :
  - Ajout de nouveaux jeux
  - Modification des jeux existants
  - Suppression de jeux
  - Upload d'images
- **Gestion des Commandes** :
  - Vue de toutes les commandes des utilisateurs
  - Mise à jour du statut de livraison
  - Détails des commandes avec informations utilisateur
- **Gestion des Utilisateurs** : Administration des comptes utilisateurs


## 📦 Installation

### Prérequis

- Docker et Docker Compose installés
- Git installé
- Port 8082 disponible (ou modifier dans `docker-compose.yml`)

**Accéder à l'application**

Ouvrir votre navigateur à l'adresse : `http://localhost:8082`



## 📖 Guide d'Utilisation

### Compte Administrateur

Pour accéder au panneau d'administration, connectez-vous avec un compte ayant le rôle `admin` dans la base de données.

#### Gestion des Jeux

**IMPORTANT** : Sur la page d'administration des jeux, il y a un comportement particulier à connaître :

- **NE PAS cliquer** sur le bouton vert "Jeu ?" qui ne fait en réalité rien du tout, purement esthétique
- **Cliquez sur** les liens textuels **"Bouton"** ou **"Ici"** pour accéder aux fonctionnalités :
  - **"Bouton"** : Permet de modifier un jeu existant
  - **"Ici"** : Permet d'ajouter un nouveau jeu

#### Petit plus

Vous avez accès à un bouton 'Cliquer' : cliquez dessus pour vous détendre (le compteur se reset à chaque refresh)

### Gestion des Commandes (Admin)

1. Accéder à "Historique des Commandes" depuis le menu admin
2. Pour chaque commande, vous pouvez :
   - Voir les détails : utilisateur, jeux commandés, date
   - Modifier le statut de livraison via le menu déroulant
   - Les statuts disponibles sont :
     - 🟡 **En cours de préparation** (par défaut)
     - 🔵 **Expédiée**
     - 🟢 **Livrée**
3. Le changement de statut se fait via AJAX sans rechargement de page

### Panier et Commandes (Utilisateur)

1. **Ajouter au panier** : Cliquez sur "Ajouter au panier" sur n'importe quel jeu
2. **Gérer le panier** : Accédez à votre panier via le menu
3. **Valider** : Cliquez sur "Valider le panier" pour créer une commande
4. **Suivre** : Consultez vos commandes dans "Historique" avec le statut de livraison

## Configuration

### Base de Données

Les paramètres de connexion sont définis dans `www/config/database.php` et utilisent les variables d'environnement :

```php
DB_HOST=db
DB_NAME=micromania_db
DB_USER=micromania_user
DB_PASS=micromania_password
```

### Upload d'Images

Les images des jeux sont stockées dans `www/public/uploads/`. Assurez-vous que le dossier a les permissions appropriées :

```bash
chmod -R 775 www/public/uploads/
```

## Améliorations Futures

- [ ] Ajout d'un système de recherche avancée
- [ ] Implementation d'un système de wishlist
- [ ] Ajout de filtres sur le catalogue (genre, plateforme, prix)
- [ ] Système de notation et avis utilisateurs
- [ ] Gestion des stocks
- [ ] Système de paiement

## Auteurs

- **Ropirard** - [GitHub](https://github.com/Ropirard)

## Licence

Ce projet est un projet éducatif/personnel.

---

**Note** : Ce projet utilise des frameworks personnalisés développés par JulienLinard pour l'apprentissage du développement PHP MVC.
