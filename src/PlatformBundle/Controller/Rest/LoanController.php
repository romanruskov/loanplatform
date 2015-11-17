<?php

namespace PlatformBundle\Controller\Rest;

use PlatformBundle\Form\Type\LoanSearchType;
use PlatformBundle\Model\Search;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LoanController extends FOSRestController
{
    /**
     * Search for a loan.
     *
     * @Annotations\Post("/loan/search")
     * @Annotations\RequestParam(name="query", default="", description="Search query.")
     * @Annotations\View()
     *
     * @param Request $request the request object
     *
     * @return array
     */
    public function postLoanSearchAction(Request $request){
        if(!is_object($this->getUser())){
            throw new AccessDeniedHttpException('Not Authenticated');
        }

        $search = new Search();
        $loanSearchForm = $this->createForm(new LoanSearchType(), $search);
        $loanSearchForm->handleRequest($request);

        $repositoryManager = $this->container->get('fos_elastica.manager');
        $repository = $repositoryManager->getRepository('PlatformBundle:Loan');
        $loans = $repository->search($search->getQuery());

        return array('result' => $loans);
    }
}