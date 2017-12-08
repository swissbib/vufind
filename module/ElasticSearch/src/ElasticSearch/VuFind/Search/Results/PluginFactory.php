<?php
namespace ElasticSearch\VuFind\Search\Results;

/**
 *
 * @category linked-swissbib
 * @package  Search_Results
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */
class PluginFactory extends \VuFind\Search\Results\PluginFactory
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->defaultNamespace = 'ElasticSearch\VuFind\Search';
        $this->classSuffix = '\Results';
    }
}
