<?php
declare(strict_types=1);

namespace Messenger\Model;

/**
 *
 */
interface DialogRepositoryInterface
{
    /**
     * @return Dialog[]
     */
    public function findAllDialogs(): array;

    /**
     * @param $id
     * @return Dialog
     */
    public function findDialog($id): Dialog;
}
