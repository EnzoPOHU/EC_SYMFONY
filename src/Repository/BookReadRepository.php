<?php

namespace App\Repository;

use App\Entity\BookRead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository personnalisé pour les entités BookRead
 * 
 * Ce repository gère les opérations de requête spécifiques aux livres lus
 * 
 * @extends ServiceEntityRepository<BookRead>
 */
class BookReadRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository
     * 
     * @param ManagerRegistry $registry Registre de gestion des entités Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookRead::class);
    }

    /**
     * Recherche des livres lus par un utilisateur
     * 
     * @param int $userId Identifiant de l'utilisateur
     * @param bool $onlyFinished Indique si seuls les livres terminés doivent être retournés
     * @return array Liste des livres lus
     */
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

    /**
     * Recherche des livres terminés par un utilisateur
     * 
     * @param int $userId Identifiant de l'utilisateur
     * @return array Liste des livres terminés
     */
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

    /**
     * Recherche des livres en cours par un utilisateur
     * 
     * @param int $userId Identifiant de l'utilisateur
     * @return array Liste des livres en cours
     */
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

    /**
     * Recherche de livres en cours par terme de recherche
     * 
     * @param int $userId Identifiant de l'utilisateur
     * @param string $searchTerm Terme de recherche
     * @return array Liste des livres en cours correspondant au terme
     */
    public function searchInProgressBooksByUser(int $userId, string $searchTerm): array
    {
        $qb = $this->createQueryBuilder('br')
            ->join('br.book', 'b')
            ->join('b.category', 'c')
            ->addSelect('b', 'c')
            ->where('br.user_id = :userId')
            ->andWhere('br.is_read = :isRead')
            ->andWhere('(b.name LIKE :searchTerm OR br.description LIKE :searchTerm OR c.name LIKE :searchTerm)')
            ->setParameter('userId', $userId)
            ->setParameter('isRead', false)
            ->setParameter('searchTerm', "%{$searchTerm}%")
            ->orderBy('br.created_at', 'DESC')
            ->getQuery()
            ->getResult();

        return $qb;
    }

    /**
     * Recherche de livres terminés par terme de recherche
     * 
     * @param int $userId Identifiant de l'utilisateur
     * @param string $searchTerm Terme de recherche
     * @return array Liste des livres terminés correspondant au terme
     */
    public function searchFinishedBooksByUser(int $userId, string $searchTerm): array
    {
        $qb = $this->createQueryBuilder('br')
            ->join('br.book', 'b')
            ->join('b.category', 'c')
            ->addSelect('b', 'c')
            ->where('br.user_id = :userId')
            ->andWhere('br.is_read = :isRead')
            ->andWhere('(b.name LIKE :searchTerm OR br.description LIKE :searchTerm OR c.name LIKE :searchTerm)')
            ->setParameter('userId', $userId)
            ->setParameter('isRead', true)
            ->setParameter('searchTerm', "%{$searchTerm}%")
            ->orderBy('br.created_at', 'DESC')
            ->getQuery()
            ->getResult();

        return $qb;
    }
}
