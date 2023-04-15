<?php

namespace App\Controller\V1;

use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    #[OA\Get(operationId: 'v1HomePage', summary: 'Home page')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Success.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string'),
            ]
        )
    )]
    #[Rest\Get('/', name: 'api_v1_index')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new protected API!',
        ]);
    }
}
