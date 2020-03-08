<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(Request $request)
    {
        $pdo = $this->getDoctrine()->getManager();

        $user = new User();
        $formUser = $this->createForm(UserType::class, $user);

        $formUser->handleRequest($request);
        if ( $formUser->isSubmitted() && $formUser->isValid() ) {
            
            $user->setCreatedAt(new\ DateTime('now')); // date d'incription
            
            $pdo->persist($user);
            $pdo->flush();

            $this->addFlash("success", "Utilisateur ajouté !");
        }

        $users = $pdo->getRepository(User::class)->findAll();

        return $this->render('home_page/index.html.twig', [
            'users' => $users,
            'form_user_add' => $formUser->createView()
        ]);
    }


    /**
     * PAGE UTILISATEUR 
     * @Route("/{id}", name="my_user")
     */

     public function user(Request $request, User $user=null){

        if ($user !=null) {
            //si l'utilisateur existe
            
            $formUser = $this->createForm(UserType::class, $user);
            $formUser->handleRequest($request);

            if ( $formUser->isSubmitted() && $formUser->isValid() ) {

                $pdo = $this->getDoctrine()->getManager();
                $pdo->persist($user);
                $pdo->flush();

                $this->addFlash("success", "Utilisateur modifié !");
            }

            return $this->render('home_page/user.html.twig', [
                'user' => $user,
                'form_user_edit' => $formUser->createView()
            ]);
        }
        else {
            // l'utilisateur n'existe pas 
            $this->addFlash("danger", "Utilisateur introuvable ");
            return $this-> redirectToRoute('home');
        }
     }

     /**
     * PAGE SUPRESSION
     * @Route("/user/delete/{id}", name="delete_user")
     */

    public function deleteUser(User $user=null) {
        if($user != null) {
            $pdo = $this->getDoctrine()->getManager();
            $pdo->remove($user); 
            $pdo->flush();
        }

        return $this->redirectToRoute('home');
    }


}
