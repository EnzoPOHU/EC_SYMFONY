<?php

namespace App\Repository;

use App\Entity\BookRead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookRead>
 */
class BookReadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookRead::class);
    }

    public function findByUserId(int $userId, bool $onlyFinished = true): array
    {
        $qb = $this->createQueryBuilder('br')
            ->join('br.book', 'b')
            ->addSelect('b')
            ->where('br.user_id = :userId')
            ->setParameter('userId', $userId);

        if ($onlyFinished) {
            $qb->andWhere('br.is_read = true');
        }

        return $qb->orderBy('br.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findFinishedByUserId(int $userId): array
    {
        return $this->createQueryBuilder('br')
            ->join('br.book', 'b')
            ->addSelect('b')
            ->where('br.user_id = :userId')
            ->andWhere('br.is_read = :isRead')
            ->setParameter('userId', $userId)
            ->setParameter('isRead', true)
            ->orderBy('br.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findInProgressByUserId(int $userId): array
    {
        return $this->createQueryBuilder('br')
            ->join('br.book', 'b')
            ->addSelect('b')
            ->where('br.user_id = :userId')
            ->andWhere('br.is_read = :isRead')
            ->setParameter('userId', $userId)
            ->setParameter('isRead', false)
            ->orderBy('br.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
