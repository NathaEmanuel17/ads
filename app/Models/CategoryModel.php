<?php

namespace App\Models;

use App\Entities\Category;
use PhpParser\Node\Stmt\Return_;

class CategoryModel extends MyBaseModel
{
    protected $DBGroup          = 'default';
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = Category::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'parent_id',
        'name',
        'slug'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['escapeDataXSS', 'generateSlug'];
    protected $beforeUpdate   = ['escapeDataXSS', 'generateSlug'];

    protected function generateSlug(array $data) : array
    {
        if(isset($data['data']['name'])) {
            $data['data']['slug'] = mb_url_title($data['data']['name'], lowercase:true);
        }

        return $data;
    }


    public function getParentCategories(int $exceptCategoryID = null) :array
    {
        $builder = $this;

        if($exceptCategoryID) {

            $builder->where('id !=', $exceptCategoryID);
        }

        $builder->orderBy('name', 'ASC');
        $builder->asArray();

        return $builder->findAll();
    }
}
