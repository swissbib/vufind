<?php

namespace Swissbib\Command\HierarchyCache;

class HierarchyCacheFactory implements \Laminas\ServiceManager\Factory\FactoryInterface
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
    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        $solrResults = $container->get('VuFind\SearchResultsPluginManager')->get('Solr');
        $recordLoader = $container->get('VuFind\RecordLoader');

        return new $requestedName($solrResults, $recordLoader);
    }
}
