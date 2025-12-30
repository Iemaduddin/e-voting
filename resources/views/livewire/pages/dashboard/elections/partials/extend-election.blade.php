<div>
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeModal"></div>

                <!-- Modal panel -->
                <div
                    class="inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-xl">
                    <!-- Header -->
                    <div class="px-6 py-4 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Perpanjang Waktu Pemilihan</h3>
                                    @if ($election)
                                        <p class="text-sm text-gray-600">{{ $election->name }}</p>
                                    @endif
                                </div>
                            </div>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <form wire:submit.prevent="extendElection" class="p-6 space-y-5">
                        @if ($election)
                            <!-- Info Waktu Saat Ini -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-blue-900">Waktu Berakhir Saat Ini</p>
                                        <p class="text-sm text-blue-700 mt-1">
                                            {{ $election->end_at->locale('id')->translatedFormat('l, d F Y') }}
                                            <span class="font-semibold">{{ $election->end_at->format('H:i') }}</span>
                                            WIB
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Fields -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Tanggal Baru -->
                                <div>
                                    <label for="newEndDate" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Berakhir Baru <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="newEndDate" wire:model="newEndDate"
                                        min="{{ now()->addDay()->format('Y-m-d') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('newEndDate') border-red-500 @enderror">
                                    @error('newEndDate')
                                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Waktu Baru -->
                                <div>
                                    <label for="newEndTime" class="block text-sm font-medium text-gray-700 mb-2">
                                        Waktu Berakhir Baru <span class="text-red-500">*</span>
                                    </label>
                                    <input type="time" id="newEndTime" wire:model="newEndTime"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('newEndTime') border-red-500 @enderror">
                                    @error('newEndTime')
                                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Alasan -->
                            <div>
                                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alasan Perpanjangan <span class="text-gray-400">(Opsional)</span>
                                </label>
                                <textarea id="reason" wire:model="reason" rows="3" maxlength="500"
                                    placeholder="Masukkan alasan perpanjangan waktu pemilihan..."
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all resize-none @error('reason') border-red-500 @enderror"></textarea>
                                <div class="flex items-center justify-between mt-1.5">
                                    @error('reason')
                                        <p class="text-sm text-red-600 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @else
                                        <span></span>
                                    @enderror
                                    <p class="text-xs text-gray-500">
                                        {{ strlen($reason ?? '') }}/500
                                    </p>
                                </div>
                            </div>

                            <!-- Preview Perpanjangan -->
                            @if ($newEndDate && $newEndTime)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-green-900">Waktu Berakhir Baru</p>
                                            <p class="text-sm text-green-700 mt-1">
                                                {{ \Carbon\Carbon::parse($newEndDate . ' ' . $newEndTime)->locale('id')->translatedFormat('l, d F Y') }}
                                                <span class="font-semibold">{{ $newEndTime }}</span> WIB
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Footer Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button" wire:click="closeModal"
                                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Perpanjang Waktu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
