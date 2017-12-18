<?php
/**
 * Record driver plugin factory
 *
 * @category LinkedSwissbib
 * @package  RecordDrivers
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:record_drivers Wiki
 */
namespace ElasticSearch\VuFind\RecordDriver;
use VuFind\RecordDriver\AbstractBase;
use Zend\ServiceManager\ConfigInterface;

/**
 * Class PluginManager
 * @package ElasticSearch\VuFind\RecordDriver
 */
class PluginManager extends \VuFind\ServiceManager\AbstractPluginManager
{
    const DEFAULT_RECORD = 'ElasticSearch';

    /**
     * Constructor
     *
     * @param ConfigInterface $configuration Configuration settings (optional)
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        // Record drivers are not meant to be shared -- every time we retrieve one,
        // we are building a brand new object.
        $this->setShareByDefault(false);

        parent::__construct($configuration);

    }

    /**
     * Return the name of the base class or interface that plug-ins must conform
     * to.
     *
     * @return string
     */
    protected function getExpectedInterface()
    {
        return 'VuFind\RecordDriver\AbstractBase';
    }

    /**
     * Convenience method to retrieve a populated Solr record driver.
     *
     * @param array $data Raw ElasticSearch data
     *
     * @return AbstractBase
     */
    public function getElasticSearchRecord($data)
    {
        if (isset($data['_type'])) {
            $key = 'ES' . ucwords($data['_type']);
            $recordType = $this->has($key) ? $key : self::DEFAULT_RECORD;
        } else {
            $recordType = self::DEFAULT_RECORD;
        }
        // Build the object:
        $driver = $this->get($recordType);
        $driver->setRawData($data);
        return $driver;
    }
}
