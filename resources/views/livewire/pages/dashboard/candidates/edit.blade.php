<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\OrganizationMember;
use App\Models\Candidate;
use Illuminate\Support\Facades\Storage;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Edit Kandidat',
        'pageTitle' => 'Edit Kandidat',
        'pageDescription' => 'Form untuk mengedit kandidat dalam sistem e-voting',
    ]),
]
class extends Component {
    use WithFileUploads;

    public $candidateId;
    public $candidate;
    public $ketua_id = '';
    public $wakil_id = '';
    public $visi = '';
    public $misi = [''];
    public $ketua_cv = null;
    public $ketua_photo = null;
    public $wakil_cv = null;
    public $wakil_photo = null;
    public $link = '';
    public $electionId = '';
    public $organizationId = '';

    // Existing files from database
    public $existing_ketua_cv = null;
    public $existing_ketua_photo = null;
    public $existing_wakil_cv = null;
    public $existing_wakil_photo = null;

    public $members = [];
    public $registeredUserIds = [];

    public function mount($id)
    {
        $this->candidateId = $id;
        $this->candidate = Candidate::with(['ketua', 'wakil'])->findOrFail($id);
        $this->electionId = $this->candidate->election_id;
        $this->organizationId = auth()->user()->organization->id ?? null;

        // Load existing data
        $this->loadCandidateData();

        if ($this->organizationId) {
            $this->members = OrganizationMember::where('is_active', true)->where('organization_id', $this->organizationId)->with('user')->get();

            // Get user_ids of members who are already registered as candidates (excluding current candidate)
            $this->registeredUserIds = Candidate::where('election_id', $this->electionId)
                ->where('id', '!=', $this->candidateId)
                ->get()
                ->flatMap(function ($candidate) {
                    return [$candidate->ketua_id, $candidate->wakil_id];
                })
                ->filter()
                ->unique()
                ->values()
                ->toArray();
        }
    }

    public function loadCandidateData()
    {
        // Find organization members by user_id
        $ketuaMember = OrganizationMember::where('user_id', $this->candidate->ketua_id)->where('organization_id', $this->organizationId)->first();
        $wakilMember = $this->candidate->wakil_id ? OrganizationMember::where('user_id', $this->candidate->wakil_id)->where('organization_id', $this->organizationId)->first() : null;

        $this->ketua_id = $ketuaMember?->id ?? '';
        $this->wakil_id = $wakilMember?->id ?? '';
        $this->visi = $this->candidate->visi;

        // Load misi
        $misiData = is_array($this->candidate->misi) ? $this->candidate->misi : json_decode($this->candidate->misi, true);
        $this->misi = $misiData ?: [''];

        $this->link = $this->candidate->link;

        // Load existing files
        $cvData = is_array($this->candidate->cv) ? $this->candidate->cv : json_decode($this->candidate->cv, true);
        $photoData = is_array($this->candidate->photo) ? $this->candidate->photo : json_decode($this->candidate->photo, true);

        $this->existing_ketua_cv = $cvData['ketua'] ?? null;
        $this->existing_wakil_cv = $cvData['wakil'] ?? null;
        $this->existing_ketua_photo = $photoData['ketua'] ?? null;
        $this->existing_wakil_photo = $photoData['wakil'] ?? null;
    }

    public function addMisi()
    {
        $this->misi[] = '';
    }

    public function removeMisi($index)
    {
        unset($this->misi[$index]);
        $this->misi = array_values($this->misi);
    }

    public function getRules()
    {
        $rules = [
            'ketua_id' => [
                'required',
                'exists:organization_members,id',
                function ($attribute, $value, $fail) {
                    $member = OrganizationMember::find($value);
                    if ($member && in_array($member->user_id, $this->registeredUserIds)) {
                        $fail('Anggota ini sudah terdaftar sebagai kandidat dalam pemilihan ini.');
                    }
                },
            ],
            'wakil_id' => [
                'nullable',
                'exists:organization_members,id',
                'different:ketua_id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $member = OrganizationMember::find($value);
                        if ($member && in_array($member->user_id, $this->registeredUserIds)) {
                            $fail('Anggota ini sudah terdaftar sebagai kandidat dalam pemilihan ini.');
                        }
                    }
                },
            ],
            'visi' => 'required|string',
            'misi' => 'required|array|min:1',
            'misi.*' => 'required|string',
            'ketua_cv' => 'nullable|file|mimes:pdf|max:2048',
            'ketua_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'wakil_cv' => 'nullable|file|mimes:pdf|max:2048',
            'wakil_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|url',
        ];

        return $rules;
    }

    public function getMessages()
    {
        return [
            'ketua_id.required' => 'Ketua wajib diisi',
            'ketua_id.exists' => 'Ketua tidak valid',
            'wakil_id.exists' => 'Wakil tidak valid',
            'wakil_id.different' => 'Wakil tidak boleh sama dengan Ketua',
            'visi.required' => 'Visi wajib diisi',
            'misi.required' => 'Misi wajib diisi',
            'misi.*.required' => 'Setiap poin misi wajib diisi',
            'ketua_cv.file' => 'CV Ketua harus berupa file',
            'ketua_cv.mimes' => 'CV Ketua harus berupa file PDF',
            'ketua_cv.max' => 'Ukuran CV Ketua maksimal 2MB',
            'ketua_photo.image' => 'Foto Ketua harus berupa gambar',
            'ketua_photo.mimes' => 'Foto Ketua harus berupa file JPEG, PNG, JPG, atau GIF',
            'ketua_photo.max' => 'Ukuran foto Ketua maksimal 2MB',
            'wakil_cv.file' => 'CV Wakil harus berupa file',
            'wakil_cv.mimes' => 'CV Wakil harus berupa file PDF',
            'wakil_cv.max' => 'Ukuran CV Wakil maksimal 2MB',
            'wakil_photo.image' => 'Foto Wakil harus berupa gambar',
            'wakil_photo.mimes' => 'Foto Wakil harus berupa file JPEG, PNG, JPG, atau GIF',
            'wakil_photo.max' => 'Ukuran foto Wakil maksimal 2MB',
            'link.url' => 'Link harus berupa URL yang valid',
        ];
    }

    public function updateCandidate()
    {
        try {
            $validated = $this->validate($this->getRules(), $this->getMessages());

            // Handle file uploads - keep existing if no new file uploaded
            $ketuaCvPath = $this->existing_ketua_cv;
            $ketuaPhotoPath = $this->existing_ketua_photo;
            $wakilCvPath = $this->existing_wakil_cv;
            $wakilPhotoPath = $this->existing_wakil_photo;

            // Update Ketua CV if new file uploaded
            if ($this->ketua_cv) {
                // Delete old file if exists
                if ($this->existing_ketua_cv && Storage::disk('public')->exists($this->existing_ketua_cv)) {
                    Storage::disk('public')->delete($this->existing_ketua_cv);
                }
                $ketuaCvPath = $this->ketua_cv->store("Kandidat/{$this->electionId}/CV/Ketua", 'public');
            }

            // Update Ketua Photo if new file uploaded
            if ($this->ketua_photo) {
                if ($this->existing_ketua_photo && Storage::disk('public')->exists($this->existing_ketua_photo)) {
                    Storage::disk('public')->delete($this->existing_ketua_photo);
                }
                $ketuaPhotoPath = $this->ketua_photo->store("Kandidat/{$this->electionId}/Photo/Ketua", 'public');
            }

            // Handle Wakil files
            if ($this->wakil_id) {
                if ($this->wakil_cv) {
                    if ($this->existing_wakil_cv && Storage::disk('public')->exists($this->existing_wakil_cv)) {
                        Storage::disk('public')->delete($this->existing_wakil_cv);
                    }
                    $wakilCvPath = $this->wakil_cv->store("Kandidat/{$this->electionId}/CV/Wakil", 'public');
                }

                if ($this->wakil_photo) {
                    if ($this->existing_wakil_photo && Storage::disk('public')->exists($this->existing_wakil_photo)) {
                        Storage::disk('public')->delete($this->existing_wakil_photo);
                    }
                    $wakilPhotoPath = $this->wakil_photo->store("Kandidat/{$this->electionId}/Photo/Wakil", 'public');
                }
            } else {
                // If wakil is removed, delete wakil files
                if ($this->existing_wakil_cv && Storage::disk('public')->exists($this->existing_wakil_cv)) {
                    Storage::disk('public')->delete($this->existing_wakil_cv);
                }
                if ($this->existing_wakil_photo && Storage::disk('public')->exists($this->existing_wakil_photo)) {
                    Storage::disk('public')->delete($this->existing_wakil_photo);
                }
                $wakilCvPath = null;
                $wakilPhotoPath = null;
            }

            // Prepare JSON data
            $cvData = json_encode([
                'ketua' => $ketuaCvPath,
                'wakil' => $wakilCvPath,
            ]);

            $photoData = json_encode([
                'ketua' => $ketuaPhotoPath,
                'wakil' => $wakilPhotoPath,
            ]);

            // Get user_id from organization members
            $ketuaMember = OrganizationMember::find($this->ketua_id);
            $wakilMember = $this->wakil_id ? OrganizationMember::find($this->wakil_id) : null;

            $ketuaUserId = $ketuaMember->user_id;
            $wakilUserId = $wakilMember ? $wakilMember->user_id : null;

            // Convert misi array to JSON string
            $misiJson = json_encode(array_filter($this->misi));

            // Update Candidate
            $this->candidate->update([
                'ketua_id' => $ketuaUserId,
                'wakil_id' => $wakilUserId,
                'visi' => $this->visi,
                'misi' => $misiJson,
                'cv' => $cvData,
                'photo' => $photoData,
                'link' => $this->link,
            ]);

            return redirect()
                ->route('elections.detail', ['id' => $this->electionId])
                ->with('success', 'Kandidat berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error updating candidate: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return;
        }
    }
};

?>

<div class="space-y-6">
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit="updateCandidate" class="space-y-6">

            <!-- Ketua -->
            <div>
                <x-input-label for="ketua_id" value="Ketua" class="text-gray-700 font-semibold" required />
                <select id="ketua_id" wire:model.live="ketua_id"
                    class="mt-2 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                    <option value="">Pilih Ketua</option>
                    @foreach ($members as $member)
                        @php
                            $isRegistered = in_array($member->user_id, $registeredUserIds);
                        @endphp
                        <option value="{{ $member->id }}" @disabled($isRegistered)
                            @if ($isRegistered) class="text-gray-400" @endif>
                            {{ $member->name }} - {{ $member->nim }}
                            @if ($isRegistered)
                                (Sudah Terdaftar)
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('ketua_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Wakil -->
            <div>
                <x-input-label for="wakil_id" value="Wakil (Opsional)" class="text-gray-700 font-semibold" />
                <select id="wakil_id" wire:model.live="wakil_id"
                    class="mt-2 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                    <option value="">Pilih Wakil (Opsional)</option>
                    @foreach ($members as $member)
                        @php
                            $isRegistered = in_array($member->user_id, $registeredUserIds);
                        @endphp
                        <option value="{{ $member->id }}" @disabled($isRegistered)
                            @if ($isRegistered) class="text-gray-400" @endif>
                            {{ $member->name }} - {{ $member->nim }}
                            @if ($isRegistered)
                                (Sudah Terdaftar)
                            @endif
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">Kosongkan jika kandidat tidak memiliki wakil</p>
                @error('wakil_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Divider -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload Foto & CV</h3>
                <div class="mb-4">
                    <p class="text-sm text-amber-600 flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Upload file baru untuk mengganti file yang sudah ada. Jika tidak upload file baru, file
                            lama akan tetap digunakan.</span>
                    </p>
                </div>
            </div>

            <!-- Layout Conditional -->
            @if ($wakil_id)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Foto Ketua -->
                    <div>
                        <x-image-upload name="ketua_photo" label="Foto Ketua" wire:model="ketua_photo"
                            accept="image/png,image/jpeg,image/jpg" helperText="Format: JPG, PNG. Maksimal 2MB" />
                        @if ($existing_ketua_photo && !$ketua_photo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $existing_ketua_photo) }}" alt="Current Photo"
                                    class="w-32 h-32 object-cover rounded-lg border">
                                <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                            </div>
                        @endif
                        @error('ketua_photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Foto Wakil -->
                    <div>
                        <x-image-upload name="wakil_photo" label="Foto Wakil" wire:model="wakil_photo"
                            accept="image/png,image/jpeg,image/jpg" helperText="Format: JPG, PNG. Maksimal 2MB" />
                        @if ($existing_wakil_photo && !$wakil_photo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $existing_wakil_photo) }}" alt="Current Photo"
                                    class="w-32 h-32 object-cover rounded-lg border">
                                <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                            </div>
                        @endif
                        @error('wakil_photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CV Ketua -->
                    <div>
                        <x-input-label for="ketua_cv" value="CV Ketua" class="text-gray-700 font-semibold" />
                        <input type="file" id="ketua_cv" wire:model="ketua_cv" accept=".pdf"
                            class="mt-2 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                        @if ($existing_ketua_cv && !$ketua_cv)
                            <a href="{{ asset('storage/' . $existing_ketua_cv) }}" target="_blank"
                                class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Lihat CV Saat Ini
                            </a>
                        @endif
                        <p class="mt-1 text-sm text-gray-500">Format: PDF. Maksimal 2MB</p>
                        @error('ketua_cv')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div wire:loading wire:target="ketua_cv" class="mt-2 text-sm text-blue-600">
                            Mengupload file...
                        </div>
                    </div>

                    <!-- CV Wakil -->
                    <div>
                        <x-input-label for="wakil_cv" value="CV Wakil" class="text-gray-700 font-semibold" />
                        <input type="file" id="wakil_cv" wire:model="wakil_cv" accept=".pdf"
                            class="mt-2 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                        @if ($existing_wakil_cv && !$wakil_cv)
                            <a href="{{ asset('storage/' . $existing_wakil_cv) }}" target="_blank"
                                class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Lihat CV Saat Ini
                            </a>
                        @endif
                        <p class="mt-1 text-sm text-gray-500">Format: PDF. Maksimal 2MB</p>
                        @error('wakil_cv')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div wire:loading wire:target="wakil_cv" class="mt-2 text-sm text-blue-600">
                            Mengupload file...
                        </div>
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    <!-- Foto Ketua -->
                    <div>
                        <x-image-upload name="ketua_photo" label="Foto Ketua" wire:model="ketua_photo"
                            accept="image/png,image/jpeg,image/jpg" helperText="Format: JPG, PNG. Maksimal 2MB" />
                        @if ($existing_ketua_photo && !$ketua_photo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $existing_ketua_photo) }}" alt="Current Photo"
                                    class="w-32 h-32 object-cover rounded-lg border">
                                <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                            </div>
                        @endif
                        @error('ketua_photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CV Ketua -->
                    <div>
                        <x-input-label for="ketua_cv_single" value="CV Ketua" class="text-gray-700 font-semibold" />
                        <input type="file" id="ketua_cv_single" wire:model="ketua_cv" accept=".pdf"
                            class="mt-2 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                        @if ($existing_ketua_cv && !$ketua_cv)
                            <a href="{{ asset('storage/' . $existing_ketua_cv) }}" target="_blank"
                                class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Lihat CV Saat Ini
                            </a>
                        @endif
                        <p class="mt-1 text-sm text-gray-500">Format: PDF. Maksimal 2MB</p>
                        @error('ketua_cv')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div wire:loading wire:target="ketua_cv" class="mt-2 text-sm text-blue-600">
                            Mengupload file...
                        </div>
                    </div>
                </div>
            @endif

            <!-- Divider - Visi & Misi -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Visi & Misi</h3>
            </div>

            <!-- Visi -->
            <div>
                <x-input-label for="visi" value="Visi" class="text-gray-700 font-semibold" required />
                <textarea id="visi" wire:model.defer="visi" rows="4"
                    class="mt-2 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                    placeholder="Masukkan visi kandidat..." required></textarea>
                @error('visi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Misi -->
            <div>
                <x-input-label for="misi" value="Misi" class="text-gray-700 font-semibold" required />
                <div class="mt-2 space-y-3">
                    @foreach ($misi as $index => $item)
                        <div class="flex gap-2">
                            <textarea wire:model.defer="misi.{{ $index }}" rows="2"
                                class="flex-1 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                placeholder="Poin misi {{ $index + 1 }}..." required></textarea>
                            @if (count($misi) > 1)
                                <button type="button" wire:click="removeMisi({{ $index }})"
                                    class="px-3 py-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button type="button" wire:click="addMisi"
                    class="mt-3 inline-flex items-center px-3 py-2 text-sm bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Poin Misi
                </button>
                @error('misi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Link -->
            <div>
                <x-input-label for="link" value="Link Tambahan (Opsional)" class="text-gray-700 font-semibold" />
                <x-text-input id="link" type="url" wire:model.live="link" class="mt-2 w-full"
                    placeholder="https://example.com" />
                <p class="mt-1 text-sm text-gray-500">Link ke website, video kampanye, atau media sosial</p>
                @error('link')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between gap-3 pt-6 border-t">
                <a href="{{ route('elections.detail', ['id' => $electionId]) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 transition duration-150">
                    Kembali
                </a>
                <button type="submit" wire:loading.attr="disabled" wire:target="updateCandidate"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading wire:target="updateCandidate">
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="updateCandidate">Perbarui Kandidat</span>
                    <span wire:loading wire:target="updateCandidate">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
