<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryIndexResource;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $categories = Category::All();

        return $this->apiResponse(
            CategoryIndexResource::collection($categories),
            'Categories fetched successfully',
            Response::HTTP_OK
        );

    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return $this->apiResponse(
            new CategoryResource($category),
            'Category created successfully',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {

        $category->update($request->validated());

        return $this->apiResponse(
            new CategoryResource($category),
            'Category updated successfully',
            Response::HTTP_OK
        );
    }

    public function show(Category  $category)
    {

        return $this->apiResponse(
            new CategoryResource($category),
            'Category fetched successfully',
            Response::HTTP_OK
        );
    }

}
