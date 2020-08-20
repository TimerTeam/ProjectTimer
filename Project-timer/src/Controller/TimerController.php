<?php

namespace App\Controller;

use App\Entity\Timer;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\TimerRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;

class TimerController extends AbstractController
{
    private $timerRepository;
    private $userRepository;
    private $teamRepository;
    private $projectRepository;

    public function __construct(TimerRepository $timerRepository, UserRepository $userRepository, TeamRepository $teamRepository, ProjectRepository $projectRepository)
    {
        $this->timerRepository = $timerRepository;
        $this->userRepository = $userRepository;
        $this->teamRepository = $teamRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @Route("/timer/{idTeam}", name="timer")
     */
    public function index($idTeam)
    {
        $date = new \DateTime();
        $dateTimeFormat = 'Y-m-d H:i:s';

        $projectListWhereIdTeam = $this->timerRepository->findBy(['team' => $idTeam]);

        $timeList = [];
        $fullInterval = 0;
        foreach ($projectListWhereIdTeam as $project){
            if (($project->getDateStart() != null) && ($project->getDateEnd() != null)){

                $dateStartTimestamp = $project->getDateStart()->getTimestamp();
                $dateEndTimestamp = $project->getDateEnd()->getTimestamp();
                $interval = $dateEndTimestamp - $dateStartTimestamp;
                $timeList[$project->getId()]['interval'] = $interval;

                $dateStart = $project->getDateStart();
                $dateEnd = $project->getDateEnd();

                $timeList[$project->getId()]['dateStart'] = $dateStart;
                $timeList[$project->getId()]['dateEnd'] = $dateEnd;
            }
        }

        foreach ($timeList as $time){
            $fullInterval += $time['interval'];
        }
        $timeProject = $date->setTimestamp($fullInterval)->format($dateTimeFormat);


        return $this->render('timer/index.html.twig', [
            'controller_name' => 'TimerController',
            'projectList' => $projectListWhereIdTeam,
            'projectTime' => $timeProject
        ]);
    }

    /**
     * @Route("/timer_start/{idTeam}/{idProject}", name="timer-start")
     */
    public function startTimer(
        EntityManagerInterface $entityManager,
        $idTeam,
        $idProject)
    {
        $timer = new Timer();

        $curentUser = $this->getUser();
        $curentUserId = $curentUser->getId();
        $timer->setUser($curentUserId);
        $timer->setTeam($idTeam);
        $timer->setProject($idProject);
        $datetimeNow = new \DateTime();
        $datetimeNow->modify('+2 hour');
        $timer->setDateStart($datetimeNow);

        $token = uniqid();
        $timer->setToken($token);

        $session = new Session();
        $session->set('tokenTimer', $token);

        $entityManager->persist($timer);
        $entityManager->flush();
        $this->addFlash('success', "L'enregristrement du temps de travail a débuté");

        return $this->render('timer/startAndStop.html.twig');
    }

    /**
     * @Route("/timer_stop", name="timer_stop")
     */
    public function stopTimer(
        EntityManagerInterface $entityManager)
    {
        $session = new Session();
        $tokenTimer = $session->get('tokenTimer');
        $timerArray = $this->timerRepository->findBy(['token' => $tokenTimer]);
        $timer = $timerArray[0];
        $datetimeNow = new \DateTime();
        $datetimeNow->modify('+2 hour');
        $timer->setDateEnd($datetimeNow);
        $idTeam = $timer->getTeam();

        $entityManager->persist($timer);
        $entityManager->flush();
        $session->remove('tokenTimer');
        $this->addFlash('success', "Votre temps de travail a été enregistrer");

        return $this->redirectToRoute('timer', [
            'idTeam' => $idTeam,
        ]);
    }

    /**
     * @Route("/timer_team", name="timer-team")
     */
    public function listTeam()
    {
        $currentUser = $this->getUser();
        $currentUserId = $currentUser->getId();
        $teams = $this->teamRepository->findAll();
        $tArray = [];
        foreach ($teams as $team) {
            $users = $team->getUsers();
            foreach ($users as $user){
                if ($user->getId() == $currentUserId){
                    $tArray[] = $team;
                }
            }
        }

        return $this->render('timer/listTeam.html.twig', [
            'tArray' => $tArray
        ]);
    }

    /**
     * @Route("/timer_group/{idTeam}", name="timer-group")
     */
    public function listGroup($idTeam)
    {
        $currentUser = $this->getUser();
        $currentUserId = $currentUser->getId();
        $projects = $this->projectRepository->findAll();
        $gArray = [];
        foreach ($projects as $project) {
            $team = $project->getTeam();
            foreach ($team as $t){
                if ($t->getId() == $idTeam){
                    $gArray[] = $project;
                }
            }
        }

        return $this->render('timer/listGroup.html.twig', [
            'gArray' => $gArray,
            'idTeam' => $idTeam
        ]);
    }
}
