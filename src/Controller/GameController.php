<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameCollection;
use App\Form\GameType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/game')]
class GameController extends AbstractController
{
    #[Route('/', name: 'app_game_index', methods: ['GET'])]
    public function index(GameRepository $gameRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('game/index.html.twig', [
            'games' => $gameRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_game_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $game = new Game();
        $game->setCreator($this->getUser());

        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // createdAt par défaut si non renseigné
            if (!$game->getCreatedAt()) {
                $game->setCreatedAt(new \DateTimeImmutable());
            }

            // gestion de l'image uploadée
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/games';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                try {
                    $imageFile->move($uploadDir, $newFilename);
                    $game->setImage('uploads/games/'.$newFilename);
                } catch (FileException $e) {
                    // on ignore l'exception ici, mais on peut logger
                }
            }

            $entityManager->persist($game);
            $entityManager->flush();

            $this->addFlash('success', 'Le jeu a été créé avec succès.');
            return $this->redirectToRoute('app_game_index');
        }

        return $this->render('game/new.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_game_show', methods: ['GET'])]
    public function show(Game $game): Response
    {
        $this->denyAccessUnlessGranted('view', $game);

        return $this->render('game/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_game_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Game $game, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('edit', $game);

        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$game->getCreatedAt()) {
                $game->setCreatedAt(new \DateTimeImmutable());
            }

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/games';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                try {
                    $imageFile->move($uploadDir, $newFilename);
                    $game->setImage('uploads/games/'.$newFilename);
                } catch (FileException $e) {
                    // logger ou gérer l'erreur
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le jeu a été modifié avec succès.');
            return $this->redirectToRoute('app_game_show', ['id' => $game->getId()]);
        }

        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_game_delete', methods: ['POST'])]
    public function delete(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('delete', $game);

        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
            $this->addFlash('success', 'Le jeu a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_game_index');
    }

    #[Route('/{id}/add-to-collection', name: 'app_game_add_to_collection', methods: ['POST'])]
    public function addToCollection(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $quantity = (int) $request->request->get('quantity', 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        $gameCollection = new GameCollection();
        $gameCollection->setUser($this->getUser());
        $gameCollection->setGame($game);
        $gameCollection->setQuantity($quantity);

        $entityManager->persist($gameCollection);
        $entityManager->flush();

        $this->addFlash('success', 'Le jeu a été ajouté à votre collection.');
        return $this->redirectToRoute('app_game_show', ['id' => $game->getId()]);
    }

    #[Route('/search', name: 'app_game_search', methods: ['GET'])]
    public function search(Request $request, GameRepository $gameRepository): Response
    {
        $query = $request->query->get('q');
        $games = $query ? $gameRepository->findBySearch($query) : [];

        return $this->render('game/search.html.twig', [
            'games' => $games,
            'query' => $query,
        ]);
    }
}
