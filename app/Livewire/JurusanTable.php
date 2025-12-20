<?php

namespace App\Livewire;

use App\Models\Jurusan;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Responsive;

final class JurusanTable extends PowerGridComponent
{
    public string $tableName = 'jurusanTable';

    public function setUp(): array
    {

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Jurusan::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('kode_jurusan')
            ->add('nama')
            ->add('created_at_formatted', fn($user) => Carbon::parse($user->created_at)->format('d/m/Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('Kode Jurusan', 'kode_jurusan')
                ->sortable()
                ->searchable(),
            Column::make('Nama Jurusan', 'nama')
                ->sortable()
                ->searchable(),

            Column::make('Dibuat pada', 'created_at_formatted', 'created_at')
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
        $this->redirect(route('jurusan.edit', ['id' => $rowId]), navigate: true);
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        Jurusan::find($rowId)?->delete();
        $this->dispatch('pg:eventRefresh-jurusanTable');
    }

    public function actions(Jurusan $row): array
    {
        return [
            Button::add('edit')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>')
                ->id()
                ->class('inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500')
                ->tooltip('Edit')
                ->dispatch('edit', ['rowId' => $row->id]),

            Button::add('delete')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>')
                ->id()
                ->class('inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500')
                ->tooltip('Hapus')
                ->dispatch('confirmDelete', ['rowId' => $row->id, 'jurusanName' => $row->nama])
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
