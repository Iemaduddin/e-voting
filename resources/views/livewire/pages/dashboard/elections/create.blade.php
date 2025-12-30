<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Election;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Tambah Pemilihan',
        'pageTitle' => 'Tambah Pemilihan',
        'pageDescription' => 'Form untuk menambahkan pemilihan baru ke dalam sistem e-voting',
    ]),
]
class extends Component {
    use WithFileUploads;
    public $name = '';
    public $description = '';
    public $pamphlet = '';
    public $banner = '';
    public $start_at = '';
    public $end_at = '';

    public $showSuccess = false;
    public $successMessage = '';
    public $notificationType = 'success';
    public $redirectToIndex = true;
    public $notificationKey = 0;

    public function mount()
    {
        //
    }

    public function getRules()
    {
        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pamphlet' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'banner' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
        ];

        return $rules;
    }

    public function getMessages()
    {
        return [
            'name.required' => 'Nama pemilihan wajib diisi',
            'description.required' => 'Deskripsi wajib diisi',
            'pamphlet.required' => 'Pamflet wajib diisi',
            'pamphlet.image' => 'Pamflet harus berupa gambar',
            'pamphlet.mimes' => 'Pamflet harus berupa file gambar dengan format jpeg, png, atau jpg',
            'pamphlet.max' => 'Ukuran pamflet maksimal 2MB',
            'banner.required' => 'Banner wajib diisi',
            'banner.image' => 'Banner harus berupa gambar',
            'banner.mimes' => 'Banner harus berupa file gambar dengan format jpeg, png, atau jpg',
            'banner.max' => 'Ukuran banner maksimal 2MB',
            'start_at.required' => 'Tanggal mulai wajib diisi',
            'end_at.required' => 'Tanggal selesai wajib diisi',
            'end_at.after' => 'Tanggal selesai harus setelah tanggal mulai',
        ];
    }

    public function createElection()
    {
        // Validate with the same rules and messages
        try {
            $validated = $this->validate($this->getRules(), $this->getMessages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->notificationType = 'error';
            $this->successMessage = 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all());
            return;
        }

        try {
            // userId
            $userId = auth()->id();
            // organizationId - ambil dari organizationElection
            $organizationElection = auth()->user()->organization;

            if (!$organizationElection) {
                $this->notificationKey++;
                $this->showSuccess = true;
                $this->notificationType = 'error';
                $this->successMessage = 'Anda tidak terdaftar sebagai anggota organisasi manapun.';
                return;
            }

            $organizationId = $organizationElection->id;
            $orgType = $organizationElection->organization_type;
            $organizationName = $organizationElection->name;

            // Set default type to organization
            $type = $orgType == 'LT' ? 'general' : ($orgType == 'Jurusan' ? 'department' : 'organization');

            // Sanitize organization name for folder (remove special characters)
            $sanitizedOrgName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $organizationName);

            // Store pamphlet in Pemilihan/{organization_name}/pamflet/
            $pamphletName = uniqid('pamflet_') . '.' . $this->pamphlet->getClientOriginalExtension();
            $pamphletPath = $this->pamphlet->storeAs("Pemilihan/{$sanitizedOrgName}/Pamflet", $pamphletName, 'public');

            // Store banner in Pemilihan/{organization_name}/banner/
            $bannerName = uniqid('banner_') . '.' . $this->banner->getClientOriginalExtension();
            $bannerPath = $this->banner->storeAs("Pemilihan/{$sanitizedOrgName}/Banner", $bannerName, 'public');
            // Create Election
            Election::create([
                'name' => $this->name,
                'description' => $this->description,
                'type' => $type,
                'organization_id' => $organizationId,
                'pamphlet' => $pamphletPath,
                'banner' => $bannerPath,
                'start_at' => $this->start_at,
                'end_at' => $this->end_at,
                'created_by' => $userId,
                'status' => 'draft',
            ]);

            if ($this->redirectToIndex) {
                return $this->redirect(route('elections.index', ['success' => 'created']), navigate: false);
            } else {
                // Reset form for creating another election
                $this->reset(['name', 'description', 'start_at', 'end_at']);
                $this->pamphlet = null;
                $this->banner = null;
                $this->redirectToIndex = true; // Reset to default

                // Increment key untuk trigger re-render notifikasi
                $this->notificationKey++;
                $this->showSuccess = true;
                $this->notificationType = 'success';
                $this->successMessage = 'Pemilihan baru berhasil ditambahkan.';

                // Dispatch browser event untuk reset preview gambar
                $this->dispatch('reset-image-upload');
            }
        } catch (\Exception $e) {
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->notificationType = 'error';
            $this->successMessage = 'Gagal menambahkan pemilihan: ' . $e->getMessage();
        }
    }

    public function createAndStay()
    {
        $this->redirectToIndex = false;
        $this->createElection();
    }
};
?>
<div class="space-y-4 sm:space-y-6">
    @if ($showSuccess)
        <div wire:key="notification-{{ $notificationKey }}">
            <x-flash-notification :show="$showSuccess" :message="$successMessage" :type="$notificationType" />
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
        <form wire:submit="createElection" class="space-y-4 sm:space-y-6" enctype="multipart/form-data">
            <!-- Name -->
            <div>
                <x-input-label for="name" value="Nama Pemilihan" class="text-gray-700 font-semibold" required />
                <textarea id="name" wire:model="name" rows="2"
                    class="mt-1 sm:mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base"
                    required></textarea>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <!-- Description -->
            <div>
                <x-rich-text-editor name="description" label="Deskripsi" wire:model="description"
                    placeholder="Tulis deskripsi di sini..." helperText="Gunakan toolbar untuk memformat teks"
                    required />
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <!-- Start at -->
                <div>
                    <x-input-label for="start_at" value="Tanggal Mulai Pemilihan" class="text-gray-700 font-semibold"
                        required />
                    <x-text-input id="start_at" type="datetime-local" wire:model="start_at" class="mt-1 sm:mt-2 w-full"
                        required autofocus />
                    @error('start_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- End at -->
                <div>
                    <x-input-label for="end_at" value="Tanggal Selesai Pemilihan" class="text-gray-700 font-semibold"
                        required />
                    <x-text-input id="end_at" type="datetime-local" wire:model="end_at" class="mt-1 sm:mt-2 w-full"
                        required autofocus />
                    @error('end_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pamphlet -->
                <div>
                    <x-image-upload name="pamphlet" label="Pamflet Pemilihan" wire:model="pamphlet"
                        accept="image/png, image/jpeg, image/jpg" :maxSize="2048"
                        helperText="PNG, JPG atau JPEG (MAX. 2MB)" required>
                        @error('pamphlet')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </x-image-upload>
                </div>
                <!-- Banner -->
                <div>
                    <x-image-upload name="banner" label="Banner Pemilihan" wire:model="banner"
                        accept="image/png, image/jpeg, image/jpg" :maxSize="2048"
                        helperText="PNG, JPG atau JPEG (MAX. 2MB)" required>
                        @error('banner')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </x-image-upload>
                </div>
            </div>



            <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 sm:pt-6 border-t">
                <a href="{{ route('elections.index') }}" wire:navigate
                    class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 transition duration-150 order-3 sm:order-1">
                    Kembali
                </a>
                <div class="flex flex-col sm:flex-row gap-3 order-1 sm:order-2">
                    <button type="button" wire:click="createAndStay" wire:loading.attr="disabled"
                        wire:target="createAndStay,createElection"
                        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
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
                    <button type="submit" wire:loading.attr="disabled" wire:target="createElection,createAndStay"
                        class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading wire:target="createElection">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="createElection">Tambah Pemilihan</span>
                        <span wire:loading wire:target="createElection">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
