<?php

namespace App\Exception\ExceptionNormalizer;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionNormalizer
{
    public function normalize($object): array
    {
        return match (true) {
            $object instanceof NotFoundHttpException => NotFountHttpExceptionNormalizer::normalize($object),
            default => ErrorExceptionNormalizer::normalize($object),
        };
    }
}
