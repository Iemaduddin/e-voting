<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Kelola Pengguna',
        'pageTitle' => 'Kelola Pengguna',
        'pageDescription' => 'Daftar semua pengguna yang terdaftar dalam sistem',
    ]),
]
class extends Component {
    public $showSuccess = false;
    public $successMessage = '';
    public $notificationKey = 0;
    public $showDeleteModal = false;
    public $deleteUserId = null;
    public $deleteUserName = '';

    public function mount()
    {
        $successType = request()->query('success');

        if ($successType === 'created') {
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->successMessage = 'Pengguna baru berhasil ditambahkan.';
            $this->js('window.history.replaceState({}, document.title, "' . route('users.index') . '")');
        } elseif ($successType === 'updated') {
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->successMessage = 'Data pengguna berhasil diperbarui.';
            $this->js('window.history.replaceState({}, document.title, "' . route('users.index') . '")');
        } elseif (session('success')) {
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->successMessage = session('success');
        }
    }

    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete($rowId, $userName)
    {
        $this->deleteUserId = $rowId;
        $this->deleteUserName = $userName;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        if ($this->deleteUserId) {
            \App\Models\User::find($this->deleteUserId)?->delete();
            $this->showDeleteModal = false;
            $this->deleteUserId = null;
            $this->deleteUserName = '';
            $this->dispatch('pg:eventRefresh-userTable');

            $this->notificationKey++;
            $this->showSuccess = true;
            $this->successMessage = 'Pengguna berhasil dihapus.';
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteUserId = null;
        $this->deleteUserName = '';
    }
};

?>
<x-slot name="headerAction">
    <a href="{{ route('users.create') }}" wire:navigate
        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Tambah Pengguna
    </a>
</x-slot>

<div class="space-y-6">
    @if ($showSuccess)
        <div wire:key="notification-{{ $notificationKey }}">
            <x-flash-notification :show="$showSuccess" :message="$successMessage" type="success" />
        </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow">
        <livewire:user-table />
    </div>

    <!-- Delete Confirmation Modal -->
    <x-confirm-modal :show="$showDeleteModal" title="Hapus Pengguna" :message="'Apakah Anda yakin ingin menghapus pengguna <strong>' .
        $deleteUserName .
        '</strong>? Data yang sudah dihapus tidak dapat dikembalikan.'" confirmText="Hapus" cancelText="Batal"
        confirmAction="deleteUser" cancelAction="cancelDelete" type="danger" />
</div>
