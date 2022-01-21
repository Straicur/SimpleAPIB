<?php
namespace App\Command;

use App\Entity\AdminUser;
use App\Tools\DBTool;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class GenerateNewLogin
 * @package App\Command
 */
class GenerateNewLogin extends Command{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->container = $container;
    }

    /**
     * command REQUIRES username and password to work correct
     * also config is setting a name of comand , descroption and help if someone doesn't know what this command do
     */
    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.')
            ->setName('app:create-user-auth')
            ->setDescription('Creates a new user')
            ->setHelp('This command sets your login and password');
    }

    /**
     * command that allows the admin to login by creating his login and password or changing one existing
     *
     * @param InputInterface $input
     *
     * @param OutputInterface $output
     *
     * @return int
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $loginArg = $input->getArgument('email');
        $passArg = $input->getArgument('password');

        try{
            $em = $this->container->get('doctrine');
            $dbTool = new DBTool($em);

            $trans = $em->getManager()->getConnection();
            $trans->beginTransaction();

            $updated_user = $dbTool->findBy(AdminUser::class, ["email" => $loginArg]);

            if(count($updated_user) > 0){

                if($updated_user[0]->getPassword()===md5($passArg)){
                    $output->writeln("<info>Success</info>");
                    $output->writeln("Nothing was updated !!!");
                }
                else{
                    $updated_user[0]->setPassword(md5($passArg));
                    $dbTool->insertData($updated_user[0]);
                    $output->writeln("<info>Success</info>");
                    $output->writeln("Your account was updated !!!");
                }
            }
            else{
                try{

                    $user = new AdminUser();
                    $user->setPassword(md5($passArg));
                    $user->setEmail($loginArg);
                    $user->setRoles(["Admin"]);

                    $dbTool->insertData($user);

                    $trans->commit();

                    $output->writeln("<info>Success</info>");
                    $output->writeln("Your account was created !!!");

                    return Command::SUCCESS;
                    //--------------------------------------------------------------------------------------------------------------
                }catch (\Exception $e){
                    $trans->rollBack();
                    $output->writeln("<error>Failure</error>");
                    $output->writeln($e->getMessage());

                    return Command::FAILURE;
                }
            }
            $trans->commit();

            return Command::SUCCESS;
            //--------------------------------------------------------------------------------------------------------------
        }catch (\Exception $e){
            $trans->rollback();
            $output->writeln("<error>Failure</error>");
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }
    }
}