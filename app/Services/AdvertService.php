<?php

namespace App\Services;

use App\Entities\Advert;
use App\Models\AdvertModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Events\Events; // para disparar eventos
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

            // É para exibir o botão?
            if($showBtnArchive) {
                // Sim
                $btnArchive = form_button(
                    [
                        'data-id' => $advert->id,
                        'id'      => 'btnArchiveAdvert', //ID do html element
                        'class'   => 'dropdown-item'
                    ],
                    lang('App.btn_archived')
                );
            }

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
            
            // O botão é para ser exibido e o anúncio está publicado?
            if($showBtnViewAdvert && $advert->is_published) {

                // Sim...podemos montar o botão ação

                $routeToViewAdvert = route_to('adverts.details', $advert->code);

                $btnViewAdvert = form_button(
                    [
                        'class'   => 'dropdown-item',
                        'onClick' => "window.open('{$routeToViewAdvert}', '_blank')",
                    ],
                    lang('Adverts.btn_view_advert')
                );

            }
            
            // O botão é para ser exibido e o anúncio está publicado?
            if($showBtnQuestion && $advert->is_published) {

                // Sim...podemos montar o botão ação

                $finalRouteToEditQuestions = route_to($baseRouteToQuestions, $advert->code);

                $btnViewQuestions = form_button(
                    [
                        'class'   => 'dropdown-item',
                        'onClick' => "location.href='{$finalRouteToEditQuestions}'"
                    ],
                    lang('Adverts.btn_view_questions')
                );

            }

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

            // Criamos as opções de botões(ações)
            $btnActions .= $btnEdit;
            $btnActions .= $btnEditImages;

            // O botão é para ser exibido e o anúncio está publicado?
            if($showBtnViewAdvert && $advert->is_published) {

                // Sim...podemos montar o botão ação
                $btnActions .= $btnViewAdvert;
            }

            // O botão é para ser exibido e o anúncio está publicado?
            if($showBtnQuestion && $advert->is_published) {

                // Sim...podemos montar o botão ação
                $btnActions .= $btnViewQuestions;
                
            }

            // É para exibir o botão?
            if($showBtnArchive) {
                // Sim
                $btnActions .= $btnArchive;
            }

            $btnActions .= '</div>'; //fechamento da div do dropdown-menu

            $btnActions .= '</div>'; //fechamento da div do dropdown

            $data[] = [
                'image'             => $advert->image(classImage: 'card-img-top img-custom', sizeImage: $sizeImage),
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

    public function getArchivedAdverts(
        bool $showBtnRecover    = true,
        string $classBtnAction  = '', 
        string $classBtnRecover = '',
        string $classBtnDelete  = '',
    ): array {

        $adverts = $this->advertModel->getAllAdverts(onlyDeleted: true);

        $data = [];

        $btnRecover = '' ;

        foreach($adverts as $advert) {

            // É para exibir o botão?
            if($showBtnRecover) {
                $btnRecover = form_button(
                    [
                        'data-id' => $advert->id,
                        'id'      => 'btnRecoverAdvert', //ID do html element
                        'class'   => 'dropdown-item'
                    ],
                    lang('App.btn_recover')
                );
            }

            $btnDelete = form_button(
                [
                    'data-id' => $advert->id,
                    'id'      => 'btnDeleteAdvert', //ID do html element
                    'class'   => 'dropdown-item'
                ],
                lang('App.btn_delete')
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

            // Criamos as opções de botões(ações)
            $btnActions .= $btnRecover;
            $btnActions .= $btnDelete;


            $btnActions .= '</div>'; //fechamento da div do dropdown-menu

            $btnActions .= '</div>'; //fechamento da div do dropdown

            $data[] = [
                'title'             => $advert->title,
                'code'              => $advert->code,
                'actions'           => $btnActions,
            ];
        }
        return $data;
    }

    public function getAdvertByID(int $id, bool $withDeleted = false)
    {
        $advert = $this->advertModel->getAdvertByID($id, $withDeleted);

        if(is_null($advert)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Advert not found');
        }

        return $advert;
    }

    public function getDropdownSituations(string $advertSituation = null)
    {
        $options   = [];
        $selected  = [];

        $options = [
            ''                     =>lang('Adverts.label_situation'), //option vazio
            self::SITUATION_NEW    =>lang('Adverts.text_new'), 
            self::SITUATION_USED   =>lang('Adverts.text_used'),
        ];

        // Estamos criando ou editando um anúncio
        if(is_null($advertSituation)) {

            // Estamos criando...

            return form_dropdown('situation', $options, $selected, ['class' => 'form-control']);
        }

        //Estamos editando um anúncio...

        $selected[] = match($advertSituation) {
            self::SITUATION_NEW     => self::SITUATION_NEW,
            self::SITUATION_USED    => self::SITUATION_USED,
            default                 => throw new \Exception("Unsupported {$advertSituation}"),
        };

        return form_dropdown('situation', $options, $selected, ['class' => 'form-control']);
    }

    public function trySaveAdvert(Advert $advert, bool $protect = true, bool $notifyUserIfPublished = false)
    {

        try {
            
            $advert->unsetAuxiliaryAttributes();

            if($advert->hasChanged()) {
                $this->advertModel->trySaveAdvert($advert, $protect);

                $this->fireAdvertEvents($advert, $notifyUserIfPublished);
            }

        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);

            die('Error saving data');
        }
    }

    public function tryStoreAdvertImages(array $images, int $advertID) 
    {

        try {
            $advert = $this->getAdvertByID($advertID);

            $dataImages = ImageService::storeImages($images, 'adverts', 'advert_id', $advert->id);
        
            $this->advertModel->tryStoreAdvertImages($dataImages, $advert->id);

            $this->fireAdvertEventForNewImages($advert);
        } catch (\Exception $e) {
           die('Error deleting data');
        }

    }

    public function tryDeleteAdvertImage(int $advertID, string $image) 
    {

        try {
            $advert = $this->getAdvertByID($advertID);
        
            $this->advertModel->tryDeleteAdvertImage($advert->id, $image);

            ImageService::destroyImage('adverts', $image);
        } catch (\Exception $e) {
            die('Error deleting data');
        }

    }

    public function tryArchiveAdvert(int $advertID) 
    {

        try {

            $advert = $this->getAdvertByID($advertID);
            
            $this->advertModel->tryArchiveAdvert($advert->id);
        
        } catch (\Exception $e) {
            die('Error archiving data');
        }

    }

    public function tryRecoverAdvert(int $advertID) 
    {
        try {

            $advert = $this->getAdvertByID($advertID, withDeleted: true);

            $advert->recover();

            $this->trySaveAdvert($advert, protect: false);
        
        } catch (\Exception $e) {
            die('Error recovering data');
        }

    }

    public function tryDeleteAdvert(int $advertID)
    {
        try {

            $advert = $this->getAdvertByID($advertID, withDeleted: true);
            
            $this->advertModel->tryDeleteAdvert($advert->id);
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);

            die('Error deleting data');
        }
    }


    private function fireAdvertEvents(Advert $advert, bool $notifyUserIfPublished)
    {
        // Se estiver sendo editado, então o email já possui valor quando da recuperação do mesmo da base.
        // Se não tem valor, então estamos criando novo anúncio, portanti, recebe o e-mail do user logado.
        $advert->email = !empty($advert->email) ? $advert->email : $this->user->email;

        if($advert->hasChanged('title') || $advert->hasChanged('description')) {
            Events::trigger('nofity_user_advert', $advert->email, "Estamos analisando o seu anúncio {$advert->code}, aguarde...");
            Events::trigger('nofity_manager', "Existem anúncios para serem auditados.");
        }
        /**
         * @todo notificar o usuário/anunciante de que o anúncio foi publicado
         */
    }
    private function fireAdvertEventForNewImages(Advert $advert)
    {
        // Se estiver sendo editado, então o email já possui valor quando da recuperação do mesmo da base.
        // Se não tem valor, então estamos criando novo anúncio, portanti, recebe o e-mail do user logado.
        $advert->email = !empty($advert->email) ? $advert->email : $this->user->email;

        Events::trigger('nofity_user_advert', $advert->email, "Estamos analisando as novas imagens do seu anúncio {$advert->code}, aguarde...");
        Events::trigger('nofity_manager', "Existem anúncios para serem auditados, novas imagens foram inseridas...");
    }
}
