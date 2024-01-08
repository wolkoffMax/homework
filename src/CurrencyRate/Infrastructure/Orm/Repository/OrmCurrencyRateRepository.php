<?php

declare(strict_types=1);

namespace App\CurrencyRate\Infrastructure\Orm\Repository;

use App\CurrencyRate\Domain\CurrencyRate;
use App\CurrencyRate\Domain\CurrencyRateRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class OrmCurrencyRateRepository extends ServiceEntityRepository implements CurrencyRateRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyRate::class);
    }

    public function findByCurrencyPair(string $baseCurrency, string $targetCurrency): ?CurrencyRate
    {
        $qb = $this->createQueryBuilder('cr')
            ->where('cr.baseCurrency = :baseCurrency')
            ->setParameter('baseCurrency', $baseCurrency)
            ->andWhere('cr.targetCurrency = :targetCurrency')
            ->setParameter('targetCurrency', $targetCurrency)
            ->orderBy('cr.conversionDate', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function add(CurrencyRate $currencyRate): void
    {
        $this->_em->persist($currencyRate);
        $this->_em->flush();
    }

    public function update(CurrencyRate $currencyRate): void
    {
        $this->_em->persist($currencyRate);
        $this->_em->flush();
    }
}
