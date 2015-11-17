<?php

namespace PlatformBundle\Controller;

use PlatformBundle\Form\Type\InvestmentType;
use PlatformBundle\Form\Type\LoanSearchType;
use PlatformBundle\Entity\Investment;
use PlatformBundle\Model\Search;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

class LoanController extends Controller
{
    public function listAction(Request $request)
    {
        $investment = new Investment();
        $investmentForm = $this->createForm(new InvestmentType(), $investment, array(
            'action' => $this->generateUrl('platform_loan_list_page'),
            'method' => 'POST'
        ));

        $search = new Search();
        $loanSearchForm = $this->createForm(new LoanSearchType(), $search, array(
            'action' => $this->generateUrl('platform_api_post_loan_search'),
            'method' => 'POST'
        ));

        if ($request->isMethod('POST')) {

            $investmentForm->handleRequest($request);

            if ($investmentForm->isValid() && $investmentForm->isSubmitted()) {
                $em = $this->getDoctrine()->getManager();
                $translator = $this->get('translator');

                $loan = $em->getRepository('PlatformBundle:Loan')->findOneById($investment->getPlainLoanId());
                $investor = $this->getUser();

                if (!$loan) {
                    $investmentForm->get('plainLoanId')->addError(new FormError(
                        $translator->trans('loan.list.select_a_loan', array(), 'PlatformBundle')));
                } else {
                    $investmentAmount = $investment->getAmount();
                    $loanAvailableForInvestments = $loan->getAvailableForInvestments();
                    $investorAvailableForInvestments = $investor->getAvailableForInvestments();

                    if ($investmentAmount > $investorAvailableForInvestments || $investmentAmount > $loanAvailableForInvestments) {
                        $investmentForm->get('amount')->addError(new FormError(
                            $translator->trans('loan.list.amount_not_available', array(), 'PlatformBundle')));
                    } else {
                        $investment->setLoan($loan);
                        $investment->setInvestor($this->getUser());

                        $loan->setAvailableForInvestments($loanAvailableForInvestments - $investmentAmount);
                        $investor->setAvailableForInvestments($investorAvailableForInvestments - $investmentAmount);

                        $em->persist($investment);
                        $em->flush();

                        return $this->redirectToRoute('platform_investment_list_page');
                    }
                }
            }
        }

        return $this->render('PlatformBundle:Loan:list.html.twig', array(
            'nav_page' => 'platform_loan_list',
            'investmentForm' => $investmentForm->createView(),
            'loanSearchForm' => $loanSearchForm->createView()
        ));
    }
}
