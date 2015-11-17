<?php

namespace PlatformBundle\Controller;

use PlatformBundle\Form\Type\RegisterType;
use PlatformBundle\Form\Type\LoginType;
use PlatformBundle\Entity\Investor;
use PlatformBundle\Entity\Avatar;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthController extends Controller
{
    public function registerAction(Request $request)
    {
        // Anonymous users are technically authenticated
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('platform_loan_list_page'));
        }

        $investor = new Investor();
        $form = $this->createForm(new RegisterType(), $investor, array(
            'action' => $this->generateUrl('platform_auth_register_page'),
            'method' => 'POST'
        ));

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($investor, $investor->getPlainPassword());
            $investor->setPassword($password);

            $avatar = new Avatar();
            $avatar->setInvestor($investor);
            $investor->setAvatar($avatar);

            $em = $this->getDoctrine()->getManager();
            $em->persist($investor);
            $em->persist($avatar);
            $em->flush();

            $token = new UsernamePasswordToken($investor, null, 'platform_area', $investor->getRoles());
            $this->container->get('security.token_storage')->setToken($token);

            return $this->redirectToRoute('platform_loan_list_page');
        }

        return $this->render('PlatformBundle:Auth:register.html.twig', array(
            'nav_page' => 'platform_auth_register',
            'form' => $form->createView()
        ));
    }

    public function loginAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('platform_loan_list_page'));
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // used only when you provide bad credentials
        $lastUsername = $authenticationUtils->getLastUsername();

        $investor = new Investor();
        $investor->setUsername($lastUsername);

        $form = $this->createForm(new LoginType(), $investor, array(
            'action' => $this->generateUrl('platform_auth_login_check_page'),
            'method' => 'POST',
        ));

        return $this->render('PlatformBundle:Auth:login.html.twig', array(
            'nav_page' => 'platform_auth_login',
            'form' => $form->createView(),
            'error' => $error
        ));
    }

    /*
     * /auth/login/check and /auth/logout don't need a controller action
     * they are processed automatically by UserRepository class - see security.yml
     * platform_user_provider:
     *     entity:
     *         class: PlatformBundle:User
     *         property: username
     * which calls class InvestorRepository (EntityRepository) method loadUserByUsername
     * http://symfony.com/doc/current/cookbook/security/entity_provider.html#using-a-custom-query-to-load-the-user
    */
}
