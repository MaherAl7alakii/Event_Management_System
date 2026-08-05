<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceOffer;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OfferService
{
    /**
     * Create Offer
     */
  public function create(Service $service, int $userId, array $data): ServiceOffer
{
    $this->checkOwner($service, $userId);

    if ($service->offer()->exists()) {
        throw new HttpException(409, 'This service already has an offer.');
    }


    return ServiceOffer::create([
        'service_id' => $service->id,
        'discount' => $data['discount'],
        'original_price' => $service->base_price,
        'offer_price' => $this->calculateOfferPrice(
            $service->base_price,
            $data['discount']
        ),
        'start_date' => $data['start_date'],
        'end_date' => $data['end_date'],
        'is_active' => true,
    ]);
}

    /**
     * Update Offer
     */
    public function update(Service $service, int $userId, array $data): ServiceOffer
    {
        $this->checkOwner($service, $userId);

        $offer = $service->offer;

        if (!$offer) {
            throw new HttpException(404, 'Offer not found.');
        }

        $offerPrice = $this->calculateOfferPrice(
            $service->base_price,
            $data['discount']
        );

        $offer->update([
            'discount'       => $data['discount'],
            'original_price' => $service->base_price,
            'offer_price'    => $offerPrice,
            'start_date'     => $data['start_date'],
            'end_date'       => $data['end_date'],
        ]);

        return $offer->fresh();
    }

    /**
     * Delete Offer
     */
    public function delete(Service $service, int $userId): void
    {
        $this->checkOwner($service, $userId);

        $offer = $service->offer;

        if (!$offer) {
            throw new HttpException(404, 'Offer not found.');
        }

        $offer->delete();
    }

    /**
     * Show Offer
     */
   public function show(Service $service): ServiceOffer
{
    $offer = $service->offer()
        ->where('is_active', true)
        ->whereDate('start_date', '<=', today())
        ->whereDate('end_date', '>=', today())
        ->first();


    if (!$offer) {
        throw new HttpException(404, 'Offer not found.');
    }


    return $offer;
}
    /**
     * Check Owner
     */
    private function checkOwner(Service $service, int $userId): void
    {
        if ($service->provider_id != $userId) {
            throw new HttpException(
                403,
                'You are not allowed to manage this offer.'
            );
        }
    }

    /**
     * Calculate Price
     */
    private function calculateOfferPrice(float $price, int $discount): float
    {
        return round(
            $price - ($price * $discount / 100),
            2
        );
    }
}