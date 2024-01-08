<?php

declare(strict_types=1);

namespace App\Account\Infrastructure\Orm\Repository;

use App\Account\Domain\Account;
use App\Account\Domain\AccountRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class OrmAccountRepository extends ServiceEntityRepository implements AccountRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function findAllByClientId(string $clientId): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.client', 'c')
            ->where('c.id = :clientId')
            ->setParameter('clientId', $clientId)
            ->getQuery()
            ->getResult();
    }

    public function findById(string $id): ?Account
    {
        return $this->find($id);
    }

    public function add(Account $account): void
    {
        $this->_em->persist($account);
        $this->_em->flush();
    }

    public function update(Account $account): void
    {
        $this->_em->persist($account);
        $this->_em->flush();
    }

    public function beginTransaction(): void
    {
        $this->_em->getConnection()->beginTransaction();
    }

    public function rollback(): void
    {
        $this->_em->getConnection()->rollBack();
    }

    public function commit(): void
    {
        $this->_em->getConnection()->commit();
    }
}
