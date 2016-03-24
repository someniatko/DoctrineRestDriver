# Motivation

In the 1940s, the first recognizably modern electrically powered computers were created. The limited speed and memory capacity forced programmers to write hand tuned assembly language programs. It was eventually realized that programming in assembly language required a great deal of intellectual effort and was error-prone.

At the University of Manchester, Alick Glennie developed Autocode in the early 1950s. A programming language, it used a compiler to automatically convert the language into machine code. The first code and compiler was developed in 1952 for the Mark 1 computer at the University of Manchester and is considered to be the first compiled high-level programming language.

Another milestone in the late 1950s was the publication, by a committee of American and European computer scientists, of "a new language for algorithms"; the ALGOL 60 Report (the "ALGOrithmic Language").

The development of C started in 1972 and first appeared in Version 2 Unix.

PHP development began in 1994 when Rasmus Lerdorf wrote several Common Gateway Interface (CGI) programs in C. He extended them to work with web forms and to communicate with databases, and called this implementation "Personal Home Page/Forms Interpreter" or PHP/FI.

Doctrine was started by Konsta Vesterinen. The project's initial commit was made on April 13, 2006. Like all of the named advantages in programming history it abstracted a language that required some kind of intellectual effort with an additional layer, the database abstraction layer, to make it easier to use.

Early PHP was not intended to be a new programming language, and grew organically, with Lerdorf noting in retrospect: "I don’t know how to stop it, there was never any intent to write a programming language […] I have absolutely no idea how to write a programming language, I just kept adding the next logical step on the way."

Let's add the next logical step.

At Circle we believe that requests are assembler instructions in the web. That's why we believe they can be used as foundation for high-level programming languages using the web as if it was a big computer. And that's why this driver exists: We use the Doctrine ORM layer to internally send REST requests to URLs. So the REST requests themselves act like assembler instructions while the Doctrine syntax is used as the high-level programming language. The goal is to get rid off writing REST request calls and instead using a readable, maintainable syntax to get your job done.


# Installation

You should first have a look at the requirements before continuing with the setup section.

## Requirements
- You need composer to download the library
- Your REST API has to strictly follow REST principles and return JSON
    - Use POST to create new data
        - Urls have the following format: ```http://www.host.de/path/to/api/entityName```
        - Use the request body to receive data
        - Respond with HTTP code 200 if successful
        - Fill the response body with the given payload plus an id
    - Use PUT to change data
        - Urls have the following format: ```http://www.host.de/path/to/api/entityName/<id>```
        - Use the request body to receive data
        - Respond with HTTP code 200 if successful
        - Fill the response body with the given payload
    - Use DELETE to remove data
        - Urls have the following format: ```http://www.host.de/path/to/api/entityName/<id>```
        - The request body must be empty
        - Respond with HTTP code 204 if successful
        - The response body must be empty
    - Use GET to receive data
        - Urls have the following format: ```http://www.host.de/path/to/api/entityName/<id>``` or ```http://www.host.de/path/to/api/entityName```
        - Use HTTP query strings as filters
        - Respond with HTTP code 200 if successful
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
    driver_class: "Circle\\DoctrineRestDriver\\Driver"
    host:         "%default_api_url%"
    port:         "%default_api_port%"
    user:         "%default_api_username%"
    password:     "%default_api_password%"
    options:
      authentication_class:  "HttpAuthentication" | "YourOwnNamespaceName" | if not specified no authentication will be used
```

Additionally you can add CURL-specific options:

```yml
doctrine:
  dbal:
    driver_class: "Circle\\DoctrineRestDriver\\Driver"
    host:         "%default_api_url%"
    port:         "%default_api_port%"
    user:         "%default_api_username%"
    password:     "%default_api_password%"
    options:
      authentication_class:  "HttpAuthentication"
      CURLOPT_CURLOPT_FOLLOWLOCATION: true
      CURLOPT_HEADER: true
```

A full list of all possible options can be found here: http://php.net/manual/en/function.curl-setopt.php

# Usage

The following code samples show how to use the driver in a Symfony environment which might be similar to your framework of choice. This configuration will be used:

```yml
doctrine:
  dbal:
    driver_class: "Circle\\DoctrineRestDriver\\Driver"
    host:         "http://www.circle.ai/api/v1"
    port:         "80"
    user:         "Circle"
    password:     "CircleIsGreat"
```

First of all you need to create one or more entities:

```php
namespace CircleBundle\Entity;

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

Afterwards you are able to use the created entity as if you were using a relational database.
Let's assume that we have used the value "http://www.yourSite.com/api/products" for the product entity's @Table annotation.

```php
<?php

namespace CircleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\HttpFoundation\Response;

class UserController extends Controller {

    /**
     * Sends the following request to the API:
     * POST http://www.yourSite.com/api/products HTTP/1.1
     * {"name": null}
     *
     * Let's assume the API responded with:
     * HTTP/1.1 200 OK
     * {"id": 1, "name": null}
     *
     * Response body is "1"
     */
    public function createAction() {
        $em     = $this->getDoctrine()->getEntityManager();
        $entity = new CircleBundle\Entity\Product();
        $em->persist($entity);
        $em->flush();
        
        return new Response($entity->getId());
    }
    
    /**
     * Sends the following request to the API by default:
     * GET http://www.yourSite.com/api/products/1 HTTP/1.1
     *
     * Let's assume the API responded with:
     * HTTP/1.1 200 OK
     * {"id": 1, "name": null}
     *
     * Response body is ""
     */
    public function readAction($id = 1) {
        $em     = $this->getDoctrine()->getEntityManager();
        $entity = $em->find('CircleBundle\Entity\Product', $id);
        
        return new Response($entity->getName());
    }
    
    /**
     * Sends the following request to the API:
     * GET http://www.yourSite.com/api/products HTTP/1.1
     *
     * Let's assume the API responded with:
     * HTTP/1.1 200 OK
     * [{"id": 1, "name": null}]
     *
     * Response body is ""
     */
    public function readAllAction() {
        $em       = $this->getDoctrine()->getEntityManager();
        $entities = $em->getRepository('CircleBundle\Entity\Product')->findAll();
        
        return new Response($entities->first()->getName());
    }
    
    /**
     * After sending a GET request (readAction) it sends the following 
     * request to the API by default:
     * PUT http://www.yourSite.com/api/products/1 HTTP/1.1
     * {"name": "myName"}
     *
     * Let's assume the API responded the GET request with:
     * HTTP/1.1 200 OK
     * {"id": 1, "name": null}
     *
     * and the PUT request with:
     * HTTP/1.1 200 OK
     * {"id": 1, "name": "myName"}
     *
     * Response body is "myName"
     */
    public function updateAction($id = 1) {
        $em     = $this->getDoctrine()->getEntityManager();
        $entity = $em->find('CircleBundle\Entity\Product', $id);
        $entity->setName('myName');
        $em->flush();
        
        return new Response($entity->getName());
    }
    
    /**
     * After sending a GET request (readAction) it sends the following 
     * request to the API by default:
     * DELETE http://www.yourSite.com/api/products/1 HTTP/1.1
     *
     * Let's assume the API responded with:
     * HTTP/1.1 204 No Content
     *
     * Response body is ""
     */
    public function deleteAction($id = 1) {
        $em     = $this->getDoctrine()->getEntityManager();
        $entity = $em->find('CircleBundle\Entity\Product', $id);
        $em->remove($entity);
        $em->flush();
        
        return new Response();
    }
}
```

#Examples

Need some more examples? Here they are.

## Using a REST API to verify addresses
Imagine you have a REST API at http://www.circle.ai/api/v1:

| Route | Method | Description | Payload | Response | Success HTTP Code | Error HTTP Code |
| ------------- |:-------------:| -----:|-----:|-----:|-----:|-----:|
| /addresses | POST | verifies and formats addresses | UnregisteredAddress | RegisteredAddress | 200 | 400 |


```
typedef UnregisteredAddress {
    street: String,
    city: String
}

typedef RegisteredAddress {
    id: Int,
    street: String,
    city: String
}
```

Let's first configure Doctrine:

```yml
doctrine:
  dbal:
    driver_class: "Circle\\DoctrineRestDriver\\Driver"
    host:         "http://www.circle.ai/api/v1"
    port:         "80"
    user:         ""
    password:     ""
```

Afterwards let's build the address entity:

```php
<?php
namespace CircleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table("addresses")
 */
class Address {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string")
     */
    private $street;
    
    /**
     * @ORM\Column(type="string")
     */
    private $city;
    
    public function setStreet($street) {
        $this->street = $stree;
        return $this;
    }
    
    public function getStreet() {
        return $this->street;
    }
    
    public function setCity($city) {
        $this->city = $city;
        return $this;
    }
    
    public function getCity() {
        return $this->city;
    }
    
    public function getId() {
        return $this->id;
    }
}
```

Then the controller and its action:

```php
<?php

namespace CircleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\HttpFoundation\Response;

class AddressController extends Controller {

    public function createAction($street, $city) {
        $em      = $this->getDoctrine()->getEntityManager();
        $address = new CircleBundle\Address();
        $address->setStreet($street)->setCity($city);
        
        $em->persist($address);
        
        try {
            $em->flush();
            return new Response('successfully registered');
        } catch(RequestFailedException) {
            return new Response('invalid address');
        }
    }
}
```

That's it. Each time the createAction is called it will send a POST request to the API.

## Associating entities

Let's extend the first example. Now we want to add users to the addresses.

The REST API offers the following additional routes:

| Route | Method | Description | Payload | Response |
| ------------- |:-------------:| -----:|-----:|-----:|
| /users | POST | persists a new user | UnregisteredUser | RegisteredUser |
| /addresses/\<id\> | GET | returns one address | NULL | RegisteredAddress |

```
typedef UnregisteredUser {
    name: String,
    password: String,
    address: Index
}

typedef RegisteredUser {
    id: Int,
    name: String,
    password: String,
    address: Index
}
```

We need to build an additional entity "User":

```php
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
     * @ORM\OneToOne(targetEntity="CircleBundle\Address")
     */
    private $address;
    
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
    
    public function setAddress(Address $address) {
        $this->address = $address;
        return $this;
    }
    
    public function getAddress() {
        return $this->address;
    }
}
```

The user has a relation to address. So let's have a look at what happens if we associate them:


```php
<?php

namespace CircleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\HttpFoundation\Response;

class UserController extends Controller {

    public function createAction($name, $password, $addressId) {
        $em      = $this->getDoctrine()->getEntityManager();
        $address = $em->find("CircleBundle\Entity\Address", $addressId);
        $user    = new User();
        
        $user->setName($name)
            ->Password($password)
            ->setAddress($address);
        
        $em->persist($user);
        $em->flush();
        
        return new Response('successfully registered');
    }
}
```

Let $name be "username", $password = "secretPassword" and $addressId = 1

The following requests are sent by using the createAction of the UserController:
```
GET  http://www.circle.ai/api/v1/addresses/1 HTTP/1.1
POST http://www.circle.ai/api/v1/users HTTP/1.1 {"name": "username", "password":"secretPassword", "address":1}
```

Great, isn't it?

## Using multiple REST APIs
Of course you can add multiple entity managers as explained in the Doctrine documentation:

```yml
doctrine:
  dbal:
    default_connection: default
    connections:
      default:
        driver_class: "Circle\\DoctrineRestDriver\\Driver"
        host:         "http://www.circle.ai/api/v1"
        port:         "80"
        user:         ""
        password:     ""
      user_api:
        driver_class: "Circle\\DoctrineRestDriver\\Driver"
        host:         "http://api.users.circle.ai/api/v1"
        port:         80
        user:         "Circle"
        password:     "CircleUsers"
        options:
          authentication_class:  "HttpAuthentication"
      validation_api:
        driver_class: "Circle\\DoctrineRestDriver\\Driver"
        host:         "http://api.validation.circle.ai/api/v1"
        port:         80
        user:         "Circle"
        password:     "CircleAddresses"
        options:
          authentication_class:  "HttpAuthentication"
```

Now it's getting crazy. We will read data from one API and send it to another.
Imagine the user API has the following routes:

| Route | Method | Description | Payload | Response |
| ------------- |:-------------:| -----:|-----:|-----:|
| /users/\<id\> | GET | returns one user | NULL | RegisteredUser |
| /addresses/\<id\> | GET | returns one address | NULL | RegisteredAddress |

and the validation API has the following route:

| Route | Method | Description | Payload | Response | Success HTTP Code | Error HTTP Code |
| ------------- |:-------------:| -----:|-----:|-----:|-----:|-----:|
| /addresses | POST | verifies and formats addresses | UnregisteredAddress | RegisteredAddress | 200 | 400 |

We want to use the user API to read the users and its addresses and the validation API to verify the address.
Then we want to persist the data by sending it to our default API.

```php
<?php

namespace CircleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\HttpFoundation\Response;

class UserController extends Controller {

    public function createAction($userId, $name, $password, $addressId) {
        $emUsers       = $this->getDoctrine()->getEntityManager('user_api');
        $emValidation  = $this->getDoctrine()->getEntityManager('validation_api');
        $emPersistence = $this->getDoctrine()->getEntityManager();
        
        $user    = $emUsers->find("CircleBundle\Entity\User", $userId);
        $address = $user->getAddress();
        
        $address = $emValidation->persist($address);
        $emValidation->flush();
        
        $emPersistence->persist($user);
        $emPersistence->persist($address);
        $emPersistence->flush();
        
        return new Response('successfully persisted');
    }
}
```

What's going on here? Have a look at the request log:

```
GET  http://api.users.circle.ai/api/v1/users/1 HTTP/1.1
GET  http://api.users.circle.ai/api/v1/addresses/1 HTTP/1.1
POST http://api.validation.circle.ai/api/v1/addresses HTTP/1.1 {"street": "someValue", "city": "someValue"}
POST http://www.circle.ai/api/v1/addresses HTTP/1.1 {"street": "someValue", "city": "someValue"}
POST http://www.circle.ai/api/v1/users HTTP/1.1 {"name": "username", "password":"secretPassword", "address":1}
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
