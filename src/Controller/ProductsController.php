<?php

// src/Controller/ProductsController.php
namespace App\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Product; 
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ProductsController extends AbstractController
{
    private $documentManager;
    private $cache;

    public function __construct(DocumentManager $documentManager, CacheInterface $cache)
    {
        $this->documentManager = $documentManager;
        $this->cache = $cache;
    }

    #[Route('/product', name: 'app_products', methods:['POST'])]
    public function index(ProductRepository $productRepository, PaginatorInterface $paginator, RequestStack $requestStack): Response
    {
        $request = $requestStack->getCurrentRequest();
        $cacheKey = md5($request->getQueryString());

        $products = $this->cache->get($cacheKey, function (ItemInterface $item) use ($productRepository, $paginator, $request) {
            $searchTerm = $request->query->get('search', '');
            $category = $request->query->get('category', '');
            $minPrice = $request->query->get('minPrice', '');
            $minAvgRating = floatval($request->query->get('minAvgRating', ''));

            $allProducts = $productRepository->findByCriteria($searchTerm, $category, $minPrice, $minAvgRating);
            $item->expiresAfter(3600); // Cache expires in 1 hour

            return $paginator->paginate(
                $allProducts,
                $request->query->getInt('page', 1),
                5
            );
        });

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'avgRatingSearch' => ($request->query->get('minAvgRating') > 0),
            'categories' => $productRepository->findAllCategories(),
        ]);
    }

    #[Route('/product/{id}', name: 'product_details')]
    public function show($id, ProductRepository $productRepository): Response
    {
        $product = $this->cache->get('product_' . $id, function (ItemInterface $item) use ($productRepository, $id) {
            $product = $productRepository->find($id);
            $item->expiresAfter(3600); // Cache expires in 1 hour

            return $product;
        });

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $avgRating = $productRepository->calculateAverageRating($product);

        return $this->render('product/details.html.twig', [
            'product' => $product,
            'avgRating' => $avgRating,
        ]);
    }

    #[Route('/product/new', name: 'product_new')]
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $this->documentManager->persist($product);
            $this->documentManager->flush();
    
            return $this->redirectToRoute('app_products', ['id' => $product->getId()]);
        }
    
        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/{id}/delete', name: 'product_delete')]
    public function delete($id): Response
    {
        $product = $this->documentManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $this->documentManager->remove($product);
        $this->documentManager->flush();

        return $this->redirectToRoute('app_products');
    }
}
