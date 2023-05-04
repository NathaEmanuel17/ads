<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    // --------------------------------------------------------------------
    // Categories
    // --------------------------------------------------------------------

    public $category = [
        'name' => 'required|min_length[3]|max_length[90]|is_unique[categories.name,id,{id}]',
    ];

    public $category_errors = [
        'name' => [
            'required'   => 'Categories.name.required', //lang() não pode ser colocado aqui... dara erro de sintaxe
            'min_length' => 'Categories.name.min_length',
            'max_length' => 'Categories.name.max_length',
            'is_unique'  => 'Categories.name.is_unique',
        ],
    ];

    
    // --------------------------------------------------------------------
    // Plans
    // --------------------------------------------------------------------
    public $plan = [
        'name'        => 'required|min_length[3]|max_length[90]|is_unique[plans.name,id,{id}]',
        'recorrence'  => 'required|in_list[monthly,quarterly,semester,yearly]',
        'value'       => 'required',
        'description' => 'required',

    ];

    public $plan_errors = [
        'recorrence' => [
            'in_list'    => 'Plans.recorrence.in_list', //lang() não pode ser colocado aqui... dara erro de sintaxe
        ],
    ];

    // --------------------------------------------------------------------
    // Adverts
    // --------------------------------------------------------------------

    public $advert = [
        'title'         => 'required|min_length[5]|max_length[120]|is_unique[adverts.title,id,{id}]',
        'situation'     => 'required|in_list[new,used]',
        'category_id'   => 'required|is_not_unique[categories.id,id,{category_id}]',
        'price'         => 'required',
        'description'   => 'required|max_length[5000]',
        'zipcode'       => 'required|exact_length[9]',
        'street'        => 'required|max_length[130]',
        'neighborhood'  => 'required|max_length[130]',
        'city'          => 'required|max_length[130]',
        'state'         => 'required|exact_length[2]',

    ];

    public $advert_errors = [
        'title' => [
            'is_unique'  => 'Adverts.title.is_unique',
        ],
    ];


    // --------------------------------------------------------------------
    // Images
    // --------------------------------------------------------------------

    public $advert_images = [
        'imagesimages' => [
            'uploaded[images]',
            'is_image[images]',
            'mime_in[images,image/jpg,image/jpeg,image/png,image/webp]',
            'max_size[images,2048]',
            'max_dims[images,1920,1080]',
        ],
    ];

    public $advert_images_errors = [];
}
