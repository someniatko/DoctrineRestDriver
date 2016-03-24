# Motivation
- What does a black sheep and a white sheep have in common?
- What does a big tiger and a small tiger have in common?
- And what does a database and a REST API have in common?

No matter which color a sheep has, it is a sheep and no matter if a tiger is big or small, it is a tiger. A database is a collection of information that is organized so that it can easily be accessed, managed, and updated and that's exactly what a REST API is.

"I have absolutely no idea how to write a programming language, I just kept adding the next logical step on the way." said Lerdorf, the creator of PHP.
Just skip the first part of his quote so you won't loose your faith in PHP ;)
Let's focus on the second part and add the next logical step:

Let's make the whole Doctrine ecosystem act as a REST client to get rid off boilerplate code.

# Prerequisites

- You need composer to download the library
- Your REST API has to return JSON and to adhere to the following rules:
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

# Installation

Add the driver to your project using composer:

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

The following code samples show how to use the driver in a Symfony environment. This configuration will be used:

```yml
doctrine:
  dbal:
    driver_class: "Circle\\DoctrineRestDriver\\Driver"
    host:         "http://www.your-url.com/api"
    port:         "80"
    user:         "Circle"
    password:     "CantRenember"
```

First of all create your entities:

```php
namespace CircleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This annotation marks the class as managed entity:
 * @ORM\Entity
 *
 * You can either only use a resource name or the whole url of
 * the resource to define your target. In the first case the target 
 * url will consist of the host, configured in your options and the 
 * given name. In the second one your argument is used as it is.
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

Afterwards you are able to use the created entity as if you were using a database.
Let's assume we have used the value ```http://www.yourSite.com/api/products``` for the product entity's ```@Table``` annotation.

```php
<?php

namespace CircleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\HttpFoundation\Response;

class UserController extends Controller {

    /**
     * Sends the following request to the API:
     * POST http://www.yourSite.com/api/products HTTP/1.1
     * {"name": "Circle"}
     *
     * Let's assume the API responded with:
     * HTTP/1.1 200 OK
     * {"id": 1, "name": "Circle"}
     *
     * Response body is "1"
     */
    public function createAction() {
        $em     = $this->getDoctrine()->getEntityManager();
        $entity = new CircleBundle\Entity\Product();
        $entity->setName('Circle');
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
     * {"id": 1, "name": "Circle"}
     *
     * Response body is "Circle"
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
     * [{"id": 1, "name": "Circle"}]
     *
     * Response body is "Circle"
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
     * {"id": 1, "name": "Circle"}
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

Need some more examples? Here they are:

## Using a REST API to verify addresses
Imagine you have a REST API at http://www.your-url.com/api:

| Route | Method | Description | Payload | Response | Success HTTP Code | Error HTTP Code |
| ------------- |:-------------:| -----:|-----:|-----:|-----:|-----:|
| /addresses | POST | verifies and formats addresses | UnregisteredAddress | RegisteredAddress | 200 | 400 |


```c2hs
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
    host:         "http://www.your-url.com/api"
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

And finally the controller and its action:

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
| /addresses | POST | persists a new address | UnregisteredAddress | RegisteredAddress |
| /addresses/\<id\> | GET | returns one address | NULL | RegisteredAddress |

```c2hs
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
     * @ORM\OneToOne(targetEntity="CircleBundle\Address", cascade={"remove"})
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

if we now set name to ```username```, password to ```secretPassword``` and adressid to ```1``` by triggering the createAction, the following requests would be sent by our driver:

```
GET  http://www.your-url.com/api/addresses/1 HTTP/1.1
POST http://www.your-url.com/api/users HTTP/1.1 {"name": "username", "password":"secretPassword", "address":1}
```

Because we have used the option ```cascade={"remove"}``` on the relation between users and addresses DELETE requests for addresses are automatically sent if the owning user is removed:

```php
<?php

namespace CircleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\HttpFoundation\Response;

class UserController extends Controller {
    
    public function remove($id = 1) {
        $em = $this->getDoctrine()->getEntityManager();
        $em->find('CircleBundle\Entity\User', $id);
        $em->remove($em);
        $em->flush();
        
        return new Response('successfully removed');
    }
}
```

The following requests are sent:

```
DELETE http://www.your-url.com/api/addresses/1 HTTP/1.1
DELETE http://www.your-url.com/api/users/1 HTTP/1.1
```


Great, isn't it?

## Using multiple Backends
Of course you can add multiple entity managers as explained in the Doctrine documentation:

```yml
doctrine:
  dbal:
    default_connection: default
    connections:
      default:
        driver:   "pdo_mysql"
        host:     "localhost"
        port:     "8060"
        user:     "root"
        password: "root"
      user_api:
        driver_class: "Circle\\DoctrineRestDriver\\Driver"
        host:         "http://www.your-url.com/api"
        port:         80
        user:         ""
        password:     ""
        options:
          authentication_class:  "HttpAuthentication"
      validation_api:
        driver_class: "Circle\\DoctrineRestDriver\\Driver"
        host:         "http://api.validation.your-url.com/api"
        port:         80
        user:         "Circle"
        password:     "CircleAddresses"
        options:
          authentication_class:  "HttpAuthentication"
```

Now it's getting crazy. We will try to read data from one API and send it to another.
Imagine a user API with the following routes:

| Route | Method | Description | Payload | Response |
| ------------- |:-------------:| -----:|-----:|-----:|
| /users/\<id\> | GET | returns one user | NULL | RegisteredUser |
| /addresses/\<id\> | GET | returns one address | NULL | RegisteredAddress |

and a validation API with this entry points:

| Route | Method | Description | Payload | Response | Success HTTP Code | Error HTTP Code |
| ------------- |:-------------:| -----:|-----:|-----:|-----:|-----:|
| /addresses | POST | verifies and formats addresses | UnregisteredAddress | RegisteredAddress | 200 | 400 |

We want to use the user API to read the users and its addresses and the validation API to verify the address.
Then we want to persist the data into our mysql database.

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
        $emPersistence->flush();
        
        return new Response('successfully persisted');
    }
}
```

What's going on here? Have a look at the request log:

```
GET  http://www.your-url.com/api/v1/users/1 HTTP/1.1
GET  http://www.your-url.com/api/v1/addresses/1 HTTP/1.1
POST http://api.validation.your-url.com/api/v1/addresses HTTP/1.1 {"street": "someValue", "city": "someValue"}
```

Afterwards both entities are persisted in the default mysql database.

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
