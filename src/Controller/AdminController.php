<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserFormType;
use App\Repository\CotationRepository;
use App\Repository\CryptocurrencyRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function dashboard(UserRepository $userRepo, CryptocurrencyRepository $cryptoRepo, CotationRepository $cotationRepo): Response
    {
        $users = $userRepo->findBy(['roles' => []]);
        $cryptos = $cryptoRepo->findAll();
        $latestCotations = [];
        foreach ($cryptos as $crypto) {
            $cotations = $cotationRepo->findBy(
                ['cryptocurrency' => $crypto],
                ['quotedAt' => 'DESC'],
                1
            );
            if ($cotations) {
                $latestCotations[$crypto->getId()] = $cotations[0]->getPrice();
            }
        }
        return $this->render('admin/dashboard.html.twig', [
            'userCount' => count($userRepo->findAll()),
            'cryptos' => $cryptos,
            'latestCotations' => $latestCotations,
        ]);
    }

    #[Route('/users', name: 'admin_users')]
    public function users(UserRepository $userRepo): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepo->findAll(),
        ]);
    }

    #[Route('/users/new', name: 'admin_user_new')]
    public function newUser(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(AdminUserFormType::class, $user);
        $form->handleRequest($request);

        $tempPassword = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $tempPassword = bin2hex(random_bytes(5));
            $user->setPassword($hasher->hashPassword($user, $tempPassword));
            $user->setRoles(['ROLE_USER']);
            $user->setBalance('500.00');
            $em->persist($user);
            $em->flush();
            return $this->render('admin/user_created.html.twig', [
                'user' => $user,
                'tempPassword' => $tempPassword,
            ]);
        }

        return $this->render('admin/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'New User',
        ]);
    }

    #[Route('/users/{id}/edit', name: 'admin_user_edit')]
    public function editUser(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AdminUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'User updated successfully.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit User',
        ]);
    }

    #[Route('/users/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(User $user, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'User deleted successfully.');
        }
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/cryptos', name: 'admin_cryptos')]
    public function cryptos(CryptocurrencyRepository $cryptoRepo, CotationRepository $cotationRepo): Response
    {
        $cryptos = $cryptoRepo->findAll();
        $latestCotations = [];
        foreach ($cryptos as $crypto) {
            $cotations = $cotationRepo->findBy(
                ['cryptocurrency' => $crypto],
                ['quotedAt' => 'DESC'],
                1
            );
            if ($cotations) {
                $latestCotations[$crypto->getId()] = $cotations[0]->getPrice();
            }
        }
        return $this->render('admin/cryptos.html.twig', [
            'cryptos' => $cryptos,
            'latestCotations' => $latestCotations,
        ]);
    }
}