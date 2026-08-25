<?php

namespace App\Filament\App\Pages;

use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class MonEspace extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Mon espace';
    protected static \UnitEnum|string|null $navigationGroup = null;
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.app.pages.mon-espace';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('employee');
    }

    public function getTitle(): string|Htmlable
    {
        return 'Mon espace employé';
    }

    public ?array $leaveData = [];

    public function leaveForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('leaveData')
            ->components([
                Select::make('leave_type_id')
                    ->label('Type de congé')
                    ->options(LeaveType::pluck('name', 'id'))
                    ->required(),
                DatePicker::make('start_date')->label('Date de début')->required(),
                DatePicker::make('end_date')->label('Date de fin')->required(),
                Textarea::make('reason')->label('Motif (optionnel)')->rows(2)->nullable(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('request_leave')
                ->label('Demander un congé')
                ->icon('heroicon-o-calendar-days')
                ->color('primary')
                ->form(fn (Schema $schema) => $this->leaveForm($schema))
                ->action(function (array $data) {
                    $user = auth()->user();

                    Leave::create([
                        'company_id'    => $user->company_id,
                        'employee_id'   => $user->employee_id,
                        'leave_type_id' => $data['leave_type_id'],
                        'start_date'    => $data['start_date'],
                        'end_date'      => $data['end_date'],
                        'reason'        => $data['reason'] ?? null,
                        'status'        => 'en_attente',
                    ]);

                    Notification::make()
                        ->title('Demande envoyée')
                        ->body('Votre demande de congé est en attente de validation.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getLeaveBalances(): array
    {
        $user = auth()->user();
        if (! $user?->employee_id) {
            return [];
        }

        return LeaveBalance::withoutGlobalScopes()
            ->where('employee_id', $user->employee_id)
            ->where('year', now()->year)
            ->with('leaveType')
            ->get()
            ->map(fn ($b) => [
                'type'      => $b->leaveType?->name ?? '—',
                'total'     => $b->total_days,
                'used'      => $b->used_days,
                'remaining' => $b->remaining_days,
            ])
            ->toArray();
    }

    public function getPendingLeaves(): array
    {
        $user = auth()->user();
        if (! $user?->employee_id) {
            return [];
        }

        return Leave::withoutGlobalScopes()
            ->where('employee_id', $user->employee_id)
            ->where('status', 'en_attente')
            ->with('leaveType')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(fn ($l) => [
                'type'  => $l->leaveType?->name ?? '—',
                'debut' => $l->start_date->format('d/m/Y'),
                'fin'   => $l->end_date->format('d/m/Y'),
                'jours' => $l->duration_days,
            ])
            ->toArray();
    }
}
