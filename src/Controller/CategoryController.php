<?php

namespace App\Controller;
use App\Form\CategoryForm;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        
        
        
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
    
    // Cette route va nous servir à ajouter une nouvelle catégorie // 
       
    #[Route('/category/new', name: 'app_category_new')]
       public function addCategory(EntityManagerInterface $em, Request $request): Response
       {   
   
        // On instancie l'entité Category //
        $category = new Category();
        
        // On créé le formulaire 
        $form = $this->createForm(CategoryForm::class, $category);

        // On traite la requête // 
        $form->handleRequest($request);

        // On vérifie si le formulaire est soumis et valide //
        if ($form->isSubmitted() && $form->isValid()) {
            
            // On récupère les données du formulaire
            $category = $form->getData();
            
            // On persiste l'entité Category //
            $em->persist($category);
            $em->flush();

            // on affiche un message flash de succés // 
            $this->addFlash('success', 'La catégorie a été ajoutée avec succès !');

            // On redirige vers la page de la liste des catégories //
            return $this->redirectToRoute('app_category_new');

           
        }
        
        return $this->render('category/new.html.twig', [
               'form' => $form->createView()
           ]);
       }
     
       // cette route va nous servir a mettre à jour une catégorie // 
       
     #[Route('/category/update', name: 'app_category_update')]
     public function updateCategory(): Response
     {
        
    
     }
         



}