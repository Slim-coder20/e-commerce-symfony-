<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{   
    public function __construct(private readonly ProductRepository $productRepository)
    {
       
    }
   
    // Cette méthode permet de créé le panier // 

    #[Route('/cart', name: 'app_cart', methods: [ 'GET'])]
    public function index(SessionInterface $session ): Response
    {
        // On récupère le panier // 
        $cart = $session->get('cart', []);
        
        // On récupère les produits du panier //
        $cartWithData = [];
        
        // on fait un for each sur le panier //
        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity,
            ];
        }
        
        // On calcule le total //
        $total = array_sum(array_map(function ($item){
            
            return $item['product']->getPrice() * $item['quantity'];
        
        }, $cartWithData));
        
        
        

        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total,
        ]);
    }

    // Cette méthode permet d'ajouter un produit au panier //
    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['GET'])]
    public function addToCart(int $id, SessionInterface $session):Response
    {
    // On récupère le panier //
     $cart = $session->get('cart', []);
    
     // On vérifie si le produit est déjà dans le panier //
    if (!empty($cart[$id])) {
        // Si le produit est déjà dans le panier, on incrémente la quantité //
        $cart[$id]++;
    } else {
        $cart[$id] = 1;
    }
    
    // On met à jour le panier //
    $session->set('cart', $cart);
    
    // On redirige vers la page du panier //
    return $this->redirectToRoute('app_cart');
    
    
    }
    // Cette méthode permet de supprimer un produit du panier //
    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['GET'])]
    public function removeToCart(int $id, SessionInterface $session): Response
    {
        // On récupère le panier //
        $cart = $session->get('cart', []);
        
        // On vérifie si le produit est dans le panier //
        if (!empty($cart[$id])) {
            // Si le produit est dans le panier, on décrémente la quantité //
            $cart[$id]--;
            
            // Si la quantité est à 0, on supprime le produit du panier //
            if ($cart[$id] === 0) {
                unset($cart[$id]);
            }
        }
        
        // On met à jour le panier //
        $session->set('cart', $cart);
        
        // On redirige vers la page du panier //
        return $this->redirectToRoute('app_cart');
    }   
    
    
    // Cette méthode permet de vider le panier // 
    #[Route('/cart/clear', name: 'app_cart_clear', methods: ['GET'])]
    public function clearCart(SessionInterface $session): Response
    {
        // On vide le panier //
        $session->remove('cart');
        
        // On redirige vers la page du panier //
        return $this->redirectToRoute('app_cart');
    }

    



}
