<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Group;
use App\Entity\User;
use App\Form\GroupType;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;


class GroupController extends AbstractController
{
    private $entityManager;
    private $groupRepository;

    public function __construct(GroupRepository $groupRepository,EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @Route("/group", name="group")
     */
    public function index()
    {
        $groupList = $this->groupRepository->findAll();
        return $this->render('group/index.html.twig', [
            'controller_name' => 'Group Controller',
            'group_list' => $groupList
        ]);
    }

    /**
     * @Route("/group_create", name="group-create")
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
            $group->setGroupAdmin($curentUserId);
            $group->addUser($curentUser);

            $entityManager->persist($group);
            $entityManager->flush();
            $this->addFlash('success', "The user has been created");

            return $this->redirectToRoute('group');

        }

        return $this->render('group/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/group_edit/{idGroup}", name="group_edit")
     */
    public function editAction(
        Request $request,
        EntityManagerInterface $entityManager,
        $idGroup)
    {
        $group = $this->groupRepository->find(['id' => $idGroup]);
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $curentUser = $this->getUser(); 
            $curentUserId = $curentUser->getId();
            $group->setGroupAdmin($curentUserId);
            $group->addUser($curentUser);

            $entityManager->persist($group);
            $entityManager->flush();
            $this->addFlash('success', "The user has been updated");

            return $this->redirectToRoute('group');

        }

        return $this->render('group/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
