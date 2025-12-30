<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-200 border border-purple-200 rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Perpanjangan Waktu</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Daftar riwayat perpanjangan waktu pemilihan yang telah dilakukan
                </p>
            </div>
        </div>
    </div>

    @if ($election->extendedLogs && $election->extendedLogs->count() > 0)
        <!-- Timeline -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-200">
                @foreach ($election->extendedLogs as $log)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex gap-4">
                            <!-- Timeline Dot -->
                            <div class="flex flex-col items-center">
                                <div
                                    class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                @if (!$loop->last)
                                    <div class="w-0.5 h-full bg-gray-200 mt-2"></div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0 pb-4">
                                <!-- Header -->
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            Perpanjangan #{{ $election->extendedLogs->count() - $loop->index }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $log->created_at->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}
                                            WIB
                                        </p>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Diperpanjang
                                    </span>
                                </div>

                                <!-- Time Change Details -->
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    <!-- Old End Time -->
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Waktu
                                                Berakhir Lama</p>
                                            <p class="text-sm text-gray-900 font-medium mt-0.5">
                                                {{ $log->old_end_at->locale('id')->translatedFormat('d F Y, H:i') }} WIB
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Arrow -->
                                    <div class="flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                        </svg>
                                    </div>

                                    <!-- New End Time -->
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Waktu
                                                Berakhir Baru</p>
                                            <p class="text-sm text-gray-900 font-medium mt-0.5">
                                                {{ $log->new_end_at->locale('id')->translatedFormat('d F Y, H:i') }} WIB
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Duration Extended -->
                                    <div class="pt-3 border-t border-gray-200">
                                        <div class="flex items-center gap-2 text-xs text-purple-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                            </svg>
                                            <span class="font-medium">
                                                Diperpanjang
                                                @php
                                                    $diff = $log->old_end_at->diffInDays($log->new_end_at);
                                                    $hours = $log->old_end_at->diffInHours($log->new_end_at) % 24;
                                                @endphp
                                                @if ($diff > 0)
                                                    {{ $diff }} hari
                                                @endif
                                                @if ($hours > 0)
                                                    {{ $hours }} jam
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reason -->
                                @if ($log->reason)
                                    <div class="mt-3 bg-blue-50 border border-blue-100 rounded-lg p-3">
                                        <p class="text-xs font-medium text-blue-900 mb-1">Alasan:</p>
                                        <p class="text-sm text-blue-800">{{ $log->reason }}</p>
                                    </div>
                                @endif

                                <!-- Extended By -->
                                <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Diperpanjang oleh: <span
                                            class="font-medium text-gray-700">{{ $log->extendedBy->name }}</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Total Perpanjangan</p>
                        <p class="text-xl font-bold text-gray-900">{{ $election->extendedLogs->count() }}x</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Waktu Berakhir Saat Ini</p>
                        <p class="text-sm font-bold text-gray-900">
                            {{ $election->end_at->locale('id')->translatedFormat('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Perpanjangan Terakhir</p>
                        <p class="text-sm font-bold text-gray-900">
                            {{ $election->extendedLogs->first()->created_at->locale('id')->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12">
            <div class="text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Riwayat Perpanjangan</h3>
                <p class="text-sm text-gray-500 max-w-md mx-auto">
                    Pemilihan ini belum pernah diperpanjang waktunya. Perpanjangan waktu akan muncul di sini ketika
                    dilakukan.
                </p>
            </div>
        </div>
    @endif
</div>
