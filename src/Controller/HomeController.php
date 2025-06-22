<?php

namespace App\Controller;

use App\Repository\MontreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(MontreRepository $montreRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'featured_montres' => $montreRepository->findBy([], ['id' => 'DESC'], 3),
        ]);
    }

    #[Route('/catalogue', name: 'app_catalogue')]
    public function catalogue(MontreRepository $montreRepository): Response
    {
        return $this->render('home/catalogue.html.twig', [
            'montres' => $montreRepository->findAll(),
        ]);
    }
}
