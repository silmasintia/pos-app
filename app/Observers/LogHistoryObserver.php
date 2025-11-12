<?php

namespace App\Observers;

use App\Models\LogHistory;
use Illuminate\Support\Facades\Auth;

class LogHistoryObserver
{
    public function created($model)
    {
        $this->logActivity($model, 'create');
    }

    public function updated($model)
    {
        $this->logActivity($model, 'update');
    }

    public function deleted($model)
    {
        $this->logActivity($model, 'delete');
    }

    protected function logActivity($model, $action)
    {
        $oldData = $action === 'create' ? null : $model->getOriginal();
        $newData = $action === 'delete' ? null : $model->getAttributes();

        $excluded = ['remember_token', 'password'];

        if (is_array($oldData)) {
            foreach ($excluded as $field) {
                unset($oldData[$field]);
            }
        }

        if (is_array($newData)) {
            foreach ($excluded as $field) {
                unset($newData[$field]);
            }
        }

        LogHistory::create([
            'table_name' => $model->getTable(),
            'entity_id' => $model->id,
            'action' => $action,
            'user' => Auth::id(),
            'old_data' => $oldData,
            'new_data' => $newData,
        ]);
    }
}
