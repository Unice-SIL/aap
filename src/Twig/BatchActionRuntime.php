<?php


namespace App\Twig;

use App\Utils\Batch\BatchActionManagerInterface;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Markup;

/**
 * Class BatchActionRuntime
 * @package App\Twig
 */
class BatchActionRuntime implements RuntimeExtensionInterface
{
    /**
     * @var BatchActionManagerInterface
     */
    private $batchActionManager;

    /**
     * BatchActionRuntime constructor.
     * @param BatchActionManagerInterface $batchActionManager
     */
    public function __construct(BatchActionManagerInterface $batchActionManager)
    {
        $this->batchActionManager = $batchActionManager;
    }

    /**
     * @param string $id
     * @param string $formId
     * @param array $attributes
     * @return Markup
     */
    public function batchActionRenderInput(string $id, string $formId, array $attributes=[])
    {
        return new Markup($this->batchActionManager->renderBreadcrumb($id, $formId, $attributes), 'UTF-8');
    }
}