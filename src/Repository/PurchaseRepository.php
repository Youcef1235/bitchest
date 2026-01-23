<?php

namespace App\Repository;

use App\Entity\Purchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    public function findByUserAndCrypto(int $userId, int $cryptoId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.user = :userId')
            ->andWhere('p.cryptocurrency = :cryptoId')
            ->setParameter('userId', $userId)
            ->setParameter('cryptoId', $cryptoId)
            ->orderBy('p.purchasedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}