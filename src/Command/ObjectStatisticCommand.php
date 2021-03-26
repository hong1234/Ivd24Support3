<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Dao\ObjectDao;

class ObjectStatisticCommand extends Command
{
    private $oDao;
    
    public function __construct(ObjectDao $oDao)
    {
        $this->oDao = $oDao;
        parent::__construct();
    }
    /**
     * In this method setup command, description, and its parameters
     */
    protected function configure()
    {
        $this->setName('object-statistic');
        $this->setDescription('Get Object Statistic.');
        //$this->addArgument('password', InputArgument::REQUIRED, 'Password to be hashed.');
    }

    /**
     * Here all logic happens
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$password = $input->getArgument('password');
        //$hashedPassword = (new Passwords())->hash($password);

        $total = $this->oDao->getObjectTotal()['Anzah_Gesamtl_Objekte'];
        $activ = $this->oDao->getObjectActiv()['Anzahl_freigegeben_Objekte'];
        $inactiv = $this->oDao->getObjectInActiv()['Anzahl_nicht_freigegeben_Objekte'];

        $output->writeln(sprintf(
            //'Your hashed password is: %s', $hashedPassword
            'Result is: %s %s %s', $total, $activ, $inactiv
        ));

        // $output->writeln(sprintf(
        //     'Your DB -------------is: %s', $this->hDao->db
        // ));

        // return value is important when using CI, to fail the build when the command fails
        // in case of fail: "return self::FAILURE;"
        //return self::SUCCESS;
        return 0;
    }
}