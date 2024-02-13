<?php
// src/Service/ProductImportService.php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Product;
use App\Document\Review;
use Psr\Log\LoggerInterface;

class ProductImportService
{
    private HttpClientInterface $httpClient;
    private DocumentManager $documentManager;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, DocumentManager $documentManager, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->documentManager = $documentManager;
        $this->logger = $logger;
    }

    private function validateProductData(array $productData): bool
    {
        $requiredKeys = ['productName'];

        foreach ($requiredKeys as $key) {
            if (!isset($productData[$key])) {
                $this->logger->warning("La clé \"$key\" est manquante dans les données du produit.", ['productData' => $productData]);
                return false;
            }
        }

        // Toutes les clés nécessaires sont présentes
        return true;
    }

    public function importProductsFromUrl(string $url): void
    {
        $response = $this->httpClient->request('GET', $url);
        $productsData = json_decode($response->getContent(), true);
        
        foreach ($productsData as $productDatas) {
            foreach ($productDatas as $productData) {
                if ($this->validateProductData($productData)) {
                    // Créer une nouvelle instance de Product avec les données
                    $product = new Product();
                    $product->setProductName($productData['productName']);
                    $product->setDescription($productData['description']);
                    $product->setPrice($productData['price']);
                    $product->setCategory($productData['category']);
                    $product->setImageUrl($productData['imageUrl']);
                    foreach ($productData['reviews'] as $reviewData) {
                        // Création d'une nouvelle review pour chaque produit
                        $review = new Review();
                        $review->setValue($reviewData['value']);
                        $review->setContent($reviewData['content']);
                    
                        // Ajout de la review au produit
                        $product->addReview($review);
                    }
                    // Convertir le document en BSON et l'insérer dans la base de données
                    $this->documentManager->persist($product);
                }
            }
        }

        // Flush des changements vers la base de données
        $this->documentManager->flush();
    }
}