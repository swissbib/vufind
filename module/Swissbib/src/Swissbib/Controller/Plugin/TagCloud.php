<?php
/**
 * TagCloud.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2018
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  Swissbib\Controller\Plugin
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller\Plugin;

use ElasticSearch\VuFind\RecordDriver\ESSubject;
use Laminas\Config\Config;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class TagCloud
 *
 * @category VuFind
 * @package  Swissbib\Controller\Plugin
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class TagCloud extends AbstractPlugin
{
    /**
     * The tag cloud config
     *
     * @var Config $_config The tag cloud config
     */
    private $_config;

    /**
     * TagCloud constructor.
     *
     * @param Config $config The tag cloud config
     */
    public function __construct(
        Config $config
    ) {
        $this->_config = $config;
    }

    /**
     * Gets the Tagcloud
     *
     * @param array $subjectIds All subject ids, including duplicates
     * @param array $subjects   All subjects
     *
     * @return array
     */
    public function getTagCloud(
        array $subjectIds, array $subjects
    ): array {
        $frequencies = array_count_values($subjectIds);
        $cloud = [];
        $max = max($frequencies);

        foreach ($frequencies as $id => $frequency) {
            $subject = $this->getSubjectById($id, $subjects);
            if ($subject !== null) {
                $name = $subject->getName();
                $cloud[$name] = [
                    "subject"   => $subject,
                    "frequency" => $frequency,
                    "weight"    => $this->calculateFontSize(
                        $frequency, $max, $this->_config->minFontSize,
                        $this->_config->maxFontSize
                    )
                ];
            }
        }

        return $cloud;
    }

    /**
     * Calculates the font size for the tag cloud
     *
     * @param int $frequency   The frequency
     * @param int $max         Max count
     * @param int $minFontSize The minimal font size
     * @param int $maxFontSize The maximal font size
     *
     * @return float
     */
    protected function calculateFontSize(
        int $frequency, int $max, int $minFontSize, int $maxFontSize
    ): float {
        return ($maxFontSize - $minFontSize) * ($frequency / $max) + $minFontSize;
    }

    /**
     * Returns the subject by id
     *
     * @param string $id       The id
     * @param array  $subjects The subjects
     *
     * @return ESSubject|null
     */
    protected function getSubjectById($id, array $subjects)
    {
        $subject = array_filter(
            $subjects,
            function (ESSubject $item) use ($id) {
                return $item->getFullUniqueID() === $id;
            }
        );

        return count($subject) > 0 ? array_shift($subject) : null;
    }
}
