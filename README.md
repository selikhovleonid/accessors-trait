# Accessors Trait

Trait for Property Accessors Generation

[![Latest Unstable Version](https://poser.pugx.org/selikhovleonid/accessors-trait/v/unstable)](https://packagist.org/packages/selikhovleonid/accessors-trait)
[![License](https://poser.pugx.org/selikhovleonid/accessors-trait/license)](https://packagist.org/packages/selikhovleonid/accessors-trait)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/selikhovleonid/accessors-trait.svg)](https://packagist.org/packages/selikhovleonid/accessors-trait)

When the trait is used in a child class, it catches the calls of undeclared methods 
of this class. If the name of the invoked method matches the setProperty, getProperty 
or isPropertySet pattern and the target class has corresponding property, then 
this trait calls needed accessor as if it was declared directly in the child-class.


You need just add one of the following tags to the PHPDoc to mark property as 
accessible to the corresponding methods: @get, @set, @isset. Tag @accessors marks
property as full-accessible.