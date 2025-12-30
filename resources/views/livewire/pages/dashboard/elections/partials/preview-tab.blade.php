<!-- Banner Header -->
@if ($election->banner)
    <div class="relative -m-6 mb-6 h-48 sm:h-64 md:h-80 overflow-hidden rounded-t-lg">
        <img src="{{ asset('storage/' . $election->banner) }}" alt="Banner" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/30 to-black/70"></div>
        <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 md:p-8 text-white">
            <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold mb-2 sm:mb-3">{{ $election->name }}</h2>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs sm:text-sm">
                <div
                    class="flex items-center gap-1 sm:gap-2 bg-white/20 backdrop-blur-sm px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-lg">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span
                        class="font-semibold truncate max-w-[120px] sm:max-w-none">{{ $election->organization->user->name ?? 'N/A' }}</span>
                </div>
                <div
                    class="flex items-center gap-1 sm:gap-2 bg-green-500/80 backdrop-blur-sm px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-lg">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span
                        class="whitespace-nowrap">{{ \Carbon\Carbon::parse($election->start_at)->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                </div>
                <div
                    class="flex items-center gap-1 sm:gap-2 bg-red-500/80 backdrop-blur-sm px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-lg">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span
                        class="whitespace-nowrap">{{ \Carbon\Carbon::parse($election->end_at)->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                </div>

                {{-- Extended Time Badge (Example: check if extended) --}}
                @php
                    // Example: Check if end_at has been extended (compare with original or check extended_logs)
                    // This is a placeholder - adjust based on your actual implementation
                    $hasExtension = false; // Replace with actual logic
                    $originalEndAt = $election->end_at; // Replace with actual original date if stored
                @endphp

                @if ($hasExtension)
                    <div
                        class="flex items-center gap-1 sm:gap-2 bg-orange-500/90 backdrop-blur-sm px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-lg border-2 border-orange-300 animate-pulse">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-bold">DIPERPANJANG</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- Extension Notice (if extended) --}}
@php
    $hasExtension = false; // Replace with actual logic
    $extensionReason = 'Karena tingginya antusiasme peserta'; // Replace with actual reason
    $extendedHours = 24; // Replace with actual calculation
@endphp

@if ($hasExtension)
    <div
        class="bg-gradient-to-r from-orange-50 to-amber-50 border-l-4 border-orange-500 rounded-lg p-3 sm:p-4 md:p-5 shadow-md -mt-3">
        <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="text-base sm:text-lg font-bold text-orange-900 mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="truncate">Pemberitahuan Perpanjangan Waktu</span>
                </h4>
                <p class="text-orange-800 text-xs sm:text-sm mb-2">
                    Waktu pemilihan telah <span class="font-bold">diperpanjang {{ $extendedHours }} jam</span>.
                </p>
                <div class="bg-white/60 rounded-lg p-2 sm:p-3 text-xs sm:text-sm space-y-2">
                    <p class="text-gray-700">
                        <span class="font-semibold text-orange-900 block sm:inline">Waktu Asli Berakhir:</span>
                        <span
                            class="line-through sm:ml-2 block sm:inline">{{ \Carbon\Carbon::parse($originalEndAt)->locale('id')->translatedFormat('d F Y, H:i') }}
                            WIB</span>
                    </p>
                    <p class="text-gray-700">
                        <span class="font-semibold text-green-700 block sm:inline">Waktu Baru Berakhir:</span>
                        <span
                            class="sm:ml-2 font-bold text-green-700 block sm:inline">{{ \Carbon\Carbon::parse($election->end_at)->locale('id')->translatedFormat('d F Y, H:i') }}
                            WIB</span>
                    </p>
                </div>
                @if ($extensionReason)
                    <p class="text-sm text-orange-700 mt-3 italic">
                        <span class="font-semibold">Alasan:</span> {{ $extensionReason }}
                    </p>
                @endif
            </div>
        </div>
    </div>
@endif

<!-- Main Content - Description & Info -->
<div class="space-y-4 sm:space-y-6">
    <!-- Pamphlet & Description Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Pamphlet (Left Side) -->
            @if ($election->pamphlet)
                <div class="lg:col-span-1">
                    <div class="lg:sticky lg:top-6">
                        <div
                            class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 overflow-hidden shadow-md hover:shadow-xl transition-all duration-300">
                            <img src="{{ asset('storage/' . $election->pamphlet) }}" alt="Pamflet"
                                class="w-full h-auto object-contain">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Description (Right Side) -->
            <div class="lg:col-span-3">
                <div class="prose prose-sm sm:prose-base lg:prose-lg max-w-none text-gray-700">
                    <h3
                        class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mb-3 sm:mb-4 flex items-center border-b pb-2 sm:pb-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 mr-2 sm:mr-3 text-blue-600 flex-shrink-0"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="truncate">Tentang Pemilihan</span>
                    </h3>
                    {!! $election->description ?? '<p class="text-gray-400 italic text-sm sm:text-base">Tidak ada deskripsi</p>' !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Candidates Section (POV Voter) -->
    <div
        class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border border-purple-200 shadow-sm p-4 sm:p-6 md:p-8">
        <div class="text-center mb-6 sm:mb-8">
            <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-2 flex items-center justify-center">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 mr-2 sm:mr-3 text-purple-600 flex-shrink-0"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="truncate">Daftar Kandidat</span>
            </h3>
            <p class="text-gray-600 text-sm sm:text-base">Pilih kandidat favorit Anda</p>
        </div>

        @if ($election->candidates && $election->candidates->count() > 0)
            <!-- Real Candidates -->
            <div class="grid grid-cols-1 gap-4 sm:gap-6">
                @foreach ($election->candidates as $index => $candidate)
                    <div
                        class="bg-white rounded-xl sm:rounded-2xl border-2 border-gray-200 overflow-hidden hover:border-purple-400 hover:shadow-2xl transition-all duration-300 group">
                        <div class="flex flex-col md:flex-row">
                            <!-- Photo Section -->
                            <div
                                class="md:w-2/5 bg-gradient-to-br from-purple-100 to-pink-100 p-4 sm:p-6 flex items-center justify-center">
                                @php
                                    $photoData = is_array($candidate->photo)
                                        ? $candidate->photo
                                        : json_decode($candidate->photo, true);
                                    $ketuaPhoto = $photoData['ketua'] ?? null;
                                    $wakilPhoto = $photoData['wakil'] ?? null;
                                @endphp

                                @if ($candidate->wakil && $wakilPhoto)
                                    <!-- Grid 2 kolom jika ada wakil -->
                                    <div class="w-full grid grid-cols-2 gap-2 sm:gap-3 md:gap-4">
                                        <!-- Foto Ketua -->
                                        <div class="space-y-1 sm:space-y-2">
                                            @if ($ketuaPhoto)
                                                <img src="{{ asset('storage/' . $ketuaPhoto) }}" alt="Foto Ketua"
                                                    class="w-full h-40 sm:h-48 md:h-60 object-cover rounded-lg sm:rounded-xl shadow-lg">
                                            @else
                                                <div
                                                    class="w-full h-40 sm:h-48 md:h-60 bg-gradient-to-br from-purple-400 to-pink-400 rounded-lg sm:rounded-xl flex items-center justify-center">
                                                    <span
                                                        class="text-white text-2xl sm:text-3xl md:text-4xl font-bold">K</span>
                                                </div>
                                            @endif
                                            <p class="text-center text-xs font-semibold text-gray-700">Ketua</p>
                                        </div>

                                        <!-- Foto Wakil -->
                                        <div class="space-y-1 sm:space-y-2">
                                            <img src="{{ asset('storage/' . $wakilPhoto) }}" alt="Foto Wakil"
                                                class="w-full h-40 sm:h-48 md:h-60 object-cover rounded-lg sm:rounded-xl shadow-lg">
                                            <p class="text-center text-xs font-semibold text-gray-700">Wakil</p>
                                        </div>
                                    </div>
                                @else
                                    <!-- Foto Ketua saja jika tidak ada wakil -->
                                    @if ($ketuaPhoto)
                                        <img src="{{ asset('storage/' . $ketuaPhoto) }}" alt="Foto Kandidat"
                                            class="w-full h-56 sm:h-64 md:h-80 object-cover rounded-lg sm:rounded-xl shadow-lg">
                                    @else
                                        <div
                                            class="w-full h-56 sm:h-64 md:h-80 bg-gradient-to-br from-purple-400 to-pink-400 rounded-lg sm:rounded-xl flex items-center justify-center">
                                            <span
                                                class="text-white text-4xl sm:text-5xl md:text-6xl font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Content Section -->
                            <div class="md:w-3/5 p-4 sm:p-5 md:p-6 space-y-3 sm:space-y-4">
                                <!-- Header with Number Badge -->
                                <div class="flex items-start justify-between gap-3 sm:gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 sm:gap-3 mb-2 sm:mb-3">
                                            <span
                                                class="inline-flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 md:w-12 md:h-12 bg-gradient-to-br from-purple-500 to-pink-500 text-white font-bold text-base sm:text-lg rounded-full shadow-lg flex-shrink-0">
                                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <h4
                                                    class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 truncate">
                                                    {{ $candidate->ketua->name ?? 'Ketua' }}
                                                </h4>
                                                @if ($candidate->wakil)
                                                    <p
                                                        class="text-xs sm:text-sm text-gray-600 flex items-center gap-1 truncate">
                                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        <span class="font-medium">Wakil:</span>
                                                        <span class="truncate">{{ $candidate->wakil->name }}</span>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Visi & Misi in Accordion Style -->
                                <div class="space-y-2 sm:space-y-3">
                                    <!-- Visi -->
                                    <details
                                        class="group/visi bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg border border-blue-200">
                                        <summary
                                            class="cursor-pointer p-3 sm:p-4 font-semibold text-gray-900 flex items-center justify-between hover:bg-blue-100 rounded-lg transition-colors text-sm sm:text-base">
                                            <span class="flex items-center gap-1.5 sm:gap-2 min-w-0">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 flex-shrink-0"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span class="truncate">Visi</span>
                                            </span>
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform group-open/visi:rotate-180 flex-shrink-0"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </summary>
                                        <div
                                            class="px-3 sm:px-4 pb-3 sm:pb-4 text-xs sm:text-sm text-gray-700 prose prose-sm max-w-none">
                                            {!! $candidate->visi !!}
                                        </div>
                                    </details>

                                    <!-- Misi -->
                                    <details
                                        class="group/misi bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                                        <summary
                                            class="cursor-pointer p-3 sm:p-4 font-semibold text-gray-900 flex items-center justify-between hover:bg-green-100 rounded-lg transition-colors text-sm sm:text-base">
                                            <span class="flex items-center gap-1.5 sm:gap-2 min-w-0">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 flex-shrink-0"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                                <span class="truncate">Misi</span>
                                            </span>
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform group-open/misi:rotate-180 flex-shrink-0"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </summary>
                                        <div class="px-3 sm:px-4 pb-3 sm:pb-4 text-xs sm:text-sm text-gray-700">
                                            @php
                                                $misiData = is_array($candidate->misi)
                                                    ? $candidate->misi
                                                    : json_decode($candidate->misi, true);
                                            @endphp
                                            @if ($misiData && is_array($misiData))
                                                <ol class="list-decimal list-inside space-y-2">
                                                    @foreach ($misiData as $misiItem)
                                                        <li>{{ $misiItem }}</li>
                                                    @endforeach
                                                </ol>
                                            @else
                                                <p>{{ $candidate->misi }}</p>
                                            @endif
                                        </div>
                                    </details>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                                    @php
                                        $cvData = is_array($candidate->cv)
                                            ? $candidate->cv
                                            : json_decode($candidate->cv, true);
                                        $ketuaCv = $cvData['ketua'] ?? null;
                                        $wakilCv = $cvData['wakil'] ?? null;
                                    @endphp
                                    @if ($ketuaCv)
                                        <a href="{{ asset('storage/' . $ketuaCv) }}" target="_blank"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Download CV Ketua
                                        </a>
                                    @endif

                                    @if ($wakilCv && $candidate->wakil)
                                        <a href="{{ asset('storage/' . $wakilCv) }}" target="_blank"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white rounded-lg font-semibold hover:from-cyan-600 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Download CV Wakil
                                        </a>
                                    @endif

                                    @if ($candidate->link)
                                        <a href="{{ $candidate->link }}" target="_blank"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg font-semibold hover:from-purple-600 hover:to-pink-600 transition-all duration-200 shadow-md hover:shadow-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                            Link Profil
                                        </a>
                                    @endif

                                    <!-- Vote Button -->
                                    <button
                                        class="ml-auto inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg font-bold hover:from-green-600 hover:to-emerald-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        PILIH KANDIDAT
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Voting Status -->
            <div class="mt-6 bg-yellow-50 border-2 border-yellow-300 rounded-xl p-5 flex items-start gap-4 shadow-sm">
                <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="font-bold text-yellow-900 text-base">Status: Belum Memilih</p>
                    <p class="text-sm text-yellow-700 mt-1">Pilih salah satu kandidat di atas untuk memberikan suara
                        Anda. Suara hanya dapat diberikan satu kali.</p>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h4 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Kandidat</h4>
                <p class="text-gray-600 mb-4">Kandidat untuk pemilihan ini belum ditambahkan</p>
            </div>
        @endif
    </div>
</div>
