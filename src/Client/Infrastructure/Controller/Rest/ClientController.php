<?php

declare(strict_types=1);

namespace App\Client\Infrastructure\Controller\Rest;

use App\Client\Application\Service\ClientsFetchService;
use App\Client\Infrastructure\Assembler\ClientListResponseDataAssembler;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @OA\Tag(name="clients")
 *
 * @Route("/rest/clients")
 */
final class ClientController extends AbstractFOSRestController
{
    private ClientsFetchService $fetchService;
    private ClientListResponseDataAssembler $assembler;

    public function __construct(ClientsFetchService $fetchService, ClientListResponseDataAssembler $assembler)
    {
        $this->fetchService = $fetchService;
        $this->assembler = $assembler;
    }

    /**
     * Get a list of clients.
     *
     * @Route("", name="client_list", methods={"GET"})
     *
     * @OA\Get(
     *     path="/rest/clients"
     * )
     *
     * @OA\Response(
     *      response=200,
     *      description="Successful operation",
     *
     *      @OA\JsonContent(
     *          type="array",
     *
     *          @OA\Items(ref=@Model(type=App\Client\Infrastructure\Dto\ClientResponseDto::class))
     *      )
     * )
     */
    public function list(): JsonResponse
    {
        try {
            $clients = $this->fetchService->fetch();

            $list = $this->assembler->assemble($clients);
        } catch (Throwable $exception) {
            // exception logging might be implemented here
            return $this->json(['error' => 'Something went wrong.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($list);
    }
}
