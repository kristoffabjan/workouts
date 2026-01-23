<?php

namespace App\Filament\App\Resources\Trainings\Pages;

use App\Enums\TrainingStatus;
use App\Filament\App\Resources\Trainings\TrainingResource;
use App\Notifications\TrainingCompletedNotification;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ViewTraining extends ViewRecord
{
    protected static string $resource = TrainingResource::class;

    public function infolist(Schema $schema): Schema
    {
        $schema = parent::infolist($schema);

        if ($this->shouldShowCompletionSection()) {
            $schema->components([
                ...$schema->getComponents(),
                $this->getCompletionSection(),
            ]);
        }

        return $schema;
    }

    protected function shouldShowCompletionSection(): bool
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        if (! $user || ! $team || $user->isCoach($team)) {
            return false;
        }

        return $this->record->assignedUsers()
            ->where('user_id', $user->id)
            ->whereNotNull('training_user.completed_at')
            ->exists();
    }

    protected function getCompletionSection(): Section
    {
        $user = auth()->user();
        $pivot = $this->record->assignedUsers()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot;

        return Section::make(__('trainings.sections.your_completion'))
            ->icon(Heroicon::CheckCircle)
            ->schema([
                TextEntry::make('completed_at')
                    ->label(__('trainings.completion.completed_at'))
                    ->state($pivot?->completed_at?->format('M j, Y g:i A')),
                TextEntry::make('feedback')
                    ->label(__('trainings.completion.feedback_label'))
                    ->state($pivot?->feedback ?? __('trainings.completion.no_feedback'))
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->collapsible();
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getMarkAsCompleteAction(),
            $this->getEditFeedbackAction(),
            EditAction::make(),
        ];
    }

    protected function getMarkAsCompleteAction(): Action
    {
        return Action::make('markAsComplete')
            ->label(__('trainings.actions.mark_complete'))
            ->icon(Heroicon::Check)
            ->color('success')
            ->authorize('markComplete')
            ->visible(fn (): bool => $this->canMarkAsComplete())
            ->schema([
                Textarea::make('feedback')
                    ->label(__('trainings.completion.feedback_label'))
                    ->placeholder(__('trainings.completion.feedback_placeholder'))
                    ->rows(4),
            ])
            ->modalHeading(__('trainings.actions.mark_complete'))
            ->modalDescription(__('trainings.completion.confirm_message'))
            ->modalSubmitActionLabel(__('trainings.actions.mark_complete'))
            ->action(function (array $data): void {
                $this->markTrainingAsComplete($data['feedback'] ?? null);
            });
    }

    protected function getEditFeedbackAction(): Action
    {
        $user = auth()->user();
        $pivot = $this->record->assignedUsers()
            ->where('user_id', $user?->id)
            ->first()
            ?->pivot;

        return Action::make('editFeedback')
            ->label(__('trainings.actions.edit_feedback'))
            ->icon(Heroicon::PencilSquare)
            ->color('gray')
            ->visible(fn (): bool => $this->canEditFeedback())
            ->schema([
                Textarea::make('feedback')
                    ->label(__('trainings.completion.feedback_label'))
                    ->placeholder(__('trainings.completion.feedback_placeholder'))
                    ->rows(4)
                    ->default($pivot?->feedback),
            ])
            ->modalHeading(__('trainings.actions.edit_feedback'))
            ->modalDescription(__('trainings.completion.edit_feedback_message'))
            ->modalSubmitActionLabel(__('app.actions.save'))
            ->action(function (array $data): void {
                $this->updateFeedback($data['feedback'] ?? null);
            });
    }

    protected function canMarkAsComplete(): bool
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        if (! $user || ! $team || $user->isCoach($team)) {
            return false;
        }

        $pivot = $this->record->assignedUsers()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot;

        if (! $pivot || $pivot->completed_at !== null) {
            return false;
        }

        if ($this->record->scheduled_at && $this->record->scheduled_at->isFuture()) {
            return false;
        }

        if ($this->isFeedbackDeadlinePassed()) {
            return false;
        }

        return true;
    }

    protected function canEditFeedback(): bool
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        if (! $user || ! $team || $user->isCoach($team)) {
            return false;
        }

        if ($this->isFeedbackDeadlinePassed()) {
            return false;
        }

        return $this->record->assignedUsers()
            ->where('user_id', $user->id)
            ->whereNotNull('training_user.completed_at')
            ->exists();
    }

    protected function isFeedbackDeadlinePassed(): bool
    {
        if (! $this->record->scheduled_at) {
            return false;
        }

        $deadlineDays = config('workouts.feedback_deadline_days', 3);
        $deadline = $this->record->scheduled_at->copy()->addDays($deadlineDays)->endOfDay();

        return now()->isAfter($deadline);
    }

    protected function markTrainingAsComplete(?string $feedback): void
    {
        $user = auth()->user();

        $this->record->assignedUsers()->updateExistingPivot($user->id, [
            'completed_at' => now(),
            'feedback' => $feedback,
        ]);

        $this->record->update([
            'status' => TrainingStatus::Completed,
        ]);

        $this->record->creator->notify(
            new TrainingCompletedNotification($this->record, $user, $feedback)
        );

        Notification::make()
            ->title(__('trainings.notifications.marked_complete'))
            ->success()
            ->send();

        $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
    }

    protected function updateFeedback(?string $feedback): void
    {
        $user = auth()->user();

        $this->record->assignedUsers()->updateExistingPivot($user->id, [
            'feedback' => $feedback,
        ]);

        Notification::make()
            ->title(__('trainings.notifications.feedback_updated'))
            ->success()
            ->send();

        $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
    }
}
