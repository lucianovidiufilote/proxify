<?php


namespace App\Controller;


use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @return Response
     */
    public function indexAction()
    {
        return new Response('Hi!');
    }

    /**
     * View the status of the jobs
     * @Route("/status", name="status")
     *
     * @param Request $request
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function statusAction(Request $request, LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $jobs = $entityManager->getRepository('App:Job')->findBy([], ['id' => 'DESC']);
        return new Response($this->renderView('jobs.html.twig', ['jobs' => $jobs]));
    }

    /**
     * Generate jobs using url and times parameters
     * @Route(name="generate", path="generate")
     * @param Request $request
     * @param MessageBusInterface $bus
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function generate(Request $request, EntityManagerInterface $entityManager)
    {
        if (!$request->query->has('url')) {
            return new Response('Please send url param: ?url=http://google.com');
        }
        $url = $request->query->get('url');
        $times = 1;
        if ($request->query->has('times')) {
            $times = $request->query->get('times');
        }

        try {
            $i = 1;
            while ($i <= $times) {
                $job = new Job();
                $job->setUrl($url);
                $entityManager->persist($job);
                $entityManager->flush();
                $i++;
            }

            return new Response($this->renderView('success.html.twig',
                ['message' => 'Successfully generated ' . $times . ' jobs with url: ' . $url]));
        } catch (\Throwable $throwable) {
            $this->logger->critical($throwable->getMessage());
            $this->logger->error($throwable->getTraceAsString());
            return new Response($this->renderView('fail.html.twig',
                ['message' => 'Failed to generate ' . $times . ' jobs with url: ' . $url . '. Error message: ' . $throwable->getMessage() . '. Trace: ' . $throwable->getTraceAsString()]));
        }


    }
}