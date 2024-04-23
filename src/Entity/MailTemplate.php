<?php

namespace App\Entity;

use App\Repository\MailTemplateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MailTemplateRepository::class)
 */
class MailTemplate extends AbstractMailTemplate
{
}
