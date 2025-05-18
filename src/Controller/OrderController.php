<?php

namespace App\Controller;
use App\Entity\Order;
use App\Form\OrderType;
use App\Form\OrderTypeForm;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, SessionInterface $session ): Response
    {
        
        $order = New Order(); 
        $form = $this->createForm(OrderTypeForm::class, $order);
        
        $form->handleRequest($request);
        
       
        
        
        
        
        
        
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }
}
