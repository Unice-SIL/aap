<?php

namespace App\Security\Provider;

use App\DataTransformer\ShibbolethUserTransformer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use UniceSIL\ShibbolethBundle\Security\User\ShibbolethUserProviderInterface;

class ShibbolethUserProvider implements ShibbolethUserProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ShibbolethUserTransformer
     */
    private $shibbolethUserTransformer;

    /**
     * @var string
     */
    private $shibbolethUsernameAttribute;

    /**
     * @param EntityManagerInterface $em
     * @param ShibbolethUserTransformer $shibbolethUserTransformer
     * @param string $shibbolethUsernameAttribute
     */
    public function __construct(
        EntityManagerInterface $em,
        ShibbolethUserTransformer $shibbolethUserTransformer,
        string $shibbolethUsernameAttribute
    ) {
        $this->em = $em;
        $this->shibbolethUserTransformer = $shibbolethUserTransformer;
        $this->shibbolethUsernameAttribute = $shibbolethUsernameAttribute;
    }

    /**
     * @param array $credentials
     * @return User|UserInterface|null
     * @throws Exception
     */
    public function loadUser(array $credentials)
    {
        if (!array_key_exists($this->shibbolethUsernameAttribute, $credentials)) {
            throw new UsernameNotFoundException();
        }

        $username = $credentials[$this->shibbolethUsernameAttribute];
        $user = $this->loadUserByUsername($username);
        $user = $this->shibbolethUserTransformer->transform(['user' => $user, 'attributes' => $credentials]);
        $this->em->flush();

        return $user;
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