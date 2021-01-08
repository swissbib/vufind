<?php
/**
 * Browse helper factory.
*/
namespace SwissCollections\View\Helper\Root;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Browse helper factory.
 *
 * @category VuFind
 * @package  View_Helpers
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class BrowseFactory implements FactoryInterface
{
  /**
   * Create an object
   *
   * @param ContainerInterface $container     Service manager
   * @param string             $requestedName Service being created
   * @param null|array         $options       Extra options (optional)
   *
   * @return object
   *
   * @throws ServiceNotFoundException if unable to resolve the service.
   * @throws ServiceNotCreatedException if an exception is raised when
   * creating a service.
   * @throws ContainerException|\Exception if any other error occurs
   */
  public function __invoke(ContainerInterface $container, $requestedName,
                           array $options = null
  ) {
    if (!empty($options)) {
      throw new \Exception('Unexpected options sent to factory.');
    }
    $config = $container->get(\VuFind\Config\PluginManager::class)->get('config');
    $helper = new $requestedName($config);
    return $helper;
  }
}
