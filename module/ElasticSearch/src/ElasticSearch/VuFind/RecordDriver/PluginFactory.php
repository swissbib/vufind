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

class PluginFactory extends \VuFind\ServiceManager\AbstractPluginFactory
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->defaultNamespace = 'ElasticSearch\VuFind\RecordDriver';
    }
}