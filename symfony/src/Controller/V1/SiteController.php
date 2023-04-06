<?php

namespace App\Controller\V1;

use App\Entity\Site;
use App\Validator\Validator;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[OA\Tag(name: 'Site')]
#[OA\Response(
    response: Response::HTTP_UNAUTHORIZED,
    description: 'Unauthorized.',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'code', type: 'int'),
            new OA\Property(property: 'description', type: 'string'),
        ]
    )
)]
#[OA\Response(
    response: Response::HTTP_FORBIDDEN,
    description: 'Forbidden.',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'code', type: 'int'),
            new OA\Property(property: 'description', type: 'string'),
        ]
    )
)]
#[Rest\Route('sites')]
class SiteController extends AbstractController
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected Validator $validator,
        protected DenormalizerInterface $denormalizer
    )
    {
    }

    #[OA\Get(operationId: 'getSites', summary: 'Get list of sites')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns list of an sites',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Site::class, groups: ['get']))
        )
    )]
    #[OA\Parameter(
        name: 'search',
        description: 'The field used to search',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: '_start',
        description: 'Start list',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: '_end',
        description: 'End list',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: '_order',
        description: 'Order list',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: '_sort',
        description: 'Sort list',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    #[Rest\QueryParam(
        name: 'search', description: 'Search', strict: true, nullable: true
    )]
    #[Rest\QueryParam(
        name: '_start', requirements: '\d+', default: 0, description: 'Start list', strict: true, nullable: true
    )]
    #[Rest\QueryParam(
        name: '_end', requirements: '\d+', default: 50, description: 'End list', strict: true, nullable: true
    )]
    #[Rest\QueryParam(
        name: '_order',
        requirements: '(ASC|DESC|asc|desc)',
        default: 'ASC',
        description: 'Order list',
        strict: true,
        nullable: true
    )]
    #[Rest\QueryParam(
        name: '_sort',
        description: 'Sort field',
        strict: true,
        nullable: true
    )]
    #[Rest\Get(name: 'api_v1_get_collection_sites')]
    public function getCollection(ParamFetcher $paramFetcher): JsonResponse
    {
        $start = $paramFetcher->get('_start');
        $end = $paramFetcher->get('_end');
        $order = $paramFetcher->get('_order');
        $sort = $paramFetcher->get('_sort');
        $search = $paramFetcher->get('search');

        return $this->json(
            data: $this->em->getRepository(Site::class)->search(
                search: $search,
                order: $order,
                sort: $sort,
                limit: $end - $start,
                offset: $start
            ),
            headers: ['X-Total-Count' => $this->em->getRepository(Site::class)->total($search)],
            context: ['groups' => 'get']
        );
    }

    #[OA\Head(
        operationId: 'checkSiteById',
        summary: 'Check site by id',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Returns site by id'
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Site not found.'
            )
        ]
    )]
    #[OA\Get(
        operationId: 'getSiteById',
        summary: 'Returns site by id',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Returns site by id',
                content: new OA\JsonContent(ref: new Model(type: Site::class, groups: ['get']))
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Site not found.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'code', type: 'int'),
                        new OA\Property(property: 'description', type: 'string'),
                    ]
                )
            )
        ]
    )]
    #[Rest\Get(path: '/{id}', name: 'api_v1_get_sites')]
    #[Rest\Head(path: '/{id}', name: 'api_v1_head_sites')]
    public function get(int $id): JsonResponse
    {
        if (null === $site = $this->em->getRepository(Site::class)->find($id)) {
            throw new NotFoundHttpException('Site not found.');
        }

        return $this->json(data: $site, context: ['groups' => 'get']);
    }

    #[OA\Delete(operationId: 'deleteSiteById', summary: 'Delete site by id')]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Delete site by id'
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Site not found.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'code', type: 'int'),
                new OA\Property(property: 'description', type: 'string'),
            ]
        )
    )]
    #[Rest\Delete(path: '/{id}', name: 'api_v1_delete_sites')]
    public function delete(int $id): JsonResponse
    {
        if (null === $site = $this->em->getRepository(Site::class)->find($id)) {
            throw new NotFoundHttpException('Site not found.');
        }

        $this->em->getRepository(Site::class)->remove($site, true);

        return $this->json(data: [], status: Response::HTTP_NO_CONTENT);
    }


    #[OA\Post(operationId: 'createSiteBy', summary: 'Create site')]
    #[OA\RequestBody(
        description: 'Site request',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Site::class, groups: ['set']))
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Site created',
        content: new OA\JsonContent(ref: new Model(type: Site::class, groups: ['get']))
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Bad request.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'code', type: 'int'),
                new OA\Property(property: 'description', type: 'string'),
            ]
        )
    )]
    #[Rest\Post(name: 'api_v1_post_sites')]
    public function post(Request $request): JsonResponse
    {
        $site = $this->denormalizer->denormalize(
            data: $request->request->all(),
            type: Site::class,
            context: ['groups' => ['set']]
        );

        if (null !== $violations = $this->validator->validate(object: $site, groups: 'set')) {
            throw new BadRequestHttpException(json_encode($violations));
        }

        $this->em->persist($site);
        $this->em->flush();

        return $this->json(data: $site, status: Response::HTTP_CREATED, context: ['groups' => 'get']);
    }

    #[OA\Put(operationId: 'updateSiteBy', summary: 'Update site')]
    #[OA\Patch(operationId: 'updatePartialSiteBy', summary: 'Update partial site')]
    #[OA\RequestBody(
        description: 'Site request',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Site::class, groups: ['set']))
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Site updated',
        content: new OA\JsonContent(ref: new Model(type: Site::class, groups: ['get']))
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Site not found.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'code', type: 'int'),
                new OA\Property(property: 'description', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Bad request.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'code', type: 'int'),
                new OA\Property(property: 'description', type: 'string'),
            ]
        )
    )]
    #[Rest\Put(path: '/{id}', name: 'api_v1_put_sites')]
    #[Rest\Patch(path: '/{id}', name: 'api_v1_patch_sites')]
    public function update(int $id, Request $request): JsonResponse
    {
        if (null === $site = $this->em->getRepository(Site::class)->find($id)) {
            throw new NotFoundHttpException(sprintf('Site not found with id %d', $id));
        }

        $this->denormalizer->denormalize(
            data: $request->request->all(),
            type: Site::class,
            context: [
                'groups' => ['set'],
                AbstractNormalizer::OBJECT_TO_POPULATE => $site,
                AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true
            ]
        );

        if (null !== $violations = $this->validator->validate(object: $site, groups: 'set')) {
            throw new BadRequestHttpException(json_encode($violations));
        }

        $this->em->persist($site);
        $this->em->flush();

        return $this->json(data: $site, context: ['groups' => 'get']);
    }
}
