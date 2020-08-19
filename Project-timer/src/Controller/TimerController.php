<?php

namespace App\Controller;

use App\Entity\Timer;
use App\Form\TimerType;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\TimerRepository;
use Symfony\Component\Routing\Annotation\Route;

class TimerController extends AbstractController
{
    private $timerRepository;

    public function __construct(TimerRepository $timerRepository)
    {
        $this->timerRepository = $timerRepository;
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
        $timer->setDateStart(new \DateTime());
        //dump($timer->getDateStart());
        //die;

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
        $timer->setDateEnd(new \DateTime());
        $idTeam = $timer->getTeam();

        $entityManager->persist($timer);
        $entityManager->flush();
        $session->remove('tokenTimer');
        $this->addFlash('success', "Votre temps de travail a été enregistrer");

        return $this->redirectToRoute('timer', [
            'idTeam' => $idTeam,
        ]);
    }
}
