<?php
namespace Swissbib\Command\Pura;

use Swissbib\Services\Pura;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendPuraReport extends \Symfony\Component\Console\Command\Command
{
    /**
     * The name of the command (the part after "public/index.php")
     *
     * @var string
     */
    protected static $defaultName = 'send-pura-report';

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
        /**
         * Pura service.
         *
         * @var Pura $puraService Pura Service
         */
        $this->puraService->sendPuraReport('Z01');
        $this->puraService->sendPuraReport('E65');
        return 0;
    }
}
