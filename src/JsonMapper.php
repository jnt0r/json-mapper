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
            return json_encode($this->getProperties($object));
        } else if ($type == 'array') {
            $json = "";
            foreach ($object as $item) {
                $json .= $this->stringify($item) . ',';
            }
            $json = substr($json, 0, -1);
            return '[' . $json . ']';
        } else {
            return json_encode($object);
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
        if (!class_exists($className)) {
            throw new InvalidArgumentException();
        }

        $object = new $className();
        $reflection = $this->reflect($className);

        $data = json_decode($json);

        if ($data != null) {
            foreach ($reflection->getProperties() as $property) {
                $property->setAccessible(true);
                $name = $property->getName();
                $property->setValue($object, $data->$name);
            }
        }
        return $object;
    }

    public function mapToArray(string $string): array
    {

    }
}

