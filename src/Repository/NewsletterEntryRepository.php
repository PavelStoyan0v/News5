<?php

namespace App\Repository;

use App\Entity\NewsletterEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NewsletterEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewsletterEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewsletterEntry[]    findAll()
 * @method NewsletterEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsletterEntryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NewsletterEntry::class);
    }

    // /**
    //  * @return NewsletterEntry[] Returns an array of NewsletterEntry objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NewsletterEntry
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
