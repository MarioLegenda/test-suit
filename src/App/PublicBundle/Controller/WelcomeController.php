<?php

namespace App\PublicBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;

class WelcomeController extends ContainerAware
{
    public function welcomeAction() {
        $templating = $this->container->get('templating');

        return $templating->renderResponse('AppPublicBundle:Welcome:welcome.html.twig');
    }
}
