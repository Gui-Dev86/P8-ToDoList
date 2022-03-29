## OpenclassRooms - Formation PHP/Symfony Projet 8 - Améliorez un projet existant

## ToDo & Co

Codacy



## Environnement de développement:
    - Symfony 5.3
    - Bootstrap 4.4
    - Composer 1.11
    - WampServer 3.2.8
        - Apache 2.4.46
        - PHP 8.1.4
        - MySQL 5.7.31

## Installation

- Cloner le Repositary GitHub dans le dossier de votre choix: 
```
git clone https://github.com/Gui-Dev86/P8-ToDoList.git
```
- Installez les dépendances du projet avec Composer:
```
composer install
```
- Réalisez une copie du fichier .env nommée .env.local qui devra être crée à la racine du projet. Vous y configurez l'accès aux bases de données du site.

Pour paramétrer votre base de données principale, modifiez cette ligne avec le nom d'utilisateur, mot de passe et nom de la base de données correspondant (ne pas oublier de retirer le # devant la ligne afin qu'elle soit prise en compte).

    # DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"

exemple : DATABASE_URL="mysql://utilisateur(root de base):mot de passe(vide de base)@127.0.0.1:3306/(nom de la base de données, ici todolist)

Pour réaliser les tests du site il est préférable de créer une copie de la base de données afin de ne pas interférer avec la base de données principale du site. Pour paramétrer votre base de données de test, modifiez cette ligne avec le nom d'utilisateur, mot de passe et nom de la base de données correspondant (ne pas oublier de retirer le # devant la ligne afin qu'elle soit prise en compte).

    # DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"

exemple : DATABASE_TEST_URL="mysql://utilisateur(root de base):mot de passe(vide de base)@127.0.0.1:3306/(nom de la base de données, ici todolist_test)

- Si elle n'existe pas déjà créez les bases de données, depuis le répertoire du projet utilisez les commande:
```
php bin/console doctrine:database:create
```
php bin/console doctrine:database:create --connection=test
```
Générez le fichier de migration des tables de la base de données:
```
php bin/console doctrine:migrations:diff
```
Effectez la migration vers la base de données :
```
php bin/console doctrine:migrations:migrate
```
Effectuez à nouveau les opérations pour la base de données de tests:
```
php bin/console doctrine:migrations:diff --em=test
```
php bin/console doctrine:migrations:migrate --em=test
```
- Si vous souhaitez installer des données fictives afin de bénéficier d'une démo vous pouvez installer les fixtures:
```
php bin/console doctrine:fixtures:load
```
Sélectionnez "yes" pour continuer.
```
Effectuez à nouveau l'opération pour la base de données de tests :
```
php bin/console doctrine:fixtures:load --em=test
```
Sélectionnez à nouveau "yes" pour continuer.

Les deux premiers utilisateurs créés sont fixes et possèdent les droits d'administrateur afin de pouvoir tester toutes les fonctionnalités du site:

pseudo: username_1
mot de passe: Azerty!1

pseudo: username_2
mot de passe: Azerty!1

Les huit utilisateurs suivant créés sont de simples utilisateurs afin de tester; entre autre; les droits d'aacès aux administrateurs:

pseudo: username_3
mot de passe: Azerty!1

pseudo: username_4
mot de passe: Azerty!1

etc

Le projet est maintenant correctemont installé. Pour le lancer déplacez vous dans le répertoire du projet et utilisez la commande :
```
$ symfony server:start
```
Auteur Guillaume Vignères - Formation Développeur d'application PHP/Symfony - Openclassroom
