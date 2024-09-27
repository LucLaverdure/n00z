<?php

namespace MeshMVC;

// chainable class
abstract class Model {

    public static $class_methods = []; // protected methods not to output
    public static $class_properties = []; // protected methods not to output
    private $computed = true;

    public function __construct() {
        // cache class methods and properties on construction
        if (empty(self::$class_methods)) {
            $reflection = new \ReflectionClass($this);
            if ($this instanceof \MeshMVC\Model) {
                $gotClass = $reflection->getParentClass();
            } else {
                $gotClass = $this;
            }

            if ($gotClass) {
                self::$class_methods = array_map(
                    function ($method) {
                        return $method->getName();
                    },
                    $gotClass->getMethods(\ReflectionMethod::IS_PUBLIC)
                );
                self::$class_properties = array_map(
                    function ($property) {
                        return $property->getName();
                    },
                    $gotClass->getProperties(\ReflectionProperty::IS_PUBLIC)
                );
            }
        }
        return $this;
    }

    public function __set(string $name, $value): void {

    }

    // TODO: write with passed storage object instead, ex: SFTP, DB, S3, Session
    public function save($filename) {
        file_put_contents($filename, $this->json());
        return $this;
    }

    // when getting output, defines if public functions should be rendered as well as properties
    public function computed($bool) {
        $this->computed = $bool;
        return $this;
    }

    // get model as json
    public function json() {
        $reflection = new \ReflectionClass($this);
        $data = [];

        // Get class properties
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            // when property doesn't start with __, get it
            $propertyName = $property->getName();
            if (!in_array($propertyName, self::$class_properties) && strncmp($propertyName, "__", 2) !== 0) {
                $data[$propertyName] = $property->getValue($this);
            }
        }

        // Get class methods
        if ($this->computed) {
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                $methodName = $method->getName();
                if (!in_array($methodName, self::$class_methods) && strncmp($methodName, "__", 2) !== 0) {
                    // when method isn't a \MeshMVC\Model method and doesn't start with __, invoke it.
                    $methodOutput = $method->invoke($this);
                    $data[$methodName] = $methodOutput;
                }
            }
        }

        // return JSON as json string (not object)
        $data = \MeshMVC\Tools::jsonEncode($data);
        return $data;
    }
}
