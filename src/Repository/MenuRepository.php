<?php 

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    /**
     * Find products associated with menus having the specified category.
     *
     * @param string $category
     * @return mixed
     */
    public function findProductsByCategory(string $category)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.produits', 'p')
            ->andWhere('m.categorie = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult();
    }
    
     /**
     * Count products associated with menus by category.
     *
     * @param string $category
     * @return mixed
     */
    public function countProductsByCategory(string $category)
    {
        $qb = $this->createQueryBuilder('m')
            ->select('COUNT(p) as productCount')
            ->leftJoin('m.produits', 'p')
            ->andWhere('m.categorie = :category')
            ->setParameter('category', $category);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
