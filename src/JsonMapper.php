<?php
/**
 * Created by PhpStorm.
 * Date: 03.06.2019
 * Time: 16:43
 */

namespace effect\mapper;

use DateTime;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Class JsonMapper
 * Short description about this class.
 *
 * @package effect\mapper
 */
class JsonMapper
{
    public function __construct() { }

    public function stringify($object): string
    {
        $type = gettype($object);

        if ($type == 'object') {
            return json_encode($this->getProperties($object), JSON_UNESCAPED_SLASHES);
        } else if ($type == 'array') {
            $json = "";
            foreach ($object as $key => $item) {
                if (is_string($key)) {
                    $json .= '"' . $key .'":' . $this->stringify($item) . ',';
                } else {
                    $json .= $this->stringify($item) . ',';
                }
            }
            $json = substr($json, 0, -1);
            return '[' . $json . ']';
        } else {
            return json_encode($object, JSON_UNESCAPED_SLASHES);
        }
    }

    private function getProperties($object)
    {
        if (get_class($object) == DateTime::class) {
            return $object->format(DateTime::ISO8601);
        }

        $reflection = $this->reflect($object);

        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            if (!$this->isIgnored($property)) {
                $property->setAccessible(true);
                $value = $property->getValue($object);

                if (gettype($value) == 'object') {
                    $value = $this->getProperties($value);
                }

                $properties[$property->getName()] = $value;
            }
        }
        return $properties;
    }

    /**
     * @param $object
     *
     * @return ReflectionClass
     */
    private function reflect($object): ReflectionClass
    {
        return new ReflectionClass($object);

    }

    /**
     * @param ReflectionProperty $property
     *
     * @return bool
     */
    private function isIgnored(ReflectionProperty $property): bool
    {
        return preg_match('/@JsonIgnore/', $property->getDocComment());
    }

    /**
     * @param string $json
     * @param string $className
     *
     * @throws InvalidArgumentException
     *
     * @return object|array
     */
    public function map(string $json, string $className)
    {
        print $className;
        if (!class_exists($className)) {
            throw new InvalidArgumentException();
        }
        $reflection = $this->reflect($className);
        $reflectionProperties = $reflection->getProperties();
        $data = json_decode($json);

        if ($data == null) {
            return null;
        } else if (is_array($data)) {
            $objects = [];

            foreach ($data as $d) {
                $objects[] = $this->setProperties($className, $reflectionProperties, $d);
            }
            return $objects;
        } else if (is_object($data)) {
            return $this->setProperties($className, $reflectionProperties, $data);
        } else {
            return $data;
        }
    }

    /**
     * @param string $className
     * @param array  $reflectionProperties
     * @param        $data
     *
     * @return mixed
     */
    private function setProperties(string &$className, array &$reflectionProperties, &$data): object
    {
        $object = new $className();
        foreach ($reflectionProperties as $property) {
            $property->setAccessible(true);
            $name = $property->getName();
            $property->setValue($object, $data->$name);
        }
        return $object;
}
}

