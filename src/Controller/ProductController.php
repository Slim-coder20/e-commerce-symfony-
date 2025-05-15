<?php

namespace App\Controller;
use App\Entity\AddProductHistory;
use App\Entity\Product;
use App\Form\ProductForm;
use App\Form\AddProductHistoryForm;
use App\Repository\AddProductHistoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('editor/product')]
final class ProductController extends AbstractController

{   
    // Cette méthode permet d'afficher la liste des produits // Elle est accessible via la route '/product' et utilise la méthode GET //
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    // Cette méthode permet d'ajouter un nouveau Produit // Elle est accessible via la route '/product/new' et utilise les méthodes GET et POST // 

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On recupère le fichier image // 
            $imageFile = $form->get('image')->getData();


            // On vérifie si un fichier a été télécharger // 
            if($imageFile){
                // On génère un nom de fichier unique //
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // On utilise le slugger pour créer un nom de fichier sécurisé //
                $safeFilename = $slugger->slug($originalFilename);
                // On génère un nom de fichier unique en ajoutant un identifiant unique //
                // On utilise la méthode guessExtension() pour obtenir l'extension du fichier //
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    // On déplace le fichier dans le répertoire de destination //
                    // On utilise la méthode move() pour déplacer le fichier dans le répertoire de destination //
                    // On utilise la méthode getParameter() pour obtenir le chemin du répertoire de destination //
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $exeption) {
                    // ici vous pouvez gérer l'exception si le fichier ne peut pas être déplacé
                }
                $product->setImage($newFilename);

                
            }
            $entityManager->persist($product);
            $entityManager->flush();

            // On va instancié la classe AddProductHistory pour ajouter du stock au produit //

            $stockHistory = new AddProductHistory();
           
            // On récupère le stock du produit //
            $stockHistory->setQte($product->getStock());

            // on recupère la quantité de produit //
            $stockHistory->setProduct($product);
            
            // On recupère la date actuelle //
            $stockHistory->setCreatedAt(new \DateTimeImmutable());
            
            // On persiste le produit en BDD // 
            $entityManager->persist($stockHistory);
            
            // On enregistre le produit en BDD //
            $entityManager->flush();

            $this->addFlash('success', 'Votre produit a  été ajouter !');

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    // Cette méthode permet d'afficher un produit en particulier // Elle est accessible via la route '/product/{id}' et utilise la méthode GET //

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
    // Cette méthode permet de modifier un produit en particulier // Elle est accessible via la route '/product/{id}/edit' et utilise les méthodes GET et POST //

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre produit a été modifié !');

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
    
    // Cette méthode permet de supprimer un produit en particulier // Elle est accessible via la route '/product/{id}' et utilise la méthode POST //
    
    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            
            $this->addFlash('danger', 'Votre produit a été supprimé !');
            
            $entityManager->flush();
            
            
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    // Cette méthode permet d'ajouter du stock à un produit en particulier // Elle est accessible via la route '/product/{id}/stock' et utilise les méthodes GET et POST //

    #[Route('/add/product/{id}/stock', name: 'app_product_stock_add', methods: ['POST', 'GET'])]
    public function addStock($id, EntityManagerInterface $em, Request $request, ProductRepository $productRepository):Response 
    {   

        $addStock = new AddProductHistory(); 
        // on instancie la classe AddProductHistory //
       
        $form = $this->createForm(AddProductHistoryForm::class, $addStock);
       // on créé le formulaire //  
        
       $form->handleRequest($request);
        // on gère la requête //

        // On récupère le produit auquel on veut ajouter du stock a partir de son id on utilisant le repository // 
        $product = $productRepository->find($id);

        // on soumet le formulaire et on vérifie si il est valide //
        if($form->isSubmitted() && $form->isValid())
        {
            if($addStock->getQte() > 0)
            {
             $newQte = $product ->getStock() + $addStock->getQte();
            // on récupère la quantité de produit //
            // on ajoute la quantité de produit au stock //
            $product->setStock($newQte);

            $addStock->setCreatedAt(new \DateTimeImmutable());
            // on récupère la date actuelle //
            
            $addStock->setProduct($product);
            // on associe le produit au stock //
            
            // on met à jour le stock du produit //
            $em->persist($addStock);
            // on persiste le nouveau stock  en BDD //
            $em->flush();
            // on enregistre le nouveau stock  en BDD //
             $this->addFlash('success', 'Votre produit a été modifié !');
            // on affiche un message de succès //
            return $this->redirectToRoute('app_product_index');

           
            
        }
        else{
            $this->addFlash('danger', 'La quantité de produit doit être supérieur à 0 !');
            return $this->redirectToRoute('app_product_stock_add', ['id' => $product->getId()]);
            // on affiche un message d'erreur //
        }   
            
        }

        return $this->render('product/addStock.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    
    
    
    
    }
    // On créé une méthode qui permet d'afficher l'historique de stock d'un produitet qui utilise la méthode GET //
    #[Route('/add/product/{id}/stock/history', name: 'app_product_stock_add_history', methods: ['GET'])]
    public function productAddHistory($id, ProductRepository $productRepository,AddProductHistoryRepository $addProductHistory):Response
    {
        
      $product = $productRepository->find($id);
        // on récupère le produit à partir de son id //
        $productAddedHistory = $addProductHistory->findBy(['product' => $product], ['id' => 'DESC']);
        // on récupère l'historique de stock du produit //
        return $this->render('product/addedStockHistory.html.twig', [
            'productsAdded' => $productAddedHistory,
            
        ]);
       
        
    
    
    
    
    }















}
