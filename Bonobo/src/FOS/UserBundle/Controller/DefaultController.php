<?php

namespace FOS\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FOSUserBundle:Default:index.html.twig');
    }
}
