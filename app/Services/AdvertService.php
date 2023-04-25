<?php

namespace App\Services;

use App\Models\AdvertModel;
use CodeIgniter\Config\Factories;

class AdvertService
{
    private $user;
    private $advertModel;

    public const SITUATION_NEW  = 'new';
    public const SITUATION_USED = 'used';

    public function __construct()
    {
        /*
        * @todo alterar para auth('api')->user()...... quando estivermos com a API
        */
        $this->user = service('auth')->user();

        $this->advertModel = Factories::models(AdvertModel::class);
    }

    public function getAllAdverts(
        bool $showBtnArchive    = true,
        bool $showBtnViewAdvert = true,
        bool $showBtnQuestion   = true,
        string $classBtnAction  = 'btn btn-primary btn-sm',
        string $sizeImage       = 'small',
    ): array {

        $adverts = $this->advertModel->getAllAdverts();

        $data = [];

        $baseRouteToEditImages = $this->user->isSuperadmin() ? 'adverts.manager.edit.images' : 'adverts.my.edit.images';

        $baseRouteToQuestions  = $this->user->isSuperadmin() ? 'adverts.manager.edit.questions' : 'adverts.my.edit.questions';

        foreach($adverts as $advert) {
            $btnEdit = form_button(
                [
                    'data-id' => $advert->id,
                    'id'      => 'btnEditAdvert', //ID do html element
                    'class'   => 'dropdown-item'
                ],
                lang('App.btn_edit')
            );

            $finalRouteToEditImages = route_to($baseRouteToEditImages, $advert->id);

            $btnEditImages = form_button(
                [
                    'class'   => 'dropdown-item',
                    'onClick' => "location.href='{$finalRouteToEditImages}'"
                ],
                lang('Adverts.btn_edit_images')
            );
            
            // Comaçamos a montar o botão de ações do dropdown

            $btnActions = '<div class="dropdown dropup">'; //abertura da div do dropdown

            $attrAction = [
                'type'            => 'button',
                'id'              => 'actions',
                'class'           => "dropdown-toggle {$classBtnAction}",
                'data-bs-toggle'  => "dropdown", // Para BS5
                'data-toggle'     => "dropdown", // Para BS4
                'aria-haspopup'   => 'true',
                'aria-expanded'   => 'false',
            ];

            $btnActions .= form_button($attrAction, lang('App.btn_actions'));

            $btnActions .= '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">'; // abertura da div do dropdown menu

            $btnActions .= $btnEdit;
            $btnActions .= $btnEditImages;

            $btnActions .= '</div>'; //fechamento da div do dropdown-menu

            $btnActions .= '</div>'; //fechamento da div do dropdown

            $data[] = [
                'image'             => $advert->image(),
                'title'             => $advert->title,
                'code'              => $advert->code,
                'category'          => $advert->category,
                'is_published'      => $advert->isPublished(),
                'address'           => $advert->address(),
                'actions'           => $btnActions,

            ];
        }

        return $data;
    }
}
?>