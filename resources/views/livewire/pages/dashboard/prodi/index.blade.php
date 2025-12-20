<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Kelola Prodi',
        'pageTitle' => 'Kelola Prodi',
        'pageDescription' => 'Daftar semua prodi yang terdaftar dalam sistem',
    ]),
]
class extends Component {
    public $showSuccess = false;
    public $successMessage = '';
    public $showDeleteModal = false;
    public $deleteProdiId = null;
    public $deleteProdiName = '';

    public function mount()
    {
        $successType = request()->query('success');

        if ($successType === 'created') {
            $this->showSuccess = true;
            $this->successMessage = 'Prodi baru berhasil ditambahkan.';
            $this->js('window.history.replaceState({}, document.title, "' . route('prodi.index') . '")');
        } elseif ($successType === 'updated') {
            $this->showSuccess = true;
            $this->successMessage = 'Data prodi berhasil diperbarui.';
            $this->js('window.history.replaceState({}, document.title, "' . route('prodi.index') . '")');
        } elseif (session('success')) {
            $this->showSuccess = true;
            $this->successMessage = session('success');
        }
    }

    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete($rowId, $prodiName)
    {
        $this->deleteProdiId = $rowId;
        $this->deleteProdiName = $prodiName;
        $this->showDeleteModal = true;
    }

    public function deleteProdi()
    {
        if ($this->deleteProdiId) {
            \App\Models\Prodi::find($this->deleteProdiId)?->delete();
            $this->showDeleteModal = false;
            $this->deleteProdiId = null;
            $this->deleteProdiName = '';
            $this->dispatch('pg:eventRefresh-prodiTable');
            $this->showSuccess = true;
            $this->successMessage = 'Prodi berhasil dihapus.';
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteProdiId = null;
        $this->deleteProdiName = '';
    }
};

?>
<x-slot name="headerAction">
    <a href="{{ route('prodi.create') }}" wire:navigate
        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Tambah Prodi
    </a>
</x-slot>

<div class="space-y-6">
    <x-flash-notification :show="$showSuccess" :message="$successMessage" type="success" />

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow">
        <livewire:prodi-table />
    </div>

    <!-- Delete Confirmation Modal -->
    <x-confirm-modal :show="$showDeleteModal" title="Hapus Prodi" :message="'Apakah Anda yakin ingin menghapus prodi <strong>' .
        $deleteProdiName .
        '</strong>? Data yang sudah dihapus tidak dapat dikembalikan.'" confirmText="Hapus" cancelText="Batal"
        confirmAction="deleteProdi" cancelAction="cancelDelete" type="danger" />
</div>
