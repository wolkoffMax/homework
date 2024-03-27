<?php

declare(strict_types=1);

namespace App\Transaction\Infrastructure\Controller;

use App\Transaction\Application\Service\StatementTransactionFetchService;
use App\Transaction\Infrastructure\Assembler\StatementTransactionListResponseDataAssembler;
use App\Transaction\Infrastructure\Request\StatementTransactionListRequest;
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
final class StatementTransactionController extends AbstractFOSRestController
{
    private StatementTransactionFetchService $fetchService;
    private StatementTransactionListResponseDataAssembler $assembler;

    public function __construct(StatementTransactionFetchService $fetchService, StatementTransactionListResponseDataAssembler $assembler)
    {
        $this->fetchService = $fetchService;
        $this->assembler = $assembler;
    }

    /**
     * Get transactions statement by account id.
     *
     * @Route("/{accountId}/statements/{year}/{month}", name="get_transaction_statements_by_account", methods={"GET"})
     *
     * @OA\Get(
     *     path="/rest/transactions/{accountId}/statements/{year}/{month}",
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
     *         name="year",
     *         in="path",
     *         required=true,
     *         description="Year of the statement",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="month",
     *         in="path",
     *         required=true,
     *         description="Month of the statement",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of transactions",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="accountId", type="string"),
     *             @OA\Property(property="year", type="string"),
     *             @OA\Property(property="month", type="string"),
     *             @OA\Property(property="credits", type="object",
     *                  @OA\Property(property="totalAmount", type="object",
     *                      @OA\Property(property="amount", type="string"),
     *                      @OA\Property(property="currency", type="string")
     *                  ),
     *                  @OA\Property(property="transactions", type="array",
     *
     *                      @OA\Items(ref=@Model(type=App\Transaction\Infrastructure\Dto\TransactionResponseDto::class))
     *                  )
     *             ),
     *
     *             @OA\Property(property="debits", type="object",
     *                  @OA\Property(property="totalAmount", type="object",
     *                       @OA\Property(property="amount", type="string"),
     *                       @OA\Property(property="currency", type="string")
     *                  ),
     *                  @OA\Property(property="transactions", type="array",
     *
     *                      @OA\Items(ref=@Model(type=App\Transaction\Infrastructure\Dto\TransactionResponseDto::class))
     *                  )
     *              )
     *          )
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
    public function listStatementByAccountId(string $accountId, string $month, string $year): JsonResponse
    {
        try {
            $request = new StatementTransactionListRequest($accountId, $year, $month);

            $transactions = $this->fetchService->fetch($request->accountId(), $request->year(), $request->month());

            if (empty($transactions)) {
                return $this->json(['error' => 'No transactions found.'], Response::HTTP_NOT_FOUND);
            }

            $list = $this->assembler->assemble($transactions, $accountId, $year, $month);
        } catch (InvalidArgumentException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $exception) {
            return $this->json(['error' => 'Something went wrong.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($list);
    }
}
