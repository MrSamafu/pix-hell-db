<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BadgeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard', methods: ['GET'])]
    public function dashboard(
        UserRepository $userRepository,
        BadgeRepository $badgeRepository
    ): Response {
        $totalUsers = count($userRepository->findAll());
        $totalBadges = count($badgeRepository->findAll());

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalBadges' => $totalBadges,
        ]);
    }

    #[Route('/users', name: 'app_admin_users', methods: ['GET'])]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}/edit-roles', name: 'app_admin_user_edit_roles', methods: ['POST'])]
    public function editUserRoles(
        User $user,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('edit_roles_' . $user->getId(), $request->request->get('_token'))) {
            $isAdmin = $request->request->get('is_admin') === '1';

            $roles = ['ROLE_USER'];
            if ($isAdmin) {
                $roles[] = 'ROLE_ADMIN';
            }

            $user->setRoles($roles);
            $em->flush();

            $this->addFlash('success', 'Rôles mis à jour avec succès.');
        }

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/user/{id}/toggle-admin', name: 'app_admin_user_toggle_admin', methods: ['POST'])]
    public function toggleAdmin(
        User $user,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('toggle_admin_' . $user->getId(), $request->request->get('_token'))) {
            $roles = $user->getRoles();

            if (in_array('ROLE_ADMIN', $roles)) {
                // Retirer ROLE_ADMIN
                $user->setRoles(['ROLE_USER']);
                $this->addFlash('success', 'Droits administrateur retirés.');
            } else {
                // Ajouter ROLE_ADMIN
                $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
                $this->addFlash('success', 'Droits administrateur accordés.');
            }

            $em->flush();
        }

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/user/{id}/manage-badges', name: 'app_admin_user_manage_badges', methods: ['GET'])]
    public function manageUserBadges(
        User $user,
        BadgeRepository $badgeRepository
    ): Response {
        $allBadges = $badgeRepository->findAll();

        return $this->render('admin/user_badges.html.twig', [
            'user' => $user,
            'allBadges' => $allBadges,
        ]);
    }
}

