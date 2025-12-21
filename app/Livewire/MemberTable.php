<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\OrganizationMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Responsive;

final class MemberTable extends PowerGridComponent
{
    public string $tableName = 'memberTable';

    public function setUp(): array
    {
        return [
            PowerGrid::responsive(),
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return OrganizationMember::query()->where('is_active', true)->where('organization_id', Auth::user()->organization->id);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nim')
            ->add('name')
            ->add('user_id')
            ->add('organization_id')
            ->add('level')
            ->add('position')
            ->add('is_leader')
            ->add('jabatan', function ($member) {
                $position = $member->position;
                $leaderType = $member->is_leader ? '(Pimpinan Organisasi)' : '';
                return $position . '<br><span class="text-sm font-semibold">' . $leaderType . '</span>';
            })
            ->add('created_at_formatted', fn($user) => Carbon::parse($user->created_at)->format('d/m/Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('NIM', 'nim', 'nim')
                ->sortable()
                ->searchable(),
            Column::make('Nama', 'name', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Tingkatan', 'level', 'level')
                ->sortable()
                ->searchable(),
            Column::make('Jabatan', 'jabatan', 'position')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Aksi')
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->redirect(route('members.edit', ['id' => $rowId]), navigate: true);
    }

    public function actions(OrganizationMember $row): array
    {
        return [
            Button::add('edit')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>')
                ->id()
                ->class('inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500')
                ->tooltip('Edit')
                ->dispatch('edit', ['rowId' => $row->id]),

            Button::add('deactivate')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>')
                ->id()
                ->class('inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500')
                ->tooltip('Nonaktifkan Member')
                ->dispatch('confirmDeactivate', ['rowId' => $row->id, 'memberName' => $row->name]),

            Button::add('delete')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>')
                ->id()
                ->class('inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500')
                ->tooltip('Hapus')
                ->dispatch('confirmDelete', ['rowId' => $row->id, 'memberName' => $row->name])
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
