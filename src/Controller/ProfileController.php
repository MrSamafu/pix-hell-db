<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\BadgeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function show(): Response
    {
        $user = $this->getUser();
        if (! $user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        if (! $user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $plainPassword)
                );
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_profile', methods: ['GET'])]
    public function userProfile(User $user, BadgeRepository $badgeRepository): Response
    {
        $allBadges = null;
        if ($this->isGranted('ROLE_ADMIN')) {
            $allBadges = $badgeRepository->findAll();
        }

        return $this->render('profile/user_profile.html.twig', [
            'user' => $user,
            'allBadges' => $allBadges,
        ]);
    }

    #[Route('/user/{id}/badge/add/{badgeId}', name: 'app_user_badge_add', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function addBadge(User $user, int $badgeId, BadgeRepository $badgeRepository, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('add_badge_' . $user->getId(), $request->request->get('_token'))) {
            $badge = $badgeRepository->find($badgeId);
            if ($badge && !$user->getBadges()->contains($badge)) {
                $user->addBadge($badge);
                $em->flush();
                $this->addFlash('success', 'Badge attribué avec succès.');
            }
        }

        return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
    }

    #[Route('/user/{id}/badge/remove/{badgeId}', name: 'app_user_badge_remove', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function removeBadge(User $user, int $badgeId, BadgeRepository $badgeRepository, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('remove_badge_' . $user->getId(), $request->request->get('_token'))) {
            $badge = $badgeRepository->find($badgeId);
            if ($badge && $user->getBadges()->contains($badge)) {
                $user->removeBadge($badge);
                $em->flush();
                $this->addFlash('success', 'Badge retiré avec succès.');
            }
        }

        return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
    }
}

