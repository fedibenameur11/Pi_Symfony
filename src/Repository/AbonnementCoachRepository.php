<?php

namespace App\Repository;

use App\Entity\AbonnementCoach;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AbonnementCoach>
 *
 * @method AbonnementCoach|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbonnementCoach|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbonnementCoach[]    findAll()
 * @method AbonnementCoach[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbonnementCoachRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbonnementCoach::class);
    }

    public function save(AbonnementCoach $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AbonnementCoach $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByColumnValue($columnName, $columnValue)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.'.$columnName.' = :val')
            ->setParameter('val', $columnValue)
            ->orderBy('u.id', 'ASC');
    }

    public function anotherFindByColumnValue($queryBuilder, $columnName, $columnValue)
    {
        return $queryBuilder
            ->andWhere('u.'.$columnName.' = :val')
            ->setParameter('val', $columnValue)
            ->orderBy('u.id', 'ASC');
    }

    public function sortedByColumn($queryBuilder, $columnName, $sortOrder = "ASC")
    {
        return $queryBuilder->orderBy('u.'.$columnName, $sortOrder);
    }
//    /**
//     * @return AbonnementCoach[] Returns an array of AbonnementCoach objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AbonnementCoach
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
