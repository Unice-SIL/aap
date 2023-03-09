# Installation

## 1. Prérequis (si déploiement sans docker)

- php 7.2
- composer
- nodejs
- yarn
- 
## 2. Configuration

Créer le fichier .env.local à partir du .env
```
$ cp -p .env .env.local
```
Renseigner avec les bonnes valeurs les variables d'environnement du fichier .env.local

## 3. Déploiement

### Avec docker (/!\ environnement de développement uniquement /!\\)

Build et démarrage des containers
```
$ docker-compose up --build
```

Url par défaut de l'application : http://localhost:9080  
Url par défaut de phpMyAdmin : http://localhost:9081

Pour entrer dans le container de l'application si besoin
```
$ docker exec -it aap_php bash
```

### Sans docker

Déploiement via Makefile
```
$ Make install
```

Configurer le CRON (en prod uniquement) avec la commande de nettoyage des fichiers inutilisés (php bin/console app:file:clean-up)