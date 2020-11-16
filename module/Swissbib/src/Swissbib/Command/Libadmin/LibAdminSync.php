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
namespace Swissbib\Command\Libadmin;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Swissbib\Libadmin\Importer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * LibAdminSync
 *
 * @category Swissbib_VuFind
 * @package  Command_Libadmin
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class LibAdminSync extends Command
{
    /**
     * The name of the command (the part after "public/index.php")
     *
     * @var string
     */
    protected static $defaultName = 'libadmin/sync';

    /**
     * Libadmin Importer
     *
     * @var Importer $importer
     */
    protected $importer;

    /**
     * LibAdminSync constructor.
     *
     * @param Importer $importer Importer
     */
    public function __construct(Importer $importer)
    {
        $this->importer = $importer;
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
                'Import library and group data from libadmin API and ' .
                'save as local files'
            )
            ->addOption(
                'result',
                'r',
                InputOption::VALUE_OPTIONAL,
                'Print out a single result info at the end.' .
                ' This is included in the verbose flag',
                false
            )->addOption(
                'dry',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Don\'t replace local files with new data ' .
                '(check if new data is available/reachable)',
                false
            );
    }

    /**
     * Synchronize with libadmin system
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
        $verbose = $input->getOption('verbose');
        $showResult = $input->getOption('result');
        $dryRun = $input->getOption('dry');

        try {
            $result   = $this->importer->import($dryRun);
            $hasErrors = $result->hasErrors();
        } catch (ServiceNotCreatedException $e) {
            // handle service exception
            echo "- Fatal error\n";
            echo "- Stopped with exception: " . get_class($e) . "\n";
            echo "===============================================================\n";
            echo $e->getMessage() . "\n";
            echo $e->getPrevious()->getMessage() . "\n";

            return false;
        }

        // Show all messages?
        if ($verbose || $hasErrors) {
            foreach ($result->getFormattedMessages() as $message) {
                echo '- ' . $message . "\n";
            }
        }

        // No messages printed, but result required?
        if (!$verbose && $showResult) {
            echo $result->isSuccess() ? 1 : 0;
        }

        return 0;
    }
}
