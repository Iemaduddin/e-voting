<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Jurusan;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Tambah Jurusan',
        'pageTitle' => 'Tambah Jurusan',
        'pageDescription' => 'Form untuk menambahkan jurusan baru ke dalam sistem e-voting',
    ]),
]
class extends Component {
    public $nama = '';
    public $kode_jurusan = '';

    public $showSuccess = false;
    public $successMessage = '';
    public $redirectToIndex = true;

    public function mount()
    {
        //
    }

    public function updated($propertyName)
    {
        // Skip validation for empty values on required fields to prevent premature validation
        $value = data_get($this, $propertyName);

        // Skip if value is empty string or null for basic fields
        if (in_array($propertyName, ['nama', 'kode_jurusan']) && empty($value)) {
            return;
        }

        // Validate individual field when it's updated
        $this->validateOnly($propertyName, $this->getRules(), $this->getMessages());
    }

    public function getRules()
    {
        // Base validation rules
        $rules = [
            'nama' => 'required|string|max:255',
            'kode_jurusan' => 'required|string|max:255|unique:jurusans,kode_jurusan',
        ];

        return $rules;
    }

    public function getMessages()
    {
        return [
            'nama.required' => 'Nama lengkap wajib diisi',
            'kode_jurusan.required' => 'Kode jurusan wajib diisi',
            'kode_jurusan.unique' => 'Kode jurusan sudah digunakan',
        ];
    }

    public function createJurusan()
    {
        // Validate with the same rules and messages
        $validated = $this->validate($this->getRules(), $this->getMessages());
        try {
            // Create Jurusan
            Jurusan::create([
                'nama' => $this->nama,
                'kode_jurusan' => $this->kode_jurusan,
            ]);
            if ($this->redirectToIndex) {
                return $this->redirect(route('jurusan.index', ['success' => 'created']), navigate: false);
            } else {
                // Reset form for creating another user
                $this->reset(['nama', 'kode_jurusan']);
                $this->redirectToIndex = true; // Reset to default

                $this->showSuccess = true;
                $this->successMessage = 'Jurusan baru berhasil ditambahkan.';
            }
        } catch (\Exception $e) {
            $this->showSuccess = false;
        }
    }

    public function createAndStay()
    {
        $this->redirectToIndex = false;
        $this->createJurusan();
    }
};

?>

<div class="space-y-6">
    <x-flash-notification :show="$showSuccess" :message="$successMessage" type="success" />


    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit="createJurusan" class="space-y-6">
            <!-- Name -->
            <div>
                <x-input-label for="nama" value="Nama Jurusan" class="text-gray-700 font-semibold" required />
                <x-text-input id="nama" type="text" wire:model.live="nama" class="mt-2 w-full" required
                    autofocus />
                @error('nama')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <!-- Kode Jurusan -->
            <div>
                <x-input-label for="kode_jurusan" value="Kode Jurusan" class="text-gray-700 font-semibold" required />
                <x-text-input id="kode_jurusan" type="text" wire:model.live="kode_jurusan" class="mt-2 w-full"
                    required autofocus />
                @error('kode_jurusan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>


            <!-- Submit Button -->
            <div class="flex items-center justify-between gap-3 pt-6 border-t">
                <a href="{{ route('jurusan.index') }}" wire:navigate
                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 transition duration-150">
                    Kembali
                </a>
                <div class="flex gap-3">
                    <button type="button" wire:click="createAndStay" wire:loading.attr="disabled"
                        wire:target="createAndStay,createJurusan"
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
                    <button type="submit" wire:loading.attr="disabled" wire:target="createJurusan,createAndStay"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading wire:target="createJurusan">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="createJurusan">Tambah Jurusan</span>
                        <span wire:loading wire:target="createJurusan">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
