<?php

namespace Ext\DirectBundle\Response;

use Doctrine\ORM\AbstractQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Ext\DirectBundle\Event\DirectEvents;
use Ext\DirectBundle\Event\ResponseEvent;

/**
 * @author Semyon Velichko <semyon@velichko.net>
 */
class Response implements ResponseInterface
{

    /**
     * @var ResponseFactory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var string
     */
    protected $success;

    /**
     * @var int
     */
    protected $total;

    /**
     * @param ResponseFactory $factory
     * @return $this
     */
    public function setFactory(ResponseFactory $factory)
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * @return \Ext\DirectBundle\Response\ResponseFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setContent($data)
    {
        $this->setData($data);
        return $this;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return array
     */
    public function extract()
    {
        return $this->formatResponse($this->data);
    }

    /**
     * @param array $root
     * @return array
     */
    public function formatResponse(array $root)
    {
        $data = array();
        
        $Rule = $this->getFactory()->getResolver()->getCurrentRule();
        
        if($Rule->getReaderParam('root'))
        {
            $data[$Rule->getReaderParam('root')] = $root;
        } else {
            $data = $root;
        }
        
        if(is_bool($this->success))
            $data[$Rule->getReaderParam('successProperty')] = $this->success;
        
        if($this->total)
            $data[$Rule->getReaderParam('totalProperty')] = $this->total;
        
        return $data;
    }

    /**
     * @param $success
     * @return $this
     */
    public function setSuccess($success)
    {
        $this->success = (bool)$success;
        return $this;
    }

    /**
     * @param $total
     * @return $this
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @param EventSubscriberInterface $subscriber
     * @return $this
     */
    public function addEventSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->getFactory()->getEventDispatcher()->addSubscriber($subscriber);
        return $this;
    }

    /**
     * @param $name
     * @param $listener
     * @param int $weight
     * @return $this
     */
    public function addEventListener($name, $listener, $weight = 0)
    {
        $this->getFactory()->getEventDispatcher()->addListener($name, $listener, $weight);
        return $this;
    }
}
