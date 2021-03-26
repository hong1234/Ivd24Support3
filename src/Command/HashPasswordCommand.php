<?php
namespace App\Command;

use Nette\Security\Passwords;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Dao\ObjectDao;

class HashPasswordCommand extends Command
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
        $this->setName('hash-password');
        $this->setDescription('Hashes provided password with BCRYPT and prints to output.');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password to be hashed.');
    }

    /**
     * Here all logic happens
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $password = $input->getArgument('password');

        $hashedPassword = (new Passwords())->hash($password);

        $output->writeln(sprintf(
            'Your hashed password is: %s', $hashedPassword
        ));

        $output->writeln(sprintf(
             'Activ Objects -------------is: %s', $this->oDao->getObjectActiv()[0]['Anzahl_freigegeben_Objekte']
        ));

        // return value is important when using CI, to fail the build when the command fails
        // in case of fail: "return self::FAILURE;"
        return 0;
    }
}