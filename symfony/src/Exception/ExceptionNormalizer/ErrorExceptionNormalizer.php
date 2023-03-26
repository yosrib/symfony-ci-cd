<?php

namespace App\Exception\ExceptionNormalizer;

use Symfony\Component\HttpFoundation\Response;

class ErrorExceptionNormalizer
{
    public static function normalize(\Throwable $exception)
    {

        return [
            'code' => Response::HTTP_BAD_REQUEST,
            'description' => json_decode($exception->getMessage()),
        ];
    }
}