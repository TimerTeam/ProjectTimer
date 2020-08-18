<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Team;
use App\Entity\User;
use App\Form\TeamType;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;


class TeamController extends AbstractController
{
    private $entityManager;
    private $teamRepository;

    public function __construct(TeamRepository $teamRepository,EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->teamRepository = $teamRepository;
    }

    /**
     * @Route("/team", name="team")
     */
    public function index()
    {
        $teamList = $this->teamRepository->findAll();
        return $this->render('team/index.html.twig', [
            'controller_name' => 'Team Controller',
            'team_list' => $teamList
        ]);
    }

    /**
     * @Route("/team_create", name="team-create")
     */
    public function newAction(
        Request $request,
        EntityManagerInterface $entityManager)
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $curentUser = $this->getUser(); 
            $curentUserId = $curentUser->getId();
            $team->setTeamAdmin($curentUserId);
            $team->addUser($curentUser);
            dump($team);

            $entityManager->persist($team);
            $entityManager->flush();
            $this->addFlash('success', "The user has been created");

            return $this->redirectToRoute('team');

        }

        return $this->render('team/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/team_edit/{idTeam}", name="team_edit")
     */
    public function editAction(
        Request $request,
        EntityManagerInterface $entityManager,
        $idTeam)
    {
        $team = $this->teamRepository->find(['id' => $idTeam]);
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $curentUser = $this->getUser(); 
            $curentUserId = $curentUser->getId();
            $team->setTeamAdmin($curentUserId);
            $team->addUser($curentUser);

            $entityManager->persist($team);
            $entityManager->flush();
            $this->addFlash('success', "The user has been updated");

            return $this->redirectToRoute('team');

        }

        return $this->render('team/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
