<?php

namespace Baskin\HistoryBundle\ParamConverter;

use Baskin\HistoryBundle\Service\Reverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Persistence\ManagerRegistry;

class HistoryParamConverter extends DoctrineParamConverter
{
    /**
     * @var Reverter
     */
    protected $reverter;

    /**
     * @param Reverter $reverter
     * @param ManagerRegistry $registry
     */
    public function __construct(Reverter $reverter, ManagerRegistry $registry)
    {
        $this->reverter = $reverter;
        parent::__construct($registry);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException       When unable to guess how to get a Doctrine instance from the request information
     * @throws NotFoundHttpException When object not found
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $class = $configuration->getClass();
        $options = $this->getOptions($configuration);

        if (null === $request->attributes->get($name, false)) {
            $configuration->setIsOptional(true);
        }

        // find by identifier?
        if (false === $object = $this->find($class, $request, $options, $name)) {
            // find by criteria
            if (false === $object = $this->findOneBy($class, $request, $options)) {
                if ($configuration->isOptional()) {
                    $object = null;
                } else {
                    throw new \LogicException(
                        'Unable to guess how to get a Doctrine instance from the request information.'
                    );
                }
            }
        }

        if (null === $object && false === $configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }
        $this->reverter->revert($object);
        $request->attributes->set($name, $object);

        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        $options = $configuration->getOptions();

        if (isset($options['revertable']) && !filter_var($options['revertable'], FILTER_VALIDATE_BOOLEAN)) {
            return false;
        }

        return parent::supports($configuration);
    }
}
