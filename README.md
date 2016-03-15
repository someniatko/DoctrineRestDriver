# Motivation

# Installation

```php
composer require circle/doctrine-rest-driver
```

Change the following doctrine dbal configuration entries:
```yml
doctrine:
  dbal:
    driver_class:   Circle\DoctrineRestDriver\Driver
    host:     "%default_api_url%"
    port:     "%default_api_port%"
    user:     "%default_api_username%"
    password: "%default_api_password%"
    options:
      security_strategy:  "basic_http" | "none"
```

# Usage
Once the driver is configured you can use all doctrine functions as usual:
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

#Contributing
