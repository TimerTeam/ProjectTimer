<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Team;
use App\Entity\Project;
use App\Form\ProjectType;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;

class ProjectController extends AbstractController
{
    private $entityManager;
    private $ProjectRepository;

    public function __construct(ProjectRepository $ProjectRepository,EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->ProjectRepository = $ProjectRepository;
    }
    /**
     * @Route("/project", name="project")
     */
    public function index()
    {
        $projectList = $this->ProjectRepository->findAll();

        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
            'projet_list' => $projectList
        ]);
    }

    /**
     * @Route("/project_create", name="project-create")
     */
    public function newAction(
        Request $request,
        EntityManagerInterface $entityManager)
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $curentUser = $this->getUser(); 
            $curentUserId = $curentUser->getId();
            $project->setProjectAdmin($curentUserId);
            dump($project);

            $entityManager->persist($project);
            $entityManager->flush();
            $this->addFlash('success', "The user has been created");

            return $this->redirectToRoute('project');

        }

        return $this->render('project/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project_edit/{idProject}", name="project_edit")
     */
    public function editAction(
        Request $request,
        EntityManagerInterface $entityManager,
        $idProject)
    {
        $project = $this->ProjectRepository->find(['id' => $idProject]);
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $curentUser = $this->getUser(); 
            $curentUserId = $curentUser->getId();
            $project->setProjectAdmin($curentUserId);

            $entityManager->persist($project);
            $entityManager->flush();
            $this->addFlash('success', "The user has been updated");

            return $this->redirectToRoute('project');

        }

        return $this->render('project/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
