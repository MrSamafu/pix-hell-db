<?php

namespace App\Controller;

use App\Entity\Console;
use App\Entity\ConsoleCollection;
use App\Form\ConsoleType;
use App\Repository\ConsoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console')]
class ConsoleController extends AbstractController
{
    #[Route('/', name: 'app_console_index', methods: ['GET'])]
    public function index(Request $request, ConsoleRepository $consoleRepository): Response
    {
        // Récupération des critères de recherche et filtres
        $criteria = [
            'search' => $request->query->get('search'),
            'manufacturer' => $request->query->get('manufacturer'),
            'generation' => $request->query->get('generation'),
            'year' => $request->query->get('year'),
            'letter' => $request->query->get('letter'),
        ];

        // Recherche avec filtres
        $consoles = $consoleRepository->findBySearchAndFilters(array_filter($criteria));

        // Données pour les filtres
        $manufacturers = $consoleRepository->findAvailableManufacturers();
        $generations = $consoleRepository->findAvailableGenerations();
        $years = $consoleRepository->findAvailableYears();

        // Alphabet pour la recherche alphabétique
        $alphabet = array_merge(['0-9'], range('A', 'Z'));

        return $this->render('console/index.html.twig', [
            'consoles' => $consoles,
            'manufacturers' => $manufacturers,
            'generations' => $generations,
            'years' => $years,
            'alphabet' => $alphabet,
            'currentFilters' => $criteria,
        ]);
    }

    #[Route('/new', name: 'app_console_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $console = new Console();
        $console->setCreator($this->getUser());

        $form = $this->createForm(ConsoleType::class, $console);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // set addedAt si non renseigné
            if (!$console->getAddedAt()) {
                $console->setAddedAt(new \DateTimeImmutable());
            }

            // l'image est une URL fournie par l'utilisateur via le champ 'image' (TextType)
            // le setter $console->setImage() sera alimenté automatiquement par le formulaire

            $entityManager->persist($console);
            $entityManager->flush();

            $this->addFlash('success', 'La console a été créée avec succès.');
            return $this->redirectToRoute('app_console_index');
        }

        return $this->render('console/new.html.twig', [
            'console' => $console,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_console_show', methods: ['GET'])]
    public function show(Console $console): Response
    {
        $this->denyAccessUnlessGranted('view', $console);

        return $this->render('console/show.html.twig', [
            'console' => $console,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_console_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Console $console, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('edit', $console);

        $form = $this->createForm(ConsoleType::class, $console);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$console->getAddedAt()) {
                $console->setAddedAt(new \DateTimeImmutable());
            }

            // l'image est une URL : le form a déjà mis à jour $console->image

            $entityManager->flush();

            $this->addFlash('success', 'La console a été modifiée avec succès.');
            return $this->redirectToRoute('app_console_show', ['id' => $console->getId()]);
        }

        return $this->render('console/edit.html.twig', [
            'console' => $console,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_console_delete', methods: ['POST'])]
    public function delete(Request $request, Console $console, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('delete', $console);

        if ($this->isCsrfTokenValid('delete'.$console->getId(), $request->request->get('_token'))) {
            $entityManager->remove($console);
            $entityManager->flush();
            $this->addFlash('success', 'La console a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_console_index');
    }

    #[Route('/{id}/add-to-collection', name: 'app_console_add_to_collection', methods: ['POST'])]
    public function addToCollection(Request $request, Console $console, EntityManagerInterface $entityManager): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        $consoleCollection = new ConsoleCollection();
        $consoleCollection->setUser($this->getUser());
        $consoleCollection->setConsole($console);
        $consoleCollection->setQuantity($quantity);

        $entityManager->persist($consoleCollection);
        $entityManager->flush();

        $this->addFlash('success', 'La console a été ajoutée à votre collection.');
        return $this->redirectToRoute('app_console_show', ['id' => $console->getId()]);
    }

    #[Route('/search', name: 'app_console_search', methods: ['GET'])]
    public function search(Request $request, ConsoleRepository $consoleRepository): Response
    {
        $query = $request->query->get('q');
        $generation = $request->query->get('generation');

        $consoles = [];
        if ($query) {
            $consoles = $consoleRepository->findBySearch($query);
        } elseif ($generation) {
            $consoles = $consoleRepository->findByGeneration((int)$generation);
        }

        return $this->render('console/search.html.twig', [
            'consoles' => $consoles,
            'query' => $query,
            'generation' => $generation,
        ]);
    }
}
