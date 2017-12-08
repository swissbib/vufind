<?php


namespace ElasticSearch\VuFind\Search\ElasticSearch;

use VuFind\Search\Base\Params as BaseParams;

/**
 * This will contain the \VuFind\Search\Sample\Params class, which must extend \VuFind\Search\Base\Params. Unless you need to do special parameter processing or add new parameters not supported by the base class, you are not required to implement any methods here – you can just extend with an empty class. You'll probably end up adding methods here eventually, but for the initial implementation it is nice to leave this empty – one less thing to worry about!
 * Class Params
 * @package ElasticSearch\VuFind\Search\ElasticSearch
 */
class Params extends BaseParams
{
    private $ids;

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
        // Special case -- did we get a list of IDs instead of a standard query?
        $ids = $request->get('overrideIds', null);
        if (is_array($ids)) {
            $this->setQueryIDs($ids);
        } else {
            // Use standard initialization:
            parent::initSearch($request);
        }
    }

    /**
     * From Solr/Params
     *
     * Override the normal search behavior with an explicit array of IDs that must
     * be retrieved.
     *
     * @param array $ids Record IDs to load
     *
     * @return void
     */
    public function setQueryIDs($ids)
    {

        $this->ids = $ids;
    }
}
