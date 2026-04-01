# Laugimmo

Projet web PHP (BTS SIO – E6) : plateforme de petites annonces immobilières avec recherche, réservation et messagerie.

## Accès rapide (E6)

- Documentation (dossier dédié) : [docs/](docs/README.md)
- Dossier de correction (installation + SQL) : [correction/](correction/README.md)

## Fonctionnalités

- Comptes : inscription, connexion, déconnexion
- Annonces : création, modification, suppression, ajout de photos
- Recherche : filtres (localisation, pièces, prix, catégorie, piscine)
- Réservations : contrôle des chevauchements de dates, calcul du total
- Messagerie : échanges liés à une annonce
- Administration : suppression utilisateurs / annonces
- Healthcheck : endpoint JSON `/health`

## Démarrage (résumé)

- Installer les dépendances : `composer install`
- Créer la base et importer le schéma : [correction/schema.sql](correction/schema.sql)
- Configurer les variables d’environnement (DB_*), voir [correction/README.md](correction/README.md)
- Ouvrir l’application via Apache/MAMP (ou un serveur PHP)

## Tests

- Lancer : `./vendor/bin/phpunit`
