<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\OrganizationMember;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Edit Anggota Organisasi',
        'pageTitle' => 'Edit Anggota Organisasi',
        'pageDescription' => 'Form untuk mengubah data anggota organisasi dalam sistem e-voting',
    ]),
]
class extends Component {
    public $memberId;
    public $member;

    public $nim = '';
    public $name = '';
    public $level = '';
    public $position = '';
    public $is_leader = false;

    public function mount($id)
    {
        $this->memberId = $id;
        $this->member = OrganizationMember::findOrFail($id);

        // Populate fields
        $this->nim = $this->member->nim;
        $this->name = $this->member->name;
        $this->level = $this->member->level;
        $this->position = $this->member->position;
        $this->is_leader = (bool) $this->member->is_leader;
    }

    public function updated($propertyName)
    {
        // Skip validation for empty values on required fields to prevent premature validation
        $value = data_get($this, $propertyName);

        if (in_array($propertyName, ['nim', 'name', 'level', 'position']) && empty($value)) {
            return;
        }

        // Validate individual field when it's updated
        $this->validateOnly($propertyName, $this->getRules(), $this->getMessages());
    }

    public function getRules()
    {
        return [
            'nim' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'level' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'is_leader' => 'boolean',
        ];
    }

    public function getMessages()
    {
        return [
            'nim.required' => 'NIM wajib diisi',
            'name.required' => 'Nama lengkap wajib diisi',
            'level.required' => 'Tingkatan wajib diisi',
            'position.required' => 'Jabatan wajib diisi',
        ];
    }

    public function updateMember()
    {
        $validated = $this->validate($this->getRules(), $this->getMessages());

        try {
            $this->member->update([
                'nim' => $validated['nim'],
                'name' => $validated['name'],
                'level' => $validated['level'],
                'position' => $validated['position'],
                'is_leader' => $validated['is_leader'],
            ]);

            return $this->redirect(route('members.index', ['success' => 'updated']), navigate: false);
        } catch (\Exception $e) {
            notyf()
                ->duration(3000)
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addError('Gagal mengupdate anggota: ' . $e->getMessage());
        }
    }
};

?>

<div class="space-y-6">
    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit="updateMember" class="space-y-6">
            <!-- NIM -->
            <div>
                <x-input-label for="nim" value="NIM" class="text-gray-700 font-semibold" required />
                <x-text-input id="nim" type="text" wire:model.live="nim" class="mt-2 w-full" required autofocus />
                @error('nim')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Lengkap -->
            <div>
                <x-input-label for="name" value="Nama Lengkap" class="text-gray-700 font-semibold" required />
                <x-text-input id="name" type="text" wire:model.live="name" class="mt-2 w-full" required />
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tingkatan -->
            <div>
                <x-input-label for="level" value="Tingkatan" class="text-gray-700 font-semibold" required />
                <x-text-input id="level" type="text" wire:model.live="level" class="mt-2 w-full" required />
                @error('level')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jabatan -->
            <div>
                <x-input-label for="position" value="Jabatan" class="text-gray-700 font-semibold" required />
                <x-text-input id="position" type="text" wire:model.live="position" class="mt-2 w-full" required />
                @error('position')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <!-- Checkbox Pemimpin Organisasi -->
            <div>
                <label class="inline-flex items-center mt-2">
                    <input type="checkbox" wire:model.live="is_leader"
                        class="form-checkbox h-5 w-5 text-blue-600 rounded-md focus:ring-blue-500">
                    <span class="ml-2 text-gray-700">Pemimpin Organisasi</span>
                </label>
                @error('is_leader')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between gap-3 pt-6 border-t">
                <a href="{{ route('members.index') }}" wire:navigate
                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 transition duration-150">
                    Kembali
                </a>
                <button type="submit" wire:loading.attr="disabled" wire:target="updateMember"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading wire:target="updateMember">
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="updateMember">Update Anggota</span>
                    <span wire:loading wire:target="updateMember">Memperbarui...</span>
                </button>
            </div>
        </form>
    </div>
</div>
