<?php

namespace App\Controller;

use App\Entity\Accessory;
use App\Entity\AccessoryCollection;
use App\Entity\AccessoryKit;
use App\Form\AccessoryType;
use App\Repository\AccessoryCollectionRepository;
use App\Repository\AccessoryKitRepository;
use App\Repository\AccessoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accessory')]
class AccessoryController extends AbstractController
{
    #[Route('/', name: 'app_accessory_index', methods: ['GET'])]
    public function index(Request $request, AccessoryRepository $accessoryRepository): Response
    {
        // Récupération des critères de recherche et filtres
        $criteria = [
            'search' => $request->query->get('search'),
            'type' => $request->query->get('type'),
            'compatibility' => $request->query->get('compatibility'),
            'year' => $request->query->get('year'),
            'letter' => $request->query->get('letter'),
        ];

        // Recherche avec filtres
        $accessories = $accessoryRepository->findBySearchAndFilters(array_filter($criteria));

        // Données pour les filtres
        $types = $accessoryRepository->findAvailableTypes();
        $compatibilities = $accessoryRepository->findAvailableCompatibilities();
        $years = $accessoryRepository->findAvailableYears();

        // Alphabet pour la recherche alphabétique
        $alphabet = array_merge(['0-9'], range('A', 'Z'));

        return $this->render('accessory/index.html.twig', [
            'accessories' => $accessories,
            'types' => $types,
            'compatibilities' => $compatibilities,
            'years' => $years,
            'alphabet' => $alphabet,
            'currentFilters' => $criteria,
        ]);
    }

    #[Route('/new', name: 'app_accessory_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $accessory = new Accessory();
        $accessory->setCreator($this->getUser());

        $form = $this->createForm(AccessoryType::class, $accessory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$accessory->getCreatedAt()) {
                $accessory->setCreatedAt(new \DateTime());
            }
            $entityManager->persist($accessory);
            $entityManager->flush();

            $this->addFlash('success', 'L\'accessoire a été créé avec succès.');
            return $this->redirectToRoute('app_accessory_index');
        }

        return $this->render('accessory/new.html.twig', [
            'accessory' => $accessory,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_accessory_show', methods: ['GET'])]
    public function show(Accessory $accessory, AccessoryCollectionRepository $accessoryCollectionRepository, AccessoryKitRepository $accessoryKitRepository): Response
    {
        $this->denyAccessUnlessGranted('view', $accessory);

        $myCollection = $this->getUser()
            ? $accessoryCollectionRepository->findOneBy(['accessory' => $accessory, 'user' => $this->getUser()])
            : null;

        $myKit = $this->getUser()
            ? $accessoryKitRepository->findOneBy(['accessory' => $accessory, 'user' => $this->getUser()])
            : null;

        return $this->render('accessory/show.html.twig', [
            'accessory' => $accessory,
            'my_collection' => $myCollection,
            'my_kit' => $myKit,
        ]);
    }

    #[Route('/{id}/update-in-collection', name: 'app_accessory_update_in_collection', methods: ['POST'])]
    public function updateInCollection(Request $request, Accessory $accessory, AccessoryCollectionRepository $accessoryCollectionRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $myCollection = $accessoryCollectionRepository->findOneBy(['accessory' => $accessory, 'user' => $this->getUser()]);

        if (!$myCollection) {
            $this->addFlash('error', 'Cet accessoire n\'est pas dans votre collection.');
            return $this->redirectToRoute('app_accessory_show', ['id' => $accessory->getId()]);
        }

        if ($request->request->get('action') === 'remove') {
            $entityManager->remove($myCollection);
            $entityManager->flush();
            $this->addFlash('success', 'L\'accessoire a été retiré de votre collection.');
        } else {
            $quantity = max(1, (int) $request->request->get('quantity', 1));
            $myCollection->setQuantity($quantity);
            $entityManager->flush();
            $this->addFlash('success', 'Quantité mise à jour avec succès.');
        }

        return $this->redirectToRoute('app_accessory_show', ['id' => $accessory->getId()]);
    }

    #[Route('/{id}/edit', name: 'app_accessory_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Accessory $accessory, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('edit', $accessory);

        $form = $this->createForm(AccessoryType::class, $accessory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'accessoire a été modifié avec succès.');
            return $this->redirectToRoute('app_accessory_show', ['id' => $accessory->getId()]);
        }

        return $this->render('accessory/edit.html.twig', [
            'accessory' => $accessory,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_accessory_delete', methods: ['POST'])]
    public function delete(Request $request, Accessory $accessory, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('delete', $accessory);

        if ($this->isCsrfTokenValid('delete'.$accessory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($accessory);
            $entityManager->flush();
            $this->addFlash('success', 'L\'accessoire a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_accessory_index');
    }

    #[Route('/{id}/add-to-collection', name: 'app_accessory_add_to_collection', methods: ['POST'])]
    public function addToCollection(Request $request, Accessory $accessory, EntityManagerInterface $entityManager): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        $accessoryCollection = new AccessoryCollection();
        $accessoryCollection->setUser($this->getUser());
        $accessoryCollection->setAccessory($accessory);
        $accessoryCollection->setQuantity($quantity);

        $entityManager->persist($accessoryCollection);
        $entityManager->flush();

        $this->addFlash('success', 'L\'accessoire a été ajouté à votre collection.');
        return $this->redirectToRoute('app_accessory_show', ['id' => $accessory->getId()]);
    }

    #[Route('/{id}/add-to-kit', name: 'app_accessory_add_to_kit', methods: ['POST'])]
    public function addToKit(Request $request, Accessory $accessory, AccessoryKitRepository $accessoryKitRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $existing = $accessoryKitRepository->findOneBy(['accessory' => $accessory, 'user' => $this->getUser()]);
        if ($existing) {
            $this->addFlash('error', 'Cet accessoire est déjà dans votre kit.');
            return $this->redirectToRoute('app_accessory_show', ['id' => $accessory->getId()]);
        }

        $quantity = max(1, (int) $request->request->get('quantity', 1));
        $note = $request->request->get('note', '');

        $kit = new AccessoryKit();
        $kit->setUser($this->getUser());
        $kit->setAccessory($accessory);
        $kit->setQuantity($quantity);
        $kit->setNote($note ?: null);

        $entityManager->persist($kit);
        $entityManager->flush();

        $this->addFlash('success', 'L\'accessoire a été ajouté à votre kit convention.');
        return $this->redirectToRoute('app_accessory_show', ['id' => $accessory->getId()]);
    }

    #[Route('/{id}/update-in-kit', name: 'app_accessory_update_in_kit', methods: ['POST'])]
    public function updateInKit(Request $request, Accessory $accessory, AccessoryKitRepository $accessoryKitRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $myKit = $accessoryKitRepository->findOneBy(['accessory' => $accessory, 'user' => $this->getUser()]);

        if (!$myKit) {
            $this->addFlash('error', 'Cet accessoire n\'est pas dans votre kit.');
            return $this->redirectToRoute('app_accessory_show', ['id' => $accessory->getId()]);
        }

        if ($request->request->get('action') === 'remove') {
            $entityManager->remove($myKit);
            $entityManager->flush();
            $this->addFlash('success', 'L\'accessoire a été retiré de votre kit.');
        } else {
            $quantity = max(1, (int) $request->request->get('quantity', 1));
            $note = $request->request->get('note', '');
            $myKit->setQuantity($quantity);
            $myKit->setNote($note ?: null);
            $entityManager->flush();
            $this->addFlash('success', 'Kit mis à jour avec succès.');
        }

        return $this->redirectToRoute('app_accessory_show', ['id' => $accessory->getId()]);
    }

    #[Route('/search', name: 'app_accessory_search', methods: ['GET'])]
    public function search(Request $request, AccessoryRepository $accessoryRepository): Response
    {
        $query = $request->query->get('q');
        $type = $request->query->get('type');
        $compatibility = $request->query->get('compatibility');

        $accessories = [];
        if ($query) {
            $accessories = $accessoryRepository->findBySearch($query);
        } elseif ($type) {
            $accessories = $accessoryRepository->findByType($type);
        } elseif ($compatibility) {
            $accessories = $accessoryRepository->findByCompatibility($compatibility);
        }

        return $this->render('accessory/search.html.twig', [
            'accessories' => $accessories,
            'query' => $query,
            'type' => $type,
            'compatibility' => $compatibility,
        ]);
    }
}
