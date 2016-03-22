# Motivation
In the beginning, when human kind created the REST API, the REST API was useless and isolated. Then human kind commanded,
"Let there be curl libraries" - and curl libraries appeared. Human kind was pleased with what they saw. Then they seperated the
curl libraries from the rest of the source code and they named the curl libraries "REST clients" and the rest of the source code
"business logic". Evening passed and morning came - that was the first day.

Then human kind commanded: "Let there be object relational mapping frameworks so object oriented programming and relational
databases come together, so that classes will appear" - and it was done. They named the classes "entities" and the relational mapping
framework "Doctrine". And human kind was pleased with what it saw. Then they commanded: "Let Doctrine produce all kinds of entities
those that have relations and those that have none" - and it was done. So Doctrine produced all kinds of entities and human kind
was pleased with what it saw. Evening passed and morning came - that was the second day.

Then human kind said: "Let's use REST clients and Doctrine side by side without any more improvement." - and it was done. There was
nothing new to give it a name and human kind was disappointed. They looked at everything they had made and it was a big piece of shit.
Evening passed and morning came - that was the third day.

Then Circle said: "And now we will make a Doctrine REST driver. It will be like us and resemble us. It will have the power of Doctrine,
but with the scalability of a RESTful application". So Circle created the Doctrine REST driver, blessed it, and said: "Have many children,
so that your descendants will live all over the earth and bring it under their control." - and it was done. Human kind looked at everything
Circle had made, and they were very pleased. Evening passed and morning came - that was the last day of the REST client's creation.

# Installation

First of all download the driver by using composer:

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

The full list of all options you can find here: http://php.net/manual/en/function.curl-setopt.php

# Usage
Entity:

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
- The REST API has the URL http://www.circle.ai/api/v1.
- It is secured by Basic HTTP Authentication.
- The username is Circle, the password is mySecretPassword.
- Let's say the REST API itself persists users.
- One user is actually stored in the database
    - id: 1
    - name: root
    - password: rootPassword

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

Let's implement all routes with the DoctrineRestDriver.

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

// If the REST API responded correctly with "{"id": 2, "name": "newUser", "password": "newPassword"}", then it prints: 2
print_r($newUser->getId());

$em->remove($user);

// Sends a DELETE request to the url http://www.circle.ai/api/v1/users/1 with no payload
$em->flush();

$sameUser = $em->find('Circle\Entity\User', 1);
// prints null, because the user has been deleted
print_r($sameUser);
```

## Using multiple REST APIs
text here

## Using a REST API and a relational database at the same time
text here

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
