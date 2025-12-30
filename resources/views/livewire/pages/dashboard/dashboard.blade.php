<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Election;
use App\Models\Candidate;
use App\Models\Vote;
use App\Models\User;
use Illuminate\Support\Carbon;

new #[
    Layout('layouts.dashboard', [
        'subtitle' => 'Dashboard',
        'pageTitle' => 'Dashboard',
        'pageDescription' => 'Ringkasan dan statistik sistem e-voting',
    ]),
]
class extends Component {
    public function with(): array
    {
        $user = auth()->user();
        $isOrganization = $user->hasRole('Organization');

        // Base queries
        $electionsQuery = $isOrganization ? Election::where('organization_id', $user->organization->id) : Election::query();

        // Statistics
        $totalElections = $electionsQuery->count();

        $activeElections = (clone $electionsQuery)->where('status', 'published')->where('start_at', '<=', now())->where('end_at', '>=', now())->get();

        $totalCandidates = $isOrganization ? Candidate::whereIn('election_id', $electionsQuery->pluck('id'))->count() : Candidate::count();

        $totalVotes = $isOrganization ? Vote::whereIn('election_id', $electionsQuery->pluck('id'))->count() : Vote::count();

        // Recent completed elections
        $recentResults = (clone $electionsQuery)
            ->where('status', 'published')
            ->where('end_at', '<', now())
            ->with(['organization', 'candidates.votes', 'candidates.ketua'])
            ->orderBy('end_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($election) {
                $totalVotes = $election->votes()->count();
                $winner = $election->candidates()->withCount('votes')->orderBy('votes_count', 'desc')->with('ketua')->first();

                // Calculate eligible voters
                $orgType = $election->organization->organization_type;
                if ($orgType === 'LT') {
                    $eligibleVoters = User::role('Voter')->where('is_active', true)->count();
                } elseif ($orgType === 'HMJ') {
                    $eligibleVoters = User::role('Voter')
                        ->where('is_active', true)
                        ->where('jurusan_id', $election->organization->user->jurusan_id)
                        ->count();
                } else {
                    $eligibleVoters = $election->organization->members()->count();
                }

                $participation = $eligibleVoters > 0 ? round(($totalVotes / $eligibleVoters) * 100, 1) : 0;

                return [
                    'election' => $election,
                    'winner' => $winner,
                    'totalVotes' => $totalVotes,
                    'participation' => $participation,
                ];
            });

        // Upcoming elections
        $upcomingElections = (clone $electionsQuery)->where('status', 'published')->where('start_at', '>', now())->with('organization')->orderBy('start_at', 'asc')->limit(3)->get();

        return [
            'totalElections' => $totalElections,
            'activeElectionsCount' => $activeElections->count(),
            'totalCandidates' => $totalCandidates,
            'totalVotes' => $totalVotes,
            'activeElections' => $activeElections,
            'recentResults' => $recentResults,
            'upcomingElections' => $upcomingElections,
            'isOrganization' => $isOrganization,
        ];
    }
};

?>
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Elections -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-2">Total Pemilihan</p>
                    <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $totalElections }}</h3>
                    <div class="h-1 w-12 bg-indigo-500 rounded-full mt-3"></div>
                </div>
                <div class="p-2.5 bg-indigo-50 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Elections -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-2">Pemilihan Aktif</p>
                    <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $activeElectionsCount }}</h3>
                    <div class="h-1 w-12 bg-green-500 rounded-full mt-3"></div>
                </div>
                <div class="p-2.5 bg-green-50 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Candidates -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-2">Total Kandidat</p>
                    <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $totalCandidates }}</h3>
                    <div class="h-1 w-12 bg-purple-500 rounded-full mt-3"></div>
                </div>
                <div class="p-2.5 bg-purple-50 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Votes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-2">Total Suara</p>
                    <h3 class="text-3xl font-bold text-gray-900 mb-1">{{ $totalVotes }}</h3>
                    <div class="h-1 w-12 bg-orange-500 rounded-full mt-3"></div>
                </div>
                <div class="p-2.5 bg-orange-50 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Elections Section -->
    @if ($activeElections->isNotEmpty())
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Pemilihan Sedang Berlangsung
            </h2>
            <div class="space-y-4">
                @foreach ($activeElections as $election)
                    @php
                        $totalVotes = $election->votes()->count();
                        $orgType = $election->organization->organization_type;
                        if ($orgType === 'LT') {
                            $eligibleVoters = \App\Models\User::role('Voter')->where('is_active', true)->count();
                        } elseif ($orgType === 'HMJ') {
                            $eligibleVoters = \App\Models\User::role('Voter')
                                ->where('is_active', true)
                                ->where('jurusan_id', $election->organization->user->jurusan_id)
                                ->count();
                        } else {
                            $eligibleVoters = $election->organization->members()->count();
                        }
                        $participation = $eligibleVoters > 0 ? round(($totalVotes / $eligibleVoters) * 100, 1) : 0;

                        // Calculate time left in a readable format
                        $endAt = \Carbon\Carbon::parse($election->end_at);
                        $now = now();
                        $totalMinutes = $now->diffInMinutes($endAt);

                        $days = floor($totalMinutes / 1440); // 1440 minutes in a day
                        $hours = floor(($totalMinutes % 1440) / 60);
                        $minutes = $totalMinutes % 60;

                        $timeLeftText = '';
                        if ($days > 0) {
                            $timeLeftText .= $days . ' hari ';
                        }
                        if ($hours > 0 || $days > 0) {
                            $timeLeftText .= $hours . ' jam ';
                        }
                        $timeLeftText .= $minutes . ' menit';
                    @endphp
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $election->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $election->organization->shorten_name }}</p>

                                <!-- Progress Bar -->
                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-sm mb-1">
                                        <span class="text-gray-600">Partisipasi</span>
                                        <span class="font-semibold text-gray-900">{{ $totalVotes }} /
                                            {{ $eligibleVoters }} ({{ $participation }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-green-600 h-2.5 rounded-full transition-all duration-500"
                                            style="width: {{ $participation }}%"></div>
                                    </div>
                                </div>

                                <!-- Countdown -->
                                <div class="flex items-center mt-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Berakhir dalam {{ $timeLeftText }}</span>
                                </div>
                            </div>

                            <div class="flex gap-2 flex-shrink-0">
                                <a href="{{ route('elections.detail', ['id' => $election->id]) }}" wire:navigate
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                    Lihat Hasil
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Results Section -->
        @if ($recentResults->isNotEmpty())
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Hasil Pemilihan Terbaru
                </h2>
                <div class="space-y-3">
                    @foreach ($recentResults as $result)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $result['election']->name }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Selesai
                                        {{ \Carbon\Carbon::parse($result['election']->end_at)->locale('id')->diffForHumans() }}
                                    </p>
                                </div>
                                <span
                                    class="flex-shrink-0 px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Selesai
                                </span>
                            </div>

                            @if ($result['winner'])
                                <div
                                    class="flex items-center gap-2 mt-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-amber-700 font-medium">Pemenang</p>
                                        <p class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $result['winner']->ketua->name }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-3 flex items-center justify-between text-sm">
                                <span class="text-gray-600">Partisipasi: <strong
                                        class="text-gray-900">{{ $result['participation'] }}%</strong></span>
                                <a href="{{ route('elections.detail', ['id' => $result['election']->id]) }}"
                                    wire:navigate class="text-purple-600 hover:text-purple-800 font-medium">
                                    Lihat Detail →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Hasil Pemilihan Terbaru
                </h2>
                <p class="text-gray-600">Belum ada pemilihan yang selesai baru-baru ini.</p>
            </div>

        @endif

        <!-- Upcoming Elections Section -->
        @if ($upcomingElections->isNotEmpty())
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pemilihan Mendatang
                </h2>
                <div class="space-y-3">
                    @foreach ($upcomingElections as $election)
                        @php
                            $startAt = \Carbon\Carbon::parse($election->start_at);
                            $now = now();
                            $totalMinutes = $now->diffInMinutes($startAt);

                            $days = floor($totalMinutes / 1440);
                            $hours = floor(($totalMinutes % 1440) / 60);
                            $minutes = $totalMinutes % 60;

                            $timeUntilText = '';
                            if ($days > 0) {
                                $timeUntilText .= $days . ' hari ';
                            }
                            if ($hours > 0 || $days > 0) {
                                $timeUntilText .= $hours . ' jam ';
                            }
                            $timeUntilText .= $minutes . ' menit';
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $election->name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $election->organization->shorten_name }}
                                    </p>
                                </div>
                                <span
                                    class="flex-shrink-0 px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Dijadwalkan
                                </span>
                            </div>

                            <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-xs text-blue-700 font-medium">Mulai dalam {{ $timeUntilText }}
                                        </p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($election->start_at)->locale('id')->translatedFormat('d F Y, H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 flex justify-end">
                                <a href="{{ route('elections.detail', ['id' => $election->id]) }}" wire:navigate
                                    class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                    Lihat Detail →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if ($isOrganization)
            <a href="{{ route('elections.create') }}" wire:navigate
                class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center mb-4">
                    <div class="bg-indigo-100 p-3 rounded-full mr-4 group-hover:bg-indigo-200 transition-colors">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Buat Pemilihan Baru</h3>
                </div>
                <p class="text-gray-600 text-sm mb-4">Mulai pemilihan baru dengan kandidat dan jadwal yang
                    ditentukan.</p>
                <span class="text-indigo-600 group-hover:text-indigo-800 font-medium text-sm">
                    Buat Sekarang →
                </span>
            </a>

            <a href="{{ route('elections.index') }}" wire:navigate
                class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center mb-4">
                    <div class="bg-purple-100 p-3 rounded-full mr-4 group-hover:bg-purple-200 transition-colors">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Kelola Pemilihan</h3>
                </div>
                <p class="text-gray-600 text-sm mb-4">Monitor hasil pemilihan dan statistik suara secara real-time.
                </p>
                <span class="text-purple-600 group-hover:text-purple-800 font-medium text-sm">
                    Lihat Detail →
                </span>
            </a>
        @endif
    </div>
</div>
