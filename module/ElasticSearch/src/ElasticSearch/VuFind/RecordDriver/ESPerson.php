<?php
/**
 * Created by IntelliJ IDEA.
 * User: boehm
 * Date: 12.12.17
 * Time: 15:26
 */

namespace ElasticSearch\VuFind\RecordDriver;


class ESPerson extends ElasticSearch
{
    /**
     * @method getBirthPlace()
     * TODO Possibly rather date than string
     * @method  getBirthYear()
     * @method getDeathPlace()
     * TODO Possibly rather date than string
     * @method getDeathYear()
     * @method getGenre()
     * @method getInfluenced()
     * @method getInfluencedBy()
     * @method getMovement()
     * @method getNationality()
     * @method getNotableWork()
     * @method getOccupation()
     * @method getPartner()
     * @method getSpouse()
     * @method getThumbnail()
     * @method getBirthPlaceDisplayField()
     * @method getGenreDisplayField()
     * @method getInfluencedDisplayField()
     * @method getInfluencedByDisplayField()
     * @method getMovementDisplayField()
     * @method getNationalityDisplayField()
     * @method getNotableWorkDisplayField()
     * @method getOccupationDisplayField()
     * @method getPartnerDisplayField()
     * @method getSpouseDisplayField()
     * @method getOccupationDisplayField()
     *
     * @param $name
     * @param $arguments
     * @return array|null
     */
    public function __call(string $name, $arguments)
    {
        if ($pos = strpos($name, "DisplayField")) {
            $userLocale = $this->getTranslatorLocale();
            $fieldName = substr($name, 3, $pos);
            $field = $this->getField('dbp' . $fieldName . 'AsLiteral', 'lsb');
            if ($field === null) {
                return null;
            }
            return $this->getValueByLanguagePriority($field, $userLocale);
        }
        $fieldName = lcfirst(substr($name, 3));
        return $this->getField($fieldName);
    }

    public function getFirstName()
    {
        return $this->getField('firstName', 'foaf');
    }

    public function getLastName()
    {
        return $this->getField('lastName', 'foaf');
    }

    public function getName()
    {
        return $this->getField('label', 'rdfs');
    }

    public function getBirthDate()
    {
        $date = $this->getField('birthDate');
        return $this->extractDate($date);
    }

    public function getAbstract()
    {
        $userLocale = $this->getTranslatorLocale();
        $abstract = $this->getField('abstract');

        return $this->getValueByLanguagePriority($abstract, $userLocale);
    }

    public function getPseudonym()
    {
        $pseudonym = $this->getField("pseudonym");
        return $this->getValueByLanguagePriority($pseudonym);
    }

    public function getBirthPlaceDisplayField()
    {
        $place = $this->getField("dbpBirthPlaceAsLiteral", "lsb");
        return $this->getValueByLanguagePriority($place);
    }

    public function getDeathPlaceDisplayField()
    {
        $place = $this->getField("dbpDeathPlaceAsLiteral", "lsb");
        return $this->getValueByLanguagePriority($place);
    }

    public function getDeathDate()
    {
        $date = $this->getField("deathDate");
        return $this->extractDate($date);
    }

    public function getSameAs()
    {
        return $this->getField("sameAs", "owl");
    }

    public function getRdfType()
    {
        return $this->getField("type", "rdf");
    }

    // TODO
    /*
     * "rdfs:label": {
            "type": "text"
          },
          "schema:alternateName": {
            "type": "text"
          },
          "schema:birthDate": {
            "type": "date",
            "format": "year"
          },
          "schema:deathDate": {
            "type": "date",
            "format": "year"
          },
          "schema:familyName": {
            "type": "text"
          },
          "schema:gender": {
            "type": "keyword"
          },
          "schema:givenName": {
            "type": "text"
          },
          "schema:sameAs": {
            "type": "keyword"
          },
          "skos:note": {
            "type": "text"
          }
     */

    protected function getValueByLanguagePriority($content, string $userLocale = "en")
    {
        if ($content !== null && is_array($content) && count($content) > 0) {
            $locales = [$userLocale, "en", "de", "fr", "it"];
            foreach ($locales as $locale) {
                foreach ($content as $valueArray) {
                    if ($valueArray[$locale] !== null) {
                        return $valueArray[$locale];
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param $date
     * @return \DateTime|null
     */
    protected function extractDate($date)
    {
        if ($date !== null) {
            return new \DateTime($date);
        }
        return null;
    }

    /**
     * @param $fieldName
     * @param $prefix
     * @return array|null
     */
    protected function getField(string $fieldName, string $prefix = "dbp")
    {
        if(array_key_exists( $prefix . ':' . $fieldName, $this->fields["_source"]))
        {
            return  $this->fields["_source"][$prefix . ':' . $fieldName];
        }
        return null;
    }
}
