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

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Tambah Pengguna',
        'pageTitle' => 'Tambah Pengguna',
        'pageDescription' => 'Form untuk menambahkan pengguna baru ke dalam sistem e-voting',
    ]),
]
class extends Component {
    use WithFileUploads;
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

    public $showSuccess = false;
    public $successMessage = '';
    public $redirectToIndex = true;
    public $notificationKey = 0;

    public function mount()
    {
        //
    }

    public function addMision()
    {
        $this->mision[] = '';
    }

    public function removeMision($index)
    {
        unset($this->mision[$index]);
        $this->mision = array_values($this->mision); // Re-index array
    }

    public function updated($propertyName)
    {
        // Skip validation for empty values on required fields to prevent premature validation
        $value = data_get($this, $propertyName);

        // Skip if value is empty string or null for basic fields
        if (in_array($propertyName, ['role', 'prodi_id', 'jenis_kelamin', 'organization_type']) && empty($value)) {
            return;
        }

        // Validate individual field when it's updated
        $this->validateOnly($propertyName, $this->getRules(), $this->getMessages());
    }

    public function getRules()
    {
        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'phone_number' => 'nullable|numeric|digits_between:10,15',
            'role' => 'required|string',
        ];

        // Add conditional validation based on role
        if ($this->role === 'Voter') {
            $rules['nim'] = 'required|string|max:15|unique:mahasiswas,nim';
            $rules['prodi_id'] = 'required|uuid|exists:prodis,id';
            $rules['tanggal_lahir'] = 'required|date|before:today';
            $rules['jenis_kelamin'] = 'required|in:Laki-laki,Perempuan';
        }

        if ($this->role === 'Organization') {
            $rules['shorten_name'] = 'required|string|max:100|unique:organizations,shorten_name';
            $rules['vision'] = 'required|string';
            $rules['mision'] = 'required|array|min:1';
            $rules['mision.*'] = 'required|string';
            $rules['description'] = 'required|string';
            $rules['organization_type'] = 'required|in:HMJ,LT,UKM';
            $rules['whatsapp_number'] = 'nullable|numeric|digits_between:10,15';
            $rules['logo'] = 'image|mimes:jpg,jpeg,png|max:2048';
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
            'password.required' => 'Password wajib diisi',
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

    public function createUser()
    {
        // Validate with the same rules and messages
        $validated = $this->validate($this->getRules(), $this->getMessages());

        DB::beginTransaction();
        try {
            // Create User
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'phone_number' => $validated['phone_number'] ?? null,
            ]);

            // Assign role
            $user->assignRole($this->role);

            if ($this->role === 'Voter') {
                // Create Mahasiswa record
                Mahasiswa::create([
                    'user_id' => $user->id,
                    'nim' => $validated['nim'],
                    'prodi_id' => $validated['prodi_id'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                ]);
            } elseif ($this->role === 'Organization') {
                // Handle logo upload
                $logoPath = null;
                if ($this->logo) {
                    $logoName = uniqid('logo_' . $validated['shorten_name']) . '.' . $this->logo->getClientOriginalExtension();
                    $logoPath = $this->logo->storeAs('Logo Organization', $logoName, 'public');
                }

                // Create Organization record
                Organization::create([
                    'user_id' => $user->id,
                    'shorten_name' => $validated['shorten_name'],
                    'vision' => $validated['vision'],
                    'mision' => json_encode($validated['mision']), // Store as JSON
                    'description' => $validated['description'],
                    'organization_type' => $validated['organization_type'],
                    'whatsapp_number' => $validated['whatsapp_number'] ?? null,
                    'logo' => $logoPath,
                ]);
            }

            DB::commit();

            if ($this->redirectToIndex) {
                return $this->redirect(route('users.index', ['success' => 'created']), navigate: false);
            } else {
                // Reset form for creating another user
                $this->reset(['name', 'username', 'email', 'password', 'phone_number', 'role', 'nim', 'prodi_id', 'tanggal_lahir', 'jenis_kelamin', 'shorten_name', 'vision', 'mision', 'description', 'organization_type', 'whatsapp_number', 'logo']);
                $this->mision = [''];
                $this->redirectToIndex = true; // Reset to default

                $this->notificationKey++;
                $this->showSuccess = true;
                $this->successMessage = 'Pengguna baru berhasil ditambahkan.';
            }
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded logo if exists
            if (isset($logoPath) && $logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }

            $this->showSuccess = false;
        }
    }

    public function createAndStay()
    {
        $this->redirectToIndex = false;
        $this->createUser();
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
    @if ($showSuccess)
        <div wire:key="notification-{{ $notificationKey }}">
            <x-flash-notification :show="$showSuccess" :message="$successMessage" type="success" />
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit="createUser" class="space-y-6" enctype="multipart/form-data">
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
                    <x-input-label for="email" value="Email" class="text-gray-700 font-semibold" required />
                    <x-text-input id="email" type="email" wire:model.live="email" class="mt-2 w-full" required />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" value="Password" class="text-gray-700 font-semibold" required />
                    <x-password-input id="password" model="password" class="mt-2" required
                        autocomplete="new-password" />
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Minimal 8 karakter</p>
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
                                    class="text-gray-700 font-semibold" required />
                                <input id="logo" type="file" accept="image/png, image/jpeg, image/jpg"
                                    wire:model="logo"
                                    class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                @error('logo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                <div class="flex gap-3">
                    <button type="button" wire:click="createAndStay" wire:loading.attr="disabled"
                        wire:target="createAndStay,createUser"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading wire:target="createAndStay">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="createAndStay">Tambah & Buat Lagi</span>
                        <span wire:loading wire:target="createAndStay">Menyimpan...</span>
                    </button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="createUser,createAndStay"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading wire:target="createUser">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="createUser">Tambah Pengguna</span>
                        <span wire:loading wire:target="createUser">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
