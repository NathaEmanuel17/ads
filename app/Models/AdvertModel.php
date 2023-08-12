<?php

namespace App\Models;

use App\Entities\Advert;

class AdvertModel extends MyBaseModel
{
    private $user;

    public function __construct()
    {
        parent::__construct();

        /**
         * @todo $this->user = service('auth')->user() ?? auth('api')->user();   // allterar quando estivermos com API 
         */

        $this->user = service('auth')->user();
    }

    protected $DBGroup          = 'default';
    protected $table            = 'adverts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = Advert::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'category_id',
        'code',
        'title',
        'description',
        'price',
        //'is_published', // esse não colocamos aqui, pois queremos ter um controle maior de quando o anúncio devera ser publicado/despublicado
        'situation',
        'zipcode',
        'street',
        'number',
        'neighborhood',
        'city',
        'state',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['escapeDataXSS', 'generateCitySlug', 'generateCode', 'setUserID'];
    protected $beforeUpdate   = ['escapeDataXSS', 'generateCitySlug', 'unplublish'];

    protected function generateCitySlug(array $data): array
    {
        if (isset($data['data']['city'])) {
            $data['data']['city_slug'] = mb_url_title($data['data']['city'], lowercase: true);
        }

        return $data;
    }

    protected function generateCode(array $data): array
    {
        if (isset($data['data'])) {
            $data['data']['code'] = strtoupper(uniqid('ADVERT_', true));
        }

        return $data;
    }

    protected function setUserID(array $data): array
    {
        if (isset($data['data'])) {
            $data['data']['user_id'] = $this->user->id;
        }

        return $data;
    }

    protected function unplublish(array $data): array
    {
        //Houve alteração no title ou description
        if (isset($data['data']['title']) || isset($data['data']['description'])) {
            // Sim... houve alteração.... então tornamos o anuncio como não publicado (false)
            $data['data']['is_published'] = false;
        }

        return $data;
    }

    /**
     * Recupera todos os anúncios de acordo com o usuário logado.
     *
     *@param bollean $onlyDeleted
     *@return array
     */
    public function getAllAdverts(bool $onlyDeleted = false)
    {
        $this->setSQLMode();

        $builder = $this;

        if ($onlyDeleted) {

            $builder->onlyDeleted();
        }

        $tableFields = [
            'adverts.*',
            'categories.name AS category',
            'adverts_images.image AS images', //apelido (alias) de 'images', que utlizaremos no metodo image do Entity Advert
        ];

        $builder->select($tableFields);

        // Quem está logado é o manager?
        if (!$this->user->isSuperadmin()) {

            // É o usuario anunciante... então recuperamos apenas os anúncios dele
            $builder->where('adverts.user_id', $this->user->id);
        }

        $builder->join('categories', 'categories.id = adverts.category_id');
        $builder->join('adverts_images', 'adverts_images.advert_id = adverts.id', 'LEFT'); //Nem todos os anuncios terão imagens
        $builder->groupBy('adverts.id'); // para não repetir registros
        $builder->orderBy('adverts.id', 'DESC');;
        return $builder->findAll();
    }

    /**
     * Recupera o anúncioo de acordo com o id.
     *
     *@param integer $id
     *@param bollean $withDeleted
     *@return object|null
     */
    public function getAdvertByID(int $id, bool $withDeleted = false)
    {
        $builder = $this;

        $tableFields = [
            'adverts.*',
            'users.email', // para notificarmos o usuário/anunciante
        ];

        $builder->select($tableFields);
        $builder->withDeleted($withDeleted);

        // Quem está logado é o manager?
        if (!$this->user->isSuperadmin()) {

            // É o usuario anunciante... então recuperamos apenas os anúncios dele

            $builder->where('adverts.user_id', $this->user->id);
        }

        $builder->join('users', 'users.id = adverts.user_id');

        $advert = $builder->find($id);

        // Foi encontrado um anúncio?
        if (!is_null($advert)) {

            // Sim... então podemos retornar a imagem do mesmo
            $advert->images = $this->getAdvertImages($advert->id);
        }

        // Retornamos o anúncio que pode ou não ter imagens
        return $advert;
    }

    public function getAdvertImages(int $advertID): array
    {
        return $this->db->table('adverts_images')->where('advert_id', $advertID)->get()->getResult();
    }

    public function trySaveAdvert(Advert $advert, bool $protect = true)
    {
        try {

            $this->db->transStart();

            $this->protect($protect)->save($advert);

            $this->db->transComplete();
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);

            die('Error saving data');
        }
    }

    public function tryStoreAdvertImages(array $dataImages, int $advertID)
    {
        try {

            $this->db->transStart();

            $this->db->table('adverts_images')->insertBatch($dataImages);

            $this->protect(false)->set('is_published', false)->where('id', $advertID)->update();

            $this->db->transComplete();
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);

            die('Error saving data');
        }
    }

    public function tryDeleteAdvertImage(int $advertID, string $image)
    {
        $criteria = [
            'advert_id' => $advertID,
            'image'     => $image,
        ];

        return $this->db->table('adverts_images')->where($criteria)->delete();
    }

    public function tryArchiveAdvert(int $advertID)
    {
        try {

            $this->db->transStart();

            $this->where('user_id', $this->user->id)->delete($advertID);

            $this->db->transComplete();
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);

            die('Error saving data');
        }
    }

    public function tryDeleteAdvert(int $advertID)
    {
        try {

            $this->db->transStart();

            // Quem está logado é o manager?
            if (!$this->user->isSuperadmin()) {

                // É o usuario anunciante... então recuperamos apenas os anúncios dele

                $this->where('user_id', $this->user->id)->delete($advertID, true);
            }

            $this->db->transComplete();
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);

            die('Error deleting data');
        }
    }

    public function getAllAdvertsPaginated(int $perPage = 10, $criteria = [])
    {
        $this->setSQLMode();

        $builder =  $this;

        $tableFields = [
            'adverts.*',
            'categories.name as category',
            'categories.slug as category_slug',
            'adverts_images.image As images', // para utilizarmos no método image  do entity advert
        ];

        $builder->select($tableFields);
        $builder->join('categories', 'categories.id = adverts.category_id');
        $builder->join('adverts_images', 'adverts_images.advert_id = adverts.id');

        if (!empty($criteria)) {

            $builder->where($criteria);
        }

        $builder->where('adverts.is_published', true);
        $builder->orderBy('adverts.id', 'DESC');
        $builder->groupBy('adverts.id');

        $adverts =  $builder->paginate($perPage);

        return $adverts;
    }

    public function getAdvertByCode(string $code, bool $ofTheLoggedInUser = false)
    {
        $tableFields = [
            'adverts.*',
            'users.name',
            'users.email', // usaremos para a parte de perguntas
            'users.username',
            'users.phone',
            'users.display_phone',
            'users.created_at As user_since',
            'categories.name as category',
            'categories.slug as category_slug', // usaremos para filtrar os anuncios por categoria
        ];

        $builder = $this;
        $builder->select($tableFields);
        $builder->join('users', 'users.id = adverts.user_id');
        $builder->join('categories', 'categories.id = adverts.category_id');
        $builder->where('adverts.is_published', true);
        $builder->where('adverts.code', $code);

        if ($ofTheLoggedInUser) {

            $builder->where('adverts.user_id', $this->user->id);
        }

        $advert = $builder->first();

        if (!is_null($advert)) {

            $advert->images = $this->getAdvertImages($advert->id);
            // recupero as imagens do esmo
        }

        if (!is_null($advert)) {

            // recupero as perguntas e respostas do mesmo
        }

        return $advert;
    }

    public function getCitiesFromPublishedAdevrts(int $limit = 5, string $categorySlug = null): array
    {
        $this->setSQLMode();

        $tableFields = [
            'adverts.*',
            'categories.name as category', // para debug
            'COUNT(adverts.id) as total_adverts'
        ];

        // recupero apenas os adverts_id da tabela de imagens
        $advertsIDS = array_column($this->db->table('adverts_images')->select('advert_id')->get()->getResultArray(), 'advert_id');

        $builder = $this;

        $builder->select($tableFields);
        // $builder->asArray(); // para debug
        $builder->join('categories', 'categories.id = adverts.category_id');
        $builder->where('adverts.is_published', true);
        $builder->where('categories.slug', $categorySlug);
        $builder->whereIn('adverts.id', $advertsIDS); // apenas em anúncios que possuem imagem
        $builder->groupBy('adverts.city');
        $builder->orderBy('total_adverts', 'DESC');

        return $builder->findAll($limit);
    }

    function countAllUserAdverts(int $userID, bool $withDeleted = true, array $criteria = []): int
    {
        $builder = $this;

        if (!empty($criteria)) {

            $builder->where($criteria);
        }

        $builder->where('adverts.user_id', $userID);
        $builder->withDeleted($withDeleted);

        return $builder->countAllResults();
    }
}
