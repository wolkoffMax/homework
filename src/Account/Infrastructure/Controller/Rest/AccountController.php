<?php

declare(strict_types=1);

namespace App\Account\Infrastructure\Controller\Rest;

use App\Account\Application\Service\AccountFetchService;
use App\Account\Infrastructure\Assembler\AccountListResponseDataAssembler;
use App\Account\Infrastructure\Request\AccountListRequest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @OA\Tag(name="accounts")
 *
 * @Route("/rest/accounts")
 */
final class AccountController extends AbstractFOSRestController
{
    private AccountFetchService $fetchService;
    private AccountListResponseDataAssembler $assembler;

    public function __construct(AccountFetchService $accountService, AccountListResponseDataAssembler $assembler)
    {
        $this->fetchService = $accountService;
        $this->assembler = $assembler;
    }

    /**
     * Get accounts by client id.
     *
     * @Route("/{clientId}", name="get_accounts_by_client", methods={"GET"})
     *
     * @OA\Get(
     *     path="/rest/accounts/{clientId}",
     *
     *     @OA\Parameter(
     *         name="clientId",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     )
     * )
     *
     * @OA\Response(
     *      response=200,
     *      description="List of accounts",
     *
     *      @OA\JsonContent(
     *          type="array",
     *
     *          @OA\Items(ref=@Model(type=App\Account\Infrastructure\Dto\AccountResponseDto::class))
     *      )
     * )
     */
    public function listByClient(string $clientId): JsonResponse
    {
        try {
            $request = new AccountListRequest($clientId);

            $accounts = $this->fetchService->fetch($request->clientId());

            if (empty($accounts)) {
                return $this->json(['error' => 'No accounts found.'], Response::HTTP_NOT_FOUND);
            }

            $list = $this->assembler->assemble($accounts);
        } catch (InvalidArgumentException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $exception) {
            // exception logging might be implemented here
            return $this->json(['error' => 'Something went wrong.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($list);
    }
}
