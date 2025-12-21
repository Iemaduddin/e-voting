<?php

namespace App\Livewire;

use App\Models\Election;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ElectionTable extends PowerGridComponent
{
    public string $tableName = 'electionTable';

    public function setUp(): array
    {

        return [
            PowerGrid::responsive(),
            PowerGrid::header()
                ->showToggleColumns()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Election::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('pamphlet', fn(Election $model) => $model->pamphlet
                ? '<img src="' . asset('storage/' . $model->pamphlet) . '" class="w-20 h-20 object-cover rounded-lg" />'
                : '<span class="text-gray-400">No Image</span>')
            ->add('banner', fn(Election $model) => $model->banner
                ? '<img src="' . asset('storage/' . $model->banner) . '" class="w-20 h-20 object-cover rounded-lg" />'
                : '<span class="text-gray-400">No Image</span>')
            ->add(
                'date_range',
                fn(Election $model) =>
                Carbon::parse($model->start_at)->locale('id')->translatedFormat('d F Y (H.i)') . ' - ' . Carbon::parse($model->end_at)->locale('id')->translatedFormat('d F Y (H.i)')
            )
            ->add('created_at_formatted', fn(Election $model) => Carbon::parse($model->created_at)->locale('id')->translatedFormat('d F Y (H.i)'));
    }

    public function columns(): array
    {
        return [
            Column::make('Nama Pemilihan', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Pamflet', 'pamphlet')
                ->sortable(),

            Column::make('Banner', 'banner')
                ->sortable(),

            Column::make('Periode Pemilihan', 'date_range')
                ->sortable(),

            Column::make('Dibuat Pada', 'created_at_formatted', 'created_at')
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
        $this->redirect(route('elections.edit', ['id' => $rowId]), navigate: true);
    }

    public function actions(Election $row): array
    {
        return [
            Button::add('edit')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>')
                ->id()
                ->class('inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500')
                ->tooltip('Edit')
                ->dispatch('edit', ['rowId' => $row->id]),
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