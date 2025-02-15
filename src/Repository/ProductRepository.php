<?php

namespace App\Repository;

use App\Classes\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Get products by search
     *
     * @param Search $search
     * @return Product[]
     */
    public function findWithSearch(Search $search){
        $qb = $this->createQueryBuilder('p');

        $query = $qb->select('c','p')
            ->join('p.category', 'c');

        if(!empty($search->categories)){
            $query=$query->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }

        if(!empty($search->string)) {
            $query = $query->andWhere('p.name LIKE :name')
                ->setParameter('name', "%$search->string%");
        }

        return $query->getQuery()->getResult();

    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
