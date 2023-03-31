<?php

namespace App\Repository;

use App\Entity\Office;
use Doctrine\ORM\Query;
use App\Entity\OfficeSearch;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Office>
 *
 * @method Office|null find($id, $lockMode = null, $lockVersion = null)
 * @method Office|null findOneBy(array $criteria, array $orderBy = null)
 * @method Office[]    findAll()
 * @method Office[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Office::class);
    }

    public function save(Office $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Office $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Find/search office by name/content
    public function findOfficesByName(string $query)
    {
    $qb = $this->createQueryBuilder('p');
    $qb
        ->join('p.location', 'a') // Join with Address entity
        ->join('a.city', 'b') // Join with Address entity
        ->where(
            $qb->expr()->andX(
                $qb->expr()->orX( 
                    $qb->expr()->like('p.title', ':query'),
                    $qb->expr()->like('p.description', ':query'),
                    $qb->expr()->like('b.name', ':query'), // Search by city
                ),                    
            )
        )
        ->setParameter('query', '%' . $query . '%')
    ;
    return $qb
        ->getQuery()
        ->getResult();
    }

    //Find with Price filter
    /* public function findAllVisibleQuery(OfficeSearch $search): Query
    {
        $query = $this->findVisibleQuery();

        if ($search->getMaxPrice()) {
            $query = $query
                ->where('p.price < :maxprice')
                ->setParameter('maxprice', $search->getMaxPrice());
        }

        return $query->getQuery();
    }  */

//    /**
//     * @return Office[] Returns an array of Office objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Office
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
