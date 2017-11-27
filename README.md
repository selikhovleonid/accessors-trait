# Accessors Trait

Trait for Property Accessors Generation

[![Latest Unstable Version](https://poser.pugx.org/selikhovleonid/accessors-trait/v/unstable)](https://packagist.org/packages/selikhovleonid/accessors-trait)
[![License](https://poser.pugx.org/selikhovleonid/accessors-trait/license)](https://packagist.org/packages/selikhovleonid/accessors-trait)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/selikhovleonid/accessors-trait.svg)](https://packagist.org/packages/selikhovleonid/accessors-trait)

## Installing

The minimum required PHP version is PHP 5.4. You will need Composer dependency 
manager to install this tiny tool.

```
php composer.phar require selikhovleonid/accessors-trait:dev-master
```

## Quick start

When the trait is used in a child class, it catches the calls of undeclared methods 
of this class. If the name of the invoked method matches the setProperty, getProperty 
or isPropertySet pattern and the target class has corresponding property, then 
this trait calls needed accessor as if it was declared directly in the child class.


You need just add one of the following tags to the PHPDoc block to mark property as 
accessible to the corresponding methods: `@get`, `@set`, `@isset`. Tag `@accessors` 
marks property as full-accessible.

```php
/**
 * Foo class description
 */
class Foo
{
    use \nadir2\AccessorsTrait;

    /**
     * @var string Property description
     * @accessors
     */
    protected $property;

    /**
     * @var array Another property description
     * @get
     * @isset
     */
    private $anotherProperty = [];

    /**
     * This method sets value of 'another property'.
     * @param array $data Passed data.
     * @return self
     */
    public function setAnotherProperty(array $data)
    {
        $this->anotherProperty = $data;
        return $this;
    }
}

$foo = new Foo();

// The following code is valid
if (!$foo->isPropertySet()) {
    $foo->setProperty('bar');
    $bar = $foo->getProperty();
}
if (empty($foo->getAnotherProperty())) {
    $baz = $foo->setAnotherProperty(['baz'])->getAnotherProperty();
}
```