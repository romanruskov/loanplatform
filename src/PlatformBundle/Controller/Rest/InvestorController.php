<?php

namespace PlatformBundle\Controller\Rest;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class InvestorController extends FOSRestController
{
    /**
     * Returns current investor.
     *
     * @Annotations\Get("/investor")
     * @Annotations\View()
     *
     * @param Request $request the request object
     *
     * @return array
     */
    public function getInvestorAction(Request $request){
        $investor = $this->getUser();

        if (!is_object($investor)) {
            throw new AccessDeniedHttpException('Not Authenticated');
        }

        return array('result' => $investor);
    }
}
