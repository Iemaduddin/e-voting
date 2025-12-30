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

                @php
                    $hasExtension = $election->extendedLogs && $election->extendedLogs->count() > 0;
                    $latestExtension = $hasExtension ? $election->extendedLogs->first() : null;
                @endphp

                @if ($hasExtension && $latestExtension)
                    <!-- Tanggal Lama (Dicoret) -->
                    <div
                        class="flex items-center gap-1 sm:gap-2 bg-red-500/80 backdrop-blur-sm px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-lg opacity-75">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span
                            class="whitespace-nowrap line-through">{{ \Carbon\Carbon::parse($latestExtension->old_end_at)->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                    </div>

                    <!-- Tanggal Baru -->
                    <div
                        class="flex items-center gap-1 sm:gap-2 bg-red-600 backdrop-blur-sm px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-lg border-2 border-red-300">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span
                            class="whitespace-nowrap font-bold">{{ \Carbon\Carbon::parse($election->end_at)->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                    </div>

                    <!-- Badge Diperpanjang -->
                    <div
                        class="flex items-center gap-1 sm:gap-2 bg-orange-500 backdrop-blur-sm px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-lg border border-orange-300">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-bold">DIPERPANJANG</span>
                    </div>
                @else
                    <!-- Tanggal Berakhir Normal -->
                    <div
                        class="flex items-center gap-1 sm:gap-2 bg-red-500/80 backdrop-blur-sm px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-lg">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span
                            class="whitespace-nowrap">{{ \Carbon\Carbon::parse($election->end_at)->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                    </div>
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

    <!-- Candidates Section -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
        <div class="text-center mb-6 sm:mb-8">
            <div class="flex items-center justify-center gap-3 mb-2">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">Daftar Kandidat</h3>
            <p class="text-gray-600 text-sm sm:text-base">Pilih kandidat favorit Anda</p>
        </div>

        @if ($election->candidates && $election->candidates->count() > 0)
            <!-- Real Candidates -->
            <div class="grid grid-cols-1 gap-4 sm:gap-5">
                @foreach ($election->candidates as $index => $candidate)
                    <div
                        class="bg-gray-50 rounded-xl border-2 border-gray-200 overflow-hidden hover:border-blue-500 hover:shadow-lg transition-all duration-300">
                        <div class="flex flex-col md:flex-row">
                            <!-- Photo Section -->
                            <div
                                class="md:w-2/5 bg-white p-4 sm:p-6 flex items-center justify-center border-b md:border-b-0 md:border-r border-gray-200">
                                @php
                                    $photoData = is_array($candidate->photo)
                                        ? $candidate->photo
                                        : json_decode($candidate->photo, true);
                                    $ketuaPhoto = $photoData['ketua'] ?? null;
                                    $wakilPhoto = $photoData['wakil'] ?? null;

                                    $cvData = is_array($candidate->cv)
                                        ? $candidate->cv
                                        : json_decode($candidate->cv, true);
                                    $ketuaCv = $cvData['ketua'] ?? null;
                                    $wakilCv = $cvData['wakil'] ?? null;
                                @endphp

                                @if ($candidate->wakil && $wakilPhoto)
                                    <!-- Grid 2 kolom jika ada wakil -->
                                    <div class="w-full grid grid-cols-2 gap-3 sm:gap-4">
                                        <!-- Foto Ketua -->
                                        <div class="space-y-2">
                                            @if ($ketuaPhoto)
                                                <img src="{{ asset('storage/' . $ketuaPhoto) }}" alt="Foto Ketua"
                                                    class="w-full h-40 md:h-64 object-cover rounded-lg border-2 border-gray-200">
                                            @else
                                                <div
                                                    class="w-full h-40 sm:h-48 md:h-56 bg-blue-100 rounded-lg flex items-center justify-center border-2 border-blue-200">
                                                    <span class="text-blue-600 text-3xl sm:text-4xl font-bold">K</span>
                                                </div>
                                            @endif
                                            <p
                                                class="text-center text-sm font-semibold text-gray-700 bg-gray-100 py-1 rounded">
                                                Ketua</p>
                                            @if ($ketuaCv)
                                                <a href="{{ asset('storage/' . $ketuaCv) }}" target="_blank"
                                                    class="inline-flex items-center gap-2 text-xs md:text-sm py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-all duration-200 shadow-md hover:shadow-lg w-full justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Download CV
                                                </a>
                                            @endif
                                        </div>

                                        <!-- Foto Wakil -->
                                        <div class="space-y-2">
                                            <img src="{{ asset('storage/' . $wakilPhoto) }}" alt="Foto Wakil"
                                                class="w-full h-40 md:h-64 object-cover rounded-lg border-2 border-gray-200">
                                            <p
                                                class="text-center text-sm font-semibold text-gray-700 bg-gray-100 py-1 rounded">
                                                Wakil</p>
                                            @if ($wakilCv)
                                                <a href="{{ asset('storage/' . $wakilCv) }}" target="_blank"
                                                    class="inline-flex items-center gap-2 text-xs md:text-sm py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-all duration-200 shadow-md hover:shadow-lg w-full justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Download CV
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <!-- Foto Ketua saja jika tidak ada wakil -->
                                    @if ($ketuaPhoto)
                                        <img src="{{ asset('storage/' . $ketuaPhoto) }}" alt="Foto Kandidat"
                                            class="w-full h-56 sm:h-64 md:h-72 object-cover rounded-lg shadow-md border-2 border-gray-200">
                                    @else
                                        <div
                                            class="w-full h-56 sm:h-64 md:h-72 bg-blue-100 rounded-lg flex items-center justify-center border-2 border-blue-200">
                                            <span
                                                class="text-blue-600 text-5xl sm:text-6xl font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Content Section -->
                            <div class="md:w-3/5 p-4 sm:p-5 md:p-6 space-y-4">
                                <!-- Header with Number Badge -->
                                <div class="flex items-start gap-3">
                                    <span
                                        class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-blue-600 text-white font-bold text-lg sm:text-xl rounded-lg shadow-md flex-shrink-0">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">
                                            {{ $candidate->ketua->name ?? 'Ketua' }}
                                        </h4>
                                        @if ($candidate->wakil)
                                            <p class="text-xs sm:text-sm text-gray-600 flex items-center gap-1.5">
                                                <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span class="font-medium">Wakil:</span>
                                                <span>{{ $candidate->wakil->name }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Visi & Misi -->
                                <div class="space-y-3">
                                    <!-- Visi -->
                                    <details class="group/visi bg-blue-50 rounded-lg border border-blue-200" open>
                                        <summary
                                            class="cursor-pointer p-3 sm:p-4 font-semibold text-gray-900 flex items-center justify-between hover:bg-blue-100 rounded-lg transition-colors text-sm sm:text-base">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Visi</span>
                                            </span>
                                            <svg class="w-5 h-5 transition-transform group-open/visi:rotate-180 flex-shrink-0 text-gray-500"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </summary>
                                        <div class="px-4 pb-4 text-sm text-gray-700 prose prose-sm max-w-none">
                                            {!! $candidate->visi !!}
                                        </div>
                                    </details>

                                    <!-- Misi -->
                                    <details class="group/misi bg-green-50 rounded-lg border border-green-200">
                                        <summary
                                            class="cursor-pointer p-3 sm:p-4 font-semibold text-gray-900 flex items-center justify-between hover:bg-green-100 rounded-lg transition-colors text-sm sm:text-base">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                                <span>Misi</span>
                                            </span>
                                            <svg class="w-5 h-5 transition-transform group-open/misi:rotate-180 flex-shrink-0 text-gray-500"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </summary>
                                        <div class="px-4 pb-4 text-sm text-gray-700">
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
                                <div
                                    class="grid grid-cols-1 gap-3 pt-4 border-t border-gray-200 md:flex md:justify-between">
                                    @if ($candidate->link)
                                        <a href="{{ $candidate->link }}" target="_blank"
                                            onclick="event.stopPropagation()"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg text-sm font-semibold hover:bg-gray-700 transition-all duration-200 shadow-md hover:shadow-lg w-full md:w-auto">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            Link Profil
                                        </a>
                                    @endif
                                    <!-- Vote Button -->
                                    <button
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white text-sm rounded-lg font-bold hover:bg-green-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed w-full md:w-auto">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
