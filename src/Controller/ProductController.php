<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductForm;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;



#[Route('/product')]
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
}
