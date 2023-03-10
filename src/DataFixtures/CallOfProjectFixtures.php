<?php

namespace App\DataFixtures;

use App\Entity\Acl;
use App\Entity\CallOfProject;
use App\Entity\ProjectFormLayout;
use App\Entity\User;
use App\Manager\CallOfProject\CallOfProjectManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CallOfProjectFixtures extends Fixture implements DependentFixtureInterface
{
    const CALL_OF_PROJECT_1 = 'Appel_projet_1';
    const CALL_OF_PROJECT_2 = 'Appel_projet_2';
    /**
     * @var CallOfProjectManagerInterface
     */
    private $callOfProjectManager;

    /**
     * CallOfProjectFixtures constructor.
     * @param CallOfProjectManagerInterface $callOfProjectManager
     */
    public function __construct(CallOfProjectManagerInterface $callOfProjectManager)
    {
        $this->callOfProjectManager = $callOfProjectManager;
    }


    public function load(ObjectManager $manager)
    {
        /**
         * OrganizingCenter
         */
        $organizingCenter1 = $this->getReference(OrganizingCenterFixtures::class . OrganizingCenterFixtures::ORGANIZING_CENTER_1);

        /**
         * Acl
         */
        $adminUser = $this->getReference(UserFixtures::class . UserFixtures::USER_ADMIN);
        $aclAdmin = new Acl();
        $aclAdmin->setUser($adminUser);
        $aclAdmin->setPermission(Acl::PERMISSION_ADMIN);

        $aclManager = new Acl();
        $aclManager->setUser($this->getReference(UserFixtures::class . UserFixtures::USER_USER1));
        $aclManager->setPermission(Acl::PERMISSION_MANAGER);

        $aclViewer = new Acl();
        $aclViewer->setUser($this->getReference(UserFixtures::class . UserFixtures::USER_USER3));
        $aclViewer->setPermission(Acl::PERMISSION_VIEWER);

        /**
         * CAP 1
         */
        $callOfProject = $this->callOfProjectManager->create();
        $callOfProject->setName(self::CALL_OF_PROJECT_1);
        $callOfProject->setDescription('Description de l\'appel à projet');
        $callOfProject->setOrganizingCenter($organizingCenter1);
        $callOfProject->setNumber(1) ;


        $callOfProject->setStartDate((new \DateTime())->modify('-1 day'));
        $callOfProject->setEndDate((new \DateTime())->modify('+1 day'));

        $callOfProject->addAcl($aclAdmin);
        $callOfProject->addAcl($aclManager);
        $callOfProject->addAcl($aclViewer);
        $this->addReference(self::class . self::CALL_OF_PROJECT_1, $callOfProject);

        /** @var ProjectFormLayout $projectFormLayout */
        $projectFormLayout = $this->getReference(ProjectFormLayoutFixtures::class . ProjectFormLayoutFixtures::FORM_LAYOUT_1);
        $callOfProject->addProjectFormLayout($projectFormLayout);

        /** @var User $user */
        $user = $this->getReference(UserFixtures::class . UserFixtures::USER_ADMIN);
        $callOfProject->setCreatedBy($user);

        $manager->persist($callOfProject);

        /**
         * CAP 2
         */
        $callOfProject = $this->callOfProjectManager->create();
        $callOfProject->setName(self::CALL_OF_PROJECT_2);
        $callOfProject->setDescription('Description de l\'appel à projet');
        $callOfProject->setOrganizingCenter($organizingCenter1);
        $callOfProject->setNumber(2);

        $callOfProject->setStartDate((new \DateTime())->modify('+3 day'));
        $callOfProject->setEndDate((new \DateTime())->modify('+5 day'));

        $this->addReference(self::class . self::CALL_OF_PROJECT_2, $callOfProject);

        /** @var ProjectFormLayout $projectFormLayout */
        $projectFormLayout = $this->getReference(ProjectFormLayoutFixtures::class . ProjectFormLayoutFixtures::FORM_LAYOUT_2);
        $callOfProject->addProjectFormLayout($projectFormLayout);

        /** @var User $user */
        $user = $this->getReference(UserFixtures::class . UserFixtures::USER_USER1);
        $callOfProject->setCreatedBy($user);

        $manager->persist($callOfProject);

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            ProjectFormLayoutFixtures::class,
            UserFixtures::class,
            OrganizingCenterFixtures::class,
        ];
    }
}
