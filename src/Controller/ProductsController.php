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
use Symfony\Component\Form\FormFactoryInterface;



class ProductsController extends AbstractController
{


    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    #[Route('/product', name: 'app_products',methods:['POST'])]
    public function index(ProductRepository $productRepository, PaginatorInterface $paginator, RequestStack $requestStack): Response
    {

        // Obtenez l'objet Request à partir de RequestStack
        $request = $requestStack->getCurrentRequest();

        $searchTerm = $request->query->get('search', '');
        $allProducts = [];
        $searchTerm = $request->query->get('searchTerm', '');
        $category = $request->query->get('category', '');
        $minPrice = $request->query->get('minPrice', '');
        $minAvgRating = floatval($request->query->get('minAvgRating', ''));

        $allProducts = $productRepository->findByCriteria($searchTerm, $category, $minPrice, $minAvgRating);

        // Déterminez si la recherche de la moyenne des notes est en cours
        $avgRatingSearch = ($minAvgRating > 0);

        // Récuperer la liste des catégories
        $categories = $productRepository->findAllCategories();

        // Paginer les produits
        $products = $paginator->paginate(
            $allProducts,                        // Requête paginée
            $request->query->getInt('page', 1),  // Numéro de page
            5                                  // Nombre d'éléments par page
        );

       // Retourner la réponse avec les produits
       return $this->render('product/index.html.twig', [
           'products' => $products,
           'avgRatingSearch' => $avgRatingSearch, // Passer la variable avgRatingSearch au template Twig
           'categories' => $categories,// Passer la variable categories au template Twig
       ]);
    }
    #[Route('/product/{id}', name: 'product_details')]
    public function show($id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);
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

        // Rediriger vers la liste des produits après la suppression
        return $this->redirectToRoute('app_products');
    }
}
