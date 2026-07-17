<?php

namespace App\Http\Controllers;

use App\Http\Requests\Portfolio\StorePortfolioRequest;
use App\Http\Requests\Portfolio\UpdatePortfolioRequest;
use App\Http\Resources\PortfolioResource;
use App\Models\Portfolio;
use App\Models\ServiceProvider;
use App\Services\PortfolioService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PortfolioController extends Controller
{
    use ResponseTrait;


    private PortfolioService $portfolioService;


    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }



    /**
     * Get service provider portfolios
     * Customer + Provider can view
     */
  public function index(ServiceProvider $serviceProvider): JsonResponse
{
    $portfolios = $this->portfolioService
        ->getProviderPortfolios($serviceProvider->id);


    return $this->apiResponse(
        PortfolioResource::collection($portfolios),
        'Portfolios fetched successfully.',
        Response::HTTP_OK
    );
}



    /**
     * Store new portfolio
     * Provider only
     */
public function store(StorePortfolioRequest $request): JsonResponse
{
    $user = auth()->user();

    $serviceProvider = ServiceProvider::where('user_id', $user->id)->first();

    if (!$serviceProvider) {
        return $this->apiResponse(
            null,
            'You must create a service provider profile first.',
            Response::HTTP_FORBIDDEN
        );
    }


    $portfolio = $this->portfolioService
        ->createPortfolio(
            $serviceProvider->id,
            $request->validated()
        );


    return $this->apiResponse(
        new PortfolioResource($portfolio),
        'Portfolio created successfully.',
        Response::HTTP_CREATED
    );
}


    /**
     * Show single portfolio
     */
    public function show(Portfolio $portfolio): JsonResponse
    {

        return $this->apiResponse(
            new PortfolioResource($portfolio),
            'Portfolio fetched successfully.',
            Response::HTTP_OK
        );

    }



    /**
     * Update portfolio
     * Provider only
     */
    public function update(
        UpdatePortfolioRequest $request,
        Portfolio $portfolio
    ): JsonResponse {


        $this->checkOwnership($portfolio);


        $portfolio = $this->portfolioService
            ->updatePortfolio(
                $portfolio,
                $request->validated()
            );


        return $this->apiResponse(
            new PortfolioResource($portfolio),
            'Portfolio updated successfully.',
            Response::HTTP_OK
        );
    }




    /**
     * Delete portfolio
     * Provider only
     */
    public function destroy(Portfolio $portfolio): JsonResponse
    {

        $this->checkOwnership($portfolio);


        $this->portfolioService
            ->deletePortfolio($portfolio);



        return $this->apiResponse(
            null,
            'Portfolio deleted successfully.',
            Response::HTTP_OK
        );

    }




    /**
     * Make sure provider owns this portfolio
     */
  private function checkOwnership(Portfolio $portfolio)
{
    $serviceProvider = ServiceProvider::where(
        'user_id',
        auth()->id()
    )->first();


    if (!$serviceProvider ||
        $portfolio->service_provider_id !== $serviceProvider->id
    ) {

        abort(
            Response::HTTP_FORBIDDEN,
            'You are not allowed to modify this portfolio.'
        );
    }
}
}