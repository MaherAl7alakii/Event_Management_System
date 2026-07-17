<?php

namespace App\Services;

use App\Models\Portfolio;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        if (isset($data['url']) && $data['url'] instanceof UploadedFile) {

            $file = $data['url'];

            $extension = $file->getClientOriginalExtension();

            $fileName = Str::uuid() . '.' . $extension;

            $data['url'] = $file->storeAs(
                'portfolio',
                $fileName,
                'public'
            );
        }


        return Portfolio::create([
            'service_provider_id' => $serviceProviderId,
            'title' => $data['title'],
            'type' => $data['type'],
            'url' => $data['url'],
        ]);
    }



  public function updatePortfolio(Portfolio $portfolio, array $data)
{
    if (isset($data['url']) && $data['url'] instanceof UploadedFile) {

        if ($portfolio->url) {
            Storage::disk('public')
                ->delete($portfolio->url);
        }

        $file = $data['url'];

        $extension = $file->getClientOriginalExtension();

        $fileName = Str::uuid() . '.' . $extension;

        $data['url'] = $file->storeAs(
            'portfolio',
            $fileName,
            'public'
        );
    }


    $portfolio->update([
        'title' => $data['title'] ?? $portfolio->title,
        'type' => $data['type'] ?? $portfolio->type,
        'url' => $data['url'] ?? $portfolio->url,
    ]);


    return $portfolio->fresh();
}



    public function deletePortfolio(Portfolio $portfolio)
    {

        if ($portfolio->url) {

            Storage::disk('public')
                ->delete($portfolio->url);
        }


        $portfolio->delete();


        return true;
    }
}