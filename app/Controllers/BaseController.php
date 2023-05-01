<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    protected $locale;


    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['form', 'number', 'superadmin'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
        $this->setUpLanguageOptions($request);
    }

    protected function removeSpoofingFromRequest(): array
    {

        $data = $this->request->getPost();

        unset($data['id']);

        unset($data['_method']);



        return $data;

    }

    private function setUpLanguageOptions($request) 
    {
        $this->locale = $request->getLocale();

        $view = Service('renderer');
        $view->setVar('locale', $this->locale);

        // Criamos as opções de URL para os idiomas suportados
        $urls = [
            'url_en'    => site_url($request->uri->setSegment(1, 'en')),
            'url_es'    => site_url($request->uri->setSegment(1, 'es')),
            'url_pt_br' => site_url($request->uri->setSegment(1, 'pt-BR')),
        ];

        // Voltamos para o original 
        $request->uri->setSegment(1, $this->locale);

        $view->setVar('urls', (object) $urls);

        helper('html');

        $language = match($this->locale){
            'en'    => img('language/english.png').' English',
            'es'    => img('language/espanha.png').' Españhol',
            default => img('language/brasil.png').' Potuguês Brasil',
        };

        $view->setVar('language', $language);
    }
}
