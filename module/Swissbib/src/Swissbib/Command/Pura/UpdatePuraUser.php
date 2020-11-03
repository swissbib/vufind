<?php
namespace Swissbib\Command\Pura;

use Swissbib\Services\Pura;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePuraUser extends \Symfony\Component\Console\Command\Command
{
    /**
     * The name of the command (the part after "public/index.php")
     *
     * @var string
     */
    protected static $defaultName = 'update-pura-user';

    protected $puraService;

    /**
     * Constructor
     *
     * @param Pura $puraService Pura Service
     */
    public function __construct(Pura $puraService)
    {
        $this->puraService = $puraService;
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
        echo "Update Pura Users cron job started.\r\n";
        $date = date("Y-m-d H:i:s");
        echo $date . "\r\n";
        echo "Process all users...\r\n";

        $this->puraService->checkValidityPuraUsers();
    }
}
