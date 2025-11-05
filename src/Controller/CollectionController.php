<?php

namespace App\Controller;

use App\Repository\GameCollectionRepository;
use App\Repository\ConsoleCollectionRepository;
use App\Repository\AccessoryCollectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/collection')]
#[IsGranted('ROLE_USER')]
class CollectionController extends AbstractController
{
    #[Route('/', name: 'app_collection_index', methods: ['GET'])]
    public function index(
        GameCollectionRepository $gameCollectionRepository,
        ConsoleCollectionRepository $consoleCollectionRepository,
        AccessoryCollectionRepository $accessoryCollectionRepository
    ): Response {
        $user = $this->getUser();

        return $this->render('collection/index.html.twig', [
            'game_collections' => $gameCollectionRepository->findByUserWithDetails($user->getId()),
            'console_collections' => $consoleCollectionRepository->findByUserWithDetails($user->getId()),
            'accessory_collections' => $accessoryCollectionRepository->findByUserWithDetails($user->getId()),
            'total_games' => $gameCollectionRepository->findTotalGamesForUser($user->getId()),
            'total_consoles' => $consoleCollectionRepository->findTotalConsolesForUser($user->getId()),
            'total_accessories' => $accessoryCollectionRepository->findTotalAccessoriesForUser($user->getId()),
        ]);
    }

    #[Route('/games', name: 'app_collection_games', methods: ['GET'])]
    public function games(GameCollectionRepository $repository): Response
    {
        return $this->render('collection/games.html.twig', [
            'collections' => $repository->findByUserWithDetails($this->getUser()->getId()),
        ]);
    }

    #[Route('/consoles', name: 'app_collection_consoles', methods: ['GET'])]
    public function consoles(ConsoleCollectionRepository $repository): Response
    {
        return $this->render('collection/consoles.html.twig', [
            'collections' => $repository->findByUserWithDetails($this->getUser()->getId()),
        ]);
    }

    #[Route('/accessories', name: 'app_collection_accessories', methods: ['GET'])]
    public function accessories(AccessoryCollectionRepository $repository): Response
    {
        return $this->render('collection/accessories.html.twig', [
            'collections' => $repository->findByUserWithDetails($this->getUser()->getId()),
        ]);
    }

    #[Route('/game/{id}/quantity', name: 'app_collection_game_update_quantity', methods: ['POST'])]
    public function updateGameQuantity(
        Request $request,
        GameCollection $gameCollection,
        EntityManagerInterface $entityManager
    ): Response {
        $this->validateCollectionOwnership($gameCollection);
        return $this->updateQuantity($request, $gameCollection, $entityManager);
    }

    #[Route('/console/{id}/quantity', name: 'app_collection_console_update_quantity', methods: ['POST'])]
    public function updateConsoleQuantity(
        Request $request,
        ConsoleCollection $consoleCollection,
        EntityManagerInterface $entityManager
    ): Response {
        $this->validateCollectionOwnership($consoleCollection);
        return $this->updateQuantity($request, $consoleCollection, $entityManager);
    }

    #[Route('/accessory/{id}/quantity', name: 'app_collection_accessory_update_quantity', methods: ['POST'])]
    public function updateAccessoryQuantity(
        Request $request,
        AccessoryCollection $accessoryCollection,
        EntityManagerInterface $entityManager
    ): Response {
        $this->validateCollectionOwnership($accessoryCollection);
        return $this->updateQuantity($request, $accessoryCollection, $entityManager);
    }

    private function validateCollectionOwnership($collection): void
    {
        if ($collection->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette collection.');
        }
    }

    private function updateQuantity(Request $request, $collection, EntityManagerInterface $entityManager): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);
        if ($quantity < 1) {
            throw new BadRequestHttpException('La quantité doit être supérieure à 0.');
        }

        $collection->setQuantity($quantity);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Quantité mise à jour avec succès.',
            'quantity' => $quantity
        ]);
    }
}
