<?php

namespace App\AuthorizedBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;

class HomeController extends ContainerAware
{
    public function homeAction() {
        $templating = $this->container->get('templating');

        return $templating->renderResponse('AppAuthorizedBundle:Home:home.html.twig');
    }
}
