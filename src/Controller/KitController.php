<?php

namespace App\Controller;

use App\Entity\GameKit;
use App\Entity\ConsoleKit;
use App\Entity\AccessoryKit;
use App\Repository\GameKitRepository;
use App\Repository\ConsoleKitRepository;
use App\Repository\AccessoryKitRepository;
use App\Repository\UserRepository;
use App\Repository\GameRepository;
use App\Repository\ConsoleRepository;
use App\Repository\AccessoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/kit')]
#[IsGranted('ROLE_USER')]
class KitController extends AbstractController
{
    #[Route('/', name: 'app_kit_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('kit/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/my', name: 'app_kit_my', methods: ['GET'])]
    public function myKit(
        GameKitRepository $gameKitRepository,
        ConsoleKitRepository $consoleKitRepository,
        AccessoryKitRepository $accessoryKitRepository
    ): Response {
        $user = $this->getUser();

        return $this->render('kit/my_kit.html.twig', [
            'game_kits' => $gameKitRepository->findByUserWithDetails($user->getId()),
            'console_kits' => $consoleKitRepository->findByUserWithDetails($user->getId()),
            'accessory_kits' => $accessoryKitRepository->findByUserWithDetails($user->getId()),
            'total_games' => $gameKitRepository->findTotalGamesForUser($user->getId()),
            'total_consoles' => $consoleKitRepository->findTotalConsolesForUser($user->getId()),
            'total_accessories' => $accessoryKitRepository->findTotalAccessoriesForUser($user->getId()),
        ]);
    }

    #[Route('/user/{id}', name: 'app_kit_user', methods: ['GET'])]
    public function userKit(
        int $id,
        UserRepository $userRepository,
        GameKitRepository $gameKitRepository,
        ConsoleKitRepository $consoleKitRepository,
        AccessoryKitRepository $accessoryKitRepository
    ): Response {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        return $this->render('kit/user_kit.html.twig', [
            'user' => $user,
            'game_kits' => $gameKitRepository->findByUserWithDetails($id),
            'console_kits' => $consoleKitRepository->findByUserWithDetails($id),
            'accessory_kits' => $accessoryKitRepository->findByUserWithDetails($id),
            'total_games' => $gameKitRepository->findTotalGamesForUser($id),
            'total_consoles' => $consoleKitRepository->findTotalConsolesForUser($id),
            'total_accessories' => $accessoryKitRepository->findTotalAccessoriesForUser($id),
        ]);
    }

    #[Route('/search', name: 'app_kit_search', methods: ['GET'])]
    public function search(
        Request $request,
        GameRepository $gameRepository,
        ConsoleRepository $consoleRepository,
        AccessoryRepository $accessoryRepository,
        GameKitRepository $gameKitRepository,
        ConsoleKitRepository $consoleKitRepository,
        AccessoryKitRepository $accessoryKitRepository
    ): Response {
        $query = $request->query->get('q', '');
        $type = $request->query->get('type', 'all');

        $results = ['games' => [], 'consoles' => [], 'accessories' => []];

        if ($query) {
            if ($type === 'all' || $type === 'game') {
                foreach ($gameRepository->searchByTitle($query) as $game) {
                    $holders = $gameKitRepository->findUsersWhoHave($game->getId());
                    if ($holders) {
                        $results['games'][] = ['item' => $game, 'holders' => $holders];
                    }
                }
            }

            if ($type === 'all' || $type === 'console') {
                foreach ($consoleRepository->searchByName($query) as $console) {
                    $holders = $consoleKitRepository->findUsersWhoHave($console->getId());
                    if ($holders) {
                        $results['consoles'][] = ['item' => $console, 'holders' => $holders];
                    }
                }
            }

            if ($type === 'all' || $type === 'accessory') {
                foreach ($accessoryRepository->searchByName($query) as $accessory) {
                    $holders = $accessoryKitRepository->findUsersWhoHave($accessory->getId());
                    if ($holders) {
                        $results['accessories'][] = ['item' => $accessory, 'holders' => $holders];
                    }
                }
            }
        }

        return $this->render('kit/search.html.twig', [
            'query' => $query,
            'type' => $type,
            'results' => $results,
        ]);
    }

    // --- Ajouter un jeu au kit ---
    #[Route('/game/add', name: 'app_kit_game_add', methods: ['POST'])]
    public function addGame(
        Request $request,
        GameRepository $gameRepository,
        GameKitRepository $gameKitRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $gameId = (int) $request->request->get('game_id');
        $quantity = max(1, (int) $request->request->get('quantity', 1));
        $note = $request->request->get('note', '');

        $user = $this->getUser();
        $game = $gameRepository->find($gameId);

        if (!$game) {
            return $this->json(['success' => false, 'message' => 'Jeu introuvable'], 404);
        }

        $existing = $gameKitRepository->findOneBy(['user' => $user, 'game' => $game]);
        if ($existing) {
            return $this->json(['success' => false, 'message' => 'Ce jeu est déjà dans votre kit'], 400);
        }

        $kit = (new GameKit())->setUser($user)->setGame($game)->setQuantity($quantity)->setNote($note);
        $em->persist($kit);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Jeu ajouté au kit']);
    }

    #[Route('/game/{id}/update', name: 'app_kit_game_update', methods: ['POST'])]
    public function updateGame(Request $request, GameKit $gameKit, EntityManagerInterface $em): JsonResponse
    {
        $this->validateKitOwnership($gameKit);
        $quantity = (int) $request->request->get('quantity');
        if ($quantity < 1) {
            return $this->json(['success' => false, 'message' => 'La quantité doit être au moins 1'], 400);
        }
        $gameKit->setQuantity($quantity)->setNote($request->request->get('note'));
        $em->flush();
        return $this->json(['success' => true, 'message' => 'Kit mis à jour']);
    }

    #[Route('/game/{id}/delete', name: 'app_kit_game_delete', methods: ['POST'])]
    public function deleteGame(GameKit $gameKit, EntityManagerInterface $em): JsonResponse
    {
        $this->validateKitOwnership($gameKit);
        $em->remove($gameKit);
        $em->flush();
        return $this->json(['success' => true, 'message' => 'Jeu retiré du kit']);
    }

    // --- Ajouter une console au kit ---
    #[Route('/console/add', name: 'app_kit_console_add', methods: ['POST'])]
    public function addConsole(
        Request $request,
        ConsoleRepository $consoleRepository,
        ConsoleKitRepository $consoleKitRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $consoleId = (int) $request->request->get('console_id');
        $quantity = max(1, (int) $request->request->get('quantity', 1));
        $note = $request->request->get('note', '');

        $user = $this->getUser();
        $console = $consoleRepository->find($consoleId);

        if (!$console) {
            return $this->json(['success' => false, 'message' => 'Console introuvable'], 404);
        }

        $existing = $consoleKitRepository->findOneBy(['user' => $user, 'console' => $console]);
        if ($existing) {
            return $this->json(['success' => false, 'message' => 'Cette console est déjà dans votre kit'], 400);
        }

        $kit = (new ConsoleKit())->setUser($user)->setConsole($console)->setQuantity($quantity)->setNote($note);
        $em->persist($kit);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Console ajoutée au kit']);
    }

    #[Route('/console/{id}/update', name: 'app_kit_console_update', methods: ['POST'])]
    public function updateConsole(Request $request, ConsoleKit $consoleKit, EntityManagerInterface $em): JsonResponse
    {
        $this->validateKitOwnership($consoleKit);
        $quantity = (int) $request->request->get('quantity');
        if ($quantity < 1) {
            return $this->json(['success' => false, 'message' => 'La quantité doit être au moins 1'], 400);
        }
        $consoleKit->setQuantity($quantity)->setNote($request->request->get('note'));
        $em->flush();
        return $this->json(['success' => true, 'message' => 'Kit mis à jour']);
    }

    #[Route('/console/{id}/delete', name: 'app_kit_console_delete', methods: ['POST'])]
    public function deleteConsole(ConsoleKit $consoleKit, EntityManagerInterface $em): JsonResponse
    {
        $this->validateKitOwnership($consoleKit);
        $em->remove($consoleKit);
        $em->flush();
        return $this->json(['success' => true, 'message' => 'Console retirée du kit']);
    }

    // --- Ajouter un accessoire au kit ---
    #[Route('/accessory/add', name: 'app_kit_accessory_add', methods: ['POST'])]
    public function addAccessory(
        Request $request,
        AccessoryRepository $accessoryRepository,
        AccessoryKitRepository $accessoryKitRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $accessoryId = (int) $request->request->get('accessory_id');
        $quantity = max(1, (int) $request->request->get('quantity', 1));
        $note = $request->request->get('note', '');

        $user = $this->getUser();
        $accessory = $accessoryRepository->find($accessoryId);

        if (!$accessory) {
            return $this->json(['success' => false, 'message' => 'Accessoire introuvable'], 404);
        }

        $existing = $accessoryKitRepository->findOneBy(['user' => $user, 'accessory' => $accessory]);
        if ($existing) {
            return $this->json(['success' => false, 'message' => 'Cet accessoire est déjà dans votre kit'], 400);
        }

        $kit = (new AccessoryKit())->setUser($user)->setAccessory($accessory)->setQuantity($quantity)->setNote($note);
        $em->persist($kit);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Accessoire ajouté au kit']);
    }

    #[Route('/accessory/{id}/update', name: 'app_kit_accessory_update', methods: ['POST'])]
    public function updateAccessory(Request $request, AccessoryKit $accessoryKit, EntityManagerInterface $em): JsonResponse
    {
        $this->validateKitOwnership($accessoryKit);
        $quantity = (int) $request->request->get('quantity');
        if ($quantity < 1) {
            return $this->json(['success' => false, 'message' => 'La quantité doit être au moins 1'], 400);
        }
        $accessoryKit->setQuantity($quantity)->setNote($request->request->get('note'));
        $em->flush();
        return $this->json(['success' => true, 'message' => 'Kit mis à jour']);
    }

    #[Route('/accessory/{id}/delete', name: 'app_kit_accessory_delete', methods: ['POST'])]
    public function deleteAccessory(AccessoryKit $accessoryKit, EntityManagerInterface $em): JsonResponse
    {
        $this->validateKitOwnership($accessoryKit);
        $em->remove($accessoryKit);
        $em->flush();
        return $this->json(['success' => true, 'message' => 'Accessoire retiré du kit']);
    }

    private function validateKitOwnership($kit): void
    {
        if ($kit->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce kit.');
        }
    }
}

