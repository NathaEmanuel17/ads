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

        $this->user = service('auth')->user() ?? auth('api')->user();

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

        foreach ($adverts as $advert) {

            // É para exibir o botão?
            if ($showBtnArchive) {
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
            if ($showBtnViewAdvert && $advert->is_published) {

                // Sim...podemos montar o botão ação

                $routeToViewAdvert = route_to('adverts.detail', $advert->code);

                $btnViewAdvert = form_button(
                    [
                        'class'   => 'dropdown-item',
                        'onClick' => "window.open('{$routeToViewAdvert}', '_blank')",
                    ],
                    lang('Adverts.btn_view_advert')
                );
            }

            // O botão é para ser exibido e o anúncio está publicado?
            if ($showBtnQuestion && $advert->is_published) {

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
            if ($showBtnViewAdvert && $advert->is_published) {

                // Sim...podemos montar o botão ação
                $btnActions .= $btnViewAdvert;
            }

            // O botão é para ser exibido e o anúncio está publicado?
            if ($showBtnQuestion && $advert->is_published) {

                // Sim...podemos montar o botão ação
                $btnActions .= $btnViewQuestions;
            }

            // É para exibir o botão?
            if ($showBtnArchive) {
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

        $btnRecover = '';

        foreach ($adverts as $advert) {

            // É para exibir o botão?
            if ($showBtnRecover) {
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

        if (is_null($advert)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Advert not found');
        }

        return $advert;
    }

    public function getDropdownSituations(string $advertSituation = null)
    {
        $options   = [];
        $selected  = [];

        $options = [
            ''                     => lang('Adverts.label_situation'), //option vazio
            self::SITUATION_NEW    => lang('Adverts.text_new'),
            self::SITUATION_USED   => lang('Adverts.text_used'),
        ];

        // Estamos criando ou editando um anúncio
        if (is_null($advertSituation)) {

            // Estamos criando...

            return form_dropdown('situation', $options, $selected, ['class' => 'form-control']);
        }

        //Estamos editando um anúncio...

        $selected[] = match ($advertSituation) {
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

            if ($advert->hasChanged()) {
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

    public function tryDeleteAdvert(int $advertID, bool $wantValidadeAdvert = true)
    {
        try {

            if ($wantValidadeAdvert) {

                $advert = $this->getAdvertByID($advertID, withDeleted: true);
                $this->advertModel->tryDeleteAdvert($advert->id);
                return true;
            }

            $this->advertModel->tryDeleteAdvert($advertID);
            return true;
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);

            die('Error deleting data');
        }
    }

    public function getAllAdvertsPaginated(int $perPage = 10, $criteria = []): array
    {
        return [
            'adverts' => $this->advertModel->getAllAdvertsPaginated($perPage, $criteria),
            'pager'   => $this->advertModel->pager
        ];
    }

    public function getAdvertByCode(string $code, bool $ofTheLoggedInUser = false)
    {
        $advert = $this->advertModel->getAdvertByCode($code, $ofTheLoggedInUser);

        if (is_null($advert)) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Advert not found');
        }

        return $advert;
    }

    public function getCitiesFromPublishedAdevrts(int $limit = 5, string $categorySlug = null): array
    {
        return $this->advertModel->getCitiesFromPublishedAdevrts($limit, $categorySlug);
    }

    public function tryInsertAdvertQuestion(Advert $advert, string $question)
    {
        try {
            $this->advertModel->insertAdvertQuestion($advert->id, $question);

            session()->remove('ask');

            $this->fireAdvertNewQuestion($advert);
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);

            die('Erro ao realizar a pergunta');
        }
    }

    public function tryAnswerAdvertQuestion(int $questionID, Advert $advert, object $request)
    {
        try {

            $this->advertModel->answerAdvertQuestion(questionID: $questionID, advertID: $advert->id, answer: $request->answer);

            $this->fireAdvertQuestionHasBeenAnswerd($advert, $request->question_owner);
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);

            die('Erro ao realizar a pergunta');
        }
    }

    public function getAllAdvertsByTerm(string $term): array
    {
        $adverts = $this->advertModel->getAllAdvertsByTerm($term);

        $data = [];

        foreach ($adverts as $advert) {

            $data[] = [
                'code'  => $advert->code,
                'value' => $advert->title,
                'label' => $advert->image(classImage: 'image-autocomplete', sizeImage: 'small') . ' ' . $advert->title,
            ];
        }

        return $data;
    }

    public function getAllAdvertsForUserAPI(int $perPage = null, int $page = null) : array
    {
        $adverts = $this->advertModel->getAllAdvertsForUserAPI($perPage, $page);
        $pager   = (!empty($adverts) ? $this->advertModel->pager->getDetails() : []);

        if (empty($adverts)) {

            // O anunciante logado possiui algum anúncio?
            return [
                'adverts' => [],
                'pager'   => $pager
            ];
        }

        $data = [];

        foreach ($adverts as $advert) {

            $data[] = [
                'id'              => $advert->id,
                'belongs_to'      => $advert->username,
                'images'          => $advert->image(),
                'title'           => $advert->title,
                'code'            => $advert->code,
                'price'           => $advert->price,
                'category'        => $advert->category,
                'category_id'     => $advert->category_id,
                'category_slug'   => $advert->category_slug,
                'is_published'    => $advert->is_published,
                'address'         => $advert->address(),
                'created_at'      => $advert->created_at,
                'updated_at'      => $advert->updated_at,
            ];
        }

        return [
            'adverts' => $data,
            'pager'   => $pager
        ];
    }

    ////-----------------Métodos privados-----------------////

    private function fireAdvertEvents(Advert $advert, bool $notifyUserIfPublished)
    {
        // Se estiver sendo editado, então o email já possui valor quando da recuperação do mesmo da base.
        // Se não tem valor, então estamos criando novo anúncio, portanti, recebe o e-mail do user logado.
        $advert->email = !empty($advert->email) ? $advert->email : $this->user->email;

        if ($advert->hasChanged('title') || $advert->hasChanged('description')) {
            Events::trigger('nofity_user_advert', $advert->email, "Estamos analisando o seu anúncio {$advert->code}, aguarde...");
            Events::trigger('nofity_manager', "Existem anúncios para serem auditados.");
        }

        if ($notifyUserIfPublished) {

            $this->fireAdvertPublished($advert);
        }
    }

    private function fireAdvertEventForNewImages(Advert $advert)
    {
        // Se estiver sendo editado, então o email já possui valor quando da recuperação do mesmo da base.
        // Se não tem valor, então estamos criando novo anúncio, portanti, recebe o e-mail do user logado.
        $advert->email = !empty($advert->email) ? $advert->email : $this->user->email;

        Events::trigger('nofity_user_advert', $advert->email, "Estamos analisando as novas imagens do seu anúncio {$advert->code}, aguarde...");
        Events::trigger('nofity_manager', "Existem anúncios para serem auditados, novas imagens foram inseridas...");
    }

    private function fireAdvertNewQuestion(Advert $advert)
    {
        Events::trigger('nofity_user_advert', $advert->email, "seu anúncio {$advert->title}, tem uma nova pergunta...");
    }

    private function fireAdvertQuestionHasBeenAnswerd(Advert $advert, int $userQuestionID)
    {
        $userWhoAskedQuestion = Factories::class(UserService::class)->getUserBycriteria(['id' => $userQuestionID]);

        Events::trigger('nofity_user_advert', $userWhoAskedQuestion->email, "A pergunta que você fez para o anuncio  {$advert->title}, foi respondida...");
    }

    private function fireAdvertPublished(Advert $advert)
    {
        if ($advert->weMustNotifyThePublication()) {

            Events::trigger('nofity_user_advert', $advert->email, "Seu anúncio {$advert->title} foi publicado...");
        }
    }
}
