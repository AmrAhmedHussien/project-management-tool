<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Http\Controllers\Api\ApproveChainController;
use App\Http\Requests\ApproveChainRequest;
use App\Models\User;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class ProjectChainRelationManager extends RelationManager
{
    protected static string $relationship = 'approveChainUsers';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $inverseRelationship = 'approveChainProjects';

    public static function attach(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('User full name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('pivot.status')
                    ->label(__('Approve status'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('customAttach')
                    ->label('Attach')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('Select Record')
                            ->searchable()
                            ->preload()
                            ->options(fn(RelationManager $livewire) => getAvailableUsersToAddToChain($livewire->getRelationship()->getParent()->id))
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
                        try {
                            $project = $livewire->getRelationship();

                            $payload = [
                                'user_id' => (int)$data['user_id'],
                                'project_id' => $project->getParent()->id,
                                'order' => $project->count() + 1,
                            ];
                            $request = new ApproveChainRequest($payload);
                            $chainController = new ApproveChainController();
                            $result = json_decode($chainController->storeForFilament($request)->getContent(), true);
                            Filament::notify($result['status'] ? 'success' : 'danger', $result['message']);
                        } catch (Exception $e) {
                            Filament::notify('danger', "Something went wrong please contact support");
                            info($e->getMessage());
                        }
                    }),
            ]);
    }

    protected function canCreate(): bool
    {
        return false;
    }

    protected function canDelete(Model $record): bool
    {
        return false;
    }

    protected function canDeleteAny(): bool
    {
        return false;
    }
}
