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

}
