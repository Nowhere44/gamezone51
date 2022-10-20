<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SphynxController extends AbstractController
{
    #[Route('/sphynx', name: 'app_sphynx')]
    public function index(): Response
    {
        return $this->render('sphynx/index.html.twig', [
            'controller_name' => 'SphynxController',
        ]);
    }
}
