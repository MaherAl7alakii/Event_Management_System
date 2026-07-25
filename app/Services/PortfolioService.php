<?php

namespace App\Services;

use App\Models\Portfolio;


class PortfolioService
{
   public function getProviderPortfolios(int $serviceProviderId)
{
    return Portfolio::where('service_provider_id', $serviceProviderId)
        ->latest()
        ->get();
}

    public function getPortfolioById(int $id)
    {
        return Portfolio::find($id);
    }


    public function createPortfolio(int $serviceProviderId, array $data)
    {
    

        return Portfolio::create([
            'service_provider_id' => $serviceProviderId,
            'title' => $data['title'],
            'type' => $data['type'],
            'url' => $data['url'],
        ]);
    }



  public function updatePortfolio(Portfolio $portfolio, array $data)
{
    
    $portfolio->update([
        'title' => $data['title'] ?? $portfolio->title,
        'type' => $data['type'] ?? $portfolio->type,
        'url' => $data['url'] ?? $portfolio->url,
    ]);


    return $portfolio->fresh();
}



    public function deletePortfolio(Portfolio $portfolio)
    {


        $portfolio->delete();


        return true;
    }
}