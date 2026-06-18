<?php

declare(strict_types=1);

namespace Wipop\Serializer;

use BackedEnum;
use DateTimeImmutable;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use RuntimeException;
use Throwable;
use Wipop\Serializer\Attribute\SerializedName;

final class Hydrator
{
    /**
     * @template T of object
     *
     * @param class-string<T>     $className
     * @param array<string,mixed> $data
     *
     * @return T
     */
    public function hydrate(string $className, array $data): object
    {
        if (!class_exists($className)) {
            throw new RuntimeException(sprintf('Unable to hydrate unknown model "%s".', $className));
        }

        return $this->hydrateObject($className, $data);
    }

    /**
     * @template T of object
     *
     * @param class-string<T>     $className
     * @param array<string,mixed> $data
     *
     * @return T
     */
    private function hydrateObject(string $className, array $data): object
    {
        $reflection = new ReflectionClass($className);
        $object = $reflection->newInstanceWithoutConstructor();

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $key = $this->serializedName($property);
            if (!array_key_exists($key, $data)) {
                continue;
            }

            $property->setValue($object, $this->normalizeValue($data[$key], $property->getType()));
        }

        return $object;
    }

    private function serializedName(ReflectionProperty $property): string
    {
        $attributes = $property->getAttributes(SerializedName::class);
        if ($attributes === []) {
            return $property->getName();
        }

        return $attributes[0]->newInstance()->name;
    }

    private function normalizeValue(mixed $value, ?ReflectionType $type): mixed
    {
        if ($value === null || $type === null) {
            return $value;
        }

        if (!$type instanceof ReflectionNamedType) {
            return $value;
        }

        $typeName = $type->getName();

        if ($type->isBuiltin()) {
            return $this->normalizeBuiltinValue($value, $typeName);
        }

        if ($value instanceof $typeName) {
            return $value;
        }

        if ($typeName === DateTimeImmutable::class) {
            return $this->dateTimeValue($value);
        }

        if (is_subclass_of($typeName, BackedEnum::class)) {
            return $this->enumValue($value, $typeName);
        }

        if (is_array($value) && class_exists($typeName)) {
            return $this->hydrateObject($typeName, $value);
        }

        return $value;
    }

    private function normalizeBuiltinValue(mixed $value, string $typeName): mixed
    {
        return match ($typeName) {
            'bool' => $this->boolValue($value),
            'float' => $this->floatValue($value),
            'int' => $this->intValue($value),
            'string' => $this->stringValue($value),
            default => $value,
        };
    }

    private function boolValue(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value) && ($value === 0 || $value === 1)) {
            return $value === 1;
        }

        if (is_string($value)) {
            return match (strtolower(trim($value))) {
                '0', 'false' => false,
                '1', 'true' => true,
                default => throw new RuntimeException('Unable to hydrate Wipop boolean value.'),
            };
        }

        throw new RuntimeException('Unable to hydrate Wipop boolean value.');
    }

    private function floatValue(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        if (is_string($value) && is_numeric(trim($value))) {
            return (float) $value;
        }

        throw new RuntimeException('Unable to hydrate Wipop numeric value.');
    }

    private function intValue(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && preg_match('/^-?\d+$/', trim($value)) === 1) {
            return (int) $value;
        }

        throw new RuntimeException('Unable to hydrate Wipop integer value.');
    }

    private function stringValue(mixed $value): string
    {
        if (is_scalar($value)) {
            return (string) $value;
        }

        throw new RuntimeException('Unable to hydrate Wipop string value.');
    }

    private function dateTimeValue(mixed $value): DateTimeImmutable
    {
        if (!is_string($value) || trim($value) === '') {
            throw new RuntimeException('Unable to hydrate Wipop date value.');
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Throwable $exception) {
            throw new RuntimeException('Unable to hydrate Wipop date value.', 0, $exception);
        }
    }

    /**
     * @param class-string<BackedEnum> $enumClass
     */
    private function enumValue(mixed $value, string $enumClass): BackedEnum
    {
        if (!is_string($value) && !is_int($value)) {
            throw new RuntimeException(sprintf('Unable to hydrate Wipop enum "%s".', $enumClass));
        }

        $enum = $enumClass::tryFrom($value);
        if ($enum === null) {
            throw new RuntimeException(sprintf('Unable to hydrate Wipop enum "%s".', $enumClass));
        }

        return $enum;
    }
}
