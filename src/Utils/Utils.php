<?php

namespace App\Utils;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class Utils
{
    public static function dto(array $data, object &$object, ?ValidatorInterface $validator = null): array
    {
        $errors = [];
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($data as $property => $value) {
            if ($accessor->isWritable($object, $property)) {
                $accessor->setValue($object, $property, $value);
            }
        }

        if ($validator instanceof ValidatorInterface) {
            /** @var ConstraintViolationInterface $violation */
            foreach ($validator->validate($object) as $violation) {
                $errors[] = "{$violation->getPropertyPath()}: {$violation->getMessage()}";
            }
        }

        return $errors;
    }
}