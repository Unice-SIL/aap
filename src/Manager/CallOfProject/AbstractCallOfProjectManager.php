<?php


namespace App\Manager\CallOfProject;


use App\Entity\CallOfProject;
use App\Entity\CallOfProjectMailTemplate;
use App\Entity\MailTemplate;
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

        foreach ($this->em->getRepository(MailTemplate::class)->findAll() as $mailTemplate) {
            if (in_array($mailTemplate->getName(), CallOfProjectMailTemplate::ALLOWED_TEMPLATES)) {
                $callOfProjectMailTemplate = (new CallOfProjectMailTemplate())->initFromMailTemplate($mailTemplate);
                $callOfProject->addMailTemplate($callOfProjectMailTemplate);
            }
        }

        return $callOfProject;
    }

    public abstract function save(CallOfProject $callOfProject);

    public abstract function update(CallOfProject $callOfProject);

    public abstract function delete(CallOfProject $callOfProject);


}