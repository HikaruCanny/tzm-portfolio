<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RecordsContentActivity
{
    public function created(Model $model): void
    {
        $this->write('created', $model);
    }

    public function updated(Model $model): void
    {
        if ($model instanceof User && ! $model->wasChanged(['role', 'disabled_at'])) {
            return;
        }

        $changes = $model->getChanges();
        unset(
            $changes['password'],
            $changes['remember_token'],
            $changes['two_factor_secret'],
            $changes['two_factor_recovery_codes'],
            $changes['updated_at'],
            $changes['body_markdown'],
        );

        $this->write('updated', $model, $changes);
    }

    public function deleted(Model $model): void
    {
        $this->write('deleted', $model);
    }

    /**
     * @param  array<string, mixed>|null  $changes
     */
    private function write(string $action, Model $model, ?array $changes = null): void
    {
        $actor = auth()->user();

        if ($actor === null) {
            return;
        }

        ActivityLog::query()->create([
            'user_id' => $actor->id,
            'action' => $action,
            'subject_type' => $model::class,
            'subject_id' => $model->getKey(),
            'changes' => $changes,
        ]);
    }
}
