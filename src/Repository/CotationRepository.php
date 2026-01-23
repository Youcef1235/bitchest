<?php

namespace App\Repository;

use App\Entity\Cotation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cotation::class);
    }

    public function findLast30Days(int $cryptoId): array
    {
        $since = new \DateTimeImmutable('-30 days');
        return $this->createQueryBuilder('c')
            ->where('c.cryptocurrency = :id')
            ->andWhere('c.quotedAt >= :since')
            ->setParameter('id', $cryptoId)
            ->setParameter('since', $since)
            ->orderBy('c.quotedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findLatestPerCrypto(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.quotedAt = (
                SELECT MAX(c2.quotedAt) FROM App\Entity\Cotation c2
                WHERE c2.cryptocurrency = c.cryptocurrency
            )')
            ->getQuery()
            ->getResult();
    }
}