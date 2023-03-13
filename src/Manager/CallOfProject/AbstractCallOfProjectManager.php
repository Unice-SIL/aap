<?php


namespace App\Manager\CallOfProject;


use App\Entity\MailTemplate;
use App\Constant\MailTemplate as MailTemplateConstant;
use App\Entity\CallOfProject;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractCallOfProjectManager implements CallOfProjectManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;


    /**
     * AbstractCallOfProjectManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(): CallOfProject
    {
        $callOfProject = new CallOfProject();

        $validationMailTemplate = $this->em->getRepository(MailTemplate::class)->findOneByName(MailTemplateConstant::VALIDATION_PROJECT);
        $callOfProject->setValidationMailTemplate($validationMailTemplate->getBody());

        $refusalMailTemplate = $this->em->getRepository(MailTemplate::class)->findOneByName(MailTemplateConstant::REFUSAL_PROJECT);
        $callOfProject->setRefusalMailTemplate($refusalMailTemplate->getBody());

        $notificationToCreatorNewProjectMailTemplate = $this->em->getRepository(MailTemplate::class)->findOneByName(MailTemplateConstant::NOTIFY_CREATOR_OF_A_NEW_PROJECT);
        $callOfProject->setNotificationToCreatorNewProjectMailTemplate($notificationToCreatorNewProjectMailTemplate->getBody());

        $notificationNewReportMailTemplate = $this->em->getRepository(MailTemplate::class)->findOneByName(MailTemplateConstant::NOTIFICATION_NEW_REPORT);
        $callOfProject->setNotificationNewReportMailTemplate($notificationNewReportMailTemplate->getBody());

        $notificationNewReportsMailTemplate = $this->em->getRepository(MailTemplate::class)->findOneByName(MailTemplateConstant::NOTIFICATION_NEW_REPORTS);
        $callOfProject->setNotificationNewReportsMailTemplate($notificationNewReportsMailTemplate->getBody());


        return $callOfProject;
    }

    public abstract function save(CallOfProject $callOfProject);

    public abstract function update(CallOfProject $callOfProject);

    public abstract function delete(CallOfProject $callOfProject);


}
