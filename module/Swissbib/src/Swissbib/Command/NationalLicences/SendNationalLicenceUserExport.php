<?php

namespace Swissbib\Command\NationalLicences;

use Swissbib\Services\NationalLicence;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Class SendNationalLicenceUserExport
 *
 * @category Swissbib_VuFind
 * @package  Commande
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class SendNationalLicenceUserExport extends Command
{
    /**
     * The name of the command (the part after "public/index.php")
     *
     * @var string
     */
    protected static $defaultName = 'send-national-licence-users-export';

    protected $nationalLicenceService;

    /**
     * Constructor
     *
     * @param NationalLicence $nationalLicenceService National Licence Service
     */
    public function __construct(NationalLicence $nationalLicenceService)
    {

        $this->nationalLicenceService = $nationalLicenceService;
        parent::__construct();
    }

    /**
     * Run the command.
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
        $this->nationalLicenceService->sendExportEmail();
        return 0;
    }


}

