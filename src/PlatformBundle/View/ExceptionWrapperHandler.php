<?php

namespace PlatformBundle\View;

use FOS\RestBundle\View\ExceptionWrapperHandlerInterface;
use Psr\Log\LoggerInterface;

class ExceptionWrapperHandler implements ExceptionWrapperHandlerInterface {

    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function wrap($data)
    {
        if ($this->logger) {
            $this->logger->warning('Failed to make an API call', $data['exception']->toArray());
        }
        return array(
            'error' => true,
            'message' => $data['message']
        );
    }
}
