<?php


namespace App\Utils\Batch;


use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface BatchActionManagerInterface
{
    public function addBatchAction(BatchActionInterface $batchAction): BatchActionManagerInterface;

    public function getBatchActions(): array;

    public function getBatchAction(string $name): ?BatchActionInterface;

    public function getForm(string $className, array $blackList = []): FormInterface;

    public function renderBreadcrumb(string $id, string $formId, array $attributes = []);

    public function saveDataInSession(FormInterface $batchActionForm, SessionInterface $session): bool;

    public function getEntitiesFromSession(SessionInterface $session, bool $remove = false): ?array;

    public function getBatchActionFromSession(SessionInterface $session, bool $remove = false): ?BatchActionInterface;

    public function removeBatchActionFromSession(SessionInterface $session): void;
}