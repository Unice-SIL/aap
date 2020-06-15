<?php


namespace App\Command\Install;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserAdminCommand
 * @package App\Command\Install
 */
class UserAdminCommand extends Command
{

    protected static $defaultName = 'app:install:user-admin';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * UserAdminCommand constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        parent::__construct();
        $this->em = $em;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create administrator user')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = 'administrator';
        $lastname = 'Administrator';

        $output->writeln('Administrator account creation');

        $helper = $this->getHelper('question');

        $question = new Question(
            "<question>Enter an email for {$username} user</question> : ",
            $username
        );
        $email = $helper->ask($input, $output, $question);

        do {
            $question = new Question(
                "<question>Enter a password for {$username} user (default = {$username})</question> : ",
                $username
            );
            $question->setHidden(true);
            $password = $helper->ask($input, $output, $question);

            $question = new Question(
                '<question>Repeat the password to confirm</question> : ',
                $username
            );
            $question->setHidden(true);
            $repeatedPassword = $helper->ask($input, $output, $question);

            if ($password !== $repeatedPassword) {
                $output->writeln('<error>Passwords does not match, please retry</error>');
            }
        }while ($password !== $repeatedPassword);


        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
        if(!$user instanceof User)
        {
            $user = new User();
            $this->em->persist($user);
        }
        $user->setUsername($username)
            ->setLastname($lastname)
            ->setEmail($email)
            ->setRoles(['ROLE_ADMIN'])
            ->setIsActive(true)
            ->setPassword($this->userPasswordEncoder->encodePassword($user, $password));

        $this->em->flush();

        $output->writeln("<info>User {$username} created</info>");

    }

}