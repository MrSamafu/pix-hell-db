<?php

namespace App\Controller;
use App\Repository\GameRepository;
use App\Repository\ConsoleRepository;
use App\Repository\AccessoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[IsGranted('ROLE_USER')]
    public function index(
        GameRepository $gameRepository,
        ConsoleRepository $consoleRepository,
        AccessoryRepository $accessoryRepository
    ): Response {
        return $this->render('home/index.html.twig', [
            'latest_games' => $gameRepository->findLatestGames(5),
            'latest_consoles' => $consoleRepository->findLatestConsoles(5),
            'latest_accessories' => $accessoryRepository->findLatestAccessories(5),
        ]);
    }
}

