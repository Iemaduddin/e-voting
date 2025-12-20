<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Jurusan;
use Illuminate\Validation\Rule;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Edit Jurusan',
        'pageTitle' => 'Edit Jurusan',
        'pageDescription' => 'Form untuk mengubah data jurusan dalam sistem e-voting',
    ]),
]
class extends Component {
    public $jurusanId;
    public $jurusan;

    public $nama = '';
    public $kode_jurusan = '';

    public function mount($id)
    {
        $this->jurusanId = $id;
        $this->jurusan = Jurusan::findOrFail($id);

        // Populate fields
        $this->nama = $this->jurusan->nama;
        $this->kode_jurusan = $this->jurusan->kode_jurusan;
    }

    public function updated($propertyName)
    {
        // Skip validation for empty values on required fields to prevent premature validation
        $value = data_get($this, $propertyName);

        if (in_array($propertyName, ['nama', 'kode_jurusan']) && empty($value)) {
            return;
        }

        // Validate individual field when it's updated
        $this->validateOnly($propertyName, $this->getRules(), $this->getMessages());
    }

    public function getRules()
    {
        return [
            'nama' => 'required|string|max:255',
            'kode_jurusan' => ['required', 'string', 'max:255', Rule::unique('jurusans', 'kode_jurusan')->ignore($this->jurusanId)],
        ];
    }

    public function getMessages()
    {
        return [
            'nama.required' => 'Nama jurusan wajib diisi',
            'kode_jurusan.required' => 'Kode jurusan wajib diisi',
            'kode_jurusan.unique' => 'Kode jurusan sudah digunakan',
        ];
    }

    public function updateJurusan()
    {
        $validated = $this->validate($this->getRules(), $this->getMessages());

        try {
            $this->jurusan->update([
                'nama' => $validated['nama'],
                'kode_jurusan' => $validated['kode_jurusan'],
            ]);

            return $this->redirect(route('jurusan.index', ['success' => 'updated']), navigate: false);
        } catch (\Exception $e) {
            notyf()
                ->duration(3000)
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addError('Gagal mengupdate jurusan: ' . $e->getMessage());
        }
    }
};

?>

<div class="space-y-6">
    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit="updateJurusan" class="space-y-6">
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
                    required />
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
                <button type="submit" wire:loading.attr="disabled" wire:target="updateJurusan"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading wire:target="updateJurusan">
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="updateJurusan">Update Jurusan</span>
                    <span wire:loading wire:target="updateJurusan">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
