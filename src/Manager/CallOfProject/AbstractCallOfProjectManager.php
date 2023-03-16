<?php


namespace App\Manager\CallOfProject;


use App\Entity\CallOfProjectMailTemplate;
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

    protected const LIST_MAIL_TEMPLATES = [
        MailTemplateConstant::VALIDATION_PROJECT,
        MailTemplateConstant::REFUSAL_PROJECT,
        MailTemplateConstant::NOTIFICATION_NEW_REPORT,
        MailTemplateConstant::NOTIFICATION_NEW_REPORTS,
        MailTemplateConstant::NOTIFY_CREATOR_OF_A_NEW_PROJECT
    ];

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
        $mailTemplates = $this->em->getRepository(MailTemplate::class)->findBy(['name' => self::LIST_MAIL_TEMPLATES]);


        $callOfProject = new CallOfProject();

        foreach ($mailTemplates as $mailTemplate) {
            $callOfProjectMailTemplate =  new CallOfProjectMailTemplate();
            $callOfProjectMailTemplate
                ->setBody($mailTemplate->getBody())
                ->setName($mailTemplate->getName())
                ->setSubject($mailTemplate->getSubject())
                ->setCallOfProject($callOfProject);
            $this->em->persist($callOfProjectMailTemplate);
            $callOfProject->addCallOfProjectMailTemplate($callOfProjectMailTemplate);
        }
        return $callOfProject;
    }

    public abstract function save(CallOfProject $callOfProject);

    public abstract function update(CallOfProject $callOfProject);

    public abstract function delete(CallOfProject $callOfProject);


}
