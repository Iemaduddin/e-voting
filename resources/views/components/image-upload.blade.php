@props([
    'name' => 'image',
    'label' => 'Upload Gambar',
    'currentImage' => null,
    'maxSize' => '2MB',
    'accept' => 'image/png, image/jpeg, image/jpg',
    'required' => false,
    'helperText' => 'PNG, JPG atau JPEG (MAX. 2MB)',
])

<div x-data="{
    imagePreview: '{{ $currentImage }}',
    isDragging: false,
    isLoading: false,
    fileName: '',
    fileSize: '',
    init() {
        // Listen to input change event (after Livewire processes it)
        this.$refs.fileInput.addEventListener('change', (event) => {
            const files = event.target.files;
            if (files.length === 0) return;

            const file = files[0];

            // Validate file type
            if (!file.type.match('image.*')) {
                alert('File harus berupa gambar!');
                this.$refs.fileInput.value = '';
                return;
            }

            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB!');
                this.$refs.fileInput.value = '';
                return;
            }

            this.fileName = file.name;
            this.fileSize = this.formatFileSize(file.size);
            this.isLoading = true;

            const reader = new FileReader();
            reader.onload = (e) => {
                setTimeout(() => {
                    this.imagePreview = e.target.result;
                    this.isLoading = false;
                }, 500);
            };
            reader.readAsDataURL(file);
        });

        // Listen to reset event from Livewire
        window.addEventListener('reset-image-upload', () => {
            this.removeImage();
        });
    },
    handleDrop(files) {
        // For drag & drop, manually set the files to input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(files[0]);
        this.$refs.fileInput.files = dataTransfer.files;

        // Trigger change event so Livewire picks it up
        this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
    },
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    },
    removeImage() {
        this.imagePreview = '';
        this.fileName = '';
        this.fileSize = '';
        const input = this.$refs.fileInput;
        input.value = '';
        // Trigger Livewire to clear the file
        input.dispatchEvent(new Event('input', { bubbles: true }));
    },
    triggerFileInput() {
        this.$refs.fileInput.click();
    }
}" class="w-full">

    <!-- Label -->
    @if ($label)
        <x-input-label :for="$name" :value="$label" class="text-gray-700 font-semibold mb-2" :required="$required" />
    @endif

    <!-- Upload Area -->
    <div @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
        @drop.prevent="isDragging = false; handleDrop($event.dataTransfer.files)" @click="triggerFileInput()"
        class="relative border-2 border-dashed rounded-xl p-6 transition-all duration-300 cursor-pointer overflow-hidden"
        :class="{
            'border-blue-500 bg-blue-50': isDragging,
            'border-gray-300 hover:border-blue-400 hover:bg-gray-50': !isDragging && !imagePreview,
            'border-green-500 bg-green-50': imagePreview && !isDragging
        }">

        <!-- Loading Overlay -->
        <div x-show="isLoading" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="inset-0 bg-white/90 backdrop-blur-sm rounded-xl flex items-center justify-center z-50 ">
            <div class="text-center px-4">
                <!-- Spinner -->
                <div class="inline-block">
                    <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
                <p class="mt-3 text-sm font-medium text-gray-700">Memuat gambar...</p>
                <div class="mt-2 w-40 h-1 bg-gray-200 rounded-full overflow-hidden mx-auto">
                    <div class="h-full bg-blue-600 rounded-full animate-progress"></div>
                </div>
            </div>
        </div>

        <!-- Hidden File Input -->
        <input x-ref="fileInput" type="file" id="{{ $name }}" name="{{ $name }}"
            accept="{{ $accept }}" class="hidden" {{ $attributes->only('wire:model') }} />

        <!-- Content -->
        <div x-show="!imagePreview && !isLoading" class="text-center">
            <!-- Upload Icon with Animation -->
            <div class="mx-auto w-16 h-16 mb-4 relative">
                <div class="absolute inset-0 bg-blue-100 rounded-full animate-ping opacity-75"></div>
                <div class="relative w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>
            </div>

            <div class="space-y-2">
                <p class="text-base font-semibold text-gray-700">
                    <span class="text-blue-600 hover:text-blue-700">Klik untuk upload</span>
                    atau drag & drop
                </p>
                <p class="text-sm text-gray-500">{{ $helperText }}</p>
            </div>
        </div>

        <!-- Image Preview -->
        <div x-show="imagePreview && !isLoading" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative">

            <!-- Image Container -->
            <div class="relative rounded-lg overflow-hidden bg-gray-100 shadow-md">
                <img :src="imagePreview" alt="Preview" class="w-full h-64 object-contain" />

                <!-- Overlay on Hover -->
                <div
                    class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                    <button @click.stop="triggerFileInput()" type="button"
                        class="px-4 py-2 bg-white text-gray-800 rounded-lg font-medium hover:bg-gray-100 transition-colors duration-200 mr-2">
                        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Ganti
                    </button>
                    <button @click.stop="removeImage()" type="button"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>

            <!-- File Info -->
            <div x-show="fileName" class="mt-3 flex items-center justify-between text-sm">
                <div class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-text="fileName" class="font-medium truncate max-w-xs"></span>
                </div>
                <span x-text="fileSize" class="text-gray-500 ml-2"></span>
            </div>
        </div>

        <!-- Drag Overlay -->
        <div x-show="isDragging" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="absolute inset-0 bg-blue-500/10 backdrop-blur-sm rounded-xl flex items-center justify-center z-20 pointer-events-none">
            <div class="text-center">
                <svg class="w-16 h-16 text-blue-600 mx-auto mb-3 animate-bounce" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
                <p class="text-lg font-bold text-blue-600">Lepaskan untuk upload</p>
            </div>
        </div>

        <!-- Livewire Upload Progress -->
        <div wire:loading wire:target="{{ $name }}"
            class="absolute inset-0 bg-white/90 backdrop-blur-sm rounded-xl flex items-center justify-center z-30">
            <div class="text-center">
                <svg class="animate-spin h-10 w-10 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <p class="mt-3 text-sm font-medium text-gray-700">Mengupload file...</p>
            </div>
        </div>
    </div>

    <!-- Error Message Slot -->
    {{ $slot }}
</div>

<style>
    @keyframes progress {
        0% {
            width: 0%;
        }

        100% {
            width: 100%;
        }
    }

    .animate-progress {
        animation: progress 1s ease-in-out infinite;
    }
</style>
