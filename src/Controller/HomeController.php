<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('home/home.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
    
    // cette route va nous sevire a afficher les détails d'un produit selectionné depuis la page d'accueil // 
    
    
    #[Route('/product/{id}/show', name: 'app_home_product_show', methods: ['GET'])]
    public function showProduct (Product $product, ProductRepository $productRepository): Response
    {
        $lastProducts = $productRepository->findBy([], ['id' => 'DESC'], 3);
        
        
        return $this->render('home/show.html.twig', [
            'product' => $product,
            'products' => $lastProducts,
        ]);
    }








}
