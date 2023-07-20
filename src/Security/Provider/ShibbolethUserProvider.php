<?php

namespace App\Security\Provider;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use UniceSIL\ShibbolethBundle\Security\Provider\AbstractShibbolethUserProvider;

class ShibbolethUserProvider extends AbstractShibbolethUserProvider
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * @param ManagerRegistry $registry
     * @param RequestStack $requestStack
     */
    public function __construct(ManagerRegistry $registry, RequestStack $requestStack)
    {
        $this->em = $registry->getManager();
        parent::__construct($requestStack);
    }

    /**
     * @param array $credentials
     * @return User|UserInterface|null
     * @throws Exception
     */
    public function loadUserByIdentifier(array $credentials)
    {
        return $this->loadUserByUsername($credentials);
    }

    /**
     * @param string $username
     * @return User|UserInterface
     */
    public function loadUserByUsername($username)
    {
        $user = $this->findUserByUsername($username);
        if (!$user instanceof User) {
            $user = (new User())
                ->setUsername($username)
                ->setAuth(User::AUTH_SHIBBOLETH);
            $this->em->persist($user);
        }

        return $user;
    }

    /**
     * @param string $username
     * @return User|null
     */
    private function findUserByUsername(string $username): ?User
    {
        return $this->em->getRepository(User::class)->findOneByUsername($username);
    }

    /**
     * @param UserInterface $user
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->findUserByUsername($user->getUsername());
    }

    /**
     * @param string $class
     * @return string
     */
    public function supportsClass($class): string
    {
        return $class === User::class;
    }
}
