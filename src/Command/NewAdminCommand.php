<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class NewAdminCommand
 * @package App\Command
 */
class NewAdminCommand extends Command
{
    protected static $defaultName = 'app:new-admin';

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @param ManagerRegistry $registry
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(ManagerRegistry $registry, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $registry->getManager();
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a new administrator user.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            ['<question>New administrator user creation</question>', '===================================']
        );
        $helper = $this->getHelper('question');

        do {
            $validUsername = true;
            $question = new Question(
                'Enter the username (3 alphanumerics chars minimum) [administrator]: ',
                'administrator'
            );
            $username = $helper->ask($input, $output, $question);

            if (preg_match('/^[a-zA-Z0-9]{3,}$/i', $username) !== 1) {
                $validUsername = false;
                $output->writeln('<error>username must contains 3 alphanumerics chars minimum</error>');
            }

            $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
            if ($user instanceof User) {
                $question = new ConfirmationQuestion(
                    'username \'' . $username . '\' is already in use.'
                    . ' Do you want update it by adding Admin role ? [yes|no] : ',
                    false
                );
                if ($helper->ask($input, $output, $question)) {
                    $roles = $user->getRoles();
                    if (!in_array(User::ROLE_ADMIN, $roles)) {
                        $roles[] = User::ROLE_ADMIN;
                    }
                    $user
                        ->setRoles($roles)
                        ->setIsActive(true);
                    $this->em->flush();
                    $output->writeln('<info>User updated successfully</info>');
                }
                return 0;
            }
        } while (!$validUsername);

        $question = new Question('Enter the lastname:');
        $lastname = $helper->ask($input, $output, $question);

        $question = new Question('Enter the firstname:');
        $firstname = $helper->ask($input, $output, $question);

        do {
            do {
                $validPassword = true;
                $question = new Question('Enter the user password (6 chars minimum): ', null);
                $question->setHidden(true)
                    ->setHiddenFallback(false);
                $password = $helper->ask($input, $output, $question);

                if (preg_match('/^.{6,}$/i', $password) !== 1) {
                    $validPassword = false;
                    $output->writeln('<error>Password must contains 6 chars minimum</error>');
                }
            } while (!$validPassword);

            $question = new Question('Confirm password: ', null);
            $question->setHidden(true)->setHiddenFallback(false);
            $confirmPassword = $helper->ask($input, $output, $question);

            if ($password !== $confirmPassword) {
                $validPassword = false;
                $output->writeln('<error>Passwords does not match</error>');
            }
        } while (!$validPassword);

        do {
            $validEmail = true;
            $question = new Question('Enter a user email: ', null);
            $email = $helper->ask($input, $output, $question);

            if (preg_match('/^.+@.+\..+$/i', $email) !== 1) {
                $validEmail = false;
                $output->writeln('<error>Email format is incorrect</error>');
            }
        } while (!$validEmail);

        $user = new User();
        $user->setUsername($username)
            ->setEmail($email)
            ->setPassword($this->passwordEncoder->encodePassword($user, $password))
            ->setRoles([User::ROLE_ADMIN])
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setIsActive(true);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('<info>User created successfully</info>');

        return 0;
    }
}
