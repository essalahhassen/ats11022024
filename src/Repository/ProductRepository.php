<?php

// src/Repository/ProductRepository.php

namespace App\Repository;

use App\Document\Product;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;



class ProductRepository extends ServiceDocumentRepository
{
    private $documentManager;
    public function __construct(ManagerRegistry $registry, DocumentManager $documentManager)
    {
        parent::__construct($registry, Product::class);
        $this->documentManager = $documentManager;
    }


    

    public function findByCriteria($searchTerm, $category, $minPrice)
    {
        $queryBuilder = $this->documentManager->createQueryBuilder(Product::class);
        $queryBuilder->hydrate(false); // Désactive l'hydratation des résultats en objets

        if ($searchTerm) {
            $queryBuilder->field('productName')->equals(new \MongoDB\BSON\Regex($searchTerm, 'i'));
        }

        if ($category) {
            $queryBuilder->field('category')->equals($category);
        }

        if ($minPrice) {
            $queryBuilder->field('price')->gte($minPrice);
        }

  

        return $queryBuilder->getQuery()->execute()->toArray();
    }
    public function findAllProducts()
    {
        $queryBuilder = $this->documentManager->createQueryBuilder(Product::class);
        $queryBuilder->hydrate(false); // Désactive l'hydratation des résultats en objets
        $query = $queryBuilder->getQuery();
    

        return $query->execute()->toArray();
    }
    public function findByPartialProductName(string $searchTerm)
    {
        $queryBuilder = $this->documentManager->createQueryBuilder(Product::class);
        $queryBuilder->hydrate(false); // Désactive l'hydratation des résultats en objets
        
        // Utilisation de l'opérateur $regex pour une recherche partielle par productName
        $queryBuilder->field('productName')->equals(new \MongoDB\BSON\Regex($searchTerm, 'i'));
        
        $query = $queryBuilder->getQuery();
        
        return $query->execute()->toArray();
    }
}
