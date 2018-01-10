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
    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->getField("title");
    }

    public function getContributors(): array
    {
        return $this->getIdFromUrlSource('dct:contributor');
    }
    public function getSubjects(): array
    {

        return $this->returnAsArray($this->getField("subject"));
    }

    protected function getField(string $name, string $prefix = "dct", string $delimiter = ':')
    {
        return parent::getField($name, $prefix, $delimiter);
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

    /**
     * @param $items
     * @return array
     */
    protected function returnAsArray($items): array
    {
        if (is_array($items)) {
            return $items;
        }
        else if (isset($items)) {
            return [$items];
        }
        return [];
    }
}
