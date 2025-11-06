<?php

declare(strict_types=1);

namespace Wipop\Serializer;

use RuntimeException;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class Hydrator
{
    private DenormalizerInterface $denormalizer;

    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $nameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $propertyInfo = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);

        $normalizers = [
            new DateTimeNormalizer(),
            new BackedEnumNormalizer(),
            new ArrayDenormalizer(),
            new ObjectNormalizer($classMetadataFactory, $nameConverter, null, $propertyInfo),
        ];

        $this->denormalizer = new Serializer($normalizers, [new JsonEncoder()]);
    }

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
        $object = $this->denormalizer->denormalize($data, $className, null, [
            'allow_extra_attributes' => true,
            AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
        ]);

        if (!is_object($object)) {
            throw new RuntimeException(sprintf('Unable to hydrate model "%s".', $className));
        }

        return $object;
    }
}
