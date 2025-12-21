<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\OrganizationMember;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Kelola Anggota Organisasi',
        'pageTitle' => 'Kelola Anggota Organisasi',
        'pageDescription' => 'Daftar semua anggota organisasi yang terdaftar dalam sistem',
    ]),
]
class extends Component {
    public $showSuccess = false;
    public $successMessage = '';
    public $notificationType = 'success';
    public $notificationKey = 0;
    public $showDeleteModal = false;
    public $showDeactivateModal = false;
    public $deleteMemberId = null;
    public $deleteMemberName = '';
    public $deactivateMemberId = null;
    public $deactivateMemberName = '';

    public function mount()
    {
        $successType = request()->query('success');

        if ($successType === 'created') {
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->successMessage = 'Anggota baru berhasil ditambahkan.';
            $this->js('window.history.replaceState({}, document.title, "' . route('members.index') . '")');
        } elseif ($successType === 'updated') {
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->successMessage = 'Data anggota berhasil diperbarui.';
            $this->js('window.history.replaceState({}, document.title, "' . route('members.index') . '")');
        } elseif (session('success')) {
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->successMessage = session('success');
        }
    }

    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete($rowId, $memberName)
    {
        $this->deleteMemberId = $rowId;
        $this->deleteMemberName = $memberName;
        $this->showDeleteModal = true;
    }

    public function deleteMember()
    {
        if ($this->deleteMemberId) {
            OrganizationMember::find($this->deleteMemberId)?->delete();
            $this->showDeleteModal = false;
            $this->deleteMemberId = null;
            $this->deleteMemberName = '';
            $this->dispatch('pg:eventRefresh-memberTable');

            $this->notificationKey++;
            $this->showSuccess = true;
            $this->notificationType = 'success';
            $this->successMessage = 'Anggota berhasil dihapus.';
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteMemberId = null;
        $this->deleteMemberName = '';
    }

    #[\Livewire\Attributes\On('confirmDeactivate')]
    public function confirmDeactivate($rowId, $memberName)
    {
        $this->deactivateMemberId = $rowId;
        $this->deactivateMemberName = $memberName;
        $this->showDeactivateModal = true;
    }

    public function deactivateMember()
    {
        if ($this->deactivateMemberId) {
            $member = OrganizationMember::find($this->deactivateMemberId);
            if ($member) {
                $member->is_active = false;
                $member->save();
            }
            $this->showDeactivateModal = false;
            $this->deactivateMemberId = null;
            $this->deactivateMemberName = '';
            $this->dispatch('pg:eventRefresh-memberTable');
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->notificationType = 'success';
            $this->successMessage = 'Anggota berhasil dinonaktifkan.';
        }
    }

    public function cancelDeactivate()
    {
        $this->showDeactivateModal = false;
        $this->deactivateMemberId = null;
        $this->deactivateMemberName = '';
    }
};

?>
<x-slot name="headerAction">
    <a href="{{ route('members.create') }}" wire:navigate
        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Tambah Anggota
    </a>
</x-slot>

<div class="space-y-6">
    @if ($showSuccess)
        <div wire:key="notification-{{ $notificationKey }}">
            <x-flash-notification :show="$showSuccess" :message="$successMessage" :type="$notificationType" />
        </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow">
        <livewire:member-table />
    </div>

    <!-- Delete Confirmation Modal -->
    <x-confirm-modal :show="$showDeleteModal" title="Hapus Anggota" :message="'Apakah Anda yakin ingin menghapus anggota <strong>' .
        $deleteMemberName .
        '</strong>? Data yang sudah dihapus tidak dapat dikembalikan.'" confirmText="Hapus" cancelText="Batal"
        confirmAction="deleteMember" cancelAction="cancelDelete" type="danger" />

    <!-- Deactivate Confirmation Modal -->
    <x-confirm-modal :show="$showDeactivateModal" title="Nonaktifkan Anggota" :message="'Apakah Anda yakin ingin menonaktifkan anggota <strong>' .
        $deactivateMemberName .
        '</strong>? Anggota yang dinonaktifkan tidak akan bisa login.'" confirmText="Nonaktifkan"
        cancelText="Batal" confirmAction="deactivateMember" cancelAction="cancelDeactivate" type="warning" />
</div>
