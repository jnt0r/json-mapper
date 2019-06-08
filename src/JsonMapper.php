<?php
/**
 * Created by PhpStorm.
 * Date: 03.06.2019
 * Time: 16:43
 */
namespace effect\mapper;

use DateTime;
use ReflectionClass;
use SimpleTestObject as SimpleTestObjectAlias;
use SimpleTestObject;

/**
 * Class JsonMapper
 * Short description about this class.
 *
 * @package effect\mapper
 */
class JsonMapper
{
    public function __construct() { }

    private function getProperties($object) {
        if (get_class($object) == DateTime::class) {
            return $object->format(DateTime::ISO8601);
        }

        try {
            $reflection = new ReflectionClass($object);
        } catch (\ReflectionException $e) {
            print $e;
        };

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

    public function stringify($object): string {
        if (gettype($object) != 'object') {
            return json_encode($object);
        }

        $properties = $this->getProperties($object);

        return json_encode($properties);
    }



    /**
     * @param \ReflectionProperty $property
     *
     * @return bool
     */
    private function isIgnored(\ReflectionProperty $property): bool
    {
        return preg_match('/@JsonIgnore/', $property->getDocComment());
    }

    public function mapToArray(string $string): array
    {

    }
}

