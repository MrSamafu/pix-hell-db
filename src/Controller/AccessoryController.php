<?php

namespace App\Controller;

use App\Entity\Accessory;
use App\Entity\AccessoryCollection;
use App\Form\AccessoryType;
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
    public function index(AccessoryRepository $accessoryRepository): Response
    {
        return $this->render('accessory/index.html.twig', [
            'accessories' => $accessoryRepository->findAll(),
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
                $accessory->setCreatedAt(new \DateTimeImmutable());
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
    public function show(Accessory $accessory): Response
    {
        $this->denyAccessUnlessGranted('view', $accessory);

        return $this->render('accessory/show.html.twig', [
            'accessory' => $accessory,
        ]);
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
