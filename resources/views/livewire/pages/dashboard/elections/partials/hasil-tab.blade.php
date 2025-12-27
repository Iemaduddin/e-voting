<div class="space-y-6">
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

        // Calculate votes for each candidate
        $candidatesWithVotes = $candidates
            ->map(function ($candidate) use ($totalVotes) {
                $voteCount = $candidate->votes()->count();
                $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 2) : 0;
                return [
                    'candidate' => $candidate,
                    'votes' => $voteCount,
                    'percentage' => $percentage,
                ];
            })
            ->sortByDesc('votes')
            ->values();
    @endphp

    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-900">Hasil Pemilihan</h3>
        <div class="flex items-center gap-4 text-sm">
            <div class="flex items-center gap-2 text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Suara Masuk: <strong class="text-green-600">{{ $totalVotes }}</strong></span>
            </div>
            <div class="flex items-center gap-2 text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Berhak Memilih: <strong class="text-blue-600">{{ $eligibleVotersCount }}</strong></span>
            </div>
        </div>
    </div>

    @if ($hasVotes)
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1">Berhak Memilih</p>
                        <p class="text-3xl font-bold">{{ $eligibleVotersCount }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium mb-1">Suara Masuk</p>
                        <p class="text-3xl font-bold">{{ $totalVotes }}</p>
                        <p class="text-green-100 text-xs mt-1">{{ $participationRate }}% partisipasi</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium mb-1">Golongan Putih</p>
                        <p class="text-3xl font-bold">{{ $golput }}</p>
                        <p class="text-red-100 text-xs mt-1">{{ $golputRate }}% tidak memilih</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            @if ($candidatesWithVotes->isNotEmpty())
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium mb-1">Pemenang Sementara</p>
                            <p class="text-lg font-bold leading-tight">
                                {{ $candidatesWithVotes->first()['candidate']->ketua->name }}
                            </p>
                            <p class="text-purple-100 text-sm mt-1">{{ $candidatesWithVotes->first()['percentage'] }}%
                            </p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chart Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h4 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Perolehan Suara
                    </h4>

                    <!-- Bar Chart -->
                    <div class="space-y-4">
                        @foreach ($candidatesWithVotes as $index => $item)
                            @php
                                $colors = [
                                    'from-blue-500 to-blue-600',
                                    'from-green-500 to-green-600',
                                    'from-purple-500 to-purple-600',
                                    'from-pink-500 to-pink-600',
                                    'from-amber-500 to-amber-600',
                                ];
                                $color = $colors[$index % count($colors)];
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-r {{ $color }} text-white text-sm font-bold">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $item['candidate']->ketua->name }}
                                                @if ($item['candidate']->wakil)
                                                    & {{ $item['candidate']->wakil->name }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-gray-900">{{ $item['votes'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $item['percentage'] }}%</p>
                                    </div>
                                </div>
                                <div class="relative w-full h-8 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="absolute inset-y-0 left-0 bg-gradient-to-r {{ $color }} rounded-full transition-all duration-500 ease-out flex items-center justify-end pr-3"
                                        style="width: {{ $item['percentage'] }}%">
                                        @if ($item['percentage'] > 15)
                                            <span
                                                class="text-xs font-bold text-white">{{ $item['percentage'] }}%</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Leaderboard Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h4 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Leaderboard
                    </h4>

                    <div class="space-y-3">
                        @foreach ($candidatesWithVotes as $index => $item)
                            @php
                                $isWinner = $index === 0;
                                $bgColors = [
                                    'bg-gradient-to-r from-amber-400 to-yellow-500',
                                    'bg-gradient-to-r from-gray-300 to-gray-400',
                                    'bg-gradient-to-r from-orange-400 to-orange-500',
                                    'bg-gradient-to-r from-blue-100 to-blue-200',
                                ];
                                $bgColor = $bgColors[$index] ?? 'bg-gray-50';
                                $borderColor = $isWinner ? 'border-amber-400' : 'border-gray-200';
                            @endphp
                            <div
                                class="relative border-2 {{ $borderColor }} rounded-lg p-4 {{ $isWinner ? 'shadow-lg' : 'shadow-sm' }} transition-all duration-300 hover:shadow-md">
                                @if ($isWinner)
                                    <div class="absolute -top-3 -right-3">
                                        <div
                                            class="bg-gradient-to-r from-amber-400 to-yellow-500 rounded-full p-2 shadow-lg">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 rounded-full {{ $bgColor }} flex items-center justify-center shadow-md">
                                        <span
                                            class="text-lg font-bold {{ $isWinner ? 'text-white' : 'text-gray-700' }}">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-sm font-bold text-gray-900 truncate {{ $isWinner ? 'text-base' : '' }}">
                                            {{ $item['candidate']->ketua->name }}
                                        </p>
                                        @if ($item['candidate']->wakil)
                                            <p class="text-xs text-gray-600 truncate">
                                                & {{ $item['candidate']->wakil->name }}
                                            </p>
                                        @endif
                                        <div class="mt-2 flex items-center gap-2">
                                            <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                                <div class="h-full {{ $bgColor }} transition-all duration-500"
                                                    style="width: {{ $item['percentage'] }}%"></div>
                                            </div>
                                            <span
                                                class="text-xs font-bold text-gray-700">{{ $item['percentage'] }}%</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">{{ $item['votes'] }} suara</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
