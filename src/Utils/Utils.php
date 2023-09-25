<?php

namespace App\Utils;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class Utils
{
    public static function dto(array $data, object &$object, ?ValidatorInterface $validator = null): ?ConstraintViolationListInterface
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($data as $property => $value) {
            if ($accessor->isWritable($object, $property)) {
                $accessor->setValue($object, $property, $value);
            }
        }

        return $validator?->validate($object);
    }
}