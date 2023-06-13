<?php

namespace App\Repository;

use App\Entity\Achievement;
use App\Entity\User;
use App\Entity\UserAchievement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAchievement>
 *
 * @method UserAchievement|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAchievement|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAchievement[]    findAll()
 * @method UserAchievement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAchivementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAchievement::class);
    }

    public function save(UserAchievement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserAchievement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUserAndNotNullAchieve(User $user): array
    {
        return $this->createQueryBuilder('ua')
            ->join('ua.user', 'user')
            ->join('ua.achievement', 'achievement')
            ->where('ua.user = :user_id')
            ->andWhere('ua.achievementDate IS NOT NULL')
            ->orderBy('ua.achievementDate')
            ->setParameter('user_id', $user->getId())
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return UserAchievement[] Returns an array of UserAchievement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserAchievement
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
