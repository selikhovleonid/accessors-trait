<?php

namespace nadir2\tools;

/**
 * This trait provides auto generation of accessors and mutators methods (get-,
 * set- and isSet-) to the public properties of the extended classes.
 * @author Leonid Selikhov
 */
trait AccessorsTrait
{
    /**
     * @var array The cache of properties accessibility.
     */
    private $propCache = [];

    /**
     * The method gets PHPDoc comment block of passed property and parses it.
     * After that it defines accessibility of the property for each type of methods
     * and pushes created structure to the cache.
     * @param string $propName The property name.
     */
    private function addPropToCache($propName)
    {
        $this->propCache[$propName] = [
            'accessors' => [
                'get'   => false,
                'set'   => false,
                'isset' => false,
            ],
            'type' => null,
        ];
        $reflection           = new \ReflectionClass(get_class($this));
        if ($reflection->hasProperty($propName)) {
            $docComment       = $reflection->getProperty($propName)
                ->getDocComment();
            $parsedDocComment = phpDocParser\parseDocComment($docComment);
            // Lambda
            $getTagByName = function ($name, array $tags) {
                foreach ($tags as $tag) {
                    if ($tag['name'] === $name) {
                        return $tag;
                    }
                }
                return null;
            };
            if (($rawType = $getTagByName('var', $parsedDocComment['tags'])) !== null) {
                if (!is_null($rawType['type'])) {
                    $this->propCache[$propName]['type'] = $rawType['type'];
                }
            }
            if (!is_null($getTagByName('accessors', $parsedDocComment['tags']))) {
                array_walk(
                    $this->propCache[$propName]['accessors'],
                    function (&$value) {
                        $value = true;
                    }
                );
            } else {
                if (!is_null($getTagByName('get', $parsedDocComment['tags']))) {
                    $this->propCache[$propName]['accessors']['get'] = true;
                }
                if (!is_null($getTagByName('set', $parsedDocComment['tags']))) {
                    $this->propCache[$propName]['accessors']['set'] = true;
                }
                if (!is_null($getTagByName('isset', $parsedDocComment['tags']))) {
                    $this->propCache[$propName]['accessors']['isset'] = true;
                }
            }
        }
    }

    /**
     * It's a reflection method, which checks a availability and accessibility
     * of the properties of the child-class.
     * @param string $propName The property name.
     * @return boolean
     */
    private function isPropAccessible($accessorName, $propName)
    {
        if (!isset($this->propCache[$propName])) {
            $this->addPropToCache($propName);
        }
        return $this->propCache[$propName]['accessors'][$accessorName];
    }

    /**
     * It throws access-level exception of a property.
     * @param string $accessorName Accessor type.
     * @param string $propName The property name.
     * @param string $className Current class name.
     * @throws \nadir2\tools\AccessorsException
     */
    private static function throwPropAccessException(
        $accessorName,
        $propName,
        $className
    ) {
        throw new AccessorsException("Undefined or not {$accessorName}-accessible "
            ."property {$className}::\${$propName} was called.");
    }

    /**
     * The method throws exception of an undefined method.
     * @param string $methodName The called method name.
     * @param string $className Current class name.
     * @throws \nadir2\tools\AccessorsException
     */
    private static function throwUndefMethodException($methodName, $className)
    {
        throw new AccessorsException("Call the undefined method {$className}::"
            .$methodName);
    }

    /**
     * This is interceptor method, which catches the calls of undeclared methods of
     * the class. If the name of the invoked method matches the setProperty, getProperty
     * or isPropertySet pattern and the target class has corresponding property,
     * then it calls needed accessor as if it was declared directly in the
     * child-class. In another case it throws exception.
     * @param string $methodName The name of the method.
     * @param mixed[] $args The array of passed args.
     * @return mixed|boolean The result is mixed for the getters and setters, is
     * boolean for isSets.
     * @throws \nadir2\tools\AccessorsException
     */
    public function __call($methodName, array $args)
    {
        if (strlen($methodName) < 4) {
            self::throwUndefMethodException($methodName, get_class($this));
        }
        if (strncmp($methodName, ($accessorName = 'get'), 3) === 0) {
            $propName     = lcfirst(substr($methodName, 3));
            if ($this->isPropAccessible($accessorName, $propName)) {
                return $this->$propName;
            } else {
                self::throwPropAccessException(
                    $accessorName,
                    $propName,
                    get_class($this)
                );
            }
        }
        if (strncmp($methodName, ($accessorName = 'set'), 3) === 0) {
            $propName     = lcfirst(substr($methodName, 3));
            if ($this->isPropAccessible($accessorName, $propName)) {
                $this->$propName = $args[0];
                return $args[0];
            } else {
                self::throwPropAccessException(
                    $accessorName,
                    $propName,
                    get_class($this)
                );
            }
        }
        $matches = [];
        if (preg_match('#^is(\w+)Set$#', $methodName, $matches)) {
            $propName     = lcfirst($matches[1]);
            $accessorName = 'isset';
            if ($this->isPropAccessible($accessorName, $propName)) {
                return !is_null($this->$propName);
            } else {
                self::throwPropAccessException(
                    $accessorName,
                    $propName,
                    get_class($this)
                );
            }
        }
        self::throwUndefMethodException($methodName, get_class($this));
    }
}
