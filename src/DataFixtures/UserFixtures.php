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
            ->setPassword($this->encoder->encodePassword($user, self::USER_ADMIN));
        $manager->persist($user);
        $this->addReference(self::class . self::USER_ADMIN, $user);

        $user = new User();
        $user->setUsername(self::USER_USER1)
            ->setPassword($this->encoder->encodePassword($user, self::USER_USER1));
        $manager->persist($user);
        $this->addReference(self::class . self::USER_USER1, $user);

        $manager->flush();
    }
}