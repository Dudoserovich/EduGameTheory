<?php

namespace App\Repository;

use App\Entity\TopicLiterature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TopicLiterature>
 *
 * @method TopicLiterature|null find($id, $lockMode = null, $lockVersion = null)
 * @method TopicLiterature|null findOneBy(array $criteria, array $orderBy = null)
 * @method TopicLiterature[]    findAll()
 * @method TopicLiterature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicLiteratureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TopicLiterature::class);
    }

    public function save(TopicLiterature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TopicLiterature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TopicLiterature[] Returns an array of TopicLiterature objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TopicLiterature
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
