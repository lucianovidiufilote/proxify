<?php


namespace App\Transport;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class JobTransportFactory implements TransportFactoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {

        $this->entityManager = $entityManager;
    }

    /**
     * @param string $dsn
     * @param array $options
     * @param SerializerInterface $serializer
     * @return TransportInterface
     */
    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        return new JobTransport($this->entityManager);
    }

    /**
     * @param string $dsn
     * @param array $options
     * @return bool
     */
    public function supports(string $dsn, array $options): bool
    {
        return 0 === strpos($dsn, 'job_transport://');
    }
}