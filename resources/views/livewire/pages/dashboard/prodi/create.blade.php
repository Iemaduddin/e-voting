<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Prodi;
use App\Models\Jurusan;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Tambah Prodi',
        'pageTitle' => 'Tambah Prodi',
        'pageDescription' => 'Form untuk menambahkan prodi baru ke dalam sistem e-voting',
    ]),
]
class extends Component {
    public $nama_prodi = '';
    public $kode_prodi = '';
    public $jurusan_id = '';

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
        if (in_array($propertyName, ['nama_prodi', 'kode_prodi', 'jurusan_id']) && empty($value)) {
            return;
        }

        // Validate individual field when it's updated
        $this->validateOnly($propertyName, $this->getRules(), $this->getMessages());
    }

    public function getRules()
    {
        // Base validation rules
        $rules = [
            'nama_prodi' => 'required|string|max:255',
            'kode_prodi' => 'required|string|max:255|unique:prodis,kode_prodi',
            'jurusan_id' => 'required|exists:jurusans,id',
        ];

        return $rules;
    }

    public function getMessages()
    {
        return [
            'nama_prodi.required' => 'Nama lengkap wajib diisi',
            'kode_prodi.required' => 'Kode prodi wajib diisi',
            'kode_prodi.unique' => 'Kode prodi sudah digunakan',
            'jurusan_id.required' => 'Jurusan wajib dipilih',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid',
        ];
    }

    public function createProdi()
    {
        // Validate with the same rules and messages
        $validated = $this->validate($this->getRules(), $this->getMessages());
        try {
            // Create Prodi
            Prodi::create([
                'nama_prodi' => $this->nama_prodi,
                'kode_prodi' => $this->kode_prodi,
                'jurusan_id' => $this->jurusan_id,
            ]);
            if ($this->redirectToIndex) {
                return $this->redirect(route('prodi.index', ['success' => 'created']), navigate: false);
            } else {
                // Reset form for creating another user
                $this->reset(['nama_prodi', 'kode_prodi', 'jurusan_id']);
                $this->redirectToIndex = true; // Reset to default

                $this->showSuccess = true;
                $this->successMessage = 'Prodi baru berhasil ditambahkan.';
            }
        } catch (\Exception $e) {
            $this->showSuccess = false;
        }
    }

    public function createAndStay()
    {
        $this->redirectToIndex = false;
        $this->createProdi();
    }
};

?>

<div class="space-y-6">
    <x-flash-notification :show="$showSuccess" :message="$successMessage" type="success" />


    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit="createProdi" class="space-y-6">
            <!-- Name -->
            <div>
                <x-input-label for="nama_prodi" value="Nama Prodi" class="text-gray-700 font-semibold" required />
                <x-text-input id="nama_prodi" type="text" wire:model.live="nama_prodi" class="mt-2 w-full" required
                    autofocus />
                @error('nama_prodi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <!-- Kode Prodi -->
            <div>
                <x-input-label for="kode_prodi" value="Kode Prodi" class="text-gray-700 font-semibold" required />
                <x-text-input id="kode_prodi" type="text" wire:model.live="kode_prodi" class="mt-2 w-full" required
                    autofocus />
                @error('kode_prodi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <!-- Jurusan -->
            <div>
                <x-input-label for="jurusan_id" value="Jurusan" class="text-gray-700 font-semibold" required />
                <select id="jurusan_id" wire:model.live="jurusan_id"
                    class="mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="" disabled>Pilih Jurusan</option>
                    @foreach (Jurusan::all() as $jurusan)
                        <option value="{{ $jurusan->id }}">{{ $jurusan->nama }}</option>
                    @endforeach
                </select>
                @error('jurusan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>


            <!-- Submit Button -->
            <div class="flex items-center justify-between gap-3 pt-6 border-t">
                <a href="{{ route('prodi.index') }}" wire:navigate
                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 transition duration-150">
                    Kembali
                </a>
                <div class="flex gap-3">
                    <button type="button" wire:click="createAndStay" wire:loading.attr="disabled"
                        wire:target="createAndStay,createProdi"
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
                    <button type="submit" wire:loading.attr="disabled" wire:target="createProdi,createAndStay"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading wire:target="createProdi">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="createProdi">Tambah Prodi</span>
                        <span wire:loading wire:target="createProdi">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
