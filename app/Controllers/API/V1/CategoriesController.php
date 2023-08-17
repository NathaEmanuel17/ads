<?php

namespace App\Controllers\API\V1;

use App\Services\CategoryService;
use CodeIgniter\Config\Factories;
use CodeIgniter\RESTful\ResourceController;

class CategoriesController extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $perPage = $this->request->getGet('perPage');
        $page    = $this->request->getGet('page');

        $categories = (object) Factories::class(CategoryService::class)->getCategoriesPaginated($perPage, $page);
        
        return $this->respond(
            [
                'code'       => 200,
                'categories' => $categories->categories,
                'pager'      => $categories->pager
            ]
        );
    }
}
