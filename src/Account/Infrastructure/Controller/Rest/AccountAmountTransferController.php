<?php

declare(strict_types=1);

namespace App\Account\Infrastructure\Controller\Rest;

use App\Account\Application\Exception\AccountHasInsufficientFunds;
use App\Account\Application\Exception\AccountNotFound;
use App\Account\Application\Service\AccountAmountTransfer;
use App\Account\Application\Service\AccountAmountTransferService;
use App\Account\Infrastructure\Request\AccountTransferRequest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use InvalidArgumentException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @OA\Tag(name="accounts")
 *
 * @Route("/rest/accounts")
 */
final class AccountAmountTransferController extends AbstractFOSRestController
{
    private AccountAmountTransferService $transferService;

    public function __construct(AccountAmountTransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    /**
     * Transfer funds between accounts.
     *
     * @Route("/transfer", name="transfer_amount_between_accounts", methods={"POST"})
     *
     * @OA\Post(
     *     path="/rest/accounts/transfer",
     *
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="sourceAccountId", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *              @OA\Property(property="targetAccountId", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174001"),
     *              @OA\Property(property="amount", type="string", example="100.00")
     *          )
     *      )
     * )
     */
    public function transfer(Request $httpRequest): JsonResponse
    {
        try {
            $request = new AccountTransferRequest(json_decode($httpRequest->getContent(), true));

            $this->transferService->transfer(new AccountAmountTransfer(
                $request->sourceAccountId(),
                $request->targetAccountId(),
                $request->amount()
            ));

            return $this->json(['success' => true], Response::HTTP_OK);
        } catch (InvalidArgumentException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (AccountNotFound $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (AccountHasInsufficientFunds $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Throwable $exception) {
            // exception logging might be implemented here
            return $this->json(['error' => 'Something went wrong.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
