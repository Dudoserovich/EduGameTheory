<?php

namespace App\Repository;

use App\Entity\EducationTasks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EducationTasks>
 *
 * @method EducationTasks|null find($id, $lockMode = null, $lockVersion = null)
 * @method EducationTasks|null findOneBy(array $criteria, array $orderBy = null)
 * @method EducationTasks[]    findAll()
 * @method EducationTasks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EducationTasksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EducationTasks::class);
    }

    public function save(EducationTasks $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EducationTasks $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EducationTasks[] Returns an array of EducationTasks objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EducationTasks
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
