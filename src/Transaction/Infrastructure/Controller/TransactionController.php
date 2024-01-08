<?php

declare(strict_types=1);

namespace App\Transaction\Infrastructure\Controller;

use App\Transaction\Application\Service\TransactionFetchService;
use App\Transaction\Infrastructure\Assembler\TransactiontListResponseDataAssembler;
use App\Transaction\Infrastructure\Request\TransactionListRequest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @OA\Tag(name="transactions")
 *
 * @Route("/rest/transactions")
 */
final class TransactionController extends AbstractFOSRestController
{
    private TransactionFetchService $fetchService;
    private TransactiontListResponseDataAssembler $assembler;

    public function __construct(TransactionFetchService $fetchService, TransactiontListResponseDataAssembler $assembler)
    {
        $this->fetchService = $fetchService;
        $this->assembler = $assembler;
    }

    /**
     * Get transactions by account id.
     *
     * @Route("/{accountId}", name="get_transactions_by_account", methods={"GET"})
     *
     * @OA\Get(
     *     path="/rest/transactions/{accountId}",
     *
     *     @OA\Parameter(
     *         name="accountId",
     *         in="path",
     *         required=true,
     *         description="Unique identifier of the account",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number of the transactions list",
     *
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Number of transactions per page",
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of transactions",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref=@Model(type=App\Transaction\Infrastructure\Dto\TransactionResponseDto::class))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="No transactions found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function listByAccountId(Request $request, string $accountId): JsonResponse
    {
        try {
            $page = (int) $request->query->get('page', 1);
            $limit = (int) $request->query->get('limit', 10);

            $request = new TransactionListRequest($accountId, $page, $limit);

            $transactions = $this->fetchService->fetch($request->accountId(), $request->page(), $request->limit());

            if (empty($transactions)) {
                return $this->json(['error' => 'No transactions found.'], Response::HTTP_NOT_FOUND);
            }

            $list = $this->assembler->assemble($transactions);
        } catch (InvalidArgumentException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $exception) {
            // exception logging might be implemented here
            return $this->json(['error' => 'Something went wrong.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($list);
    }
}
