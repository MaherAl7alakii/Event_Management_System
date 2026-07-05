<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $categories = Category::All();

        return $this->apiResponse(
            CategoryResource::collection($categories),
            'Categories fetched successfully',
            Response::HTTP_OK
        );

    }
}
