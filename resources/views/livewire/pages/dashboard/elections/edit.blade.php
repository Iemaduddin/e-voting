<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Election;
use Illuminate\Support\Facades\Storage;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Edit Pemilihan',
        'pageTitle' => 'Edit Pemilihan',
        'pageDescription' => 'Form untuk mengedit data pemilihan',
    ]),
]
class extends Component {
    use WithFileUploads;

    public $electionId;
    public $election;
    public $name = '';
    public $description = '';
    public $pamphlet = null;
    public $banner = null;
    public $status = '';
    public $start_at = '';
    public $end_at = '';

    // For storing current images
    public $currentPamphlet = '';
    public $currentBanner = '';

    // For displaying images in component
    public $pamphletUrl = '';
    public $bannerUrl = '';

    public $showSuccess = false;
    public $successMessage = '';
    public $notificationType = 'success';
    public $notificationKey = 0;

    public function mount($id)
    {
        $this->electionId = $id;
        // Eager load organization to prevent N+1 query
        $this->election = Election::with('organization')->findOrFail($id);
        $this->name = $this->election->name;
        $this->description = $this->election->description ?? '';
        $this->status = $this->election->status ?? 'draft';
        // Format datetime for datetime-local input (Y-m-d\TH:i)
        $this->start_at = \Carbon\Carbon::parse($this->election->start_at)->format('Y-m-d\TH:i');
        $this->end_at = \Carbon\Carbon::parse($this->election->end_at)->format('Y-m-d\TH:i');
        $this->currentPamphlet = $this->election->pamphlet;
        $this->currentBanner = $this->election->banner;

        // Set image URLs for display - only generate if exists
        if ($this->election->pamphlet) {
            $this->pamphletUrl = asset('storage/' . $this->election->pamphlet);
        }
        if ($this->election->banner) {
            $this->bannerUrl = asset('storage/' . $this->election->banner);
        }
    }

    public function getRules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'status' => 'required|in:draft,published,archived',
        ];

        // Only validate images if new files are uploaded
        if ($this->pamphlet && is_object($this->pamphlet)) {
            $rules['pamphlet'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        }

        if ($this->banner && is_object($this->banner)) {
            $rules['banner'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        }

        return $rules;
    }

    public function getMessages()
    {
        return [
            'name.required' => 'Nama pemilihan wajib diisi',
            'description.required' => 'Deskripsi wajib diisi',
            'pamphlet.image' => 'Pamflet harus berupa gambar',
            'pamphlet.mimes' => 'Pamflet harus berupa file gambar dengan format jpeg, png, atau jpg',
            'pamphlet.max' => 'Ukuran pamflet maksimal 2MB',
            'banner.image' => 'Banner harus berupa gambar',
            'banner.mimes' => 'Banner harus berupa file gambar dengan format jpeg, png, atau jpg',
            'banner.max' => 'Ukuran banner maksimal 2MB',
            'status.required' => 'Status pemilihan wajib diisi',
            'status.in' => 'Status pemilihan tidak valid',
            'start_at.required' => 'Tanggal mulai wajib diisi',
            'end_at.required' => 'Tanggal selesai wajib diisi',
            'end_at.after' => 'Tanggal selesai harus setelah tanggal mulai',
        ];
    }

    public function updateElection()
    {
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
            $organizationElection = $this->election->organization;
            $organizationName = $organizationElection->name;
            $sanitizedOrgName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $organizationName);

            $updateData = [
                'name' => $this->name,
                'description' => $this->description,
                'start_at' => $this->start_at,
                'end_at' => $this->end_at,
                'status' => $this->status,
            ];

            // Handle pamphlet upload
            if ($this->pamphlet && is_object($this->pamphlet)) {
                // Delete old pamphlet
                if ($this->election->pamphlet && Storage::disk('public')->exists($this->election->pamphlet)) {
                    Storage::disk('public')->delete($this->election->pamphlet);
                }

                // Store new pamphlet
                $pamphletName = uniqid('pamflet_') . '.' . $this->pamphlet->getClientOriginalExtension();
                $pamphletPath = $this->pamphlet->storeAs("Pemilihan/{$sanitizedOrgName}/Pamflet", $pamphletName, 'public');
                $updateData['pamphlet'] = $pamphletPath;
            }

            // Handle banner upload
            if ($this->banner && is_object($this->banner)) {
                // Delete old banner
                if ($this->election->banner && Storage::disk('public')->exists($this->election->banner)) {
                    Storage::disk('public')->delete($this->election->banner);
                }

                // Store new banner
                $bannerName = uniqid('banner_') . '.' . $this->banner->getClientOriginalExtension();
                $bannerPath = $this->banner->storeAs("Pemilihan/{$sanitizedOrgName}/Banner", $bannerName, 'public');
                $updateData['banner'] = $bannerPath;
            }

            // Update Election
            $this->election->update($updateData);

            return $this->redirect(route('elections.index', ['success' => 'updated']), navigate: false);
        } catch (\Exception $e) {
            $this->notificationKey++;
            $this->showSuccess = true;
            $this->notificationType = 'error';
            $this->successMessage = 'Gagal mengupdate pemilihan: ' . $e->getMessage();
        }
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
        <form wire:submit="updateElection" class="space-y-6" enctype="multipart/form-data">
            <!-- Name -->
            <div>
                <x-input-label for="name" value="Nama Pemilihan" class="text-gray-700 font-semibold" required />
                <textarea id="name" wire:model.defer="name" rows="2"
                    class="mt-2 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required></textarea>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div wire:ignore>
                <x-rich-text-editor name="description" label="Deskripsi" wire:model="description"
                    placeholder="Tulis deskripsi di sini..." helperText="Gunakan toolbar untuk memformat teks" />
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Start at -->
                <div>
                    <x-input-label for="start_at" value="Tanggal Mulai Pemilihan" class="text-gray-700 font-semibold"
                        required />
                    <x-text-input id="start_at" type="datetime-local" wire:model.defer="start_at" class="mt-2 w-full"
                        required autofocus />
                    @error('start_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End at -->
                <div>
                    <x-input-label for="end_at" value="Tanggal Selesai Pemilihan" class="text-gray-700 font-semibold"
                        required />
                    <x-text-input id="end_at" type="datetime-local" wire:model.defer="end_at" class="mt-2 w-full"
                        required autofocus />
                    @error('end_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pamphlet -->
                <div>
                    <x-image-upload name="pamphlet" label="Pamflet Pemilihan" wire:model="pamphlet"
                        accept="image/png, image/jpeg, image/jpg" :maxSize="2048"
                        helperText="PNG, JPG atau JPEG (MAX. 2MB) - Kosongkan jika tidak ingin mengubah"
                        :currentImage="$pamphletUrl">
                        @error('pamphlet')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </x-image-upload>
                </div>

                <!-- Banner -->
                <div>
                    <x-image-upload name="banner" label="Banner Pemilihan" wire:model="banner"
                        accept="image/png, image/jpeg, image/jpg" :maxSize="2048"
                        helperText="PNG, JPG atau JPEG (MAX. 2MB) - Kosongkan jika tidak ingin mengubah"
                        :currentImage="$bannerUrl">
                        @error('banner')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </x-image-upload>
                </div>
            </div>

            <!-- Status -->
            <div>
                <x-input-label for="status" value="Status Publikasi" class="text-gray-700 font-semibold" required />
                <select id="status" wire:model.defer="status"
                    class="mt-2 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                    <option value="draft">Draft (Tidak tampil di halaman voter)</option>
                    <option value="published">Published (Tampil di halaman voter)</option>
                    <option value="archived">Archived (Disembunyikan, sudah selesai)</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">
                    <span class="font-semibold">Draft:</span> Pemilihan tidak akan terlihat oleh voter. <br>
                    <span class="font-semibold">Published:</span> Pemilihan aktif dan terlihat oleh voter. <br>
                    <span class="font-semibold">Archived:</span> Pemilihan sudah selesai dan disembunyikan.
                </p>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between gap-3 pt-6 border-t">
                <a href="{{ route('elections.index') }}" wire:navigate
                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 transition duration-150">
                    Kembali
                </a>
                <button type="submit" wire:loading.attr="disabled" wire:target="updateElection"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading wire:target="updateElection">
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="updateElection">Update Pemilihan</span>
                    <span wire:loading wire:target="updateElection">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
