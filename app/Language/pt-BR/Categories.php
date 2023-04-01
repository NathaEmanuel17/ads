<?php 

return  [
    'title_index'  => 'Listando as Categorias',
    'title_new'    => 'Criar cateforia',
    'title_edit'   => 'Editar cateforia',

    'label_name'            => 'Nome',
    'label_choose_category' => 'Escolha uma categoria...',
    'label_slug'            => 'Slug',
    'label_parent_name'     => 'Categoria Pai',

    // Validations
    'name' => [
        'required'   => 'O campo nome é obrigatorio.',
        'min_length' => 'Informe pelo menos 3 caracteres no tamanho.',
        'max_length' => 'Informe no máximo 90 caracteres no tamanho.',
        'is_unique'  => 'Essa categoria já existe.',
    ],
];

?>