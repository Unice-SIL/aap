<?php


namespace App\DataFixtures;


use App\Entity\Acl;
use App\Entity\OrganizingCenter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class OrganizingCenterFixtures extends Fixture implements DependentFixtureInterface
{
    const ORGANIZING_CENTER_1 = 'Centre_organisateur_1';
    const ORGANIZING_CENTER_2 = 'Centre_organisateur_2';
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        //Acls
        $adminUser = $this->getReference(UserFixtures::class . UserFixtures::USER_ADMIN);
        $aclAdmin = new Acl();
        $aclAdmin->setUser($adminUser);
        $aclAdmin->setPermission(Acl::PERMISSION_ADMIN);

        $aclManager = new Acl();
        $aclManager->setUser($this->getReference(UserFixtures::class . UserFixtures::USER_USER1));
        $aclManager->setPermission(Acl::PERMISSION_MANAGER);

        $aclViewer = new Acl();
        $aclViewer->setUser($this->getReference(UserFixtures::class . UserFixtures::USER_USER2));
        $aclViewer->setPermission(Acl::PERMISSION_VIEWER);

        //OrganizingCenters
        $organizingCenter = new OrganizingCenter();
        $organizingCenter->setName(self::ORGANIZING_CENTER_1);
        $organizingCenter->addAcl($aclAdmin);
        $organizingCenter->addAcl($aclManager);
        $organizingCenter->addAcl($aclViewer);
        $organizingCenter->setCreatedBy($adminUser);
        $this->setReference(self::class . self::ORGANIZING_CENTER_1, $organizingCenter);
        $manager->persist($organizingCenter);

        $organizingCenter = new OrganizingCenter();
        $organizingCenter->setName(self::ORGANIZING_CENTER_2);
        $organizingCenter->setCreatedBy($adminUser);
        $this->setReference(self::class . self::ORGANIZING_CENTER_2, $organizingCenter);
        $manager->persist($organizingCenter);

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}