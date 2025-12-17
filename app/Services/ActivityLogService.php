<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Log an activity
     */
    public static function log(
        string $action,
        Model $model = null,
        string $description = null,
        array $changes = null
    ): ActivityLog {
        $data = [
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        if ($model) {
            $data['model_type'] = get_class($model);
            $data['model_id'] = $model->id;
        }

        return ActivityLog::create($data);
    }

    /**
     * Log a create action
     */
    public static function created(Model $model, string $description = null): ActivityLog
    {
        $description = $description ?? "Created {$model->getTable()} #{$model->id}";
        return self::log('created', $model, $description);
    }

    /**
     * Log an update action
     */
    public static function updated(Model $model, array $oldValues = null, string $description = null): ActivityLog
    {
        $description = $description ?? "Updated {$model->getTable()} #{$model->id}";
        
        $changes = null;
        if ($oldValues) {
            $newValues = $model->getAttributes();
            $changes = [
                'old' => $oldValues,
                'new' => $newValues,
            ];
        }

        return self::log('updated', $model, $description, $changes);
    }

    /**
     * Log a delete action
     */
    public static function deleted(Model $model, string $description = null): ActivityLog
    {
        $description = $description ?? "Deleted {$model->getTable()} #{$model->id}";
        return self::log('deleted', $model, $description);
    }

    /**
     * Log a view action
     */
    public static function viewed(Model $model, string $description = null): ActivityLog
    {
        $description = $description ?? "Viewed {$model->getTable()} #{$model->id}";
        return self::log('viewed', $model, $description);
    }

    /**
     * Log a custom action
     */
    public static function custom(string $action, Model $model = null, string $description = null): ActivityLog
    {
        return self::log($action, $model, $description);
    }
}





