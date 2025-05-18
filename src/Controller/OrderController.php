<?php

namespace App\Controller;
use App\Entity\Order;
use App\Form\OrderType;
use App\Form\OrderTypeForm;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, SessionInterface $session, ProductRepository $productRepository): Response
    {
        // On récupère le panier // 
        $cart = $session->get('cart', []);
        
        // On récupère les produits du panier //
        $cartWithData = [];
        
        // on fait un for each sur le panier //
        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $this->$productRepository->find($id),
                'quantity' => $quantity,
            ];
        }
        
        // On calcule le total //
        $total = array_sum(array_map(function ($item){
            
            return $item['product']->getPrice() * $item['quantity'];
        
        }, $cartWithData));


        $order = New Order(); 
        $form = $this->createForm(OrderTypeForm::class, $order);
        
        $form->handleRequest($request);
        
       
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'total' => $total,
            
        ]);
    }
}
