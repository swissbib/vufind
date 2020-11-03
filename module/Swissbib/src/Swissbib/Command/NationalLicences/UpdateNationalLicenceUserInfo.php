<?php
namespace Swissbib\Command\NationalLicences;

use Swissbib\Services\NationalLicence;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateNationalLicenceUserInfo
 *
 * @category Swissbib_VuFind
 * @package  Commande
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class UpdateNationalLicenceUserInfo extends Command
{
    /**
     * The name of the command (the part after "public/index.php")
     *
     * @var string
     */
    protected static $defaultName = 'update-national-licence-user-info';

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
        echo "\r\n\r\n-------------------------------\r\n";
        echo "Update national licence users info cron job started.\r\n";
        $date = date("Y-m-d H:i:s");
        echo $date . "\r\n";
        echo "Process all users, this takes a long time (15-20 minutes)...\r\n";

        $this->nationalLicenceService->checkAndUpdateNationalLicenceUserInfo();
        return 0;
    }
}
