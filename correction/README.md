# Dossier de correction – Laugimmo

Ce dossier regroupe le nécessaire pour corriger/relancer rapidement le projet (installation, configuration, schéma BDD).

## Pré-requis

- PHP (avec extensions PDO + pdo_mysql)
- MySQL/MariaDB
- Composer

## Installation

1) Dépendances PHP

- À la racine du projet : `composer install`

2) Base de données

- Créer une base (ex : `laugimmo`)
- Importer le schéma : [schema.sql](schema.sql)

3) Configuration (variables d’environnement)

Le projet lit sa configuration via les variables d’environnement (voir [config.php](../config.php)).

- `DB_HOST` (ex : `127.0.0.1`)
- `DB_PORT` (ex : `3306` ou port MAMP)
- `DB_NAME` (ex : `laugimmo`)
- `DB_USER`
- `DB_PASS`
- `DB_SOCKET` (optionnel)

Exemple (Apache/.htaccess) :

```
SetEnv DB_HOST "127.0.0.1"
SetEnv DB_PORT "3306"
SetEnv DB_NAME "laugimmo"
SetEnv DB_USER "root"
SetEnv DB_PASS "root"
```

4) Démarrage

- Via Apache/MAMP : ouvrir l’URL du vhost/dossier
- Vérifier l’état : `GET /health`

## Création d’un compte admin (recommandé pour correction)

Une route de setup existe : `/setup/admin?token=...`

1) Définir une variable d’environnement `APP_SETUP_TOKEN` (valeur au choix)
2) Ouvrir `/setup/admin?token=<APP_SETUP_TOKEN>`
3) Renseigner email + mot de passe (≥ 8), valider

Ensuite, l’interface admin est accessible sur `/admin`.

## Tests

- Lancer : `./vendor/bin/phpunit`
