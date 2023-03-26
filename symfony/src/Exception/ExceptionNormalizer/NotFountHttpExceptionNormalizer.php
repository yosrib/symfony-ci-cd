<?php

namespace App\Exception\ExceptionNormalizer;

use Symfony\Component\HttpFoundation\Response;

class NotFountHttpExceptionNormalizer
{
    public const MESSAGE = 'Object not found';

    public static function normalize(\Throwable $exception)
    {
        if (strpos($message = $exception->getMessage(), 'object not found')) {
            $message = self::MESSAGE;
        }

        return [
            'code' => Response::HTTP_NOT_FOUND,
            'description' => $message,
        ];
    }
}
