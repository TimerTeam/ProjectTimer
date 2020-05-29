<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Group;
use App\Entity\User;
use App\Form\GroupType;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;


class GroupController extends AbstractController
{
    private $entityManager;
    private $userRepository;

    public function __construct(UserRepository $userRepository,EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/group", name="group")
     */
    public function index()
    {
        return $this->render('group/index.html.twig', [
            'controller_name' => 'GroupController',
        ]);
    }

    /**
     * @Route("/group-create", name="group-create")
     */
    public function newAction(
        Request $request,
        EntityManagerInterface $entityManager)
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $curentUser = $this->getUser(); 
            $curentUserId = $curentUser->getId();
            //$password = $passwordEncoder->encodePassword($group, $group->getPassword());
            //$group->setPassword($password);

            //$entityManager->persist($group);

            //$entityManager->flush();
            //$this->addFlash('success', "The user has been created");

            return $this->redirectToRoute('group');

        }

        return $this->render('group/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
