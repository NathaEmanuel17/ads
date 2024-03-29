<?php

namespace App\Models;

use App\Entities\Category;

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

    protected function generateSlug(array $data): array
    {
        if (isset($data['data']['name'])) {
            $data['data']['slug'] = mb_url_title($data['data']['name'], lowercase: true);
        }

        return $data;
    }


    public function getParentCategories(int $exceptCategoryID = null): array
    {
        $builder = $this;

        if ($exceptCategoryID) {

            $builder->where('id !=', $exceptCategoryID);
        }

        $builder->orderBy('name', 'ASC');
        $builder->asArray();

        return $builder->findAll();
    }

    /**
     * Método que recupera as categorias que fazem parte de anuncios publicados
     *
     * @param integer $limit
     * @return array 
     */

    public function getCategoriesFromPublishedAdverts(int $limit = 5): array
    {
        $this->setSQLMode();

        $tableFields = [
            'categories.*',
            'COUNT(adverts.id) as total_adverts'
        ];

        // recupero apenas os adverts_id da tabela de imagens
        $advertsIDS = array_column($this->db->table('adverts_images')->select('advert_id')->get()->getResultArray(), 'advert_id');

        $builder = $this;
        $builder->select($tableFields);
        $builder->asObject();
        $builder->join('adverts', 'adverts.category_id = categories.id');
        $builder->where('adverts.is_published', true);

        // quero garantir que apenas anúncios com imagens sejam contabilzados 
        if (!empty($advertsIDS)) {

            $builder->whereIn('adverts.id', $advertsIDS);
        }

        $builder->groupBy('categories.name');
        $builder->orderBy('total_adverts', 'DESC');

        return $builder->findAll($limit);
    }
}
