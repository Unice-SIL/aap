<?php


namespace App\Utils\Batch;


use App\Form\BatchAction\BatchActionType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BatchActionManager implements BatchActionManagerInterface
{
    /**
     * @var array
     */
    private $batchActions = [];
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * BatchActionManager constructor.
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }


    public function addBatchAction(BatchActionInterface $batchAction): BatchActionManagerInterface
    {
        $this->batchActions[$batchAction->getName()] = $batchAction;

        return $this;
    }

    public function getBatchActions(): array
    {
        return $this->batchActions;
    }

    public function getBatchAction(string $name): ?BatchActionInterface
    {
        if (isset($this->batchActions[$name])) {
            return $this->batchActions[$name];
        }

        return null;
    }

    public function getForm(string $className, array $blackList = []): FormInterface
    {
        $batchActionsSupported = array_filter($this->batchActions, function ($batchAction) use ($className, $blackList) {
            return $batchAction->supports(new $className()) and !in_array(get_class($batchAction), $blackList);
        });

        return $this->formFactory->create(BatchActionType::class, null, [
            'batch_actions' => $batchActionsSupported,
            'class_name' => $className
        ]);
    }

    public function saveDataInSession(FormInterface $batchActionForm, SessionInterface $session): bool
    {
        if (null === $batchActionForm->getClickedButton()) {
            return false;
        }
        try {
            $session->set('app.batch_action_data', [
                'entities' => $batchActionForm->getData()['entities'],
                'batch_action' => $this->getBatchAction($batchActionForm->getClickedButton()->getName()),
            ]);
        } catch (\Exception $e) {
            return false;
        }

        return true;

    }

    public function getEntitiesFromSession(SessionInterface $session, bool $remove = false): ?array
    {
        if ($remove) {
            return $session->remove('app.batch_action_data')['entities'];
        }
        return $session->get('app.batch_action_data')['entities'];
    }

    public function getBatchActionFromSession(SessionInterface $session, bool $remove = false): ?BatchActionInterface
    {
        if ($remove) {
            return $session->remove('app.batch_action_data')['batch_action'];
        }
        return $session->get('app.batch_action_data')['batch_action'];
    }


    public function renderBreadcrumb(string $id, string $formId)
    {
        return '<input type="checkbox" value="' . $id . '" class="batch-input" data-form-target="#' . $formId . '">';
    }

    public function removeBatchActionFromSession(SessionInterface $session): void
    {
        $session->set('app.batch_action_data', null);
    }


}