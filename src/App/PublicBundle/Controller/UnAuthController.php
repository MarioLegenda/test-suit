<?php

namespace App\PublicBundle\Controller;

use App\PublicBundle\Models\InstallModel;
use App\PublicBundle\Helpers\InstallHelper;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UnAuthController extends ContainerAware
{
    public function authAction() {
        $doctrine = $this->container->get('doctrine');
        $request = $this->container->get('request');

        /**
        1. Check if database exists. If it doesn't, procedd immediately to installment
         */

        $em = $doctrine->getManager();
        $installHelper = new InstallHelper($em);

        if( ! $installHelper->isAppInstalled() OR ! $installHelper->doesAppHasAdmin()) {
            $templating = $this->container->get('templating');

            return $templating->renderResponse('AppPublicBundle:Login:uninstalled.html.twig');
        }

        $templating = $this->container->get('templating');

        return $templating->renderResponse('AppPublicBundle:Login:login.html.twig');
    }
} 