<?php
/**
 * A console command or factory
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2020
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
 * @package  Swissbib\Command
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Command\Tab40Import;

use Swissbib\Tab40Import\Importer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Tab40Import
 *
 * Import tab40.xxx files and convert them to label files
 * Use this controller over the command line
 *
 * @category Swissbib_VuFind
 * @package  Swissbib\Command\Tab40Import
 * @author   Lionel Walter  <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class Tab40Import extends Command
{
    /**
     * The name of the command (the part after "public/index.php")
     *
     * @var string
     */
    protected static $defaultName = 'tab40import';

    /**
     * Libadmin Importer
     *
     * @var Importer
     */
    protected $tab40Importer;

    /**
     * Tab40Import constructor.
     *
     * @param Importer $tab40Importer Tab 40 Importer
     */
    public function __construct(Importer $tab40Importer)
    {
        $this->tab40Importer = $tab40Importer;
        parent::__construct();
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDescription(
                'Extract label information from an aleph tab40
                 file and convert to vufind language format'
            )->addArgument(
                'network',
                InputArgument::REQUIRED,
                'Network key the file contains information
                 about. Ex: idsbb'
            )->addArgument(
                'locale',
                InputArgument::REQUIRED,
                'Locale key: de, en, fr, etc'
            )->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Path to input file. Ex: ~/myalephdata/tab40.ger'
            );
    }

    /**
     * Import tab40
     *
     * @param InputInterface  $input  Input object
     * @param OutputInterface $output Output object
     *
     * @return int 0 for success
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $network = $input->getArgument('network');
        $locale = $input->getArgument('locale');
        $sourceFile = $input->getArgument('source');

        $importResult = $this->tab40Importer->import($network, $locale, $sourceFile);

        echo "Imported language data from tab40 file\n";
        echo "Source: $sourceFile\n";
        echo "Network: $network\n";
        echo "Locale: $locale\n";
        echo "\nResult:\n";
        echo "Written File: {$importResult->getFilePath()}\n";
        echo "Items imported: {$importResult->getRecordCount()}\n";

        return 0;
    }
}
