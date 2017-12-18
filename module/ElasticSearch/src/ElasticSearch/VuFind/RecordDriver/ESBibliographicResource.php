<?php
/**
 * Created by IntelliJ IDEA.
 * User: boehm
 * Date: 13.12.17
 * Time: 18:04
 */

namespace ElasticSearch\VuFind\RecordDriver;


class ESBibliographicResource extends ElasticSearch
{

    public function getContributors(): array
    {
        return $this->getIdFromUrlSource('dct:contributor');
    }
    public function getSubjects(): array
    {
        $subjects = $this->fields["_source"]['dct:subject'];
        return $subjects ? $subjects : [];
    }

    /**
     * @param $field
     * @return mixed
     */
    protected function getIdFromUrlSource($field)
    {
        $contributors = $this->fields["_source"][$field];
        preg_match_all("/\/([\w-]+)(,+|$)/", implode(",", $contributors), $matches);
        return $matches[1];
    }

}