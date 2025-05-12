<?php

namespace App\Controller;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;


 class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        
        
        
        
        return $this->render('user/user.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    // cette route est pour créé un éditeur ROLE_EDITOR // 

      #[Route('/admin/user:{id}/to/editor', name: 'app_user_to_editor')]
      public function changeRole(EntityManagerInterface $em, User $user): Response
      {

        // ici on va changer le role de l'utilisateur en ROLE_EDITOR
        $user->setRoles(['ROLE_EDITOR', 'ROLE_USER']);
       
        // on va flusher les changements dans la base de données
        $em->flush();
        
        // on envoie un message flash pour infoler l'utilisateur que le rôle a été changé
         $this->addFlash('success', "L'utilisateur a été promu éditeur.");

        // on redirige vers la page de l'éditeur
        return $this->redirectToRoute('app_user');
         
      }
      // cette route va nous permettre de retiré le rôle d'éditeur à un utilisateur //
        #[Route('/admin/user:{id}/remove/editor/role', name: 'app_user_remove_editor_role')]
        public function editorRoleRemove (EntityManagerInterface $em, User $user): Response
      {

        // ici on va changer le role de l'utilisateur en ROLE_EDITOR
        $user->setRoles([]);
       
        // on va flusher les changements dans la base de données
        $em->flush();
        
        // on envoie un message flash pour infoler l'utilisateur que le rôle a été changé
         $this->addFlash('success', "Le rôle d'éditeur a été retiré à l'utilisateur.");

        // on redirige vers la page de l'éditeur
        return $this->redirectToRoute('app_user');
         
      }

        // cette route va nous permettre de supprimer un utillisateur depuis l'espace admin //

        #[Route('/admin/user:{id}/remove', name: 'app_user_remove')]
        public function userRemove (EntityManagerInterface $em, $id ,UserRepository $userRepository): Response
      {

        // ici on va supprimer l'utilisateur depuis son id avec le repository // 
        $user = $userRepository->find($id);
        
        $em->remove($user);
       
        // on va flusher les changements dans la base de données
        $em->flush();
        
        // on envoie un message flash pour infoler l'utilisateur que le rôle a été changé
         $this->addFlash('success', "L'utilisateur a été supprimé.");

        // on redirige vers la page de l'éditeur
        return $this->redirectToRoute('app_user');
         
      }












}
