<?php


namespace SwissCollections\Controller;


use Laminas\Config\Config;
use Laminas\ServiceManager\ServiceLocatorInterface;
use VuFind\Controller\BrowseController as VuFindBrowseController;

class BrowseController extends VuFindBrowseController {

  /**
   * Constructor
   *
   * @param ServiceLocatorInterface $sm     Service manager
   * @param Config                  $config VuFind configuration
   */
  public function __construct(ServiceLocatorInterface $sm, Config $config)
  {
    parent::__construct($sm, $config);
  }


  /**
   * TODO: Only fixed for Author!
   *
   * Get the facet search term for an action
   *
   * @param string $action action to be translated
   *
   * @return string
   */
  protected function getCategory($action = null)
  {
    if ($action == null) {
      $action = $this->getCurrentAction();
    }
    switch (strtolower($action)) {
      case 'alphabetical':
        return $this->getCategory();
      case 'dewey':
        return 'dewey-hundreds';
      case 'lcc':
        return 'callnumber-first';
      case 'author':
        return 'navAuthor_full';
      case 'topic':
        return 'topic_facet';
      case 'genre':
        return 'navSubform';
      case 'region':
        return 'geographic_facet';
      case 'era':
        return 'era_facet';
    }
    return $action;
  }
}