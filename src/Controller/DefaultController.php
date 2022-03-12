<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * Show homepage
     *
     * @return Response
     *
     * @Route("/", name="homepage")
     */
    public function showHomepage() : Response
    {
        return $this->render('default/index.html.twig');
    }
}
