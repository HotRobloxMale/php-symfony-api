<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\UserGroups;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserGroups>
 *
 * @method UserGroups|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGroups|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGroups[]    findAll()
 * @method UserGroups[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserGroupsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGroups::class);
    }


//    /**
//     * @return Groups[] Returns an array of Groups objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Groups
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
