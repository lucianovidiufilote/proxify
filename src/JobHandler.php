<?php


namespace App;


use App\Contants\Status;
use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class JobHandler implements MessageHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * JobHandler constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @param Job $job
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function __invoke(Job $job)
    {
        try{
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', $job->getUrl());
            $job->setHttpCode($response->getStatusCode());
            $job->setStatus(Status::DONE);
        }
        catch (\Throwable $throwable){
            $job->setStatus(Status::ERROR);
            $this->logger->critical($throwable->getMessage());
            $this->logger->error($throwable->getTraceAsString());
        }
        $this->entityManager->persist($job);
        $this->entityManager->flush();
    }
}