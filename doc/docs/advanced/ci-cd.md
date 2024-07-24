# CI/CD 

CI/CD (standing for Continuous Integration & Continuous Development) is an [Agile](https://www.agilealliance.org/agile101/the-agile-manifesto/) concept closely related to Test Driven Development (TDD).

The idea is to run  set of tests whenever an update is made during development. By doing so we have a feedback loop as small as possible that allows to quickly identify potential issues.

A real-world situation would be, for example, using a GIT versioning platform with a CI pipeline service that automatically runs our test units in any needed environment, whenever we make a new commit.

We'll immediately see if something goes wrong, and easily locate where the problem comes from depending of the quality of our tests set.



## Docker

> Note: if you're looking for a guide that is not specific to eQual, please read [getting started with docker](https://docs.docker.com/get-started/)

In this section we'll focus on the main role of docker: setting up a **replicable environment** for your project.

Below is a specific example to use eQual with Docker using an official php image.



### Setup

Go ahead and [install Docker](https://docs.docker.com/get-docker/)

On windows it comes with **Docker Compose**, which aims to automate the deployment process.

In its simplest form, all you need to use Docker is a few command lines plus those 2 files in your project :

- Dockerfile
- docker-compose.yml

Directory usually looks like this :

```
project@root/
	.docker/
 		Dockerfile
 		another-config-file.example
	docker-compose.yml
```



### Dockerfile

Dockerfile is used to build an image with additional requirements.

The syntax :

**FROM** tells which image you're gonna use as starting point (it can be an existing image from [Docker Hub](https://hub.docker.com/search?q=&type=image), or homemade)

**COPY** tells what files of your project you want to bring inside the image, it can be specific config files aswell as the full project

**WORKDIR** changes your default location inside the directory

**RUN** tells what commands to run within the image; like additional requirements or rights management

> For more informations about dockerfile syntax, read [Best practices for writing Dockerfiles](https://docs.docker.com/develop/develop-images/dockerfile_best-practices/)

Here is an example using eQual with a php-apache image : 

```dockerfile
FROM php:7.3-apache   
# official image taken from docker hub
COPY . /var/www/html	
# copy all the project into apache html folder (must be at project's root)

RUN docker-php-ext-install pdo_mysql mysqli \	
	# installing required mysql service
    && chown -R www-data:www-data /var/www \ 	
    # granting permissions in apache folder
    && cp /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/	
    # apache config
    
WORKDIR /var/www/html	
# changing current location in directory

COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf	
# custom apache vhost
COPY .docker/config.inc.php /config/config.inc.php	  
# changing DB host to "mysql"
COPY .docker/apache2.conf /etc/apache2/apache2.conf	  
# apache config
```

To build an image out of this file, use the following command :

```bash
docker build --file .docker/Dockerfile -t todolist .
```



### docker-compose.yml

`docker-compose.yml` is used to deploy container(s) from one or multiple image(s).

For example, here is what we used to deploy an apache server along with its mysql service :

```yaml
version: '3'
services:
  localhost:
    build:
      context: .
      dockerfile: .docker/Dockerfile
    image: todolist		# using the image previously created with Dockerfile
    container_name: localhost
    restart: always
    ports:
      - 80:80
    volumes:
      - /var/www/html
    environment:
      - QN_DB_NAME=equal
      - QN_DB_HOST=localhost
      - QN_DB_PORT=3306
      - HTTPS_METHOD=noredirect
      - VIRTUAL_PORT=80
      - VIRTUAL_HOST=localhost
    links:
      - mysql
    
  mysql:
    image: mysql:5.7	# using an official image for mySQL
    container_name: mysql
    restart: always
    ports:
      - 3306:3306
    environment:
      MYSQL_DATABASE: equal
      MYSQL_ALLOW_EMPTY_PASSWORD: 'true'
```

To launch the associated Docker command :

```bash
docker-compose up -d
```

With this, you have what you need to develop and test your application using docker.

If you want to see the running containers :

```bash 
docker ps
```

To access the bash CLI of a container :

```bash
docker exec -ti CONTAINER_ID /bin/bash
```

To run a bash script within a container (instead of opening it as CLI) :

```bash
docker exec -ti CONTAINER_ID /bin/bash /full/directory/path/script.sh
```

The following section will cover how that command can be used.



### Automating the process & database init

Unfortunately if you want to setup a database once a container has been created, you still need to run a few commands manually. We'll work this around and use it as an opportunity to automate the whole process, from image creation to deployment.

What we'll do is create a .cmd script (for windows) that we'll launch from the root of the project, using a few of the commands listed above plus the ones needed for the DB setup of a project called "todolist".

```bash
docker build --file .docker/Dockerfile -t todolist .
docker-compose up -d
sleep 10
docker exec -ti localhost /bin/bash .docker/init.sh
```

You've already seen the 2 first lines, they refer to Dockerfile and `docker-compose.yml`.

Sleep 10 is important, granting time for the containers to establish a bridge before we connect the database.

Last line launches the following bash script within the container "localhost".

```bash
php run.php --do=init_db
php run.php --do=init_package --package=core
```

> Note: if it returns a "wrong host" error, try to increase the sleep time to 20 or 30 sec. If it still doesn't work it means some config for apache is either missing or incorrect.



## Travis CI

[Travis CI](https://docs.travis-ci.com/) is a testing environment offering many options and compatibilities.

Configuration example (in YAML) for a **`.travis.yml`** file, running basic tests for checking eQual installation integrity:

```yaml
dist: xenial
language: php
php: 
  - '7.2'  
  
services:
  - mysql

env:
  global:
    - DB_NAME="equal"
    
before_script:
  # install apache2
  - chmod +x .travis/*.sh
  - .travis/init-php.sh 
  
script: 
  # check that mandatory directories are present and have correct access rights set
  - php run.php --do=test_fs-consistency
  # check ability to connect to the dbms service
  - php run.php --do=test_db-connectivity
  # create an empty database 
  - php run.php --do=init_db  
  # initialise database & demo data
  - php run.php --do=init_package --package=core
  # run test units
  - php run.php --do=test_package --package=core
```



## BitBucket Pipelines

[BitBucket](https://bitbucket.org/) has a built-in testing environment called Pipelines that relies on [Docker](https://www.docker.com/). 

They have an amazing [tutorial to get started](https://support.atlassian.com/bitbucket-cloud/docs/get-started-with-bitbucket-pipelines/), as well as a very handful [YAML validator](https://bitbucket-pipelines.prod.public.atl-paas.net/validator).

Here is a minimal syntax example taken from their page about [build artifacts](https://support.atlassian.com/bitbucket-cloud/docs/publish-and-link-your-build-artifacts/) :

```yaml
image: php:7.2-fpm
pipelines:
  default:
    - step:
        script:
          - apt-get update && apt-get install -y default-mysql-client
          - docker-php-ext-install mysqli
          # check that mandatory directories are present and have correct access 
          # rights set
          - php run.php --do=test_fs-consistency
          # check ability to connect to the dbms service
          - php run.php --do=test_db-connectivity
          # create an empty database
          - php run.php --do=init_db
          # initialise database with demo data
          - php run.php --do=init_package --package=core
          # run test units
          - php run.php --do=test_package --package=core --logs=true
        services: 
          - mysql
definitions: 
  services: 
    mysql: 
      image: mysql:5.7
      environment: 
        MYSQL_DATABASE: 'qinoa'
        MYSQL_ROOT_PASSWORD: 'test'
```



## CircleCI

Configuration example (in YAML) for a **`.config.yml`** file, running basic tests for checking eQual installation integrity:

```yaml
version: 2.1

# Define the jobs we want to run for this project
jobs:
  build:
    docker:
    - image: php:7.3-apache
    
    - image: mysql:5.7
      environment:
        MYSQL_ROOT_PASSWORD: test
        MYSQL_DATABASE: equal
    
    working_directory: ~/repo
  
    steps:
      # install dependencies
      - run: apt-get update && apt-get -y install git      
      - run: docker-php-ext-install pdo pdo_mysql mysqli
      - run: docker-php-ext-enable mysqli
    
      - checkout:
          path: ~/repo
      
      # check that mandatory directories are present and have correct access 
      # rights set
      - run: php run.php --do=test_fs-consistency
      # check ability to connect to the dbms service
      - run: php run.php --do=test_db-connectivity
      # create an empty database 
      - run: php run.php --do=init_db  
      # initialise database with demo data
      - run: php run.php --do=init_package --package=core --import=true
      # run test units
      - run: php run.php --do=test_package --package=core	  

# Orchestrate our job run sequence
workflows:
  build_and_test:
    jobs:
      - build

```

