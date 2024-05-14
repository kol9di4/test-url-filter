<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
    public function findAllBrands() : array{
        return $this->createQueryBuilder('p')
            ->select('p.brand')
            ->distinct('p.brand')
            ->getQuery()
            ->getResult();
    }
    public function isBrandsExists($brand) : array{
        return $this->createQueryBuilder('p')
            ->select('p.brand')
            ->distinct('p.brand')
            ->where('p.brand = :brand')
            ->setParameter('brand', $brand)
            ->getQuery()
            ->getResult();
    }
    public function findAllColors() : array{
        return $this->createQueryBuilder('p')
            ->select('p.color')
            ->distinct('p.color')
            ->getQuery()
            ->getResult();
    }
    public function findAllMaterials() : array{
        return $this->createQueryBuilder('p')
            ->select('p.material')
            ->distinct('p.material')
            ->getQuery()
            ->getResult();
    }
    public function findMaxPrice() : int{
        return $this->createQueryBuilder('p')
            ->select('MAX(p.price)')
            ->getQuery()
            ->getResult()[0][1];
    }
    public function findAllProductsInBrand(string $brand="", array $colors=[], int $minPrice = 0, int $maxPrice = 0, array $materials = [], int $availability = -1): array {
        $query = $this->createQueryBuilder('p');
        if (!empty($colors)) {
            $strQuery = "(p.color = '$colors[0]'";
            for ($i = 1; $i < count($colors); $i++) {
                $strQuery.="or p.color = '$colors[$i]'";
            }
            $strQuery.=")";
            $query->andWhere($strQuery);
        }

        if(!empty($materials))
        {
            $strQuery = "(p.material = '$materials[0]'";
            for($i=1;$i<count($materials);$i++){
                 $strQuery.="or p.material = '$materials[$i]'";
            }
            $strQuery.=")";
            $query->andWhere($strQuery);
        }
        $query->andWhere("p.brand = '$brand'");
        if($minPrice !== 0)
            $query->andWhere("p.price > $minPrice");
        if($maxPrice !== 0)
            $query->andWhere("p.price < $maxPrice");
        if($availability !== -1){
            $query->andWhere("p.availability = $availability");
        }
        $query->expr()->andX();

        return $query->getQuery()->getResult();
    }
}
