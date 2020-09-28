<?php

namespace Swissbib\Command\Libadmin;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Swissbib\Libadmin\Importer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LibAdminSyncMapPortal extends \Symfony\Component\Console\Command\Command
{
    /**
     * The name of the command (the part after "public/index.php")
     *
     * @var string
     */
    protected static $defaultName = 'libadmin/syncMapPortal';

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
            ->addOption(
                'result',
                'r',
                InputOption::VALUE_OPTIONAL,
                'show result',
                false
            )->addOption(
                'path',
                null,
                InputOption::VALUE_OPTIONAL,
                'mapportal/green.json, orange.json, ...',
                'mapportal/green.json'
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
        $path = $input->getOption('path');

        try {
            $result   = $this->importer->importMapPortalData($path);
            $hasErrors = $result->hasErrors();
        } catch (ServiceNotCreatedException $e) {
            // handle service exception
            echo "- Fatal error\n";
            echo "- Stopped with exception: " . get_class($e) . "\n";
            echo "===============================================================\n";
            echo $e->getMessage() . "\n";
            echo $e->getPrevious()->getMessage() . "\n";

            return 1;
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

