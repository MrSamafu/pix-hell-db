<?php

namespace App\Controller;

use App\Entity\GameCollection;
use App\Entity\ConsoleCollection;
use App\Entity\AccessoryCollection;
use App\Repository\GameCollectionRepository;
use App\Repository\ConsoleCollectionRepository;
use App\Repository\AccessoryCollectionRepository;
use App\Repository\UserRepository;
use App\Repository\GameRepository;
use App\Repository\ConsoleRepository;
use App\Repository\AccessoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/collection')]
#[IsGranted('ROLE_USER')]
class CollectionController extends AbstractController
{
    #[Route('/', name: 'app_collection_index', methods: ['GET'])]
    public function index(
        UserRepository $userRepository
    ): Response {
        return $this->render('collection/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/my', name: 'app_collection_my', methods: ['GET'])]
    public function myCollection(
        GameCollectionRepository $gameCollectionRepository,
        ConsoleCollectionRepository $consoleCollectionRepository,
        AccessoryCollectionRepository $accessoryCollectionRepository
    ): Response {
        $user = $this->getUser();

        return $this->render('collection/my_collection.html.twig', [
            'game_collections' => $gameCollectionRepository->findByUserWithDetails($user->getId()),
            'console_collections' => $consoleCollectionRepository->findByUserWithDetails($user->getId()),
            'accessory_collections' => $accessoryCollectionRepository->findByUserWithDetails($user->getId()),
            'total_games' => $gameCollectionRepository->findTotalGamesForUser($user->getId()),
            'total_consoles' => $consoleCollectionRepository->findTotalConsolesForUser($user->getId()),
            'total_accessories' => $accessoryCollectionRepository->findTotalAccessoriesForUser($user->getId()),
        ]);
    }

    #[Route('/user/{id}', name: 'app_collection_user', methods: ['GET'])]
    public function userCollection(
        int $id,
        UserRepository $userRepository,
        GameCollectionRepository $gameCollectionRepository,
        ConsoleCollectionRepository $consoleCollectionRepository,
        AccessoryCollectionRepository $accessoryCollectionRepository
    ): Response {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        return $this->render('collection/user_collection.html.twig', [
            'user' => $user,
            'game_collections' => $gameCollectionRepository->findByUserWithDetails($id),
            'console_collections' => $consoleCollectionRepository->findByUserWithDetails($id),
            'accessory_collections' => $accessoryCollectionRepository->findByUserWithDetails($id),
            'total_games' => $gameCollectionRepository->findTotalGamesForUser($id),
            'total_consoles' => $consoleCollectionRepository->findTotalConsolesForUser($id),
            'total_accessories' => $accessoryCollectionRepository->findTotalAccessoriesForUser($id),
        ]);
    }

    #[Route('/search', name: 'app_collection_search', methods: ['GET'])]
    public function search(
        Request $request,
        GameRepository $gameRepository,
        ConsoleRepository $consoleRepository,
        AccessoryRepository $accessoryRepository,
        GameCollectionRepository $gameCollectionRepository,
        ConsoleCollectionRepository $consoleCollectionRepository,
        AccessoryCollectionRepository $accessoryCollectionRepository
    ): Response {
        $query = $request->query->get('q', '');
        $type = $request->query->get('type', 'all');

        $results = [
            'games' => [],
            'consoles' => [],
            'accessories' => []
        ];

        if ($query) {
            if ($type === 'all' || $type === 'game') {
                $games = $gameRepository->searchByTitle($query);
                foreach ($games as $game) {
                    $owners = $gameCollectionRepository->findUsersWhoOwn($game->getId());
                    $results['games'][] = [
                        'item' => $game,
                        'owners' => $owners
                    ];
                }
            }

            if ($type === 'all' || $type === 'console') {
                $consoles = $consoleRepository->searchByName($query);
                foreach ($consoles as $console) {
                    $owners = $consoleCollectionRepository->findUsersWhoOwn($console->getId());
                    $results['consoles'][] = [
                        'item' => $console,
                        'owners' => $owners
                    ];
                }
            }

            if ($type === 'all' || $type === 'accessory') {
                $accessories = $accessoryRepository->searchByName($query);
                foreach ($accessories as $accessory) {
                    $owners = $accessoryCollectionRepository->findUsersWhoOwn($accessory->getId());
                    $results['accessories'][] = [
                        'item' => $accessory,
                        'owners' => $owners
                    ];
                }
            }
        }

        return $this->render('collection/search.html.twig', [
            'query' => $query,
            'type' => $type,
            'results' => $results
        ]);
    }

    // Gestion des jeux dans la collection
    #[Route('/game/{id}/update', name: 'app_collection_game_update', methods: ['POST'])]
    public function updateGameCollection(
        Request $request,
        GameCollection $gameCollection,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->validateCollectionOwnership($gameCollection);

        $quantity = (int) $request->request->get('quantity');
        $note = $request->request->get('note');

        if ($quantity < 1) {
            return $this->json(['success' => false, 'message' => 'La quantité doit être au moins 1'], 400);
        }

        $gameCollection->setQuantity($quantity);
        if ($note !== null) {
            $gameCollection->setNote($note);
        }

        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Collection mise à jour avec succès'
        ]);
    }

    #[Route('/game/{id}/delete', name: 'app_collection_game_delete', methods: ['POST'])]
    public function deleteGameCollection(
        GameCollection $gameCollection,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->validateCollectionOwnership($gameCollection);

        $entityManager->remove($gameCollection);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Jeu retiré de la collection'
        ]);
    }

    // Gestion des consoles dans la collection
    #[Route('/console/{id}/update', name: 'app_collection_console_update', methods: ['POST'])]
    public function updateConsoleCollection(
        Request $request,
        ConsoleCollection $consoleCollection,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->validateCollectionOwnership($consoleCollection);

        $quantity = (int) $request->request->get('quantity');
        $note = $request->request->get('note');

        if ($quantity < 1) {
            return $this->json(['success' => false, 'message' => 'La quantité doit être au moins 1'], 400);
        }

        $consoleCollection->setQuantity($quantity);
        if ($note !== null) {
            $consoleCollection->setNote($note);
        }

        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Collection mise à jour avec succès'
        ]);
    }

    #[Route('/console/{id}/delete', name: 'app_collection_console_delete', methods: ['POST'])]
    public function deleteConsoleCollection(
        ConsoleCollection $consoleCollection,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->validateCollectionOwnership($consoleCollection);

        $entityManager->remove($consoleCollection);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Console retirée de la collection'
        ]);
    }

    // Gestion des accessoires dans la collection
    #[Route('/accessory/{id}/update', name: 'app_collection_accessory_update', methods: ['POST'])]
    public function updateAccessoryCollection(
        Request $request,
        AccessoryCollection $accessoryCollection,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->validateCollectionOwnership($accessoryCollection);

        $quantity = (int) $request->request->get('quantity');
        $note = $request->request->get('note');

        if ($quantity < 1) {
            return $this->json(['success' => false, 'message' => 'La quantité doit être au moins 1'], 400);
        }

        $accessoryCollection->setQuantity($quantity);
        if ($note !== null) {
            $accessoryCollection->setNote($note);
        }

        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Collection mise à jour avec succès'
        ]);
    }

    #[Route('/accessory/{id}/delete', name: 'app_collection_accessory_delete', methods: ['POST'])]
    public function deleteAccessoryCollection(
        AccessoryCollection $accessoryCollection,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->validateCollectionOwnership($accessoryCollection);

        $entityManager->remove($accessoryCollection);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Accessoire retiré de la collection'
        ]);
    }

    private function validateCollectionOwnership($collection): void
    {
        if ($collection->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette collection.');
        }
    }
}
