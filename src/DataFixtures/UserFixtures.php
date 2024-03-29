<?php


namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture
{

    const USER_ADMIN = 'admin';
    const USER_USER1 = 'user1';
    const USER_USER2 = 'user2';
    const USER_USER3 = 'user3';

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * CallOfProjectSubscriber constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername(self::USER_ADMIN)
            ->setFirstname(self::USER_ADMIN)
            ->setLastname(self::USER_ADMIN)
            ->setEmail(self::USER_ADMIN . '@aap.com')
            ->setPassword($this->encoder->encodePassword($user, self::USER_ADMIN))
            ->setIsActive(true)
        ;
        $manager->persist($user);
        $this->addReference(self::class . self::USER_ADMIN, $user);
        $user->setRoles([
            'ROLE_ADMIN'
        ]);

        $user = new User();
        $user->setUsername(self::USER_USER1)
            ->setFirstname(self::USER_USER1)
            ->setLastname(self::USER_USER1)
            ->setEmail(self::USER_USER1 . '@aap.com')
            ->setPassword($this->encoder->encodePassword($user, self::USER_USER1))
            ->setIsActive(true)
        ;
        $manager->persist($user);
        $this->addReference(self::class . self::USER_USER1, $user);

        $user = new User();
        $user->setUsername(self::USER_USER2)
            ->setFirstname(self::USER_USER2)
            ->setLastname(self::USER_USER2)
            ->setEmail(self::USER_USER2 . '@aap.com')
            ->setPassword($this->encoder->encodePassword($user, self::USER_USER2));
        $manager->persist($user);
        $this->addReference(self::class . self::USER_USER2, $user);

        $user = new User();
        $user->setUsername(self::USER_USER3)
            ->setFirstname(self::USER_USER3)
            ->setLastname(self::USER_USER3)
            ->setEmail(self::USER_USER3 . '@aap.com')
            ->setPassword($this->encoder->encodePassword($user, self::USER_USER3));
        $manager->persist($user);
        $this->addReference(self::class . self::USER_USER3, $user);

        $manager->flush();
    }
}