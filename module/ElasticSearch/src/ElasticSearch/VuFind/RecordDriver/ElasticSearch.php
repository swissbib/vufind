<?php
/**
 * RecordDriver
 *
 * @category ElasticSearch
 * @package  RecordDriver
 * @author   Christoph Böhm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://www.swissbib.ch/
 */
namespace ElasticSearch\VuFind\RecordDriver;

use VuFind\RecordDriver\AbstractBase;

/**
 * Class ElasticSearch
 *
 * @package ElasticSearch\VuFind\RecordDriver
 */
class ElasticSearch extends AbstractBase
{

    protected $sourceIdentifier = 'ElasticSearch';

    /**
     * Get text that can be displayed to represent this record in breadcrumbs.
     *
     * @return string Breadcrumb text to represent this record.
     */
    public function getBreadcrumb()
    {
        return $this->getName();
    }

    /**
     * Return the unique identifier of this record for retrieving additional
     * information (like tags and user comments) from the external MySQL database.
     *
     * @return string Unique identifier.
     */
    public function getUniqueID()
    {
        return $this->fields["_id"];
    }

    public function getType()
    {
        return $this->fields["_type"];
    }

    /**
     * @param string $name
     * @param string $prefix
     * @param string $delimiter
     * @return array|null
     */
    protected function getField(string $name, string $prefix, string $delimiter = ':')
    {
        $fieldName = $this->getQualifiedFieldName($name, $prefix, $delimiter);

        return array_key_exists($fieldName, $this->fields["_source"])
          ? $this->fields["_source"][$fieldName]
          : null;
    }

    /**
     * @param string $name
     * @param string $prefix
     * @param string $delimiter
     * @return string
     */
    protected function getQualifiedFieldName(string $name, string $prefix, string $delimiter) 
    {
        return sprintf('%s%s%s', $prefix, $delimiter, $name);
    }
}
