# BitChest 🪙

Plateforme d'échange de cryptomonnaies développée avec Symfony 8.

## Présentation

BitChest est une application web permettant à des clients d'acheter et vendre des cryptomonnaies avec un portefeuille virtuel. Les administrateurs peuvent gérer les utilisateurs et consulter les cours. Projet réalisé dans le cadre de la formation CDA 3ème année à L'École Multimédia.

## Fonctionnalités

### Interface Client
- Inscription / Connexion / Déconnexion
- Dashboard avec valeur totale du portefeuille et profit/perte global
- Consultation des cryptomonnaies avec graphiques 30 jours (Chart.js)
- Achat de cryptomonnaies (vérification du solde)
- Vente complète d'une position
- Portfolio détaillé avec calcul des plus-values en temps réel

### Interface Admin
- Gestion des utilisateurs (Créer, Lire, Modifier, Supprimer)
- Génération de mot de passe temporaire pour les nouveaux comptes
- Consultation des cryptomonnaies et cotations

## Technologies

- **Backend** : Symfony 8, PHP 8.4, Doctrine ORM
- **Base de données** : MySQL 8
- **Frontend** : Twig, Bootstrap 5, Chart.js
- **Sécurité** : Symfony Security Bundle, Password Hasher
- **Outils** : Composer, Doctrine Migrations, DataFixtures

## Installation

### Prérequis
- PHP 8.2+
- Composer 2.x
- MySQL 8.0+

### Étapes

1. Cloner le dépôt
```bash
git clone https://github.com/Youcef1235/bitchest.git
cd bitchest
```

2. Installer les dépendances
```bash
composer install
```

3. Configurer la base de données dans `.env.local`
```
DATABASE_URL="mysql://root:@127.0.0.1:3306/bitchest?serverVersion=8.0&charset=utf8mb4"
```

4. Créer la base de données et exécuter les migrations
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Charger les données de test
```bash
php bin/console doctrine:fixtures:load
```

6. Lancer le serveur
```bash
php -S localhost:8000 -t public/
```

## Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@bitchest.com | Admin1234! |
| Client | client@bitchest.com | Client1234! |

## Équipe

- **Youcef** – Architecture, entités, authentification, interface admin, redesign UI
- **Malik** – Configuration Symfony, migrations, fixtures, interface client, graphiques

## Licence

Projet scolaire – L'École Multimédia 2025/2026
