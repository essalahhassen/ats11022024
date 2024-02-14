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

    

    public function findByCriteria($searchTerm, $category, $minPrice,$minAvgRating)
    {
        $queryBuilder = $this->documentManager->createQueryBuilder(Product::class);


        if ($searchTerm) {
            $queryBuilder->field('productName')->equals(new \MongoDB\BSON\Regex($searchTerm, 'i'));
        }

        if ($category) {
            $queryBuilder->field('category')->equals($category);
        }

        if ($minPrice) {
            $queryBuilder->field('price')->gte($minPrice);
        }

        // Récupérer les produits
        $products = $queryBuilder->getQuery()->execute();

        // Filtrer les produits par note minimale moyenne des avis
        $filteredProducts = [];
        foreach ($products as $product) {
            $averageRating = $this->calculateAverageRating($product);
            if ($averageRating !== null && $averageRating >= $minAvgRating) {
                $filteredProducts[] = $product;
            }
        }
    
        return $filteredProducts; 

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


    public function calculateAverageRating(Product $product): ?float
    {
        $reviews = $product->getReviews();
        $totalReviews = count($reviews);
        $totalRating = 0;
    
        if ($totalReviews === 0) {
            return null;
        }
    
        foreach ($reviews as $review) {
            $totalRating += $review->getValue(); // Assurez-vous d'avoir un champ `value` dans votre document Review pour stocker la note
        }
    
        return $totalRating / $totalReviews;
    }
    public function findAllCategories(): array
    {
        try {
            $queryBuilder = $this->documentManager->createQueryBuilder(Product::class);
            $queryBuilder->distinct('category'); // Retrieve distinct values for the category field
    
            // Execute the query to retrieve distinct categories
            $categories = $queryBuilder->getQuery()->execute();
    

    
            // Extract category values from the query result
            $distinctCategories = [];
            foreach ($categories as $category) {
                $distinctCategories[] = $category;
            }
    
            return $distinctCategories;
        } catch (\Exception $e) {
            // Handle any exceptions
            echo "An error occurred: " . $e->getMessage();
            // Log the error if necessary
            // Log::error($e->getMessage());
            return []; // Return an empty array or handle the error appropriately
        }
    }
    
}
