<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Validation\Rule;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Edit Prodi',
        'pageTitle' => 'Edit Prodi',
        'pageDescription' => 'Form untuk mengubah data prodi dalam sistem e-voting',
    ]),
]
class extends Component {
    public $prodiId;
    public $prodi;

    public $nama_prodi = '';
    public $kode_prodi = '';
    public $jurusan_id = '';

    public function mount($id)
    {
        $this->prodiId = $id;
        $this->prodi = Prodi::findOrFail($id);

        // Populate fields
        $this->nama_prodi = $this->prodi->nama_prodi;
        $this->kode_prodi = $this->prodi->kode_prodi;
        $this->jurusan_id = $this->prodi->jurusan_id;
    }

    public function updated($propertyName)
    {
        // Skip validation for empty values on required fields to prevent premature validation
        $value = data_get($this, $propertyName);

        if (in_array($propertyName, ['nama_prodi', 'kode_prodi', 'jurusan_id']) && empty($value)) {
            return;
        }

        // Validate individual field when it's updated
        $this->validateOnly($propertyName, $this->getRules(), $this->getMessages());
    }

    public function getRules()
    {
        return [
            'nama_prodi' => 'required|string|max:255',
            'kode_prodi' => ['required', 'string', 'max:255', Rule::unique('prodis', 'kode_prodi')->ignore($this->prodiId)],
            'jurusan_id' => 'required|exists:jurusans,id',
        ];
    }

    public function getMessages()
    {
        return [
            'nama_prodi.required' => 'Nama prodi wajib diisi',
            'kode_prodi.required' => 'Kode prodi wajib diisi',
            'kode_prodi.unique' => 'Kode prodi sudah digunakan',
            'jurusan_id.required' => 'Jurusan wajib dipilih',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid',
        ];
    }

    public function updateProdi()
    {
        $validated = $this->validate($this->getRules(), $this->getMessages());

        try {
            $this->prodi->update([
                'nama_prodi' => $validated['nama_prodi'],
                'kode_prodi' => $validated['kode_prodi'],
                'jurusan_id' => $validated['jurusan_id'],
            ]);

            return $this->redirect(route('prodi.index', ['success' => 'updated']), navigate: false);
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
        <form wire:submit="updateProdi" class="space-y-6">
            <!-- Name -->
            <div>
                <x-input-label for="nama_prodi" value="Nama Prodi" class="text-gray-700 font-semibold" required />
                <x-text-input id="nama_prodi" type="text" wire:model.live="nama_prodi" class="mt-2 w-full" required
                    autofocus />
                @error('nama')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kode Prodi -->
            <div>
                <x-input-label for="kode_prodi" value="Kode Prodi" class="text-gray-700 font-semibold" required />
                <x-text-input id="kode_prodi" type="text" wire:model.live="kode_prodi" class="mt-2 w-full"
                    required />
                @error('kode_prodi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <!-- Jurusan -->
            <div>
                <x-input-label for="jurusan_id" value="Jurusan" class="text-gray-700 font-semibold" required />
                <select id="jurusan_id" wire:model.live="jurusan_id"
                    class="mt-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 p-3">
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
                <button type="submit" wire:loading.attr="disabled" wire:target="updateProdi"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading wire:target="updateProdi">
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="updateProdi">Update Jurusan</span>
                    <span wire:loading wire:target="updateProdi">Memperbarui...</span>
                </button>
            </div>
        </form>
    </div>
</div>
