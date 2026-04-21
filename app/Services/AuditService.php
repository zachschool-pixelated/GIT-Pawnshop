<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    /**
     * Log a create action
     */
    public static function logCreate(Model $model, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'created',
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'new_values' => $model->toArray(),
            'description' => class_basename($model) . ' #' . $model->id . ' created',
        ]);
    }

    /**
     * Log an update action
     */
    public static function logUpdate(Model $model, array $oldValues, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'updated',
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $model->toArray(),
            'description' => class_basename($model) . ' #' . $model->id . ' updated',
        ]);
    }

    /**
     * Log a delete action
     */
    public static function logDelete(Model $model, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'deleted',
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'old_values' => $model->toArray(),
            'description' => class_basename($model) . ' #' . $model->id . ' deleted',
        ]);
    }

    /**
     * Log a custom action
     */
    public static function logAction($action, $description, array $data = [], $userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => 'Custom',
            'model_id' => 0,
            'new_values' => $data,
            'description' => $description,
        ]);
    }

    /**
     * Get audit logs for a specific model
     */
    public static function getLogsForModel($modelType, $modelId)
    {
        return AuditLog::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
