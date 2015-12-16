<?php

namespace Baskin\HistoryBundle\Service;

use Doctrine\ORM\EntityManager;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Gedmo\Mapping\MappedEventSubscriber;
use Gedmo\Tool\Wrapper\AbstractWrapper;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Reverter
{
    /** @var EntityManager */
    private $em;

    /** @var RequestStack */
    private $requestStack;

    /** @var MappedEventSubscriber */
    private $eventSubscriber;

    private $versionParameter;

    public function __construct(
        RegistryInterface $registry,
        RequestStack $requestStack,
        MappedEventSubscriber $eventSubscriber,
        $versionParameter
    ) {
        $this->em = $registry->getManager();
        $this->requestStack = $requestStack;
        $this->eventSubscriber = $eventSubscriber;
        $this->versionParameter = $versionParameter;
    }

    public function revert($entity)
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request->query->has($this->versionParameter)) {
            $wrapped = AbstractWrapper::wrap($entity, $this->em);
            $meta = $wrapped->getMetadata();

            $config = $this->eventSubscriber->getExtensionMetadataFactory($this->em)->getExtensionMetadata($meta);

            if (!array_key_exists('loggable', $config) || $config['loggable'] !== true) {
                return array();
            }

            $logEntryClass = 'Gedmo\\Loggable\\Entity\\LogEntry';
            if (array_key_exists('logEntryClass', $config) && !empty($config['logEntryClass'])) {
                $logEntryClass = $config['logEntryClass'];
            }

            /** @var LogEntryRepository $logEntryRepo */
            $logEntryRepo = $this->em->getRepository($logEntryClass);
            $logEntryRepo->revert($entity, $request->query->get($this->versionParameter));
        }
    }

}
