<?php

namespace App\Service;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class Normalizer
{
    private ObjectNormalizer $normalizer;

    public function __construct(ObjectNormalizer $normalizer, SerializerInterface $serializer)
    {
        $normalizer->setSerializer($serializer);
        $this->normalizer = $normalizer;
    }

    /**
     * @throws ExceptionInterface
     */
    public function normalizeObject(object $object, array $attributes): array
    {
        return $this->normalizer->normalize($object, 'array', [
            AbstractNormalizer::ATTRIBUTES => $attributes,
        ]);
    }

    public function normalizeArray(array $objects, array $attributes): array
    {
        $normalizeData = [];
        foreach ($objects as $object) {
            $normalizeData[] = $this->normalizeObject($object, $attributes);
        }

        return $normalizeData;
    }
}
