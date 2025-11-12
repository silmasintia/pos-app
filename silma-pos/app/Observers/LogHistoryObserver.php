<?php

namespace App\Observers;

use App\Models\LogHistory;
use Illuminate\Support\Facades\Auth;

class LogHistoryObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created($model)
    {
        $this->logActivity($model, 'create');
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated($model)
    {
        $this->logActivity($model, 'update');
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted($model)
    {
        $this->logActivity($model, 'delete');
    }

    /**
     * Log activity to log_histories table
     *
     * @param  mixed  $model
     * @param  string  $action
     * @return void
     */
    protected function logActivity($model, $action)
    {
        $oldData = $action === 'create' ? null : $model->getOriginal();
        $newData = $action === 'delete' ? null : $model->getAttributes();
        
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