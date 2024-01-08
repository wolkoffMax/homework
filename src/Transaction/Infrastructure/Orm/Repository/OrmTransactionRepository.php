<?php

declare(strict_types=1);

namespace App\Transaction\Infrastructure\Orm\Repository;

use App\Transaction\Domain\Transaction;
use App\Transaction\Domain\TransactionRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class OrmTransactionRepository extends ServiceEntityRepository implements TransactionRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findLatestByAccountId(string $accountId, int $offset, int $limit): array
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.account', 'a')
            ->where('a.id = :accountId')
            ->setParameter('accountId', $accountId)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('t.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function add(Transaction $transaction): void
    {
        $this->_em->persist($transaction);
        $this->_em->flush();
    }

    public function update(Transaction $transaction): void
    {
        $this->_em->persist($transaction);
        $this->_em->flush();
    }
}
