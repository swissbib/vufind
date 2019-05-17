<?php
namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Query\Elasticsearch;

use ElasticsearchAdapter\Exception\RequiredParameterException;
use ElasticsearchAdapter\Params\Params;
use ElasticsearchAdapter\Params\ParamsReplacer;
use InvalidArgumentException;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\ExistsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\IdsQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermsQuery;
use ONGR\ElasticsearchDSL\Search;

/**
 * TemplateQuery
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class TemplateQuery implements Query
{
    /**
     * @var array
     */
    protected $template = [];

    /**
     * @var array
     */
    protected $query = null;

    /**
     * @var Search
     */
    protected $search = null;

    /**
     * @var Params
     */
    protected $params = null;

    /**
     * @var ParamsReplacer
     */
    protected $paramsReplacer = null;

    /**
     * @var array
     */
    protected $boolQueryConfigToConst = [
        'must' => BoolQuery::MUST,
        'must_not' => BoolQuery::MUST_NOT,
        'should' => BoolQuery::SHOULD,
        'filter' => BoolQuery::FILTER
    ];

    /**
     * @param array $template
     * @param Params $params
     */
    public function __construct(array $template, Params $params = null)
    {
        $this->template = $template;
        $this->params = $params;
        $this->paramsReplacer = new ParamsReplacer($params);
    }

    /**
     * @return array
     */
    public function getQuery() : array
    {
        if ($this->query === null) {
            $this->query = $this->buildQuery();
        }

        return $this->query;
    }

    /**
     * @inheritdoc
     *
     * @throws RequiredParameterException
     */
    public function build()
    {
        $this->query = $this->buildQuery();
    }

    /**
     * @return Params
     */
    public function getParams() : Params
    {
        return $this->params;
    }

    /**
     * @param Params $params
     */
    public function setParams(Params $params)
    {
        $this->params = $params;

        $this->paramsReplacer->setParams($params);
    }

    /**
     * @return array
     *
     * @throws RequiredParameterException
     */
    public function toArray() : array
    {
        return $this->getQuery();
    }

    /**
     * @return array
     *
     * @throws RequiredParameterException
     */
    protected function buildQuery()
    {
        $this->search = new Search();

        if (isset($this->template['query'])) {
            foreach ($this->template['query'] as $type => $config) {
                $this->search->addQuery($this->buildQueryClause($type, $config));
            }
        }

        return $this->search->toArray();
    }

    /**
     * @param string $queryType
     * @param array $config
     *
     * @return BuilderInterface
     *
     * @throws RequiredParameterException
     */
    protected function buildQueryClause(string $queryType, array $config) : BuilderInterface
    {
        switch ($queryType) {
            case 'ids':
                return $this->buildIdsQueryClause($config);
            case 'match':
                return $this->buildMatchQueryClause($config);
            case 'multi_match':
                return $this->buildMultiMatchQueryClause($config);
            case 'bool':
                return $this->buildBoolQueryClause($config);
            case 'term':
                return $this->buildTermQueryClause($config);
            case 'terms':
                return $this->buildTermsQueryClause($config);
            case 'exists':
                return $this->buildExistsQueryClause($config);
            case 'match_all':
                return $this->buildMatchAllQueryClause($config);

            default:
                throw new InvalidArgumentException('QueryType "' . $queryType . '" is not implemented yet.');
        }
    }

    /**
     * @param array $query
     *
     * @return IdsQuery
     */
    protected function buildIdsQueryClause(array $query) : IdsQuery
    {
        $this->checkRequiredParameter('values', $query);

        $values = $this->paramsReplacer->replace($query['values']);
        $parameters = [];

        foreach ($query as $parameterName => $parameterValue) {
            if ($parameterName !== 'values') {
                $parameters[$parameterName] = $this->paramsReplacer->replace($parameterValue);
            }
        }

        $idsQuery = new IdsQuery($this->fixValuesArray($values), $parameters);

        return $idsQuery;
    }

    /**
     * @param array $config
     *
     * @return MatchQuery
     *
     * @throws RequiredParameterException
     */
    protected function buildMatchQueryClause(array $config) : MatchQuery
    {
        $name = key($config);
        $parameters = [];

        if (is_array($config[$name])) {
            $this->checkRequiredParameter('query', $config[$name]);

            $value = $this->paramsReplacer->replace($config[$name]['query']);

            foreach ($config[$name] as $parameterName => $parameterValue) {
                if ($parameterName !== 'query') {
                    $parameters[$parameterName] = $this->paramsReplacer->replace($parameterValue);
                }
            }
        } else {
            $value = $this->paramsReplacer->replace($config[$name]);
        }

        $matchQuery = new MatchQuery($name, $value, $parameters);

        return $matchQuery;
    }

    /**
     * @param array $config
     *
     * @return MultiMatchQuery
     *
     * @throws RequiredParameterException
     */
    protected function buildMultiMatchQueryClause(array $config) : MultiMatchQuery
    {
        $this->checkRequiredParameter('query', $config);
        $this->checkRequiredParameter('fields', $config);

        $query = $this->paramsReplacer->replace($config['query']);
        $fields = $this->paramsReplacer->replace(explode(',', $config['fields']));
        $parameters = [];

        foreach ($config as $parameterName => $parameterValue) {
            if (!in_array($parameterName, ['query', 'fields'])) {
                $parameters[$parameterName] = $this->paramsReplacer->replace($parameterValue);
            }
        }

        $multiMatchQuery = new MultiMatchQuery($fields, $query, $parameters);

        return $multiMatchQuery;
    }

    /**
     * @param array $config
     *
     * @return BoolQuery
     */
    protected function buildBoolQueryClause(array $config) : BoolQuery
    {
        $boolQuery = new BoolQuery();

        foreach ($config as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $boolQuery->addParameter($key, $value);
            } else {
                $boolQueryType = $this->boolQueryConfigToConst[$key];

                foreach ($value as $type => $clauseConfig) {
                    if (is_int($type)) {
                        //multiple leaf clauses
                        $type = key($clauseConfig);
                        $query = $this->buildQueryClause($type, $clauseConfig[$type]);
                    } else {
                        $query = $this->buildQueryClause($type, $clauseConfig);
                    }

                    $boolQuery->add($query, $boolQueryType);
                }
            }
        }

        return $boolQuery;
    }

    /**
     * @param array $config
     *
     * @return TermQuery
     *
     * @throws RequiredParameterException
     */
    protected function buildTermQueryClause(array $config) : TermQuery
    {
        $name = key($config);
        $parameters = [];

        if (is_array($config[$name])) {
            $this->checkRequiredParameter('value', $config[$name]);
            $value = $this->paramsReplacer->replace($config[$name]['value']);

            foreach ($config[$name] as $parameterName => $parameterValue) {
                if ($parameterName !== 'value') {
                    $parameters[$parameterName] = $this->paramsReplacer->replace($parameterValue);
                }
            }
        } else {
            $value = $this->paramsReplacer->replace($config[$name]);
        }

        $termQuery = new TermQuery($name, $value, $parameters);

        return $termQuery;
    }


    /**
     * @param array $config
     *
     * @return TermsQuery
     */
    protected function buildTermsQueryClause(array $config) : TermsQuery
    {
        $name = key($config);
        $parameters = [];

        $values = $this->paramsReplacer->replace($config[$name]);

        if (!is_array($values)) {
            $values = [$values];
        }

        $termsQuery = new TermsQuery($name, $values, $parameters);

        return $termsQuery;
    }

    /**
     * @param array $config
     *
     * @return ExistsQuery
     *
     * @throws InvalidArgumentException
     */
    protected function buildExistsQueryClause(array $config) : ExistsQuery
    {
        $name = key($config);

        if (is_array($config[$name])) {
            throw new InvalidArgumentException('Exists Query in combintaion 
            with array type '. $name .' in config is not allowed ');
        } else {
            $value = $this->paramsReplacer->replace($config[$name]);
        }

        $existsQuery = new ExistsQuery($value);

        return $existsQuery;
    }


    /**
     * @param array $config
     *
     * @return MatchAllQuery
     *
     * @throws RequiredParameterException
     */
    protected function buildMatchAllQueryClause(array $config) : MatchAllQuery
    {
        //we do not need any parameters
        return new MatchAllQuery();
    }




    /**
     * @param string $name
     * @param array $parameters
     *
     * @throws RequiredParameterException
     */
    protected function checkRequiredParameter(string $name, array $parameters)
    {
        if (!isset($parameters[$name])) {
            throw new RequiredParameterException('Required parameter "' . $name . '" not set for query.');
        }
    }

    /**
     * Fixes $values arrays like this:
     *
     * [["a", "b"]] to ["a", "b"]
     *
     * This happens in id search with multiple ids
     *
     * @param $values
     * @return array
     */
    protected function fixValuesArray($values)
    {
        if ($values !== null && count($values === 1) && is_array($values[0])) {
            $values = $values[0];
        }
        return $values;
    }
}
