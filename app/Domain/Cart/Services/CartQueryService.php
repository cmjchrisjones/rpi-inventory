<?php

namespace App\Domain\Cart\Services;

use App\Domain\Cart\Contracts\CartInterface;
use Illuminate\Http\Request;
use App\Domain\Cart\Contracts\CartRepositoryInterface;

class CartQueryService
{
    /** @var CartRepositoryInterface $cartRepository */
    private CartRepositoryInterface $cartRepository;

    /**
     * Create CartQuery Service instance.
     *
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param Request $request
     * @return CartInterface
     */
    public function getCurrent(Request $request): CartInterface
    {
        return $this->cartRepository->getCurrentOrCreate($request);
    }

}
