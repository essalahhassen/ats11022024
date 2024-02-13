<?php
// src/Controller/ProductsController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductsController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function index(): Response
    {
        $searchTerm = $request->query->get('search');
        $category = $request->query->get('category');
        $minPrice = $request->query->get('min_price');
        $minAvgRating = $request->query->get('min_avg_rating');

        $products = $this->getDoctrine()->getRepository(Product::class)->findByCriteria($searchTerm, $category, $minPrice, $minAvgRating);

        return $this->render('product/index.html.twig', [
            'products' => $products,

        ]);
    }
}
