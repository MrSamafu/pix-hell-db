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
        // récupérer quelques éléments récents de chaque type
        $games = $gameRepository->findLatestGames(10);
        $consoles = $consoleRepository->findLatestConsoles(10);
        $accessories = $accessoryRepository->findLatestAccessories(10);

        $items = [];

        foreach ($games as $g) {
            $date = $g->getCreatedAt() ?? $g->getReleaseDate();
            if ($date) {
                $items[] = ['type' => 'game', 'entity' => $g, 'date' => $date];
            }
        }

        foreach ($consoles as $c) {
            $date = $c->getAddedAt() ?? $c->getReleaseDate();
            if ($date) {
                $items[] = ['type' => 'console', 'entity' => $c, 'date' => $date];
            }
        }

        foreach ($accessories as $a) {
            $date = $a->getCreatedAt() ?? $a->getReleaseDate();
            if ($date) {
                $items[] = ['type' => 'accessory', 'entity' => $a, 'date' => $date];
            }
        }

        // trier par date desc
        usort($items, function ($a, $b) {
            $ta = $a['date'] instanceof \DateTimeInterface ? $a['date']->getTimestamp() : 0;
            $tb = $b['date'] instanceof \DateTimeInterface ? $b['date']->getTimestamp() : 0;
            return $tb <=> $ta;
        });

        // garder les 10 premiers
        $latestItems = array_slice($items, 0, 10);

        return $this->render('home/index.html.twig', [
            'latest_items' => $latestItems,
        ]);
    }
}
