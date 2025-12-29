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
            ->add('name', fn(Election $model) => '<div class="max-w-xs truncate" title="' . e($model->name) . '">' . e($model->name) . '</div>')
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
            ->add('status_badge', function (Election $model) {
                $colors = [
                    'draft' => 'bg-gray-100 text-gray-800',
                    'published' => 'bg-green-100 text-green-800',
                    'archived' => 'bg-red-100 text-red-800',
                ];
                $labels = [
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'archived' => 'Archived',
                ];
                $color = $colors[$model->status] ?? 'bg-gray-100 text-gray-800';
                $label = $labels[$model->status] ?? ucfirst($model->status);
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full ' . $color . '">' . $label . '</span>';
            })
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

            Column::make('Status', 'status_badge', 'status')
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

    #[\Livewire\Attributes\On('detail')]
    public function detail($rowId): void
    {
        $this->redirect(route('elections.detail', ['id' => $rowId]), navigate: true);
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->redirect(route('elections.edit', ['id' => $rowId]), navigate: true);
    }

    public function actions(Election $row): array
    {
        return [
            Button::add('detail')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>')
                ->id()
                ->class('inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500')
                ->tooltip('Detail')
                ->dispatch('detail', ['rowId' => $row->id]),
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
