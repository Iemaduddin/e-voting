<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Kelola Pemilihan',
        'pageTitle' => 'Kelola Pemilihan',
        'pageDescription' => 'Daftar semua pemilihan yang terdaftar dalam sistem',
    ]),
]
class extends Component {
    public $showSuccess = false;
    public $successMessage = '';
    public $notificationKey = 0;
    public $showDeleteModal = false;
    public $deleteElectionId = null;
    public $deleteElectionName = '';

    public function mount()
    {
        $successType = request()->query('success');

        if ($successType === 'created') {
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->successMessage = 'Election baru berhasil ditambahkan.';
            $this->js('window.history.replaceState({}, document.title, "' . route('elections.index') . '")');
        } elseif ($successType === 'updated') {
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->successMessage = 'Data election berhasil diperbarui.';
            $this->js('window.history.replaceState({}, document.title, "' . route('elections.index') . '")');
        } elseif (session('success')) {
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->successMessage = session('success');
        }
    }

    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete($rowId, $electionName)
    {
        $this->deleteElectionId = $rowId;
        $this->deleteElectionName = $electionName;
        $this->showDeleteModal = true;
    }

    public function deleteElection()
    {
        if ($this->deleteElectionId) {
            \App\Models\Election::find($this->deleteElectionId)?->delete();
            $this->showDeleteModal = false;
            $this->deleteElectionId = null;
            $this->deleteElectionName = '';
            $this->dispatch('pg:eventRefresh-electionTable');
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->successMessage = 'Election berhasil dihapus.';
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteElectionId = null;
        $this->deleteElectionName = '';
    }
};

?>
<x-slot name="headerAction">
    <a href="{{ route('elections.create') }}" wire:navigate
        class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 whitespace-nowrap">
        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        <span class="hidden sm:inline">Tambah Election</span>
        <span class="sm:hidden">Tambah</span>
    </a>
</x-slot>

<div class="space-y-4 sm:space-y-6">
    @if ($showSuccess)
        <div wire:key="notification-{{ $notificationKey }}">
            <x-flash-notification :show="$showSuccess" :message="$successMessage" type="success" />
        </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <livewire:election-table />
    </div>

    <!-- Extend Election Modal -->
    <livewire:extend-election />

    <!-- Delete Confirmation Modal -->
    <x-confirm-modal :show="$showDeleteModal" title="Hapus Election" :message="'Apakah Anda yakin ingin menghapus election <strong>' .
        $deleteElectionName .
        '</strong>? Data yang sudah dihapus tidak dapat dikembalikan.'" confirmText="Hapus" cancelText="Batal"
        confirmAction="deleteElection" cancelAction="cancelDelete" type="danger" />
</div>
