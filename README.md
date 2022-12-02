# Project-Of-Projects

A website dedicated to projects presentation. In particular, project teams can present their project needs (ex: materials, skills, advices), and visitors can contact them. Coded with Symfony PHP Framework 5.3.

Note (2022 - 04 - 19) : the code is low quality because of my skills. If you want to make it better, your help will be much appreciated :-)

# Installation Procedure 

#### Important Remark: if you have any troubles with the procedure, do not hesitate to contact me so that I can try to help (contact\[at]propon\[dot]org).

#### 0-a- Prerequisites: make sure these programs are installed:

* Install **Git** (Git is a version control system for tracking changes in computer files) (https://git-scm.com/downloads)

* Install **PHP (7.1 minimum)** along with **MySQL**  (to do so, if you use windows os, you can install WAMP Server for example)

* Install **Composer** (Composer is an application-level package manager for the PHP programming language) (https://getcomposer.org/download/)


#### 0-b- Test your configuration: to do so, type the following commands in a terminal:

* type the command **git** in a terminal, and make sure there is no error message

* type the command **php -v**, and make sure you have et least version 7.1 

* type the command **composer -V** , and make sure there is no error message

#### Remark: if you have troubles lauching the website, make sure MySQL is launched (to do so, you simply have to run wampserver if you use it)
	
#### 1- Download or clone github repository: in a terminal:
git clone https://github.com/LoloForThePOP/Project-Of-Projects.git

#### 2- Move to the right folder
cd project-of-projects

#### 3- Install Dependancies
composer install

#### 4- Configure your database access: in the .env file:
Example for mysql: go to this line: DATABASE_URL=mysql://your_db_username:your_db_password@127.0.0.1:3306/choose_a_db_name

Then replace it like this (adapt to your own): DATABASE_URL=mysql://root:@127.0.0.1:3306/projectofprojects

#### 5- Create a blank Database
php bin/console doctrine:database:create

#### 6-a Create a database scheme migration
php bin/console doctrine:migrations:diff

#### 6-b Execute the database scheme migration
php bin/console doctrine:migrations:execute 'DoctrineMigrations\Versionxxx' (where xxx is your own migration number generated at previous step, you can copy it from your console)

#### 7- Execute Fixtures (= populate your database with fake data so that you can use the website and make some tests)
php bin/console doctrine:fixtures:load --no-interaction

#### 8- Enable or Disable Algolia Search Engine

The website uses Algolia which is a powerfull search engine that can be easily integrated in websites. If you want to use it as it is implemented by default, you need to register to Algolia website (https://www.algolia.com/) in order to get Algolia credentials (then insert these credentials in your .env.local file) (there is a free plan).

If you do not want to use Algolia and avoid errors, go to file algolia_search.yaml and unsubsribe to automatic Algolia calls : 

algolia_search:
    doctrineSubscribedEvents: []

#### 9- Start your Local Web Server

To do that, you can **install "Symfony Local Web Server"** (https://symfony.com/download)

Then, in a terminal, type : symfony server:start