<?php

namespace App\Livewire;

use App\Models\Candidate;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class CandidateTable extends PowerGridComponent
{
    public string $tableName = 'candidateTable';

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
        return Candidate::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('ketua_id', function ($candidate) {
                return $candidate->ketua->name ?? '-';
            })
            ->add('wakil_id', function ($candidate) {
                return $candidate->wakil->name ?? '-';
            })
            ->add('visi')
            ->add('misi')
            ->add('cv')
            ->add('photo')
            ->add('link')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable()
                ->searchable(),

            Column::make('Election id', 'election_id')
                ->sortable()
                ->searchable(),

            Column::make('Ketua id', 'ketua_id')
                ->sortable()
                ->searchable(),

            Column::make('Wakil id', 'wakil_id')
                ->sortable()
                ->searchable(),

            Column::make('Visi', 'visi')
                ->sortable()
                ->searchable(),

            Column::make('Misi', 'misi')
                ->sortable()
                ->searchable(),

            Column::make('Cv', 'cv')
                ->sortable()
                ->searchable(),

            Column::make('Photo', 'photo')
                ->sortable()
                ->searchable(),

            Column::make('Link', 'link')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Candidate $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
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
