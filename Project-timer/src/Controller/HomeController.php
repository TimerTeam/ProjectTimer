<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(
    AuthenticationUtils $authenticationUtils,
    Request $request,
    EntityManagerInterface $entityManager,
    UserPasswordEncoderInterface $passwordEncoder)
    {
            // récupérer l'erreur de connexion si il y a
            $error = $authenticationUtils->getLastAuthenticationError();
            // dernier email entré par l'utilisateur
            $lastUsername = $authenticationUtils->getLastUsername();

             $user = new User();

            $form = $this->createForm(UserType::class, $user);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);

                $entityManager->persist($user);

                $entityManager->flush();
                //$request->getSession()->getFlashBag()->add();
                $this->addFlash('success', "votre compte a été crée, veuillez vous connecter");

            }
            return $this->render('home/index.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
                'form' => $form->createView(),

            ]);


    }

    /**
     * @Route("/profile", name="profile")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function profileAction(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);


            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
             $password = $passwordEncoder->encodePassword($user, $user->getPassword());

            $user->setPassword($password);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profile');

        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' =>$form->createView(),
        ]);
    }

}
