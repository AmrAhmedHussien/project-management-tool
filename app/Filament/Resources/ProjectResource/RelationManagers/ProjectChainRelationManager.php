<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

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
            ]);
            // ->headerActions([
            //     Tables\Actions\CreateAction::make(),
            //     Tables\Actions\AttachAction::make()
            //         ->preloadRecordSelect()
            //         ->form(fn(Tables\Actions\AttachAction $action): array => [
            //             $action->getRecordSelect(),
            //             Forms\Components\Select::make('role')
            //                 ->label(__('User role'))
            //                 ->searchable()
            //                 ->default(fn() => config('system.projects.affectations.roles.default'))
            //                 ->options(fn() => config('system.projects.affectations.roles.list'))
            //                 ->required(),
            //         ]),
            // ]);
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
