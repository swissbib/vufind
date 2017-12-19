<?php
/**
 * Params
 *
 * @category ElasticSearch
 * @package ElasticSearch
 * @author Christoph Böhm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://www.swissbib.ch/
 */
namespace ElasticSearch\VuFind\Search\ElasticSearch;

use VuFind\Search\Base\Params as BaseParams;
use VuFindSearch\Query\Query;

/**
 * This will contain the \VuFind\Search\Sample\Params class, which must extend \VuFind\Search\Base\Params. Unless you need to do special parameter processing or add new parameters not supported by the base class, you are not required to implement any methods here – you can just extend with an empty class. You'll probably end up adding methods here eventually, but for the initial implementation it is nice to leave this empty – one less thing to worry about!
 * Class Params
 *
 * @package ElasticSearch\VuFind\Search\ElasticSearch
 */
class Params extends BaseParams
{
    /**
     * @var String $index
     */
    private $index;

    /**
     * @var String $template
     */
    private $template;

    /**
     * @return String
     */
    public function getIndex(): String
    {
        return $this->index;
    }

    /**
     * @param String $index
     */
    public function setIndex(String $index)
    {
        $this->index = $index;
    }

    /**
     * @return String
     */
    public function getTemplate(): String
    {
        return $this->template;
    }

    /**
     * @param String $template
     */
    public function setTemplate(String $template)
    {
        $this->template = $template;
    }


    /**
     * From Solr/Params
     *
     * Initialize the object's search settings from a request object.
     *
     * @param \Zend\StdLib\Parameters $request Parameter object representing user
     * request.
     *
     * @return void
     */
    protected function initSearch($request)
    {
        // TODO Get default values from config
        $this->setIndex($request->get('index', 'lsb'));
        $this->setTemplate($request->get('template', 'id'));
        // Special case -- did we get a list of IDs instead of a standard query?
        $ids = $request->get('overrideIds', null);
        if (is_array($ids)) {
            // TODO Remove or overwrite overrideIds behaviour
//            $this->setQueryIDs($ids);
            $this->query->setHandler($request->get('type', null));
            $this->query->setString('[' . implode(",", $ids) . ']');
        } else {
            // Use standard initialization:
            parent::initSearch($request);
        }
    }

    /**
     * Override the normal search behavior with an explicit array of IDs that must
     * be retrieved.
     *
     * @param array $ids Record IDs to load
     *
     * @return void
     */
    public function setQueryIDs($ids)
    {
        // No need for spell checking or highlighting on an ID query!
        $this->getOptions()->spellcheckEnabled(false);
        $this->getOptions()->disableHighlighting();

        // Special case -- no IDs to set:
        if (empty($ids)) {
            return $this->setOverrideQuery('NOT *:*');
        }

        $callback = function ($i) {
            return '"' . addcslashes($i, '"') . '"';
        };
        $ids = array_map($callback, $ids);
        $this->setOverrideQuery('id:(' . implode(' OR ', $ids) . ')');
    }

    /**
     * Return search query object.
     *
     * @return VuFindSearch\Query\AbstractQuery
     */
    public function getQuery(): Query
    {
        if ($this->overrideQuery) {
            return new Query($this->overrideQuery);
        }
        return $this->query;
    }
}
