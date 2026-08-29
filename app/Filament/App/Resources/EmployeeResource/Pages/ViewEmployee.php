<?php

namespace App\Filament\App\Resources\EmployeeResource\Pages;

use App\Filament\App\Resources\EmployeeResource;
use App\Models\EmployeeDocument;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class ViewEmployee extends ViewRecord
{
    use WithFileUploads;

    protected static string $resource = EmployeeResource::class;
    protected string $view = 'filament.app.pages.view-employee';

    public $uploadedFile = null;
    public string $documentName = '';

    public function mountCanAuthorizeAccess(): void
    {
        $user = auth()->user();

        if ($user?->hasRole('employee')) {
            abort_unless(
                $user->employee_id && (int) ($this->record?->id ?? $this->record) === (int) $user->employee_id,
                403
            );
            return;
        }

        parent::mountCanAuthorizeAccess();
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Modifier'),
        ];
    }

    public function uploadDocument(): void
    {
        $this->validate([
            'uploadedFile' => 'required|file|max:10240',
            'documentName' => 'required|string|max:255',
        ]);

        $path = $this->uploadedFile->store('employee-documents', 'public');

        EmployeeDocument::create([
            'company_id'  => $this->record->company_id,
            'employee_id' => $this->record->id,
            'name'        => $this->documentName,
            'file_path'   => $path,
            'file_type'   => $this->uploadedFile->getMimeType(),
            'file_size'   => $this->uploadedFile->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        $this->uploadedFile = null;
        $this->documentName = '';
        $this->record->refresh();

        Notification::make()
            ->title('Document ajouté avec succès')
            ->success()
            ->send();
    }

    public function deleteDocument(int $documentId): void
    {
        $doc = EmployeeDocument::find($documentId);
        if ($doc && $doc->employee_id === $this->record->id) {
            Storage::disk('public')->delete($doc->file_path);
            $doc->delete();
            $this->record->refresh();

            Notification::make()->title('Document supprimé')->success()->send();
        }
    }

    public function getLeaveStats(): array
    {
        $leaves = $this->record->leaves()->withoutGlobalScopes()->get();
        return [
            'pris'       => $leaves->where('status', 'approuvé')->sum(fn ($l) => $l->start_date->diffInDays($l->end_date) + 1),
            'en_attente' => $leaves->where('status', 'en_attente')->sum(fn ($l) => $l->start_date->diffInDays($l->end_date) + 1),
            'refuses'    => $leaves->where('status', 'refusé')->sum(fn ($l) => $l->start_date->diffInDays($l->end_date) + 1),
        ];
    }
}
