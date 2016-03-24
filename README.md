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
        - Urls have the following format: http://www.host.de/path/to/api/entityName/&lt;id&gt;
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

```php
<?php

namespace CircleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\HttpFoundation\Response;

class UserController extends Controller {

    /**
     * Sends the following request to the API:
     *
     * If you used @Table("products"):
     * POST http://www.circle.ai/api/v1/products HTTP/1.1
     * {"name": null}
     *
     * Or if you used @Table("http://www.yourSite.com/api/products"):
     * POST http://www.yourSite.com/api/products HTTP/1.1
     * {"name": null}
     *
     * Response body is "1"
     */
    public function createAction() {
        $em     = $this->getDoctrine()->getEntityManager();
        $entity = new CircleBundle\Product();
        $em->persist($entity);
        $em->flush();
        
        return new Response($entity->getId());
    }
    
    /**
     * $id may be 1 in our case
     * Sends the following request to the API:
     * 
     * If you used @Table("products"):
     * GET http://www.circle.ai/api/v1/products/1 HTTP/1.1
     *
     * Or if you used @Table("http://www.yourSite.com/api/products"):
     * GET http://www.yourSite.com/api/products/1 HTTP/1.1
     *
     * Response body is null if the createAction was executed before
     */
    public function readAction($id) {
        $em     = $this->getDoctrine()->getEntityManager();
        $entity = $em->find('CircleBundle\Product', $id);
        
        return new Response($entity->getName());
    }
    
    /**
     * Sends the following request to the API:
     * 
     * If you used @Table("products"):
     * GET http://www.circle.ai/api/v1/products HTTP/1.1
     *
     * Or if you used @Table("http://www.yourSite.com/api/products"):
     * GET http://www.yourSite.com/api/products HTTP/1.1
     *
     * Response body is null if the createAction was executed before
     */
    public function readAllAction() {
        $em       = $this->getDoctrine()->getEntityManager();
        $entities = $em->getRepository('CircleBundle\Product')->findAll();
        
        return new Response($entities->first()->getName());
    }
    
    /**
     * $id may be 1 in our case
     * After sending a GET request (readAction) it sends the following 
     * request to the API:
     *
     * If you used @Table("products"):
     * PUT http://www.circle.ai/api/v1/products/1 HTTP/1.1
     * {"name": "myName"}
     *
     * Or if you used @Table("http://www.yourSite.com/api/products"):
     * PUT http://www.yourSite.com/api/products/1 HTTP/1.1
     * {"name": "myName"}
     *
     * Response body is "myName"
     */
    public function updateAction($id) {
        $em     = $this->getDoctrine()->getEntityManager();
        $entity = $em->find('CircleBundle\Product', $id);
        $entity->setName('myName');
        $em->flush();
        
        return new Response($entity->getName());
    }
    
    /**
     * $id may be 1 in our case
     * After sending a GET request (readAction) it sends the following 
     * request to the API:
     *
     * If you used @Table("products"):
     * DELETE http://www.circle.ai/api/v1/products/1 HTTP/1.1
     *
     * Or if you used @Table("http://www.yourSite.com/api/products"):
     * DELETE http://www.yourSite.com/api/products/1 HTTP/1.1
     */
    public function deleteAction($id) {
        $em     = $this->getDoctrine()->getEntityManager();
        $entity = $em->find('CircleBundle\Product', $id);
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

Let's improve the first example. Now we want to add users to the addresses.

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
        $user->setName($name)->Password($password)->setAddress($address);
        
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
      twitter_api:
        driver_class:   "Circle\\DoctrineRestDriver\\Driver"
        host:           "http://twitter.com/api"
        port:           80
        user:           "Circle"
        password:       "CircleTwitter"
        options:
          authentication_class:  "HttpAuthentication"
      facebook_api:
        driver_class:   "Circle\\DoctrineRestDriver\\Driver"
        host:           "http://facebook.com/api"
        port:           80
        user:           "Circle"
        password:       "CircleFacebook"
        options:
          authentication_class:  "HttpAuthentication"
```

Now it's getting crazy. We will read data from one API and send it to another.
Imagine the twitter API has the following routes:

| Route | Method | Description | Payload | Response |
| ------------- |:-------------:| -----:|-----:|-----:|
| /users/\<id\> | GET | returns one user | NULL | RegisteredUser |
| /addresses/\<id\> | GET | returns one address | NULL | RegisteredAddress |

and the facebook API has the following route:

| Route | Method | Description | Payload | Response | Success HTTP Code | Error HTTP Code |
| ------------- |:-------------:| -----:|-----:|-----:|-----:|-----:|
| /addresses | POST | verifies and formats addresses | UnregisteredAddress | RegisteredAddress | 200 | 400 |

We want to use the twitter API to read the users and its addresses and the facebook API to verify the address.
Then we want to persist the data by sending it to our default API.

```php
<?php

namespace CircleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\HttpFoundation\Response;

class UserController extends Controller {

    public function createAction($userTwitterId, $name, $password, $addressId) {
        $emTwitter  = $this->getDoctrine()->getEntityManager('twitter_api');
        $emFacebook = $this->getDoctrine()->getEntityManager('facebook_api');
        $em         = $this->getDoctrine()->getEntityManager();
        
        $user       = $emTwitter->find("CircleBundle\Entity\User", $userTwitterId);
        $address    = $user->getAddress();
        
        $address    = $emFacebook->persist($address);
        $emFacebook->flush();
        
        $em->persist($user);
        $em->persist($address);
        $em->flush();
        
        return new Response('successfully registered');
    }
}
```

What's going on here? Have a look at the request log:

```
GET  http://twitter.com/api/v1/users/1 HTTP/1.1
GET  http://twitter.com/api/v1/addresses/1 HTTP/1.1
POST http://facebook.com/api/addresses HTTP/1.1 {"street": "someValue", "city": "someValue"}
POST http://facebook.com/api/users HTTP/1.1 {"name": "username", "password":"secretPassword", "address":1}
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
