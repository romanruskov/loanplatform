<?php

namespace PlatformBundle\Controller;

use PlatformBundle\Form\Type\InvestorType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class InvestorController extends Controller
{
    public function profileAction(Request $request)
    {
        $investor = $this->getUser();
        $investorForm = $this->createForm(new InvestorType(), $investor, array(
            'action' => $this->generateUrl('platform_investor_profile_page'),
            'method' => 'POST'
        ));

        if ($request->isMethod('POST')) {
            $investorForm->handleRequest($request);

            if ($investorForm->isValid() && $investorForm->isSubmitted()) {
                $avatar = $investor->getAvatar();

                $em = $this->getDoctrine()->getManager();
                $em->persist($investor);
                $em->persist($avatar);
                $em->flush();

                if ($investor->getAvatarFile()) {
                    $msg = serialize(array(
                        'PlatformBundle:Avatar',
                        $avatar->getId(),
                        'thumb_100x100',
                        'img/avatar/'
                    ));
                    $this->get('old_sound_rabbit_mq.generate_thumbnail_producer')->publish($msg);
                }

                $this->addFlash(
                    'notice',
                    $this->get('translator')->trans('investor.profile.saved', array(), 'PlatformBundle')
                );
            }
        }

        return $this->render('PlatformBundle:Investor:profile.html.twig', array(
            'nav_page' => 'platform_investor_profile',
            'form' => $investorForm->createView()
        ));
    }
}
