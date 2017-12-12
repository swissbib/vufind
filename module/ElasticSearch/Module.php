<?php
namespace ElasticSearch;

/**
 *
 * @category linked-swissbib
 * @package  /
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */
use Zend\ModuleManager\Feature\ConfigProviderInterface as Configurable;

class Module implements Configurable
{
    /**
     * @return    Array|mixed|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return    Array
     */
    public function getAutoloaderConfig()
    {

            return [
                'Zend\Loader\StandardAutoloader' => [
                    'namespaces' => [
                        __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    ],
                ],
            ];

    }

}
