<?php

namespace SmartEnum;

abstract class Enum implements \JsonSerializable
{
    /** @var Enum[] */
    private static $instances = [];

    /** @var \ReflectionClassConstant[] */
    private static $constReflections = [];

    /** @var \ReflectionClass[] */
    private static $reflections = [];

    /** @var string */
    private $name;

    final private function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     * @return static
     * @throws EnumException
     */
    final public static function fromName(string $name)
    {
        if (in_array($name, static::getNames(), true)) {
            return static::createNamedInstance($name);
        }

        throw EnumException::becauseUnknownMember(static::class, $name);
    }

    /**
     * @param mixed $value
     * @return static
     * @throws EnumException
     */
    final public static function fromValue($value)
    {
        $name_values = self::getEnumReflection(static::class)->getConstants();

        if (\in_array($value, $name_values, true)) {
            return static::createNamedInstance(array_search($value, $name_values, true));
        }

        if (ctype_digit($value) && \in_array((int)$value, $name_values, true)) {
            return static::createNamedInstance(array_search((int)$value, $name_values, true));
        }

        throw EnumException::becauseUnknownMember(static::class, $value);
    }

    /**
     * @param mixed $value_or_name
     * @return static
     * @throws EnumException
     */
    final public static function fromValueOrName($value_or_name)
    {
        $name_values = self::getEnumReflection(static::class)->getConstants();

        if (is_int($value_or_name) && \in_array($value_or_name, $name_values, true)) {
            return static::createNamedInstance(array_search($value_or_name, $name_values, true));
        }

        if (is_string($value_or_name)) {
            if (ctype_digit($value_or_name) && \in_array((int)$value_or_name, $name_values, true)) {
                return static::createNamedInstance(array_search((int)$value_or_name, $name_values, true));
            }
            if (array_key_exists($value_or_name, $name_values)) {
                return static::createNamedInstance($value_or_name);
            }
        }

        throw EnumException::becauseUnknownMember(static::class, $value_or_name);
    }

    /**
     * Creates enum instance with short static constructor
     * @param string $name
     * @param array $arguments
     * @return static
     * @throws EnumException
     */
    final public static function __callStatic(string $name, array $arguments)
    {
        return static::fromName($name);
    }

    /**
     * @return string[]
     */
    public static function getNames() : array
    {
        return array_keys(self::getEnumReflection(static::class)->getConstants());
    }

    /**
     * @return string[]
     */
    public static function getValues() : array
    {
        return array_values(self::getEnumReflection(static::class)->getConstants());
    }

    /**
     * @return static[]
     */
    final public static function getAll() : array
    {
        $result = [];
        foreach (self::getNames() as $name) {
            $result[] = static::createNamedInstance($name);
        }
        return $result;
    }

    private static function getConstantReflection(string $class, string $name): \ReflectionClassConstant
    {
        $key = self::getConstKey($class, $name);
        if (!array_key_exists($key, self::$constReflections)) {
            $refl = self::getEnumReflection(static::class);
            self::$constReflections[$key] = $refl->getReflectionConstant($name);
        }

        return self::$constReflections[$key];
    }

    private static function getConstKey(string $class, string $name) : string
    {
        return $class . '::' . $name;
    }

    private static function findParentClassForConst(string $name) : string
    {
        return self::getConstantReflection(static::class, $name)->getDeclaringClass()->getName();
    }

    private static function getEnumReflection(string $class): \ReflectionClass
    {
        if (!array_key_exists($class, self::$reflections)) {
            try {
                self::$reflections[$class] = new \ReflectionClass($class);
                // @codeCoverageIgnoreStart
            } catch (\ReflectionException $exception) {
                throw new \LogicException('Class should be valid FQCN. Fix internal calls.');
                // @codeCoverageIgnoreEnd
            }
        }

        return self::$reflections[$class];
    }

    /**
     * Create named enum instance
     * @param string $name
     * @return static
     */
    private static function createNamedInstance(string $name)
    {
        $class = self::findParentClassForConst($name);
        $key = self::getConstKey($class, $name);

        if (!array_key_exists($key, self::$instances)) {
            self::$instances[$key] = new static($name);
        }

        return self::$instances[$key];
    }

    /**
     * @return int|float|string|bool|null
     */
    final public function getValue()
    {
        return self::getEnumReflection(static::class)->getConstant($this->name);
    }

    final public function getName() : string
    {
        return $this->name;
    }

    final public function __toString() : string
    {
        return $this->name;
    }
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->name;
    }
}
