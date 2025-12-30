<div class="space-y-4 sm:space-y-6">
    @php
        $candidates = $election->candidates;
        $totalVotes = $election->votes()->count();

        // Calculate eligible voters based on organization type
        $orgType = $election->organization->organization_type;
        $eligibleVotersCount = 0;

        if ($orgType === 'LT') {
            // LT: All active voters
            $eligibleVotersCount = \App\Models\User::role('Voter')->where('is_active', true)->count();
        } elseif ($orgType === 'HMJ') {
            // HMJ: Voters from same jurusan as organization creator
            $orgJurusanId = $election->organization->user->jurusan_id;
            $eligibleVotersCount = \App\Models\User::role('Voter')
                ->where('is_active', true)
                ->where('jurusan_id', $orgJurusanId)
                ->count();
        } elseif ($orgType === 'UKM') {
            // UKM: Only organization members
            $eligibleVotersCount = $election->organization->members()->count();
        }

        $golput = $eligibleVotersCount - $totalVotes;
        $hasVotes = $totalVotes > 0 || $eligibleVotersCount > 0;
        $participationRate = $eligibleVotersCount > 0 ? round(($totalVotes / $eligibleVotersCount) * 100, 2) : 0;
        $golputRate = $eligibleVotersCount > 0 ? round(($golput / $eligibleVotersCount) * 100, 2) : 0;

        // Calculate votes for each candidate (percentage from eligible voters, not just total votes)
        $candidatesWithVotes = $candidates
            ->map(function ($candidate) use ($eligibleVotersCount) {
                $voteCount = $candidate->votes()->count();
                $percentage = $eligibleVotersCount > 0 ? round(($voteCount / $eligibleVotersCount) * 100, 2) : 0;
                return [
                    'candidate' => $candidate,
                    'votes' => $voteCount,
                    'percentage' => $percentage,
                ];
            })
            ->sortByDesc('votes')
            ->values();
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-4 sm:mb-6">
        <h3 class="text-lg sm:text-xl font-bold text-gray-900">Hasil Pemilihan</h3>
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 md:gap-4 text-xs sm:text-sm">
            <div class="flex items-center gap-1.5 sm:gap-2 text-gray-600 whitespace-nowrap">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Suara Masuk: <strong class="text-green-600">{{ $totalVotes }}</strong></span>
            </div>
            <div class="flex items-center gap-1.5 sm:gap-2 text-gray-600 whitespace-nowrap">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Berhak Memilih: <strong class="text-blue-600">{{ $eligibleVotersCount }}</strong></span>
            </div>
        </div>
    </div>

    @if ($hasVotes)
        <!-- Leaderboard & Perolehan Suara -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
            <h4 class="text-base sm:text-lg font-bold text-gray-900 mb-4 sm:mb-6 flex items-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-amber-600 flex-shrink-0" fill="currentColor"
                    viewBox="0 0 20 20">
                    <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Leaderboard & Perolehan Suara
            </h4>

            <div class="space-y-3 sm:space-y-4">
                @foreach ($candidatesWithVotes as $index => $item)
                    @php
                        $isWinner = $index === 0;
                        $colors = [
                            'from-blue-500 to-blue-600',
                            'from-green-500 to-green-600',
                            'from-purple-500 to-purple-600',
                            'from-pink-500 to-pink-600',
                            'from-amber-500 to-amber-600',
                        ];
                        $color = $colors[$index % count($colors)];
                        $borderColor = $isWinner ? 'border-amber-400' : 'border-gray-200';
                    @endphp
                    <div
                        class="relative border-2 {{ $borderColor }} rounded-lg sm:rounded-xl p-3 sm:p-4 {{ $isWinner ? 'shadow-lg bg-gradient-to-r from-amber-50 to-yellow-50' : 'shadow-sm' }} transition-all duration-300 hover:shadow-md">
                        @if ($isWinner)
                            <div class="absolute -top-2 -right-2 sm:-top-3 sm:-right-3">
                                <div
                                    class="bg-gradient-to-r from-amber-400 to-yellow-500 rounded-full p-1.5 sm:p-2 shadow-lg">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between mb-2 sm:mb-3 gap-2">
                            <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 flex-shrink-0 rounded-full bg-gradient-to-r {{ $color }} text-white text-base sm:text-lg font-bold shadow-md">
                                    {{ $index + 1 }}
                                </span>

                                <!-- Foto Ketua & Wakil -->
                                <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                                    @php
                                        $photoData = is_array($item['candidate']->photo)
                                            ? $item['candidate']->photo
                                            : json_decode($item['candidate']->photo, true);
                                        $ketuaPhoto = $photoData['ketua'] ?? null;
                                        $wakilPhoto = $photoData['wakil'] ?? null;
                                    @endphp

                                    @if ($ketuaPhoto)
                                        <img src="{{ asset('storage/' . $ketuaPhoto) }}"
                                            alt="{{ $item['candidate']->ketua->name }}"
                                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover border-2 border-white shadow-md">
                                    @else
                                        <div
                                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center border-2 border-white shadow-md text-white font-bold text-sm sm:text-base">
                                            {{ substr($item['candidate']->ketua->name, 0, 1) }}
                                        </div>
                                    @endif

                                    @if ($item['candidate']->wakil && $wakilPhoto)
                                        <img src="{{ asset('storage/' . $wakilPhoto) }}"
                                            alt="{{ $item['candidate']->wakil->name }}"
                                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover border-2 border-white shadow-md -ml-2 sm:-ml-3">
                                    @elseif ($item['candidate']->wakil)
                                        <div
                                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-blue-400 to-indigo-400 flex items-center justify-center border-2 border-white shadow-md -ml-2 sm:-ml-3 text-white font-bold text-sm sm:text-base">
                                            {{ substr($item['candidate']->wakil->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p
                                        class="text-sm sm:text-base font-bold text-gray-900 {{ $isWinner ? 'sm:text-lg' : '' }} truncate">
                                        {{ $item['candidate']->ketua->name }}
                                        @if ($item['candidate']->wakil)
                                            <span class="hidden sm:inline">&
                                                {{ $item['candidate']->wakil->name }}</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $item['votes'] }} suara</p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 ml-2 sm:ml-4">
                                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $item['percentage'] }}%</p>
                                <p class="text-xs text-gray-500 hidden sm:block">dari total pemilih</p>
                            </div>
                        </div>

                        <div class="relative w-full h-6 sm:h-8 bg-gray-200 rounded-full overflow-hidden">
                            <div class="absolute inset-y-0 left-0 bg-gradient-to-r {{ $color }} rounded-full transition-all duration-500 ease-out flex items-center justify-end pr-3"
                                style="width: {{ $item['percentage'] }}%">
                                @if ($item['percentage'] > 15)
                                    <span class="text-xs font-bold text-white">{{ $item['percentage'] }}%</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Golput Card -->
                <div
                    class="border-2 border-red-200 rounded-lg sm:rounded-xl p-3 sm:p-4 bg-gradient-to-r from-red-50 to-pink-50 shadow-sm">
                    <div class="flex items-center justify-between mb-2 sm:mb-3 gap-2">
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-r from-red-500 to-red-600 text-white shadow-md flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm sm:text-base font-bold text-gray-900 truncate">Golongan Putih</p>
                                <p class="text-xs text-gray-500 mt-0.5">Tidak memilih</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xl sm:text-2xl font-bold text-red-600">{{ $golputRate }}%</p>
                            <p class="text-xs text-gray-500">{{ $golput }} orang</p>
                        </div>
                    </div>

                    <div class="relative w-full h-6 sm:h-8 bg-gray-200 rounded-full overflow-hidden">
                        <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-red-500 to-red-600 rounded-full transition-all duration-500 ease-out flex items-center justify-end pr-2 sm:pr-3"
                            style="width: {{ $golputRate }}%">
                            @if ($golputRate > 15)
                                <span class="text-xs font-bold text-white">{{ $golputRate }}%</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <x-empty-state>
            <x-slot:icon>
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </x-slot:icon>
            <x-slot:title>Belum Ada Hasil</x-slot:title>
            <x-slot:description>Hasil pemilihan akan ditampilkan setelah proses voting dimulai dan ada suara yang
                masuk.</x-slot:description>
        </x-empty-state>
    @endif
</div>
