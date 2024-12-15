<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Http\Controllers\Api\ApproveChainController;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Http;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('kanban')
                ->label(
                    fn()
                    => ($this->record->type === 'scrum' ? __('Scrum board') : __('Kanban board'))
                )
                ->icon('heroicon-o-view-boards')
                ->color('secondary')
                ->url(function () {
                    if ($this->record->type === 'scrum') {
                        return route('filament.pages.scrum/{project}', ['project' => $this->record->id]);
                    } else {
                        return route('filament.pages.kanban/{project}', ['project' => $this->record->id]);
                    }
                }),

            Actions\EditAction::make(),
            Actions\Action::make('approveProject')
                ->label('Approve Project')
                ->color('success')
                ->visible(fn() => checkIfUserCanApproveProject($this->record->id))
                ->action(function () {
                    $chainController = new ApproveChainController();
                    $result = json_decode($chainController->approve($this->record->id)->getContent(), true);
                    $this->notify($result['status'] ? 'success' : 'danger', $result['message']);
                    $this->record->refresh();
                    $this->fillForm();
                }),
        ];
    }
}
