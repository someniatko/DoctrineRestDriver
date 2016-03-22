# Motivation

One year ago we built a REST client, because using the PHP internal curl library felt like we were in the stone age and someone just made fire - without any knowledge how to repeat it. But after playing a bit with this client we felt like we were in stone age, too, but have a look at what we created. Big progress so far :clap:

![alt tag](http://img.memecdn.com/stone-penises_o_2506359.webp)

So what is the problem with all these REST clients out there?

The short version: All of them are like our stone buildings, but have different sizes.

We believe that translating SQL to REST is possible, so we decided to create a REST driver for Doctrine that uses the full power of ORM, but with the scalability of RESTful applications. You don't need to write HTTP requests by yourself - Doctrine is handling the requests in the background, maps the responses automatically and returns well-formed entities. 

Now REST requests are handled like SQL: You only have to manually create them if you have special use cases - which we believe is great.


# Installation

You should first have a look at the requirements before continuing with the setup section.

## Requirements
- You need composer to download the library
- Your REST API has to strictly follow REST principles and return JSON
    - Use POST to create new data
        - Urls have the following format: http://www.host.de/path/to/api/entityName
        - Use the request body to receive data
        - Has to respond with HTTP code 200 if successful
        - Fill the response body with the given payload plus an id
    - Use PUT to change data
        - Urls have the following format: http://www.host.de/path/to/api/entityName/\<id\>
        - Use the request body to receive data
        - Has to respond with HTTP code 200 if successful
        - Fill the response body with the given payload
    - Use DELETE to remove data
        - Urls have the following format: http://www.host.de/path/to/api/entityName/\<id\>
        - The request body must be empty
        - Has to respond with HTTP code 204 if successful
        - The response body must be empty
    - Use GET to receive data
        - Urls have the following format: http://www.host.de/path/to/api/entityName/\<id\> or http://www.host.de/path/to/api/entityName
        - Use HTTP query strings as filters
        - Has to respond with HTTP code 200 if successful
        - Fill the response body with the read data

## Setup

Add the driver to your project by using composer:

```php
composer require circle/doctrine-rest-driver
```

Change the following doctrine dbal configuration entries:

```yml
doctrine:
  dbal:
    driver_class:   "Circle\\DoctrineRestDriver\\Driver"
    host:     "%default_api_url%"
    port:     "%default_api_port%"
    user:     "%default_api_username%"
    password: "%default_api_password%"
    options:
      authentication_class:  "HttpBasicAuthentication" | "NoAuthentication" | "YourOwnNamespaceName"
```

Additionally you can add CURL-specific options:

```yml
doctrine:
  dbal:
    driver_class:   "Circle\\DoctrineRestDriver\\Driver"
    host:     "%default_api_url%"
    port:     "%default_api_port%"
    user:     "%default_api_username%"
    password: "%default_api_password%"
    options:
      authentication_class:  "HttpBasicAuthentication"
      CURLOPT_CURLOPT_FOLLOWLOCATION: true
      CURLOPT_HEADER: true
```

A full list of all possible options can be found here: http://php.net/manual/en/function.curl-setopt.php

# Usage
First of all we need to create one or more entities:

```php
namespace MyNamespace;

use Doctrine\ORM\Mapping as ORM;

/**
 * This annotation marks the class as managed entity:
 * @ORM\Entity
 *
 * This annotation is used to define the target resource of the API. You can either use only a resource name in which 
 * case the target url will consist of the host, configured in your options and the given name or you can use the whole 
 * url of the target:
 * @ORM\Table("products|http://www.yourSite.com/api/products")
 */
class Product {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;
    
    public function getId() {
        return $this->id;
    }
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }
}
```

Afterwards you are able to use the created entity as if you were using a relational database:

```php
<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// database configuration parameters
$conn = [
    'user'          => 'Circle',
    'password'      => 'mySecretPassword',
    'host'          => 'http://www.myApi.com',
    'port'          => 8080,
    'driverClass'   => 'Circle\DoctrineRestDriver\Driver',
    'driverOptions' => [
        'authentication_class' => 'HttpBasicAuthentication'
    ],
];

// obtaining the entity manager
$em = EntityManager::create($conn, $config);

// These ones are sending GET requests
$entity   = $em->find('MyNamespace\Product', $id);
$entity   = $em->getRepository('MyNamespace\Product')->findOneBy(['someAttribute' => 'someValue']);
$entity   = $em->createQuery('SELECT s FROM MyNamespace\Product WHERE s.id=1')->getSingleResult();
$entities = $em->getRepository('MyNamespace\Product')->findAll();
$entities = $em->getRepository('MyNamespace\Product')->findBy(['someAttribute' => 'someValue']);
$entity   = $em->createQuery('SELECT s FROM MyNamespace\Product')->getResult();

// This one sends a POST request
$entity = new MyNamespace\Product();
$em->persist($entity);
$em->flush();

// This one first sends a GET request and afterwards a PUT request
$entity = $em->find('MyNamespace\Product', $id);
$entity->setName('name');
$em->flush();

// This one first sends a GET request and afterwards a DELETE request
$entity = $em->find('MyNamespace\Product', $id);
$em->remove($entity);
$em->flush();
```

#Examples

## Using a REST API as persistent storage
Imagine you want to build an application that just acts like a REST API's client.
- The REST API has the URL http://www.circle.ai/api/v1
- It is secured by Basic HTTP Authentication
- The username is Circle, the password is mySecretPassword
- Let's say the REST API itself persists users
- One user is actually stored in the database
    - {id: 1, name: root, password: rootPassword }

```
typedef UnregisteredUser {
    name: String,
    Password: String
}

typedef RegisteredUser {
    id: Int,
    name: String,
    Password: String
}
```

The REST API offers the following routes:

| Route | Method | Description | Payload | Response |
| ------------- |:-------------:| -----:|-----:|-----:|
| /users | GET | returns all users | NULL | [ RegisteredUser ] |
| /users/\<id\> | GET | returns one user | NULL | RegisteredUser |
| /users | POST | persists a new user | UnregisteredUser | RegisteredUser |
| /users/\<id\> | DELETE | deletes a user | NULL | NULL |
| /users/\<id\> | PUT | edits a user | RegisteredUser | RegisteredUser |

Let's connect to the REST API via DoctrineRestDriver.

Entity:

```
namespace Circle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table("users")
 */
class User {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string")
     */
    private $name;
    
    /**
     * @ORM\Column(type="string")
     */
    private $password;
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function getId() {
        return $this->id;
    }
}
```

Create, Read, Update, Delete Script for users:

```
<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), true);

$conn = [
    'user'          => 'Circle',
    'password'      => 'mySecretPassword',
    'host'          => 'http://www.circle.ai/api/v1',
    'port'          => 80,
    'driverClass'   => 'Circle\DoctrineRestDriver\Driver',
    'driverOptions' => [
        'authentication_class' => 'HttpBasicAuthentication'
    ],
];

// obtaining the entity manager
$em = EntityManager::create($conn, $config);

// Sends a GET request to the url http://www.circle.ai/api/v1/users/1
$user = $em->find('Circle\Entity\User', 1);

// prints 'root'
print_r($user->getName());

$user->setName('circle');

// Sends a PUT request to the url http://www.circle.ai/api/v1/users/1 with the payload "{"id": 1, "name": "circle", "password": "rootPassword"}"
$em->flush();

$newUser = new User();
$user->setName('newUser');
$user->setPassword('newPassword');
$em->persist($user);

// Sends a POST request to the url http://www.circle.ai/api/v1/users with the payload "{"name": "newUser", "password": "newPassword"}"
$em->flush();

// If the REST API responded correctly with "{"id": 2, "name": "newUser", "password": "newPassword"}" it prints: 2
print_r($newUser->getId());

$em->remove($user);

// Sends a DELETE request to the url http://www.circle.ai/api/v1/users/1 with no payload
$em->flush();

$sameUser = $em->find('Circle\Entity\User', 1);
// prints null, because the user has been deleted
print_r($sameUser);
```

## Using multiple REST APIs
Of course you can add multiple entity managers as explained in the Doctrine documentation:

```yml
doctrine:
  dbal:
    default_connection: twitter_api
    connections:
      twitter_api:
        driver_class:   "Circle\\DoctrineRestDriver\\Driver"
        host:     "%twitter_api_url%"
        port:     "%twitter_api_port%"
        user:     "%twitter_api_username%"
        password: "%twitter_api_password%"
        options:
          authentication_class:  "HttpBasicAuthentication"
          CURLOPT_CURLOPT_FOLLOWLOCATION: true
          CURLOPT_HEADER: true
      facebook_api:
        driver_class:   "Circle\\DoctrineRestDriver\\Driver"
        host:     "%facebook_api_url%"
        port:     "%facebook_api_port%"
        user:     "%facebook_api_username%"
        password: "%facebook_api_password%"
        options:
          authentication_class:  "HttpBasicAuthentication"
          CURLOPT_CURLOPT_FOLLOWLOCATION: true
          CURLOPT_HEADER: true
```

## Using a REST API and a relational database at the same time
It is also possible to use a REST connection and a relational database connection like mysql.

```yml
doctrine:
  dbal:
    default_connection: my_database
    connections:
      my_database:
        driver:   "pdo_mysql"
        host:     "%twitter_api_url%"
        port:     "%twitter_api_port%"
        user:     "%twitter_api_username%"
        password: "%twitter_api_password%"
      facebook_api:
        driver_class:   "Circle\\DoctrineRestDriver\\Driver"
        host:     "%facebook_api_url%"
        port:     "%facebook_api_port%"
        user:     "%facebook_api_username%"
        password: "%facebook_api_password%"
        options:
          authentication_class:  "HttpBasicAuthentication"
          CURLOPT_CURLOPT_FOLLOWLOCATION: true
          CURLOPT_HEADER: true
```

#Testing

To test the bundle just type:

```
make test
```

#Contributing
If you want to contribute to this repository, please ensure ...
  - to follow the existing coding style.
  - to use the linting tools that are listed in the ```composer.json``` (which you get for free when using ```make```).
  - to add and/or customize unit tests for any changed code.
  - to reference the corresponding issue in your pull request with a small description of your changes.

All contributors are listed in the ```AUTHORS``` file, sorted by the time of their first contribution.
