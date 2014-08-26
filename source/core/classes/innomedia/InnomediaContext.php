<?php
/**
 * Innomedia
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @copyright  2008-2014 Innoteam Srl
 * @license    http://www.innomatic.org/license/   BSD License
 * @link       http://www.innomatic.org
 * @since      Class available since Release 1.0.0
 */
namespace Innomedia;

/**
 *
 * @author Alex Pagnoni <alex.pagnoni@innoteam.it>
 * @copyright Copyright 2008-2013 Innoteam Srl
 * @since 1.0
 */
class InnomediaContext extends \Innomatic\Util\Singleton
{

    protected $home;

    protected $request;

    protected $response;
    // protected $user;
    protected $session;

    protected $locales = array();

    protected $registeredAjaxCalls = array();

    protected $registeredAjaxSetupCalls = array();

    public function ___construct($home, \Innomatic\Webapp\WebAppRequest $request, \Innomatic\Webapp\WebAppResponse $response)
    {
        $this->home = realpath($home) . '/';
        $this->request = $request;
        $this->response = $response;
        $this->session = new \Innomatic\Php\PHPSession();
        $this->session->start();
        
        // Sets 'session.gc_maxlifetime' and 'session.cookie_lifetime' to the value
        // defined by the 'sessionLifetime' parameter in web.xml
        $lifetime = \Innomatic\Webapp\WebAppContainer::instance('\Innomatic\Webapp\WebAppContainer')->getCurrentWebApp()->getInitParameter('sessionLifetime');
        if ($lifetime !== false) {
            $this->session->setLifeTime($lifetime);
        }
        
        // Process the request
        $this->process();
    }

    public function getHome()
    {
        return $this->home;
    }

    public function getThemesHome()
    {
        return $this->home . 'shared/themes/';
    }

    public function getModulesHome()
    {
        return $this->home . 'core/innomedia/modules/';
    }

    public function getBlocksHome($module)
    {
        return $this->home . 'core/innomedia/modules/' . $module . '/blocks/';
    }

    public function getPagesHome($module)
    {
        return $this->home . 'core/innomedia/modules/' . $module . '/pages/';
    }

    /**
     * Gets the webapp request object.
     *
     * @return WebAppRequest
     * @since 5.1
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Gets the webapp response object.
     *
     * @return WebAppResponse
     * @since 5.1
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function getSession()
    {
        return $this->session;
    }

    /**
     * Gets the list of allowed locales.
     *
     * Returns the list of the allowed locales.
     *
     * @return array
     * @since 5.1
     */
    public function getLocales()
    {
        return $this->locales;
    }

    public function getModulesList()
    {
        $list = array();
        if ($dh = opendir($this->home . 'core/innomedia/modules')) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' and $file != '..' and is_dir($this->home . 'core/innomedia/modules/' . $file)) {
                    $list[] = $file;
                }
            }
            closedir($dh);
        }
        return $list;
    }

    public function getThemesList()
    {
        $list = array();
        if ($dh = opendir($this->home . 'shared/themes')) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' and $file != '..' and is_dir($this->home . 'shared/themes/' . $file) and file_exists($this->home . 'shared/themes/' . $file . '/grid.tpl.php')) {
                    $list[] = $file;
                }
            }
            closedir($dh);
        }
        return $list;
    }

    /**
     * Imports a module.
     *
     * Adds module's classes to include_path
     *
     * @param string $module
     *            Module name.
     * @return void
     * @since 5.1
     */
    public function importModule($module)
    {
        if (is_dir($this->getModulesHome() . $module . '/classes/')) {
            set_include_path(get_include_path() . ':' . $this->getModulesHome() . $module . '/classes/');
        }
    }

    /**
     * Process and initializes the context.
     *
     * @return void
     * @since 5.1
     */
    private function process()
    {
        // Checks if the locale has been passed as parameter
        if ($this->request->parameterExists('innomedia_setlocale')) {
            // Stores the locale into the session
            $this->session->put('innomedia_locale', $this->request->getParameter('innomedia_setlocale'));
        }
        
        // Retrieves the locale from the session, if set
        if ($this->session->isValid('innomedia_locale')) {
            $this->locales[] = $this->session->get('innomedia_locale');
        }
        
        // Adds the locales supported by the web agent
        $this->locales = array_merge($this->locales, $this->request->getLocales());
    }

    public function registerAjaxCall($callName)
    {
        $this->registeredAjaxCalls[$callName] = true;
    }

    public function getRegisteredAjaxCalls()
    {
        return $this->registeredAjaxCalls;
    }

    public function isRegisteredAjaxCall($callName)
    {
        return isset($this->registeredAjaxCalls[$callName]);
    }

    public function unregisterAjaxCall($callName)
    {
        if (isset($this->registeredAjaxCalls[$callName])) {
            unset($this->registeredAjaxCalls[$callName]);
        }
    }

    public function countRegisteredAjaxCalls()
    {
        return count($this->registeredAjaxCalls);
    }

    public function registerAjaxSetupCall($call)
    {
        $this->registeredAjaxSetupCalls[] = $call;
    }

    public function getRegisteredAjaxSetupCalls()
    {
        return $this->registeredAjaxSetupCalls;
    }

    public function countRegisteredAjaxSetupCalls()
    {
        return count($this->registeredAjaxSetupCalls);
    }
}

?>