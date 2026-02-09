<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\CotationRepository;
use App\Repository\PurchaseRepository;

class WalletService
{
    public function __construct(
        private PurchaseRepository $purchaseRepo,
        private CotationRepository $cotationRepo
    ) {}

    public function getWallet(User $user): array
    {
        $purchases = $user->getPurchases();
        $wallet = [];

        foreach ($purchases as $purchase) {
            $cryptoId = $purchase->getCryptocurrency()->getId();
            if (!isset($wallet[$cryptoId])) {
                $wallet[$cryptoId] = [
                    'crypto'         => $purchase->getCryptocurrency(),
                    'totalQuantity'  => 0,
                    'totalCost'      => 0,
                    'purchases'      => [],
                ];
            }
            $wallet[$cryptoId]['totalQuantity'] += (float) $purchase->getQuantity();
            $wallet[$cryptoId]['totalCost']     += (float) $purchase->getQuantity() * (float) $purchase->getPriceAtPurchase();
            $wallet[$cryptoId]['purchases'][]    = $purchase;
        }

        foreach ($wallet as $cryptoId => &$data) {
            $data['avgPrice'] = $data['totalQuantity'] > 0
                ? $data['totalCost'] / $data['totalQuantity']
                : 0;

            $latest = $this->cotationRepo->findBy(
                ['cryptocurrency' => $data['crypto']],
                ['quotedAt' => 'DESC'],
                1
            );
            $currentPrice = $latest ? (float) $latest[0]->getPrice() : 0;
            $data['currentPrice'] = $currentPrice;

            $currentValue = $data['totalQuantity'] * $currentPrice;
            $data['profitLoss'] = $currentValue - $data['totalCost'];
        }

        return $wallet;
    }
}