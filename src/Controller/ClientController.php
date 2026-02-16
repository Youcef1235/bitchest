<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Repository\CotationRepository;
use App\Repository\CryptocurrencyRepository;
use App\Repository\PurchaseRepository;
use App\Service\WalletService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('/dashboard', name: 'client_dashboard')]
    public function dashboard(WalletService $walletService): Response
    {
        $user = $this->getUser();
        $wallet = $walletService->getWallet($user);
        return $this->render('client/dashboard.html.twig', [
            'wallet' => $wallet,
        ]);
    }

    #[Route('/portfolio', name: 'client_portfolio')]
    public function portfolio(WalletService $walletService): Response
    {
        return $this->render('client/portfolio.html.twig', [
            'wallet' => $walletService->getWallet($this->getUser()),
        ]);
    }

    #[Route('/cryptos', name: 'client_cryptos')]
    public function cryptos(CryptocurrencyRepository $cryptoRepo, CotationRepository $cotationRepo): Response
    {
        $cryptos = $cryptoRepo->findAll();
        $latestCotations = [];
        $chartData = [];

        foreach ($cryptos as $crypto) {
            $latest = $cotationRepo->findBy(['cryptocurrency' => $crypto], ['quotedAt' => 'DESC'], 1);
            if ($latest) {
                $latestCotations[$crypto->getId()] = $latest[0]->getPrice();
            }
            $cotations = $cotationRepo->findLast30Days($crypto->getId());
            $labels = [];
            $prices = [];
            foreach ($cotations as $c) {
                $labels[] = $c->getQuotedAt()->format('d/m');
                $prices[] = (float) $c->getPrice();
            }
            $chartData[] = [
                'id'     => $crypto->getId(),
                'labels' => $labels,
                'prices' => $prices,
            ];
        }

        return $this->render('client/cryptos.html.twig', [
            'cryptos'         => $cryptos,
            'latestCotations' => $latestCotations,
            'chartData'       => $chartData,
        ]);
    }

    #[Route('/cryptos/{id}', name: 'client_crypto_show')]
    public function cryptoShow(int $id, CryptocurrencyRepository $cryptoRepo, CotationRepository $cotationRepo): Response
    {
        $crypto = $cryptoRepo->find($id);
        if (!$crypto) {
            throw $this->createNotFoundException();
        }
        $cotations = $cotationRepo->findLast30Days($id);
        $labels = [];
        $prices = [];
        foreach ($cotations as $c) {
            $labels[] = $c->getQuotedAt()->format('d/m');
            $prices[] = (float) $c->getPrice();
        }
        $latest = $cotationRepo->findBy(['cryptocurrency' => $crypto], ['quotedAt' => 'DESC'], 1);
        $currentPrice = $latest ? (float) $latest[0]->getPrice() : 0;

        return $this->render('client/crypto_show.html.twig', [
            'crypto'       => $crypto,
            'labels'       => $labels,
            'prices'       => $prices,
            'currentPrice' => $currentPrice,
        ]);
    }

    #[Route('/buy/{id}', name: 'client_buy', methods: ['POST'])]
    public function buy(int $id, Request $request, CryptocurrencyRepository $cryptoRepo, CotationRepository $cotationRepo, EntityManagerInterface $em): Response
    {
        $crypto = $cryptoRepo->find($id);
        $user   = $this->getUser();
        $quantity = (float) $request->request->get('quantity');

        if ($quantity <= 0) {
            $this->addFlash('error', 'Invalid quantity.');
            return $this->redirectToRoute('client_cryptos');
        }

        $latest = $cotationRepo->findBy(['cryptocurrency' => $crypto], ['quotedAt' => 'DESC'], 1);
        $currentPrice = $latest ? (float) $latest[0]->getPrice() : 0;
        $totalCost = $quantity * $currentPrice;

        if ((float) $user->getBalance() < $totalCost) {
            $this->addFlash('error', 'Insufficient balance.');
            return $this->redirectToRoute('client_cryptos');
        }

        $purchase = new Purchase();
        $purchase->setUser($user)
                 ->setCryptocurrency($crypto)
                 ->setQuantity((string) $quantity)
                 ->setPriceAtPurchase((string) $currentPrice)
                 ->setPurchasedAt(new \DateTimeImmutable());

        $user->setBalance((string) ((float) $user->getBalance() - $totalCost));

        $em->persist($purchase);
        $em->flush();

        $this->addFlash('success', "Purchase successful! You bought {$quantity} {$crypto->getSymbol()}.");
        return $this->redirectToRoute('client_dashboard');
    }

    #[Route('/sell/{id}', name: 'client_sell', methods: ['POST'])]
    public function sell(int $id, CryptocurrencyRepository $cryptoRepo, CotationRepository $cotationRepo, PurchaseRepository $purchaseRepo, EntityManagerInterface $em): Response
    {
        $crypto = $cryptoRepo->find($id);
        $user   = $this->getUser();

        $purchases = $purchaseRepo->findByUserAndCrypto($user->getId(), $id);
        if (empty($purchases)) {
            $this->addFlash('error', 'You do not own this cryptocurrency.');
            return $this->redirectToRoute('client_dashboard');
        }

        $latest = $cotationRepo->findBy(['cryptocurrency' => $crypto], ['quotedAt' => 'DESC'], 1);
        $currentPrice = $latest ? (float) $latest[0]->getPrice() : 0;

        $totalQuantity = array_sum(array_map(fn($p) => (float) $p->getQuantity(), $purchases));
        $saleValue = $totalQuantity * $currentPrice;

        foreach ($purchases as $purchase) {
            $em->remove($purchase);
        }

        $user->setBalance((string) ((float) $user->getBalance() + $saleValue));
        $em->flush();

        $this->addFlash('success', "Sold all {$crypto->getSymbol()} for € " . number_format($saleValue, 2));
        return $this->redirectToRoute('client_dashboard');
    }

    #[Route('/profile', name: 'client_profile')]
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $user->setFirstname($request->request->get('firstname'));
            $user->setLastname($request->request->get('lastname'));
            $user->setEmail($request->request->get('email'));
            $em->flush();
            $this->addFlash('success', 'Profile updated.');
        }
        return $this->render('client/profile.html.twig', ['user' => $user]);
    }
}