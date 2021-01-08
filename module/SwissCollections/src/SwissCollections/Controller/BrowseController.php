<?php


namespace SwissCollections\Controller;


use Laminas\Config\Config;
use Laminas\ServiceManager\ServiceLocatorInterface;
use VuFind\Controller\BrowseController as VuFindBrowseController;

class BrowseController extends VuFindBrowseController {

  /**
   * @var Config
   */
  protected $actions;
  /**
   * @var array
   */
  protected $categories;

  /**
   * Constructor
   *
   * @param ServiceLocatorInterface $sm     Service manager
   * @param Config                  $config VuFind configuration
   */
  public function __construct(ServiceLocatorInterface $sm, Config $config) {
    parent::__construct($sm, $config);
    $this->actions = $config->Browse->actions;
    foreach ($config->Browse->categories->toArray() as $action => $categoryList) {
      $categories = explode(",", $categoryList);
      $entry = [];
      foreach ($categories as $category) {
        $facet = $this->actions->get($category, $category);
        $entry[$facet] = $category;
      }
      $this->categories[$action] = $entry;
    }
  }

  /**
   * Action for Browse
   *
   * @return \Laminas\View\Model\ViewModel
   */
  public function browseAction() {
    $action = $this->params()->fromQuery('action');

    $facet = $this->actions->get($action);
    if ($facet !== NULL) {
      $categoryList = $this->categories[$action];
      return $this->performBrowse($action, $categoryList, TRUE);
    }
  }

  /**
   * Given a list of active options, format them into details for the view.
   *
   * @return array
   */
  protected function buildBrowseOptions() {
    // Initialize the array of top-level browse options.
    $browseOptions = [];

    $activeOptions = $this->actions->toArray();
    foreach ($activeOptions as $action => $facet) {
      $browseOptions[] = $this->buildBrowseOption($action, $action);
    }
    return $browseOptions;
  }

  /**
   * Get the facet search term for an action
   *
   * @param string $action action to be translated
   *
   * @return string
   */
  protected function getCategory($action = NULL) {
    if ($action == NULL || $this->actions[$action] == NULL) {
      $action = $this->getCurrentAction();
    }

    return $this->actions[$action];
  }

  /**
   * Get array with two values: a filter name and a secondary list based on facets
   *
   * @param $action
   * @return array
   */
  protected function getSecondaryList($action) {
    // eg Genre
    $category = $this->getCategory();
    if ($action === 'alphabetical') {
      return ['', $this->getAlphabetList()];
    }
    $facet = $this->actions->get($action, $action);
    return [
      $facet,
      $this->quoteValues($this->getFacetList($facet, $category))
    ];
  }
}
