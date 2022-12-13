<?php

namespace BiberLtd\Bundle\BaseEntityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BaseEntityBundle:Default:index.html.twig');
    }
}
