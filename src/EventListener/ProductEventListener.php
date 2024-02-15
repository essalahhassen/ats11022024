<?php

// src/EventListener/ProductEventListener.php
namespace App\EventListener;

use App\Document\Product;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Symfony\Contracts\Cache\CacheInterface;

class ProductEventListener
{
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $product = $args->getDocument();
        
        // Mise en cache des détails du produit après l'insertion d'un nouveau produit
        $this->cacheProductDetails($product);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $product = $args->getDocument();
        
        // Supprimer les détails du produit mis en cache après la suppression d'un produit
        $this->deleteCachedProductDetails($product);
    }

    private function cacheProductDetails(Product $product)
    {
        // Utiliser une clé unique pour chaque produit
        $cacheKey = 'product_details_' . $product->getId();

        // Mettre en cache les détails du produit avec une durée de vie appropriée
        $this->cache->set($cacheKey, $product, $expirationTimeInSeconds);
    }

    private function deleteCachedProductDetails(Product $product)
    {
        // Utiliser la même clé que celle utilisée pour mettre en cache les détails du produit
        $cacheKey = 'product_details_' . $product->getId();

        // Supprimer les détails du produit mis en cache
        $this->cache->delete($cacheKey);
    }
}
