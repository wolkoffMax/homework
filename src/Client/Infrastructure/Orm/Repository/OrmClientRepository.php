<?php

declare(strict_types=1);

namespace App\Client\Infrastructure\Orm\Repository;

use App\Client\Domain\Client;
use App\Client\Domain\ClientRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class OrmClientRepository extends ServiceEntityRepository implements ClientRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult();
    }

    public function add(Client $client): void
    {
        $this->_em->persist($client);
        $this->_em->flush();
    }

    public function update(Client $client): void
    {
        $this->_em->persist($client);
        $this->_em->flush();
    }
}
