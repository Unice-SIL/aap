<?php


namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

class HumanizeRuntime implements RuntimeExtensionInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * HumanizeRuntime constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {

        $this->translator = $translator;
    }

    public function humanizeData($data)
    {
        $noCommunicateContent = $this->translator->trans('app.data.none');
        if ($data === null)
        {
            return $noCommunicateContent;
        }

        if (is_string($data) and $data === '') {
            return $noCommunicateContent;
        }

        return $data;
    }
}