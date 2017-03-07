<?php
/**
 * Swissbib MyResearchController
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Controller;

use VuFind\Exception\ILS;
use VuFind\ILS\Driver\AlephRestfulException;
use VuFindSearch\Service;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\ViewModel,
    Zend\Http\Response as HttpResponse,
    VuFind\Controller\MyResearchController as VuFindMyResearchController,
    VuFind\Db\Row\User,
    Swissbib\VuFind\ILS\Driver\Aleph,
    Zend\Session\Container as SessionContainer;

use VuFind\Exception\ListPermission as ListPermissionException,
    Zend\Stdlib\Parameters;

use Zend\Uri\UriFactory;

/**
 * Swissbib MyResearchController
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class MyResearchController extends VuFindMyResearchController
{
    /**
     * Show photo copy requests
     *
     * @return ViewModel
     */
    public function photocopiesAction()
    {
        // Stop now if the user does not have valid catalog credentials available:
        if (!is_array($patron = $this->catalogLogin())) {
            return $patron;
        }

        /**
         * Aleph
         *
         * @var Aleph $catalog
         */
        $catalog = $this->getILS();

        // Get photo copies details:
        $photoCopies = $catalog->getPhotocopies($patron['id']);

        return $this->createViewModel(['photoCopies' => $photoCopies]);
    }

    /**
     * Get bookings
     *
     * @return ViewModel
     */
    public function bookingsAction()
    {
        // Stop now if the user does not have valid catalog credentials available:
        if (!is_array($patron = $this->catalogLogin())) {
            return $patron;
        }

        /**
         * Aleph
         *
         * @var Aleph $catalog
         */
        $catalog = $this->getILS();

        // Get photo copies details:
        $bookings = $catalog->getBookings($patron['id']);

        return $this->createViewModel(['bookings' => $bookings]);
    }

    /**
     * Get location parameter from route
     *
     * @return String|Boolean
     */
    protected function getLocationFromRoute()
    {
        return $this->params()->fromRoute('location', false);
    }

    /**
     * Inject location from route
     *
     * @param null $params Parameters
     *
     * @return ViewModel
     */
    protected function createViewModel($params = null)
    {
        $viewModel = parent::createViewModel($params);
        $viewModel->location = $this->getLocationFromRoute() ?: 'baselbern';

        return $viewModel;
    }

    /**
     * (local) Search User Settings
     *
     * @return mixed
     */
    public function settingsAction()
    {
        $account = $this->getAuthManager();

        if ($account->isLoggedIn() == false) {
            return $this->forceLogin();
        }

        /**
         * User
         *
         * @var User $user
         */
        $user = $this->getUser();

        if ($this->getRequest()->isPost()
            && $this->params()->fromPost(
                'myResearchSettingsForm'
            )
        ) {
            $language = $this->params()->fromPost('language');
            $maxHits = $this->params()->fromPost('max_hits');
            $defaultSort = $this->params()->fromPost('default_sort');

            $user->language = trim($language);
            $user->max_hits = intval($maxHits);
            $user->default_sort = serialize($defaultSort);

            $user->save();

            $this->flashMessenger()->addMessage(
                'save_settings_success', 'success'
            );

            setcookie('language', $language, time() + 3600 * 24 * 100, '/');

            return $this->redirect()->toRoute('myresearch-settings');
        }

        $serviceManager = $this->event->getApplication()->getServiceManager();

        $defaultSort = unserialize($user->default_sort);
        $sortOptions = $this->getSortOptions($serviceManager, $defaultSort);

        $language = $user->language;
        $maxHits = $user->max_hits;

        return new ViewModel(
            [
                'max_hits' => $maxHits,
                'language' => $language,
                'optsLanguage' => [
                    'de' => 'Deutsch',
                    'en' => 'English',
                    'fr' => 'Francais',
                    'it' => 'Italiano'
                ],
                'optsMaxHits' => [
                    10, 20, 40, 60, 80, 100
                ],
                'defaultSort' => $sortOptions
            ]
        );
    }

    /**
     * Creates View snippet to provide users more information
     * about the multi accounts in swissbib
     *
     * @return ViewModel
     */
    public function backgroundaccountsAction()
    {
        return $this->createViewModel();
    }

    /**
     * Convenience method to get a session initiator URL. Returns false if not
     * applicable.
     * what does "not applicable" mean:
     * for me (GH) it makes no sense to create a session initiator instance in
     * case we are within the normal workflow of the application
     * (no authentication procedure in conjunction with shibboleth authentication
     * took place) at the moment I compare the domain strings to decide if we should
     * create a session initiator because an authentication with shibboleth tool
     * place another possibilty might be to test the Sibboleth.sso/Session response
     * at the moment we have to issues:
     * a) why redirect prefix in apache session variables?
     * b) access to the shibboleth session variables is only possible immediately
     * after shibboleth authentication process - why?
     * question are pending at switch
     *
     * @return string|bool
     */
    protected function getSessionInitiator()
    {
        $uri = $this->getRequest()->getUri();
        $base = sprintf('%s://%s', $uri->getScheme(), $uri->getHost());
        $baseEscaped = str_replace("/", "\/", $base);

        if (preg_match(
            "/$baseEscaped/",
            $this->getRequest()->getServer()->get('HTTP_REFERER')
        ) == 0
        ) {
            $url = $this->getServerUrl('myresearch-home');

            return $this->getAuthManager()->getSessionInitiator($url);
        } else {
            return false;
        }
    }

    /**
     * Login Action
     * Need to overwrite because of a special handling for Shibboleth workflow
     *
     * @return mixed
     */
    public function loginAction()
    {
        //we need to differantiate between Shibboleh and not Shibboleth
        // authentication mechanisms
        //in case of Shibboleth we will get a problem with HTTP_Referer after
        // successful authentication at IDP
        //because then the Referer points to the IDP address instead of a valid
        // VuFind resource (often something like save a record in various contexts)
        //therefor this mechanisms where we store a temporary session for the latest
        // Referer before the IDP request is executed in the next step by the user
        //at the moment it is used in Swissbib/Controller/RecordController
        $clazz = $this->getAuthManager()->getAuthClassForTemplateRendering();
        if ($clazz == "Swissbib\\VuFind\\Auth\\Shibboleth") {
            //store the current referrer into a special Session
            $followup = new SessionContainer('ShibbolethSaveFollowup');
            //$tURL = $this->getRequest()->getServer()->get('HTTP_REFERER');
            $tURL = $this->getRequest()->getServer()->get('REQUEST_URI');

            //$this->getRequest()->getServer()->set('HTTP_REFERER',
            //   $this->getRequest()->getServer()->get('REDIRECT_URL'));
            //foreach ($this->getRequest()->getServer() as $key => $value)
            //{
            //    $test = "";
            //}

            $followup->url = $tURL;
        }

        // If this authentication method doesn't use a VuFind-generated login
        // form, force it through:
        if ($this->getSessionInitiator()) {
            // Don't get stuck in an infinite loop -- if processLogin is already
            // set, it probably means Home action is forwarding back here to
            // report an error!
            //
            // Also don't attempt to process a login that hasn't happened yet;
            // if we've just been forced here from another page, we need the user
            // to click the session initiator link before anything can happen.
            //
            // Finally, we don't want to auto-forward if we're in a lightbox, since
            // it may cause weird behavior -- better to display an error there!
            if (!$this->params()->fromPost('processLogin', false)
                && !$this->params()->fromPost('forcingLogin', false)
            ) {
                $this->getRequest()->getPost()->set('processLogin', true);

                return $this->forwardTo('MyResearch', 'Home');
            }
        }

        // Make request available to view for form updating:
        $view = $this->createViewModel();
        $view->request = $this->getRequest()->getPost();

        return $view;
    }

    /**
     * Logout
     *
     * @return HttpResponse
     */
    public function logoutAction()
    {
        $config = $this->getConfig();
        if (isset($config->Site->logOutRoute)) {
            $logoutTarget = $this->getServerUrl($config->Site->logOutRoute);
        } else {
            $logoutTarget = $this->getRequest()->getServer()->get('HTTP_REFERER');
            if (empty($logoutTarget)) {
                $logoutTarget = $this->getServerUrl('home');
            }

            // If there is an auth_method parameter in the query, we should strip
            // it out. Otherwise, the user may get stuck in an infinite loop of
            // logging out and getting logged back in when using environment-based
            // authentication methods like Shibboleth.
            $logoutTarget = preg_replace(
                '/([?&])auth_method=[^&]*&?/', '$1', $logoutTarget
            );
            $logoutTarget = rtrim($logoutTarget, '?');
        }

        if (count(preg_grep('/Search\/Results|Summon\/Search/', [$logoutTarget])) > 0
        ) {
            //GH: It might happen (depends on context) that limit and sort query
            // parameter are still
            //part of the former URL when user called the logout function
            // (logoutTarget) and contains sort
            // or limit parameter customized by the user. This is not desired
            // especially at access points in the public space
            //But we have to be careful: we should append additional default
            // parameters only for Solr or Summon
            // search Routes
            $solrResultsManager = $this->getServiceLocator()
                ->get('VuFind\SearchResultsPluginManager')->get('Solr');
            $options = $solrResultsManager->getParams()->getOptions();
            $defaultSort = $options->getDefaultSortByHandler();
            $defaultLimit = $options->getDefaultLimit();
            $logoutTarget .= '&limit=' . $defaultLimit . '&sort=' . $defaultSort;

        }

        return $this->redirect()
            ->toUrl($this->getAuthManager()->logout($logoutTarget));
    }

    /**
     * Store a referer (if appropriate) to keep post-login redirect pointing
     * to an appropriate location.
     *
     * @return void
     */
    protected function storeRefererForPostLoginRedirect()
    {
        // Get the referer -- if it's empty, there's nothing to store!
        $referer = $this->getRequest()->getServer()->get('HTTP_REFERER');
        if (empty($referer)) {
            return;
        }

        // Normalize the referer URL so that inconsistencies in protocol
        // and trailing slashes do not break comparisons; this same normalization
        // is applied to all URLs examined below.
        $refererNorm = trim(end(explode('://', $referer, 2)), '/');

        // If the referer lives outside of VuFind, don't store it! We only
        // want internal post-login redirects.
        $clazz = $this->getAuthManager()->getAuthClass();
        if ($clazz === "VuFind\\Auth\\ILS") {
            //tests were done with referrers from outside and inside
            //$referer = "http://www.woz.ch/diesunddas"; // -> not stored
            //$referer = "http://sb-vf1.swissbib.unibas.ch"; // -> stored
            //$referer = "http://test.swissbib.ch"; // -> stored
            //$referer = "http://baselbern.swissbib.ch"; // -> stored

            //I guess we should use only the scheme (hostname) because the whole URL
            //something like this: http://localhost/vufind/Record/304410349/
            //HierarchyTree?hierarchy=125488483&recordID=304410349
            //could contain the searched pattern with no intent
            // (especially webpages from UB Basel)
            $uri = UriFactory::factory($referer);
            $scheme = $uri->getHost();

            //hosts running VuFind are labeled similar to
            //test.swissbib.ch || sb-vf1.swissbib.unibas.ch ..
            //these links could be defined via configuration once the
            // "Bestellvorgang" - seems to be a monster -  is stable
            // (I guess this won't happen in the future...)
            $matches = array_filter(
                ["/swissbib\.?.*?\.ch/", "/localhost/"],
                function ($pattern) use ($scheme) {
                    $matched = preg_match($pattern, $scheme);

                    return $matched == 1 ? true : false;
                }
            );
            if (count($matches) == 0) {
                //referrer doesn't match against a "friendly" domain
                //so it has to be a link from outside of the VuFind world
                // which we don't store for later use
                return;
            }
        }

        // If we got this far, we want to store the referer:
        $this->followup()->store([], $referer);
    }

    /**
     * Sort Options
     *
     * @param ServiceManager $serviceManager Service Manager
     * @param Array          $defaultSort    Default sorting
     *
     * @return Array
     */
    protected function getSortOptions(ServiceManager $serviceManager, $defaultSort)
    {
        $sortOptions = [];
        $searchTabs = $this->getConfig()->get('SearchTabs');
        $searchOptionsPluginManager = $serviceManager
            ->get('VuFind\SearchOptionsPluginManager');

        if (!$searchTabs->count()) {
            $config = $this->getConfig()->get('Index');
            $sortOptions[] = [
                'options' => $searchOptionsPluginManager
                    ->get('solr')->getSortOptions(),
                'engine' => 'solr',
                'selected' => isset($defaultSort['solr']) ? $defaultSort['solr'] : ''
            ];

            return $sortOptions;
        }

        foreach ($searchTabs as $searchTabEngine => $searchTabLabel) {
            $sortOptions[] = [
                'engine' => $searchTabEngine,
                'options' => $searchOptionsPluginManager->get($searchTabEngine)
                    ->getSortOptions(),
                'label' => $searchTabLabel,
                'selected' => $defaultSort[$searchTabEngine]
            ];
        }

        return $sortOptions;
    }

    /**
     * Action to change address
     *
     * @return ViewModel
     */
    public function changeAddressAction()
    {
        if (!is_array($patron = $this->catalogLogin())) {
            return $patron;
        }

        $addressForm = $this->serviceLocator
            ->get('Swissbib\MyResearch\Form\AddressForm');

        try {
            if ($this->request->isPost()
                && $this->request->getPost('form-name') === 'changeaddress'
            ) {
                $addressForm->setData($this->request->getPost());

                if ($addressForm->isValid()) {
                    $address = $this->getILS()->getMyAddress($patron);
                    $newAddress = $addressForm->getData();
                    //make sure nobody changes his name
                    $newAddress['z304-address-1'] = $address['z304-address-1'];
                    $newAddress['z304-date-from']
                        = $address['z304-date-from'] === '00000000' ?
                        date('Ymd') : $address['z304-date-from'];
                    $newAddress['z304-date-to']
                        = $address['z304-date-to'] === '00000000'
                        ?
                        date('Ymd', strtotime('+10 years'))
                        :
                        $address['z304-date-to'];

                    $this->getILS()->changeMyAddress($patron, $newAddress);
                    $this->flashMessenger()
                        ->addMessage('save_address_success', 'success');
                } else {
                    $this->flashMessenger()
                        ->addMessage('save_address_error', 'error');
                }
            } else {
                $addressForm->setData($this->getILS()->getMyAddress($patron));
            }
        } catch (AlephRestfulException $e) {
            $this->flashMessenger()
                ->addMessage('address_error', 'error');
        } catch (ILS $e) {
            $this->flashMessenger()
                ->addMessage('address_error', 'error');

            return $this->createViewModel();
        }

        return $this->createViewModel(
            [
                'form' => $addressForm
            ]
        );
    }

    /**
     * Check if we are in lightbox mode such a method was removed by the core
     * we want to exclude Lightbox with Shibboleth authentication
     * 
     * @return boolean
     */
    protected function checkInLightbox()
    {
        return ($this->getRequest()->getQuery('layout', 'no') === 'lightbox'
            || 'layout/lightbox' == $this->layout()->getTemplate()
        );
    }
}
