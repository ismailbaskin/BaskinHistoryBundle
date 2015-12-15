<?php

namespace Baskin\HistoryBundle\Service\Twig;

use Baskin\HistoryBundle\Service\Stringifier;
use Doctrine\ORM\EntityManager;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Gedmo\Mapping\MappedEventSubscriber;
use Gedmo\Tool\Wrapper\AbstractWrapper;
use Symfony\Bridge\Doctrine\RegistryInterface;

class HistoryExtension extends \Twig_Extension
{
    /** @var EntityManager */
    private $em;

    /** @var \Twig_Environment */
    private $twig;

    /** @var MappedEventSubscriber */
    private $eventSubscriber;

    private $template;

    public function __construct(
        RegistryInterface $registry,
        \Twig_Environment $twig,
        MappedEventSubscriber $eventSubscriber,
        $template
    ) {
        $this->em = $registry->getManager();
        $this->twig = $twig;
        $this->eventSubscriber = $eventSubscriber;
        $this->template = $template;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getLogs', array($this, 'getLogs'), array('is_safe' => array('html'))),
        );
    }

    public function getLogs($entity)
    {
        if (!is_object($entity)) {
            return '';
        }

        return $this->twig->render($this->template, array('logEntities' => $this->logsFromEntity($entity)));
    }

    /**
     * @param $entity
     * @return array
     */
    private function logsFromEntity($entity)
    {

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

        $stringifier = new Stringifier();
        /** @var LogEntryRepository $repo */
        $repo = $this->em->getRepository($logEntryClass);
        /** @var AbstractLogEntry[] $logs */
        $logs = array_reverse($repo->getLogEntries($entity));
        $logsArray = array();
        $logLastData = array();
        if (is_array($logs)) {
            foreach ($logs as $log) {
                if (!$log instanceof AbstractLogEntry || !is_array($log->getData())) {
                    continue;
                }
                $logRow = new \stdClass();
                $logRow->id = $log->getId();
                $logRow->loggedAt = $log->getLoggedAt();
                $logRow->username = $log->getUsername();
                $logRow->action = $log->getAction();
                $logRow->data = array();
                foreach ($log->getData() as $name => $value) {
                    $dataRow = array('name' => $name, 'old' => null, 'new' => $stringifier->getString($value));
                    if (isset($logLastData[$name])) {
                        $dataRow['old'] = $stringifier->getString($logLastData[$name]);
                    }
                    $logLastData[$name] = $value;
                    $logRow->data[] = (object)$dataRow;
                }
                $logsArray[] = $logRow;
            }
        } else {
            $logsArray = array();
        }

        return array_reverse($logsArray);
    }

    public function getName()
    {
        return 'history_extension';
    }
}
