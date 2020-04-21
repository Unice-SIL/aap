<?php


namespace App\Widget;

abstract class AbstractWidget
{

    public function getTemplate(): string
    {
        $chunks = explode('\\', static::class);

        unset($chunks[0]);

        $templateName = 'partial/';

        $i = 1;
        foreach ($chunks as $chunk) {

            if ($i++ === count($chunks)) {
                $templateName .= '_' . ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $chunk)), '_') . '.html.twig';
                continue;
            }

            $templateName .= ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $chunk)), '_') . '/';

        }

        return $templateName;
    }

    final public function getName(): string
    {
        return static::class;
    }
}