<?php
/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch_Response
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult;
use VuFindSearch\Response\RecordInterface;

class Record implements RecordInterface {


    /**
     * Set the source backend identifier.
     *
     * @param string $identifier Backend identifier
     *
     * @return void
     */
    public function setSourceIdentifier($identifier)
    {
        // TODO: Implement setSourceIdentifier() method.
    }

    /**
     * Return the source backend identifier.
     *
     * @return string
     */
    public function getSourceIdentifier()
    {
        return $this->fields['_id'];
    }
}