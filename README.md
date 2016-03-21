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
      authentication_class:  "Circle\\DoctrineRestDriver\\Security\\HttpBasicAuthentication"
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
      authentication_class:  "Circle\\DoctrineRestDriver\\Security\\HttpBasicAuthentication"
      CURLOPT_CURLOPT_FOLLOWLOCATION: true
      CURLOPT_HEADER: true
```

The full list of all options you can find here: http://php.net/manual/en/function.curl-setopt.php

# Usage
Once the driver is configured you can use Doctrine as described in its documentation. Let's first build an entity.

```php
namespace Some;

use Doctrine\ORM\Mapping as ORM;

/**
 * This annotation marks the class as managed entity:
 * @ORM\Entity
 *
 * This annotation is used to define the target resource of the API. You can whether 
 * use the resource name (then the target url will consist of the configured host 
 * parameter and the resource name) or the target url itself
 * @ORM\Table("product|http://www.yourSite.com/api")
 */
class Namespace {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $someAttribute;
    
    public function getId() {
        return $this->id;
    }
    
    public function setSomeAttribute($someAttribute) {
        $this->someAttribute = $someAttribute;
        return $this;
    }
    
    public function getSomeAttribute() {
        return $this->someAttribute;
    }
}
```

Afterwards you are able to use the created entity as if you were using a relational database:

```php
/* @var $em Doctrine\ORM\EntityManager */

// These ones are sending GET requests
$entity   = $em->find('Some\Namespace', $id);
$entity   = $em->getRepository('Some\Namespace')->findOneBy(['someAttribute' => 'someValue']);
$entity   = $em->createQuery('SELECT s FROM Some\Namespace WHERE s.id=1')->getSingleResult();
$entities = $em->getRepository('Some\Namespace')->findAll();
$entities = $em->getRepository('Some\Namespace')->findBy(['someAttribute' => 'someValue']);
$entity   = $em->createQuery('SELECT s FROM Some\Namespace')->getResult();

// This one sends a POST request
$entity = new Some\Namespace();
$em->persist($entity);
$em->flush();

// This one first sends a GET request and afterwards a PUT request
$entity = $em->find('Some\Namespace', $id);
$entity->setSomeAttribute('someNewValue');
$em->flush();

// This one first sends a GET request and afterwards a DELETE request
$entity = $em->find('Some\Namespace', $id);
$em->remove($entity);
$em->flush();
```

#Examples

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
