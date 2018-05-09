<?php
/**
 * Record driver view helper
 *
 * PHP version 7
 *
 * Copyright (C) Villanova University 2010.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
namespace VuFind\View\Helper\Root;

use VuFind\Cover\Router as CoverRouter;

/**
 * Record driver view helper
 *
 * @category VuFind
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class Record extends AbstractClassBasedTemplateRenderer
{
    /**
     * Context view helper
     *
     * @var \VuFind\View\Helper\Root\Context
     */
    protected $contextHelper;

    /**
     * Cover router
     *
     * @var CoverRouter
     */
    protected $coverRouter = null;

    /**
     * Record driver
     *
     * @var \VuFind\RecordDriver\AbstractBase
     */
    protected $driver;

    /**
     * VuFind configuration
     *
     * @var \Zend\Config\Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param \Zend\Config\Config $config VuFind configuration
     */
    public function __construct($config = null)
    {
        $this->config = $config;
    }

    /**
     * Inject the cover router
     *
     * @param CoverRouter $router Cover router
     *
     * @return void
     */
    public function setCoverRouter($router)
    {
        $this->coverRouter = $router;
    }

    /**
     * Render a template within a record driver folder.
     *
     * @param string $name    Template name to render
     * @param array  $context Variables needed for rendering template; these will
     * be temporarily added to the global view context, then reverted after the
     * template is rendered (default = record driver only).
     *
     * @return string
     */
    public function renderTemplate($name, $context = null)
    {
        $template = 'RecordDriver/%s/' . $name;
        $className = get_class($this->driver);
        return $this->renderClassTemplate(
            $template, $className, $context ?? ['driver' => $this->driver]
        );
    }

    /**
     * Store a record driver object and return this object so that the appropriate
     * template can be rendered.
     *
     * @param \VuFind\RecordDriver\AbstractBase $driver Record driver object.
     *
     * @return Record
     */
    public function __invoke($driver)
    {
        // Set up context helper:
        $contextHelper = $this->getView()->plugin('context');
        $this->contextHelper = $contextHelper($this->getView());

        // Set up driver context:
        $this->driver = $driver;
        return $this;
    }

    /**
     * Render the core metadata area of the record view.
     *
     * @return string
     */
    public function getCoreMetadata()
    {
        return $this->renderTemplate('core.phtml');
    }

    /**
     * Render the a brief record for use in collection mode.
     *
     * @return string
     */
    public function getCollectionBriefRecord()
    {
        return $this->renderTemplate('collection-record.phtml');
    }

    /**
     * Render the core metadata area of the collection view.
     *
     * @return string
     */
    public function getCollectionMetadata()
    {
        return $this->renderTemplate('collection-info.phtml');
    }

    /**
     * Export the record in the requested format.  For legal values, see
     * the export helper's getFormatsForRecord() method.
     *
     * @param string $format Export format to display
     *
     * @return string        Exported data
     */
    public function getExport($format)
    {
        $format = strtolower($format);
        return $this->renderTemplate('export-' . $format . '.phtml');
    }

    /**
     * Get the CSS class used to properly render a format.  (Note that this may
     * not be used by every theme).
     *
     * @param string $format Format text to convert into CSS class
     *
     * @return string
     */
    public function getFormatClass($format)
    {
        return $this->renderTemplate(
            'format-class.phtml', ['format' => $format]
        );
    }

    /**
     * Render a list of record formats.
     *
     * @return string
     */
    public function getFormatList()
    {
        return $this->renderTemplate('format-list.phtml');
    }

    /**
     * Render an entry in a Merklisten list.
     *
     * @param \VuFind\Db\Row\UserList $list Currently selected list (null for
     * combined Merklisten)
     * @param \VuFind\Db\Row\User     $user Current logged in user (false if none)
     *
     * @return string
     */
    public function getListEntry($list = null, $user = false)
    {
        // Get list of lists containing this entry
        $lists = null;
        if ($user) {
            $lists = $this->driver->getContainingLists($user->id);
        }
        return $this->renderTemplate(
            'list-entry.phtml',
            [
                'driver' => $this->driver,
                'list' => $list,
                'user' => $user,
                'lists' => $lists
            ]
        );
    }

    /**
     * Render previews (data and link) of the item if configured.
     *
     * @return string
     */
    public function getPreviews()
    {
        return $this->getPreviewData() . $this->getPreviewLink();
    }

    /**
     * Render data needed to get previews.
     *
     * @return string
     */
    public function getPreviewData()
    {
        return $this->renderTemplate(
            'previewdata.phtml',
            ['driver' => $this->driver, 'config' => $this->config]
        );
    }

    /**
     * Render links to previews of the item if configured.
     *
     * @return string
     */
    public function getPreviewLink()
    {
        return $this->renderTemplate(
            'previewlink.phtml',
            ['driver' => $this->driver, 'config' => $this->config]
        );
    }

    /**
     * Collects ISBN, LCCN, and OCLC numbers to use in calling preview APIs
     *
     * @return array
     */
    public function getPreviewIds()
    {
        // Extract identifiers from record driver if it supports appropriate methods:
        $isbn = is_callable([$this->driver, 'getCleanISBN'])
            ? $this->driver->getCleanISBN() : '';
        $lccn = is_callable([$this->driver, 'getLCCN'])
            ? $this->driver->getLCCN() : '';
        $oclc = is_callable([$this->driver, 'getOCLC'])
            ? $this->driver->getOCLC() : [];

        // Turn identifiers into class names to communicate with jQuery logic:
        $idClasses = [];
        if (!empty($isbn)) {
            $idClasses[] = 'ISBN' . $isbn;
        }
        if (!empty($lccn)) {
            $idClasses[] = 'LCCN' . $lccn;
        }
        if (!empty($oclc)) {
            foreach ($oclc as $oclcNum) {
                if (!empty($oclcNum)) {
                    $idClasses[] = 'OCLC' . $oclcNum;
                }
            }
        }
        return $idClasses;
    }

    /**
     * Get HTML to render a title.
     *
     * @param int $maxLength Maximum length of non-highlighted title.
     *
     * @return string
     */
    public function getTitleHtml($maxLength = 180)
    {
        $highlightedTitle = $this->driver->tryMethod('getHighlightedTitle');
        $title = trim($this->driver->tryMethod('getTitle'));
        if (!empty($highlightedTitle)) {
            $highlight = $this->getView()->plugin('highlight');
            $addEllipsis = $this->getView()->plugin('addEllipsis');
            return $highlight($addEllipsis($highlightedTitle, $title));
        }
        if (!empty($title)) {
            $escapeHtml = $this->getView()->plugin('escapeHtml');
            $truncate = $this->getView()->plugin('truncate');
            return $escapeHtml($truncate($title, $maxLength));
        }
        $transEsc = $this->getView()->plugin('transEsc');
        return $transEsc('Title not available');
    }

    /**
     * Render the link of the specified type.
     *
     * @param string $type    Link type
     * @param string $lookfor String to search for at link
     *
     * @return string
     */
    public function getLink($type, $lookfor)
    {
        $link = $this->renderTemplate(
            'link-' . $type . '.phtml', ['lookfor' => $lookfor]
        );
        $link .= $this->getView()->plugin('searchTabs')
            ->getCurrentHiddenFilterParams($this->driver->getSourceIdentifier());
        return $link;
    }

    /**
     * Render the contents of the specified record tab.
     *
     * @param \VuFind\RecordTab\TabInterface $tab Tab to display
     *
     * @return string
     */
    public function getTab(\VuFind\RecordTab\TabInterface $tab)
    {
        $context = ['driver' => $this->driver, 'tab' => $tab];
        $classParts = explode('\\', get_class($tab));
        $template = 'RecordTab/' . strtolower(array_pop($classParts)) . '.phtml';
        $oldContext = $this->contextHelper->apply($context);
        $html = $this->view->render($template);
        $this->contextHelper->restore($oldContext);
        return $html;
    }

    /**
     * Render a toolbar for use on the record view.
     *
     * @return string
     */
    public function getToolbar()
    {
        return $this->renderTemplate('toolbar.phtml');
    }

    /**
     * Render a search result for the specified view mode.
     *
     * @param string $view View mode to use.
     *
     * @return string
     */
    public function getSearchResult($view)
    {
        return $this->renderTemplate('result-' . $view . '.phtml');
    }

    /**
     * Render an HTML checkbox control for the current record.
     *
     * @param string $idPrefix Prefix for checkbox HTML ids
     * @param string $formAttr ID of form for [form] attribute
     * @param int    $number   Result number (for label of checkbox)
     *
     * @return string
     */
    public function getCheckbox($idPrefix = '', $formAttr = false, $number = null)
    {
        $id = $this->driver->getSourceIdentifier() . '|'
            . $this->driver->getUniqueId();
        $context
            = ['id' => $id, 'number' => $number, 'prefix' => $idPrefix];
        if ($formAttr) {
            $context['formAttr'] = $formAttr;
        }
        return $this->contextHelper->renderInContext(
            'record/checkbox.phtml', $context
        );
    }

    /**
     * Render a cover for the current record.
     *
     * @param string $context Context of code being generated
     * @param string $default The default size of the cover
     * @param string $link    The link for the anchor
     *
     * @return string
     */
    public function getCover($context, $default, $link = false)
    {
        $details = $this->getCoverDetails($context, $default, $link);
        return $details['html'];
    }

    /**
     * Should cover images be linked to previews (when applicable) in the provided
     * template context?
     *
     * @param string $context Context of code being generated
     *
     * @return bool
     */
    protected function getPreviewCoverLinkSetting($context)
    {
        static $previewContexts = false;
        if (false === $previewContexts) {
            $previewContexts = isset($this->config->Content->linkPreviewsToCovers)
                ? array_map(
                    'trim',
                    explode(',', $this->config->Content->linkPreviewsToCovers)
                ) : ['*'];
        }
        return in_array('*', $previewContexts)
            || in_array($context, $previewContexts);
    }

    /**
     * Get the rendered cover plus some useful parameters.
     *
     * @param string $context Context of code being generated
     * @param string $default The default size of the cover
     * @param string $link    The link for the anchor
     *
     * @return array
     */
    public function getCoverDetails($context, $default, $link = false)
    {
        $details = compact('link', 'context') + [
            'driver' => $this->driver, 'cover' => false, 'size' => false,
            'linkPreview' => $this->getPreviewCoverLinkSetting($context),
        ];
        $preferredSize = $this->getCoverSize($context, $default);
        if (empty($preferredSize)) {    // covers disabled entirely
            $details['html'] = '';
        } else {
            // Find best option if more than one size is defined (e.g. small:medium)
            foreach (explode(':', $preferredSize) as $size) {
                if ($details['cover'] = $this->getThumbnail($size)) {
                    $details['size'] = $size;
                    break;
                }
            }

            $details['html'] = $this->contextHelper->renderInContext(
                'record/cover.phtml', $details
            );
        }
        return $details;
    }

    /**
     * Get the configured thumbnail size for record lists
     *
     * @param string $context Context of code being generated
     * @param string $default The default size of the cover
     *
     * @return string
     */
    protected function getCoverSize($context, $default = 'medium')
    {
        if (isset($this->config->Content->coversize)
            && !$this->config->Content->coversize
        ) {
            // covers disabled entirely
            return false;
        }
        // check for context-specific overrides
        return isset($this->config->Content->coversize[$context])
            ? $this->config->Content->coversize[$context] : $default;
    }

    /**
     * Get the configured thumbnail alignment
     *
     * @param string $context telling the context asking, prepends the config key
     *
     * @return string
     */
    public function getThumbnailAlignment($context = 'result')
    {
        $view = $this->getView();
        $configField = $context . 'ThumbnailsOnLeft';
        $left = !isset($this->config->Site->$configField)
            ? true : $this->config->Site->$configField;
        $mirror = !isset($this->config->Site->mirrorThumbnailsRTL)
            ? true : $this->config->Site->mirrorThumbnailsRTL;
        if ($view->layout()->rtl && !$mirror) {
            $left = !$left;
        }
        return $left ? 'left' : 'right';
    }

    /**
     * Generate a qrcode URL (return false if unsupported).
     *
     * @param string $context Context of code being generated (core or results)
     * @param array  $extra   Extra details to pass to the QR code template
     * @param string $level   QR code level
     * @param int    $size    QR code size
     * @param int    $margin  QR code margin
     *
     * @return string|bool
     */
    public function getQrCode($context, $extra = [], $level = "L", $size = 3,
        $margin = 4
    ) {
        if (!isset($this->config->QRCode)) {
            return false;
        }

        switch ($context) {
        case "core":
        case "results":
            $key = 'showIn' . ucwords(strtolower($context));
            break;
        default:
            return false;
        }

        if (!isset($this->config->QRCode->$key)
            || !$this->config->QRCode->$key
        ) {
            return false;
        }

        $template = $context . "-qrcode.phtml";

        // Try to build text:
        $text = $this->renderTemplate(
            $template, $extra + ['driver' => $this->driver]
        );
        $qrcode = [
            "text" => $text, 'level' => $level, 'size' => $size, 'margin' => $margin
        ];

        $urlHelper = $this->getView()->plugin('url');
        return $urlHelper('qrcode-show') . '?' . http_build_query($qrcode);
    }

    /**
     * Generate a thumbnail URL (return false if unsupported).
     *
     * @param string $size Size of thumbnail (small, medium or large -- small is
     * default).
     *
     * @return string|bool
     */
    public function getThumbnail($size = 'small')
    {
        return $this->coverRouter
            ? $this->coverRouter->getUrl($this->driver, $size)
            : false;
    }

    /**
     * Get all URLs associated with the record.  Returns an array of strings.
     *
     * @return array
     */
    public function getUrlList()
    {
        // Use a filter to pick URLs from the output of getLinkDetails():
        $filter = function ($i) {
            return $i['url'];
        };
        return array_map($filter, $this->getLinkDetails());
    }

    /**
     * Get all the links associated with this record.  Returns an array of
     * associative arrays each containing 'desc' and 'url' keys.
     *
     * @param bool $openUrlActive Is there an active OpenURL on the page?
     *
     * @return array
     */
    public function getLinkDetails($openUrlActive = false)
    {
        // See if there are any links available:
        $urls = $this->driver->tryMethod('getURLs');
        if (empty($urls) || ($openUrlActive && $this->hasOpenUrlReplaceSetting())) {
            return [];
        }

        // If we found links, we may need to convert from the "route" format
        // to the "full URL" format.
        $urlHelper = $this->getView()->plugin('url');
        $serverUrlHelper = $this->getView()->plugin('serverurl');
        $formatLink = function ($link) use ($urlHelper, $serverUrlHelper) {
            // Error if route AND URL are missing at this point!
            if (!isset($link['route']) && !isset($link['url'])) {
                throw new \Exception('Invalid URL array.');
            }

            // Build URL from route/query details if missing:
            if (!isset($link['url'])) {
                $routeParams = $link['routeParams'] ?? [];

                $link['url'] = $serverUrlHelper(
                    $urlHelper($link['route'], $routeParams)
                );
                if (isset($link['queryString'])) {
                    $link['url'] .= $link['queryString'];
                }
            }

            // Apply prefix if found
            if (isset($link['prefix'])) {
                $link['url'] = $link['prefix'] . $link['url'];
            }
            // Use URL as description if missing:
            if (!isset($link['desc'])) {
                $link['desc'] = $link['url'];
            }

            return $link;
        };

        return array_map($formatLink, $urls);
    }

    /**
     * Get all the links associated with this record depending on the OpenURL setting
     * replace_other_urls.  Returns an array of associative arrays each containing
     * 'desc' and 'url' keys.
     *
     * @return bool
     */
    protected function hasOpenUrlReplaceSetting()
    {
        return isset($this->config->OpenURL->replace_other_urls)
            && $this->config->OpenURL->replace_other_urls;
    }
}
