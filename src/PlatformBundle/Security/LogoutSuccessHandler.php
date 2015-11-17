<?php

namespace PlatformBundle\Security;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;

class LogoutSuccessHandler extends ContainerAware implements LogoutSuccessHandlerInterface
{
    public function onLogoutSuccess(Request $request)
    {
        $target_url = $request->query->get('target_url')
            ? $request->query->get('target_url')
            : "/";
        $target_url .= (parse_url($target_url, PHP_URL_QUERY) ? '&' : '?') . 'locale=' . $request->getLocale();
        return new RedirectResponse($target_url);
    }
}
