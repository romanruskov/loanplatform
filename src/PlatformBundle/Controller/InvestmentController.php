<?php

namespace PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InvestmentController extends Controller
{
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository('PlatformBundle:Investment');

        $investments = $repository->findBy(
            array('investor' => $this->getUser()),
            array('id' => 'ASC')
        );

        return $this->render('PlatformBundle:Investment:list.html.twig', array(
            'nav_page' => 'platform_investment_list',
            'investments' => $investments
        ));
    }
}
