<?php


namespace App\Utils\Batch;


use App\Form\BatchAction\BatchActionType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class BatchActionManager
 * @package App\Utils\Batch
 */
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


    /**
     * @param BatchActionInterface $batchAction
     * @return $this|BatchActionManagerInterface
     */
    public function addBatchAction(BatchActionInterface $batchAction): BatchActionManagerInterface
    {
        $this->batchActions[$batchAction->getName()] = $batchAction;

        return $this;
    }

    /**
     * @return array
     */
    public function getBatchActions(): array
    {
        return $this->batchActions;
    }

    /**
     * @param string $name
     * @return BatchActionInterface|null
     */
    public function getBatchAction(string $name): ?BatchActionInterface
    {
        if (isset($this->batchActions[$name])) {
            return $this->batchActions[$name];
        }

        return null;
    }

    /**
     * @param string $className
     * @param array $blackList
     * @return FormInterface
     */
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

    /**
     * @param FormInterface $batchActionForm
     * @param SessionInterface $session
     * @return bool
     */
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

    /**
     * @param SessionInterface $session
     * @param bool $remove
     * @return array|null
     */
    public function getEntitiesFromSession(SessionInterface $session, bool $remove = false): ?array
    {
        if ($remove) {
            return $session->remove('app.batch_action_data')['entities'];
        }
        return $session->get('app.batch_action_data')['entities'];
    }

    /**
     * @param SessionInterface $session
     * @param bool $remove
     * @return BatchActionInterface|null
     */
    public function getBatchActionFromSession(SessionInterface $session, bool $remove = false): ?BatchActionInterface
    {
        if ($remove) {
            return $session->remove('app.batch_action_data')['batch_action'];
        }
        return $session->get('app.batch_action_data')['batch_action'];
    }


    /**
     * @param string $id
     * @param string $formId
     * @param array $attributes
     * @return string
     */
    public function renderBreadcrumb(string $id, string $formId, array $attributes = [])
    {
        $element =  '<input type="checkbox" value="' . $id . '" class="batch-input" data-form-target="#' . $formId . '"';
        foreach ($attributes as $attribute => $value)
        {
            $element.= ' '.$attribute.'="'.$value.'"';
        }
        $element.= '>';
        return $element;
    }

    /**
     * @param SessionInterface $session
     */
    public function removeBatchActionFromSession(SessionInterface $session): void
    {
        $session->set('app.batch_action_data', null);
    }


}