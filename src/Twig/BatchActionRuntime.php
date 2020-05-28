<?php


namespace App\Twig;

use App\Utils\Batch\BatchActionManagerInterface;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Markup;

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

    public function batchActionRenderInput(string $id, string $formId)
    {
        return new Markup($this->batchActionManager->renderBreadcrumb($id, $formId), 'UTF-8');
    }
}