<?php


namespace App\Transport;


use App\Contants\Status;
use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\TransportInterface;

class JobTransport implements TransportInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get a single job from the queue
     * Mark job with PROCESSING status
     * @return iterable
     */
    public function get(): iterable
    {
        /** @var Job $row */
        $row = $this->entityManager->getRepository('App:Job')->findOneBy(['status' => 'NEW']);

        if (empty($row)) {
            return [];
        }
        $envelope = new Envelope($row, []);
        /**
         * @var Job $job
         */
        $job = $envelope->getMessage();
        $job->setStatus(Status::PROCESSING);
        $this->entityManager->persist($job);
        $this->entityManager->flush();
        return [$envelope->with(new TransportMessageIdStamp($row->getId()))];
    }

    /**
     * @param Envelope $envelope
     */
    public function ack(Envelope $envelope): void
    {
        $stamp = $envelope->last(TransportMessageIdStamp::class);
        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new \LogicException('No TransportMessageIdStamp found on the Envelope.');
        }
    }

    /**
     * Mark job with ERROR status
     * @param Envelope $envelope
     */
    public function reject(Envelope $envelope): void
    {
        $stamp = $envelope->last(TransportMessageIdStamp::class);
        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new \LogicException('No TransportMessageIdStamp found on the Envelope.');
        }
        /**
         * @var Job $job
         */
        $job = $envelope->getMessage();
        $job->setStatus(Status::ERROR);
        $this->entityManager->persist($job);
        $this->entityManager->flush();
    }

    /**
     * @param Envelope $envelope
     * @return Envelope
     */
    public function send(Envelope $envelope): Envelope
    {
        return $envelope;
    }
}