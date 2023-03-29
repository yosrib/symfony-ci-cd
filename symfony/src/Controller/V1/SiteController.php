<?php

namespace App\Controller\V1;

use App\Entity\Site;
use App\Validator\Validator;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

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

    #[Rest\Get(path: '/{id}', name: 'api_v1_get_sites')]
    #[Rest\Head(path: '/{id}', name: 'api_v1_head_sites')]
    public function get(int $id): JsonResponse
    {
        return $this->json(data: $this->em->getRepository(Site::class)->find($id), context: ['groups' => 'get']);
    }

    #[Rest\Delete(path: '/{id}', name: 'api_v1_delete_sites')]
    public function delete(int $id): JsonResponse
    {
        $entity = $this->em->getRepository(Site::class)->find($id);
        $this->em->getRepository(Site::class)->remove($entity, true);
        return $this->json(data: [], status: Response::HTTP_NO_CONTENT, context: ['groups' => 'get']);
    }

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

        return $this->json(data: $site, context: ['groups' => 'get']);
    }

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
