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

#[Route('/admin/kits')]
#[IsGranted('ROLE_ADMIN')]
class AdminKitController extends AbstractController
{
    /**
     * Liste de tous les utilisateurs avec leurs compteurs de kit.
     */
    #[Route('', name: 'app_admin_kit_users', methods: ['GET'])]
    public function kitUsers(
        UserRepository $userRepository,
        GameKitRepository $gameKitRepository,
        ConsoleKitRepository $consoleKitRepository,
        AccessoryKitRepository $accessoryKitRepository
    ): Response {
        $users = $userRepository->findAll();

        $stats = [];
        foreach ($users as $user) {
            $stats[$user->getId()] = [
                'games'       => $gameKitRepository->findTotalGamesForUser($user->getId()),
                'consoles'    => $consoleKitRepository->findTotalConsolesForUser($user->getId()),
                'accessories' => $accessoryKitRepository->findTotalAccessoriesForUser($user->getId()),
            ];
        }

        return $this->render('admin/kit_users.html.twig', [
            'users' => $users,
            'stats' => $stats,
        ]);
    }

    /**
     * Vue admin du kit d'un utilisateur avec formulaires d'ajout/modification/suppression.
     */
    #[Route('/user/{id}', name: 'app_admin_kit_user', methods: ['GET'])]
    public function kitUser(
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

        return $this->render('admin/kit_user.html.twig', [
            'user'             => $user,
            'game_kits'        => $gameKitRepository->findByUserWithDetails($id),
            'console_kits'     => $consoleKitRepository->findByUserWithDetails($id),
            'accessory_kits'   => $accessoryKitRepository->findByUserWithDetails($id),
            'total_games'      => $gameKitRepository->findTotalGamesForUser($id),
            'total_consoles'   => $consoleKitRepository->findTotalConsolesForUser($id),
            'total_accessories'=> $accessoryKitRepository->findTotalAccessoriesForUser($id),
        ]);
    }

    // ---- Jeux ----

    #[Route('/user/{id}/game/add', name: 'app_admin_kit_game_add', methods: ['POST'])]
    public function adminAddGame(
        int $id,
        Request $request,
        UserRepository $userRepository,
        GameRepository $gameRepository,
        GameKitRepository $gameKitRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Utilisateur introuvable'], 404);
        }

        $gameId   = (int) $request->request->get('game_id');
        $quantity = max(1, (int) $request->request->get('quantity', 1));
        $note     = $request->request->get('note', '');

        $game = $gameRepository->find($gameId);
        if (!$game) {
            return $this->json(['success' => false, 'message' => 'Jeu introuvable'], 404);
        }

        $existing = $gameKitRepository->findOneBy(['user' => $user, 'game' => $game]);
        if ($existing) {
            return $this->json(['success' => false, 'message' => 'Ce jeu est déjà dans le kit de cet utilisateur'], 400);
        }

        $kit = (new GameKit())->setUser($user)->setGame($game)->setQuantity($quantity)->setNote($note);
        $em->persist($kit);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Jeu ajouté au kit']);
    }

    #[Route('/game/{id}/update', name: 'app_admin_kit_game_update', methods: ['POST'])]
    public function adminUpdateGame(Request $request, GameKit $gameKit, EntityManagerInterface $em): JsonResponse
    {
        $quantity = (int) $request->request->get('quantity');
        if ($quantity < 1) {
            return $this->json(['success' => false, 'message' => 'La quantité doit être au moins 1'], 400);
        }
        $gameKit->setQuantity($quantity)->setNote($request->request->get('note'));
        $em->flush();
        return $this->json(['success' => true, 'message' => 'Kit mis à jour']);
    }

    #[Route('/game/{id}/delete', name: 'app_admin_kit_game_delete', methods: ['POST'])]
    public function adminDeleteGame(GameKit $gameKit, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($gameKit);
        $em->flush();
        return $this->json(['success' => true, 'message' => 'Jeu retiré du kit']);
    }

    // ---- Consoles ----

    #[Route('/user/{id}/console/add', name: 'app_admin_kit_console_add', methods: ['POST'])]
    public function adminAddConsole(
        int $id,
        Request $request,
        UserRepository $userRepository,
        ConsoleRepository $consoleRepository,
        ConsoleKitRepository $consoleKitRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Utilisateur introuvable'], 404);
        }

        $consoleId = (int) $request->request->get('console_id');
        $quantity  = max(1, (int) $request->request->get('quantity', 1));
        $note      = $request->request->get('note', '');

        $console = $consoleRepository->find($consoleId);
        if (!$console) {
            return $this->json(['success' => false, 'message' => 'Console introuvable'], 404);
        }

        $existing = $consoleKitRepository->findOneBy(['user' => $user, 'console' => $console]);
        if ($existing) {
            return $this->json(['success' => false, 'message' => 'Cette console est déjà dans le kit de cet utilisateur'], 400);
        }

        $kit = (new ConsoleKit())->setUser($user)->setConsole($console)->setQuantity($quantity)->setNote($note);
        $em->persist($kit);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Console ajoutée au kit']);
    }

    #[Route('/console/{id}/update', name: 'app_admin_kit_console_update', methods: ['POST'])]
    public function adminUpdateConsole(Request $request, ConsoleKit $consoleKit, EntityManagerInterface $em): JsonResponse
    {
        $quantity = (int) $request->request->get('quantity');
        if ($quantity < 1) {
            return $this->json(['success' => false, 'message' => 'La quantité doit être au moins 1'], 400);
        }
        $consoleKit->setQuantity($quantity)->setNote($request->request->get('note'));
        $em->flush();
        return $this->json(['success' => true, 'message' => 'Kit mis à jour']);
    }

    #[Route('/console/{id}/delete', name: 'app_admin_kit_console_delete', methods: ['POST'])]
    public function adminDeleteConsole(ConsoleKit $consoleKit, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($consoleKit);
        $em->flush();
        return $this->json(['success' => true, 'message' => 'Console retirée du kit']);
    }

    // ---- Accessoires ----

    #[Route('/user/{id}/accessory/add', name: 'app_admin_kit_accessory_add', methods: ['POST'])]
    public function adminAddAccessory(
        int $id,
        Request $request,
        UserRepository $userRepository,
        AccessoryRepository $accessoryRepository,
        AccessoryKitRepository $accessoryKitRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Utilisateur introuvable'], 404);
        }

        $accessoryId = (int) $request->request->get('accessory_id');
        $quantity    = max(1, (int) $request->request->get('quantity', 1));
        $note        = $request->request->get('note', '');

        $accessory = $accessoryRepository->find($accessoryId);
        if (!$accessory) {
            return $this->json(['success' => false, 'message' => 'Accessoire introuvable'], 404);
        }

        $existing = $accessoryKitRepository->findOneBy(['user' => $user, 'accessory' => $accessory]);
        if ($existing) {
            return $this->json(['success' => false, 'message' => 'Cet accessoire est déjà dans le kit de cet utilisateur'], 400);
        }

        $kit = (new AccessoryKit())->setUser($user)->setAccessory($accessory)->setQuantity($quantity)->setNote($note);
        $em->persist($kit);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Accessoire ajouté au kit']);
    }

    #[Route('/accessory/{id}/update', name: 'app_admin_kit_accessory_update', methods: ['POST'])]
    public function adminUpdateAccessory(Request $request, AccessoryKit $accessoryKit, EntityManagerInterface $em): JsonResponse
    {
        $quantity = (int) $request->request->get('quantity');
        if ($quantity < 1) {
            return $this->json(['success' => false, 'message' => 'La quantité doit être au moins 1'], 400);
        }
        $accessoryKit->setQuantity($quantity)->setNote($request->request->get('note'));
        $em->flush();
        return $this->json(['success' => true, 'message' => 'Kit mis à jour']);
    }

    #[Route('/accessory/{id}/delete', name: 'app_admin_kit_accessory_delete', methods: ['POST'])]
    public function adminDeleteAccessory(AccessoryKit $accessoryKit, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($accessoryKit);
        $em->flush();
        return $this->json(['success' => true, 'message' => 'Accessoire retiré du kit']);
    }
}

