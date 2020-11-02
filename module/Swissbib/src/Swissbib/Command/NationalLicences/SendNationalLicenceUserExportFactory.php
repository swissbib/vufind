<?php

namespace Swissbib\Command\NationalLicences;

use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class SendNationalLicenceUserExportFactory
 *
 * @category Swissbib_VuFind
 * @package  Swissbib\Command\NationalLicences
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class SendNationalLicenceUserExportFactory implements FactoryInterface
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
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(
        \Interop\Container\ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $nationalLicenceService = $container->get('Swissbib\NationalLicenceService');
        return new $requestedName($nationalLicenceService);
    }


}

