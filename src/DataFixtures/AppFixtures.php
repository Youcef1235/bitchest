<?php

namespace App\DataFixtures;

use App\Entity\Cotation;
use App\Entity\Cryptocurrency;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        // ── CRYPTOS ──────────────────────────────────────────────
        $cryptos = [
            ['Bitcoin',      'BTC',  40000],
            ['Ethereum',     'ETH',  2500],
            ['Ripple',       'XRP',  0.60],
            ['Bitcoin Cash', 'BCH',  300],
            ['Cardano',      'ADA',  0.45],
            ['Litecoin',     'LTC',  80],
            ['Dash',         'DASH', 50],
            ['Iota',         'IOTA', 0.25],
            ['NEM',          'XEM',  0.05],
            ['Stellar',      'XLM',  0.12],
        ];

        $cryptoEntities = [];
        foreach ($cryptos as [$name, $symbol, $basePrice]) {
            $crypto = new Cryptocurrency();
            $crypto->setName($name)->setSymbol($symbol);
            $manager->persist($crypto);
            $cryptoEntities[] = [$crypto, $basePrice];
        }

        // ── COTATIONS (30 jours) ──────────────────────────────────
        foreach ($cryptoEntities as [$crypto, $basePrice]) {
            $price = $basePrice;
            for ($i = 30; $i >= 0; $i--) {
                // Variation aléatoire entre -5% et +5%
                $variation = $price * (mt_rand(-500, 500) / 10000);
                $price = max(0.01, $price + $variation);

                $cotation = new Cotation();
                $cotation->setCryptocurrency($crypto);
                $cotation->setPrice(number_format($price, 2, '.', ''));
                $cotation->setQuotedAt(new \DateTimeImmutable("-{$i} days"));
                $manager->persist($cotation);
            }
        }

        // ── ADMIN ─────────────────────────────────────────────────
        $admin = new User();
        $admin->setEmail('admin@bitchest.com')
              ->setFirstname('Jerome')
              ->setLastname('Admin')
              ->setRoles(['ROLE_ADMIN'])
              ->setBalance('0.00')
              ->setPassword($this->hasher->hashPassword($admin, 'Admin1234!'));
        $manager->persist($admin);

        // ── CLIENT ────────────────────────────────────────────────
        $client = new User();
        $client->setEmail('client@bitchest.com')
               ->setFirstname('Bruno')
               ->setLastname('Client')
               ->setRoles(['ROLE_USER'])
               ->setBalance('500.00')
               ->setPassword($this->hasher->hashPassword($client, 'Client1234!'));
        $manager->persist($client);

        $manager->flush();
    }
}