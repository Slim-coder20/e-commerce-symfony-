<?php

namespace App\Controller;
use App\Form\CategoryForm;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository): Response
    {   
        // On récupère toutes les catégories //
        $categories = $categoryRepository->findAll();
        
        // On les envoie à la vue //
        return $this->render('category/category.html.twig', [
            'categories' => $categories,
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
            return $this->redirectToRoute('app_category');

           
        }
        
            return $this->render('category/new.html.twig', [
               'form' => $form->createView()
            ]);
        }
     
       // cette route va nous servir a mettre à jour une catégorie // 
       
        #[Route('/category/{id}/update', name: 'app_category_update')]
        public function updateCategory(Category $category, EntityManagerInterface $em, Request $request): Response
        {
            $form = $this->createForm(CategoryForm::class, $category);
            
            // On traite la requête // 
            $form->handleRequest($request);

            // On vérifie si le formulaire est soumis et valide //
            if($form->isSubmitted() && $form->isValid())
            {
                // On récupère les données du formulaire
                $category = $form->getData();
               
                $em->flush();

                // on affiche un message flash de succés //
                $this->addFlash('success', 'La catégorie a été mise à jour avec succès !');
            
                // On redirige vers la page de la liste des catégories //
                return $this->redirectToRoute('app_category');

            
            }
            return $this->render('category/update.html.twig', [
            'form' => $form->createView()
            ]);
    
        }
        
        // Cette route va nous servir à supprimer une catégorie //
        #[Route('/category/{id}/delete', name: 'app_category_delete')]
        public function deleteCategory(Category $category, EntityManagerInterface $em): Response
        {
            // On supprime la catégorie //
            $em->remove($category);
            $em->flush();

            // On affiche un message flash de succés //
            $this->addFlash('danger', 'La catégorie a été supprimée avec succès !');

            // On redirige vers la page de la liste des catégories //
            return $this->redirectToRoute('app_category');
        }
         



}