<?php

namespace App\Repository;

use App\Entity\PdfInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PdfInfo>
 *
 * @method PdfInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PdfInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PdfInfo[]    findAll()
 * @method PdfInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PdfInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PdfInfo::class);
    }

//    /**
//     * @return PdfInfo[] Returns an array of PdfInfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PdfInfo
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
