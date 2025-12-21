<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\OrganizationMember;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Tambah Anggota Organisasi',
        'pageTitle' => 'Tambah Anggota Organisasi',
        'pageDescription' => 'Form untuk menambahkan anggota organisasi baru ke dalam sistem e-voting',
    ]),
]
class extends Component {
    public $nim = '';
    public $name = '';
    public $user_id = '';
    public $organization_id = '';
    public $level = '';
    public $position = '';
    public $is_leader = false;

    public $showSuccess = false;
    public $successMessage = '';
    public $notificationType = 'success';
    public $redirectToIndex = true;
    public $notificationKey = 0;

    public function mount()
    {
        //
    }

    public function updated($propertyName)
    {
        // Skip validation for empty values on required fields to prevent premature validation
        $value = data_get($this, $propertyName);

        // Skip if value is empty string or null for basic fields
        if (in_array($propertyName, ['nim', 'name', 'level', 'position', 'is_leader']) && empty($value)) {
            return;
        }

        // Validate individual field when it's updated
        $this->validateOnly($propertyName, $this->getRules(), $this->getMessages());
    }

    public function getRules()
    {
        // Base validation rules
        $rules = [
            'nim' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'level' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'is_leader' => 'required|boolean',
        ];

        return $rules;
    }

    public function getMessages()
    {
        return [
            'nim.required' => 'NIM wajib diisi',
            'name.required' => 'Nama lengkap wajib diisi',
            'level.required' => 'Tingkatan wajib diisi',
            'position.required' => 'Jabatan wajib diisi',
            'is_leader.required' => 'Status pimpinan wajib diisi',
        ];
    }

    public function createMember()
    {
        // Validate with the same rules and messages
        $validated = $this->validate($this->getRules(), $this->getMessages());
        try {
            // userId
            $userId = auth()->id();
            // organizationId - ambil dari organizationMember
            $organizationMember = auth()->user()->organization;

            if (!$organizationMember) {
                $this->notificationKey++;
                $this->showSuccess = true;
                $this->notificationType = 'error';
                $this->successMessage = 'Anda tidak terdaftar sebagai anggota organisasi manapun.';
                return;
            }

            $organizationId = $organizationMember->id;

            // Create Member
            OrganizationMember::create([
                'nim' => $validated['nim'],
                'name' => $validated['name'],
                'user_id' => $userId,
                'organization_id' => $organizationId,
                'level' => $validated['level'],
                'position' => $validated['position'],
                'is_leader' => $validated['is_leader'],
            ]);

            if ($this->redirectToIndex) {
                return $this->redirect(route('members.index', ['success' => 'created']), navigate: false);
            } else {
                // Reset form for creating another user
                $this->reset(['nim', 'name', 'level', 'position', 'is_leader']);
                $this->redirectToIndex = true; // Reset to default

                // Increment key untuk trigger re-render notifikasi
                $this->notificationKey++;
                $this->showSuccess = true;
                $this->notificationType = 'success';
                $this->successMessage = 'Anggota organisasi baru berhasil ditambahkan.';
            }
        } catch (\Exception $e) {
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->notificationType = 'error';
            $this->successMessage = 'Gagal menambahkan anggota: ' . $e->getMessage();
        }
    }

    public function createAndStay()
    {
        $this->redirectToIndex = false;
        $this->createMember();
    }
};

?>

<div class="space-y-6">
    @if ($showSuccess)
        <div wire:key="notification-{{ $notificationKey }}">
            <x-flash-notification :show="$showSuccess" :message="$successMessage" :type="$notificationType" />
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit="createMember" class="space-y-6">
            <!-- NIM -->
            <div>
                <x-input-label for="nim" value="NIM" class="text-gray-700 font-semibold" required />
                <x-text-input id="nim" type="text" wire:model.live="nim" class="mt-2 w-full" required
                    autofocus />
                @error('nim')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <!-- Nama Lengkap -->
            <div>
                <x-input-label for="name" value="Nama Lengkap" class="text-gray-700 font-semibold" required />
                <x-text-input id="name" type="text" wire:model.live="name" class="mt-2 w-full" required
                    autofocus />
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <!-- Level -->
            <div>
                <x-input-label for="level" value="Level" class="text-gray-700 font-semibold" required />
                <select id="level" wire:model.live="level"
                    class="mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="" disabled>Pilih Level</option>
                    <option value="SC">Steering Committee (SC)</option>
                    <option value="OC">Organizing Committee (OC)</option>

                </select>
                @error('level')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <!-- Position/Jabatan -->
            <div>
                <x-input-label for="position" value="Jabatan" class="text-gray-700 font-semibold" required />
                <x-text-input id="position" type="text" wire:model.live="position" class="mt-2 w-full" required
                    autofocus />
                @error('name')
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
                <div class="flex gap-3">
                    <button type="button" wire:click="createAndStay" wire:loading.attr="disabled"
                        wire:target="createAndStay,createMember"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading wire:target="createAndStay">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="createAndStay">Tambah & Buat Lagi</span>
                        <span wire:loading wire:target="createAndStay">Menyimpan...</span>
                    </button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="createMember,createAndStay"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading wire:target="createMember">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="createMember">Tambah Anggota</span>
                        <span wire:loading wire:target="createMember">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
