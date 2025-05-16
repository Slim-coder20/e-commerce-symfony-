<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Entity\Product;
use App\Repository\SubcategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginatorInterface ): Response
    {
        $data = $productRepository->findBy([],['id' => 'DESC']);
        $product = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            6
        );
        
        return $this->render('home/home.html.twig', [
            'products' => $product,            
            'categories' => $categoryRepository->findAll(),
        ]);
    }
    
    
    // cette route va nous sevire a afficher les détails d'un produit selectionné depuis la page d'accueil // 
    #[Route('/product/{id}/show', name: 'app_home_product_show', methods: ['GET'])]
    public function showProduct (Product $product, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $lastProducts = $productRepository->findBy([], ['id' => 'DESC'], 3);
        
        
        return $this->render('home/show.html.twig', [
            'product' => $product,
            'products' => $lastProducts,
            'categories' => $categoryRepository->findAll(),
        ]);

    }
    
    // cette route va nous permettre de filtrer les produits par catégories // 
    #[Route('/home/product/subcategory/{id}/filter}', name: 'app_home_product_filter', methods: ['GET'])]
    public function filter ($id ,SubcategoryRepository $subcategoryRepository,CategoryRepository $categoryRepository): Response
    {
        
        $products = $subcategoryRepository->find($id)->getProducts();
        $subcategory = $subcategoryRepository->find($id);
        
   
            
        return $this->render('home/filter.html.twig', [
        'products' => $products,
        'subcategory' => $subcategory,
        'categories' => $categoryRepository->findAll(),
            
        ]);
    }








}
