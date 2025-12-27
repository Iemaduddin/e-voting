<div>
    <!-- Modal Backdrop -->
    @if ($show)
        <div x-data="{ show: @entangle('show') }" x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

            <!-- Modal container -->
            <div class="flex min-h-full items-center justify-center p-4">
                <!-- Modal content -->
                <div x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 text-center">
                    <!-- Success Icon -->
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                        <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>

                    <!-- Success Message -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">
                        Suara Berhasil Terkirim!
                    </h3>

                    <p class="text-gray-600 mb-8">
                        Terima kasih telah berpartisipasi dalam pemilihan ini. Suara Anda telah tercatat dengan aman.
                    </p>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-3">
                        <button wire:click="$set('show', false)"
                            class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold py-3 px-6 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Tutup
                        </button>

                        <a href="{{ route('vote.index') }}" wire:navigate
                            class="w-full bg-gray-100 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-200 transition-all duration-200">
                            Kembali ke Daftar Pemilihan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
