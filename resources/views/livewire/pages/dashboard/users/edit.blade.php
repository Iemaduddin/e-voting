<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Role;
use App\Models\Prodi;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Edit Pengguna',
        'pageTitle' => 'Edit Pengguna',
        'pageDescription' => 'Form untuk mengubah data pengguna dalam sistem e-voting',
    ]),
]
class extends Component {
    use WithFileUploads;

    public $userId;
    public $user;

    public $name = '';
    public $username = '';
    public $email = '';
    public $password = '';
    public $phone_number = '';
    public $role = '';

    // Mahasiswa fields
    public $nim = '';
    public $prodi_id = '';
    public $tanggal_lahir = '';
    public $jenis_kelamin = '';

    // Organization fields
    public $shorten_name = '';
    public $vision = '';
    public $mision = [''];
    public $description = '';
    public $organization_type = '';
    public $whatsapp_number = '';
    public $logo = '';
    public $currentLogo = '';

    public $initialRole = ''; // Store initial role for detecting changes

    public function mount($id)
    {
        $this->userId = $id;
        $this->user = User::with(['mahasiswa', 'organization', 'roles'])->findOrFail($id);

        // Populate basic fields
        $this->name = $this->user->name;
        $this->username = $this->user->username;
        $this->email = $this->user->email;
        $this->phone_number = $this->user->phone_number;
        $this->role = $this->user->roles->first()?->name ?? '';
        $this->initialRole = $this->role;

        // Populate Mahasiswa fields if role is Voter
        if ($this->role === 'Voter' && $this->user->mahasiswa) {
            $this->nim = $this->user->mahasiswa->nim;
            $this->prodi_id = $this->user->mahasiswa->prodi_id;
            $this->tanggal_lahir = $this->user->mahasiswa->tanggal_lahir?->format('Y-m-d');
            $this->jenis_kelamin = $this->user->mahasiswa->jenis_kelamin;
        }

        // Populate Organization fields if role is Organization
        if ($this->role === 'Organization' && $this->user->organization) {
            $this->shorten_name = $this->user->organization->shorten_name;
            $this->vision = $this->user->organization->vision;
            $this->mision = json_decode($this->user->organization->mision, true) ?? [''];
            $this->description = $this->user->organization->description;
            $this->organization_type = $this->user->organization->organization_type;
            $this->whatsapp_number = $this->user->organization->whatsapp_number;
            $this->currentLogo = $this->user->organization->logo;
        }
    }

    public function addMision()
    {
        $this->mision[] = '';
    }

    public function removeMision($index)
    {
        unset($this->mision[$index]);
        $this->mision = array_values($this->mision);
    }

    public function updated($propertyName)
    {
        // Skip validation for empty values on required fields to prevent premature validation
        $value = data_get($this, $propertyName);

        if (in_array($propertyName, ['role', 'prodi_id', 'jenis_kelamin', 'organization_type']) && empty($value)) {
            return;
        }

        // Validate individual field when it's updated
        $this->validateOnly($propertyName, $this->getRules(), $this->getMessages());
    }

    public function getRules()
    {
        // Base validation rules with unique exception for current user
        $rules = [
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($this->userId)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => 'nullable|string|min:8',
            'phone_number' => 'nullable|numeric|digits_between:10,15',
            'role' => 'required|string',
        ];

        // Add conditional validation based on role
        if ($this->role === 'Voter') {
            $rules['nim'] = ['required', 'string', 'max:15', Rule::unique('mahasiswas', 'nim')->ignore($this->user->mahasiswa?->id)];
            $rules['prodi_id'] = 'required|uuid|exists:prodis,id';
            $rules['tanggal_lahir'] = 'required|date|before:today';
            $rules['jenis_kelamin'] = 'required|in:Laki-laki,Perempuan';
        }

        if ($this->role === 'Organization') {
            $rules['shorten_name'] = ['required', 'string', 'max:100', Rule::unique('organizations', 'shorten_name')->ignore($this->user->organization?->id)];
            $rules['vision'] = 'required|string';
            $rules['mision'] = 'required|array|min:1';
            $rules['mision.*'] = 'required|string';
            $rules['description'] = 'required|string';
            $rules['organization_type'] = 'required|in:HMJ,LT,UKM';
            $rules['whatsapp_number'] = 'nullable|numeric|digits_between:10,15';
            $rules['logo'] = 'nullable|image|mimes:jpg,jpeg,png|max:2048';
        }

        return $rules;
    }

    public function getMessages()
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'phone_number.numeric' => 'Nomor WhatsApp harus berupa angka',
            'phone_number.digits_between' => 'Nomor WhatsApp harus 10-15 digit',
            'role.required' => 'Peran Pengguna wajib dipilih',
            'nim.required' => 'NIM wajib diisi',
            'nim.unique' => 'NIM sudah terdaftar',
            'nim.max' => 'NIM maksimal 15 karakter',
            'prodi_id.required' => 'Program Studi wajib dipilih',
            'prodi_id.exists' => 'Program Studi tidak valid',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'shorten_name.required' => 'Nama singkat organisasi wajib diisi',
            'shorten_name.unique' => 'Nama singkat organisasi sudah digunakan',
            'vision.required' => 'Visi wajib diisi',
            'mision.required' => 'Misi wajib diisi',
            'mision.min' => 'Minimal harus ada 1 misi',
            'mision.*.required' => 'Setiap poin misi wajib diisi',
            'description.required' => 'Deskripsi wajib diisi',
            'organization_type.required' => 'Jenis organisasi wajib dipilih',
            'whatsapp_number.numeric' => 'Nomor WhatsApp harus berupa angka',
            'whatsapp_number.digits_between' => 'Nomor WhatsApp harus 10-15 digit',
            'logo.image' => 'Logo harus berupa file gambar',
            'logo.mimes' => 'Logo harus berformat jpg, jpeg, atau png',
            'logo.max' => 'Ukuran logo maksimal 2MB',
        ];
    }

    public function updateUser()
    {
        $validated = $this->validate($this->getRules(), $this->getMessages());

        DB::beginTransaction();
        try {
            // Update User basic info
            $userData = [
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'] ?? null,
            ];

            // Only update password if provided
            if (!empty($validated['password'])) {
                $userData['password'] = bcrypt($validated['password']);
            }

            $this->user->update($userData);

            // Handle role changes
            $roleChanged = $this->initialRole !== $this->role;

            if ($roleChanged) {
                // Remove old role-specific records
                if ($this->initialRole === 'Voter' && $this->user->mahasiswa) {
                    $this->user->mahasiswa->delete();
                } elseif ($this->initialRole === 'Organization' && $this->user->organization) {
                    // Delete old logo if exists
                    if ($this->user->organization->logo && Storage::disk('public')->exists($this->user->organization->logo)) {
                        Storage::disk('public')->delete($this->user->organization->logo);
                    }
                    $this->user->organization->delete();
                }

                // Sync new role
                $this->user->syncRoles([$this->role]);
            }

            // Handle Voter data
            if ($this->role === 'Voter') {
                $mahasiswaData = [
                    'nim' => $validated['nim'],
                    'prodi_id' => $validated['prodi_id'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                ];

                if ($this->user->mahasiswa && !$roleChanged) {
                    $this->user->mahasiswa->update($mahasiswaData);
                } else {
                    Mahasiswa::create(array_merge(['user_id' => $this->user->id], $mahasiswaData));
                }
            }

            // Handle Organization data
            if ($this->role === 'Organization') {
                $logoPath = $this->currentLogo;

                // Handle new logo upload
                if ($this->logo) {
                    // Delete old logo if exists
                    if ($this->currentLogo && Storage::disk('public')->exists($this->currentLogo)) {
                        Storage::disk('public')->delete($this->currentLogo);
                    }

                    $logoName = uniqid('logo_' . $validated['shorten_name'] . '_') . '.' . $this->logo->getClientOriginalExtension();
                    $logoPath = $this->logo->storeAs('logos', $logoName, 'public');
                }

                $organizationData = [
                    'shorten_name' => $validated['shorten_name'],
                    'vision' => $validated['vision'],
                    'mision' => json_encode($validated['mision']),
                    'description' => $validated['description'],
                    'organization_type' => $validated['organization_type'],
                    'whatsapp_number' => $validated['whatsapp_number'] ?? null,
                    'logo' => $logoPath,
                ];

                if ($this->user->organization && !$roleChanged) {
                    $this->user->organization->update($organizationData);
                } else {
                    Organization::create(array_merge(['user_id' => $this->user->id], $organizationData));
                }
            }

            DB::commit();

            return $this->redirect(route('users.index', ['success' => 'updated']), navigate: false);
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded logo if exists
            if (isset($logoPath) && $logoPath && $logoPath !== $this->currentLogo && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }

            notyf()
                ->duration(3000)
                ->position('x', 'right')
                ->position('y', 'bottom')
                ->addError('Gagal mengupdate pengguna: ' . $e->getMessage());
        }
    }

    public function with()
    {
        return [
            'roles' => Role::where('name', '!=', 'Super Admin')->pluck('name'),
            'prodis' => Prodi::select('id', 'nama_prodi')->get(),
        ];
    }
};

?>

<div class="space-y-6">
    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit="updateUser" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name -->
                <div>
                    <x-input-label for="name" value="Nama Lengkap" class="text-gray-700 font-semibold" required />
                    <x-text-input id="name" type="text" wire:model.live="name" class="mt-2 w-full" required
                        autofocus />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <x-input-label for="username" value="Username" class="text-gray-700 font-semibold" required />
                    <x-text-input id="username" type="text" wire:model.live="username" class="mt-2 w-full"
                        required />
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Email" class="text-gray-700 font-semibold" />
                    <x-text-input id="email" type="email" wire:model.live="email" class="mt-2 w-full" required />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" value="Password" class="text-gray-700 font-semibold" />
                    <x-password-input id="password" model="password" class="mt-2" autocomplete="new-password" />
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah password</p>
                </div>

                <!-- Phone Number -->
                <div>
                    <x-input-label for="phone_number" value="Nomor HP" class="text-gray-700 font-semibold" />
                    <x-text-input id="phone_number" type="text" wire:model.live="phone_number" class="mt-2 w-full"
                        placeholder="08123456789" />
                    @error('phone_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Role -->
                <div>
                    <x-input-label for="role" value="Peran Pengguna" class="text-gray-700 font-semibold" required />
                    <select id="role" wire:model.live="role"
                        class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                        <option value="">Pilih Peran</option>
                        @foreach ($roles as $roleOption)
                            <option value="{{ $roleOption }}">{{ $roleOption }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if ($initialRole !== $role && $role !== '')
                        <p class="mt-1 text-sm text-amber-600">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Perubahan role akan menghapus data terkait role sebelumnya
                        </p>
                    @endif
                </div>
            </div>

            <!-- Mahasiswa Fields (hanya tampil jika role = Voter) -->
            @if ($role === 'Voter')
                <div class="border-t pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Mahasiswa</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- NIM -->
                        <div>
                            <x-input-label for="nim" value="NIM" class="text-gray-700 font-semibold" required />
                            <x-text-input id="nim" type="text" wire:model.live="nim" class="mt-2 w-full"
                                required />
                            @error('nim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Prodi -->
                        <div>
                            <x-input-label for="prodi_id" value="Program Studi" class="text-gray-700 font-semibold"
                                required />
                            <select id="prodi_id" wire:model.live="prodi_id"
                                class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required>
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                                @endforeach
                            </select>
                            @error('prodi_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <x-input-label for="tanggal_lahir" value="Tanggal Lahir" class="text-gray-700 font-semibold"
                                required />
                            <x-text-input id="tanggal_lahir" type="date" wire:model.live="tanggal_lahir"
                                class="mt-2 w-full" required />
                            @error('tanggal_lahir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <x-input-label for="jenis_kelamin" value="Jenis Kelamin" class="text-gray-700 font-semibold"
                                required />
                            <select id="jenis_kelamin" wire:model.live="jenis_kelamin"
                                class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- Organization Fields (hanya tampil jika role = Organization) -->
            @if ($role === 'Organization')
                <div class="border-t pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Organisasi</h3>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Shorten Name -->
                            <div>
                                <x-input-label for="shorten_name" value="Nama Singkat Organisasi"
                                    class="text-gray-700 font-semibold" required />
                                <x-text-input id="shorten_name" type="text" wire:model.live="shorten_name"
                                    class="mt-2 w-full" placeholder="Contoh: HIMTI" required />
                                @error('shorten_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Organization Type -->
                            <div>
                                <x-input-label for="organization_type" value="Jenis Organisasi"
                                    class="text-gray-700 font-semibold" required />
                                <select id="organization_type" wire:model.live="organization_type"
                                    class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="HMJ">HMJ (Himpunan Mahasiswa Jurusan)</option>
                                    <option value="LT">LT (Lembaga Tingkat)</option>
                                    <option value="UKM">UKM (Unit Kegiatan Mahasiswa)</option>
                                </select>
                                @error('organization_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Vision -->
                        <div>
                            <x-input-label for="vision" value="Visi" class="text-gray-700 font-semibold"
                                required />
                            <textarea id="vision" wire:model.live="vision" rows="3"
                                class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required></textarea>
                            @error('vision')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mission -->
                        <div>
                            <x-input-label for="mision" value="Misi" class="text-gray-700 font-semibold"
                                required />
                            <div class="space-y-3 mt-2">
                                @foreach ($mision as $index => $misiItem)
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <div class="flex items-start gap-2">
                                                <span
                                                    class="inline-block mt-3 text-gray-600 font-medium">{{ $index + 1 }}.</span>
                                                <textarea wire:model.live="mision.{{ $index }}" rows="2"
                                                    class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                    placeholder="Masukkan poin misi ke-{{ $index + 1 }}" required></textarea>
                                            </div>
                                            @error('mision.' . $index)
                                                <p class="mt-1 ml-6 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        @if (count($mision) > 1)
                                            <button type="button" wire:click="removeMision({{ $index }})"
                                                class="mt-2 px-3 py-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" wire:click="addMision"
                                class="mt-3 inline-flex items-center px-3 py-2 text-sm bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Poin Misi
                            </button>
                            @error('mision')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" value="Deskripsi" class="text-gray-700 font-semibold"
                                required />
                            <textarea id="description" wire:model.live="description" rows="4"
                                class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- WhatsApp Number -->
                            <div>
                                <x-input-label for="whatsapp_number" value="Nomor WhatsApp"
                                    class="text-gray-700 font-semibold" />
                                <x-text-input id="whatsapp_number" type="text" wire:model.live="whatsapp_number"
                                    class="mt-2 w-full" placeholder="08123456789" />
                                @error('whatsapp_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Opsional - untuk kontak organisasi</p>
                            </div>
                            <!-- Logo -->
                            <div>
                                <x-input-label for="logo" value="Logo Organisasi"
                                    class="text-gray-700 font-semibold" />
                                <input id="logo" type="file" accept="image/png, image/jpeg, image/jpg"
                                    wire:model="logo"
                                    class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                @error('logo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @if ($currentLogo)
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-600 mb-2">Logo saat ini:</p>
                                        <img src="{{ asset('storage/' . $currentLogo) }}" alt="Current Logo"
                                            class="h-20 w-20 object-contain border border-gray-200 rounded-lg p-2">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Submit Button -->
            <div class="flex items-center justify-between gap-3 pt-6 border-t">
                <a href="{{ route('users.index') }}" wire:navigate
                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 transition duration-150">
                    Kembali
                </a>
                <button type="submit" wire:loading.attr="disabled" wire:target="updateUser"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="updateUser">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <span wire:loading wire:target="updateUser">
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="updateUser">Update Pengguna</span>
                    <span wire:loading wire:target="updateUser">Memperbarui...</span>
                </button>
            </div>
        </form>
    </div>
</div>
