<?php

use App\Models\Election;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.vote', ['subtitle' => 'Pemilihan'])] class extends Component {
    public $activeElections = [];
    public $upcomingElections = [];
    public $pastElections = [];
    public $showElection = false;

    public function mount()
    {
        $user = Auth::user();

        // Cek apakah user punya role 'voter' dan aktif
        if (!$user->hasRole('Voter') || !$user->is_active) {
            $this->activeElections = collect([]);
            $this->upcomingElections = collect([]);
            $this->pastElections = collect([]);
            return;
        }

        $mahasiswa = $user->mahasiswa;
        $userJurusanId = $user->jurusan_id;

        // Get active elections with filter
        $this->activeElections = Election::where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->where('status', 'published')
            ->with(['candidates.ketua', 'candidates.wakil', 'organization.user', 'organization.members'])
            ->get()
            ->filter(function ($election) use ($user, $userJurusanId) {
                $orgType = $election->organization->organization_type;

                // LT: Semua user dengan role 'Voter' yang aktif
                if ($orgType === 'LT') {
                    return true;
                }

                // HMJ: Cek jurusan_id dari organization creator dengan user login
                if ($orgType === 'HMJ') {
                    $orgJurusanId = $election->organization->user->jurusan_id;
                    return $orgJurusanId && $orgJurusanId === $userJurusanId;
                }

                // UKM: Hanya user yang ada di organization_members dengan organization_id yang sama
                if ($orgType === 'UKM') {
                    return $election->organization->members->where('user_id', $user->id)->isNotEmpty();
                }

                return false;
            })
            ->map(function ($election) use ($mahasiswa) {
                // Check if user has voted
                if ($mahasiswa) {
                    $election->user_has_voted = Vote::where('election_id', $election->id)->where('mahasiswa_id', $mahasiswa->id)->exists();
                } else {
                    $election->user_has_voted = false;
                }
                return $election;
            });

        // Get upcoming elections with filter
        $this->upcomingElections = Election::where('start_at', '>', now())
            ->where('status', 'published')
            ->orderBy('start_at', 'asc')
            ->with(['candidates.ketua', 'candidates.wakil', 'organization.user', 'organization.members'])
            ->get()
            ->filter(function ($election) use ($user, $userJurusanId) {
                $orgType = $election->organization->organization_type;

                if ($orgType === 'LT') {
                    return true;
                }

                if ($orgType === 'HMJ') {
                    $orgJurusanId = $election->organization->user->jurusan_id;
                    return $orgJurusanId && $orgJurusanId === $userJurusanId;
                }

                if ($orgType === 'UKM') {
                    return $election->organization->members->where('user_id', $user->id)->isNotEmpty();
                }

                return false;
            });

        // Get past elections with user's vote
        $this->pastElections = Election::where('end_at', '<', now())
            ->orderBy('end_at', 'desc')
            ->with(['candidates.ketua', 'candidates.wakil', 'organization.user', 'organization.members'])
            ->get()
            ->filter(function ($election) use ($user, $userJurusanId) {
                $orgType = $election->organization->organization_type;

                if ($orgType === 'LT') {
                    return true;
                }

                if ($orgType === 'HMJ') {
                    $orgJurusanId = $election->organization->user->jurusan_id;
                    return $orgJurusanId && $orgJurusanId === $userJurusanId;
                }

                if ($orgType === 'UKM') {
                    return $election->organization->members->where('user_id', $user->id)->isNotEmpty();
                }

                return false;
            })
            ->map(function ($election) use ($mahasiswa) {
                // Only load user vote if user is mahasiswa
                if ($mahasiswa) {
                    $userVote = Vote::where('election_id', $election->id)->where('mahasiswa_id', $mahasiswa->id)->with('candidate.ketua', 'candidate.wakil')->first();

                    $election->user_vote = $userVote;
                }
                return $election;
            });
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Hero Section -->
    <div class="relative bg-blue-600 text-white rounded-b-3xl shadow-lg overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 w-full h-full">
            <img src="https://picsum.photos/seed/picsum/1920/400" alt="Background"
                class="w-full h-full object-cover opacity-20">
        </div>

        <!-- Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('assets/image/id_logo.png') }}" alt="Logo"
                        class="w-24 h-24 object-contain drop-shadow-2xl">
                </div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4">E-Voting System</h1>
                <p class="text-xl text-indigo-100 max-w-2xl mx-auto">
                    Partisipasi Anda sangat berarti! Gunakan hak suara Anda untuk masa depan yang lebih baik.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Active Elections -->
        @if ($activeElections->count() > 0)
            <div class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-3xl font-bold text-gray-800">Pemilihan Aktif</h2>
                        <p class="text-gray-600">Pilih kandidat favorit Anda sekarang!</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($activeElections as $election)
                        <div
                            class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition duration-300 transform hover:-translate-y-1">
                            <!-- Pamflet Image -->
                            @if ($election->pamphlet)
                                <div class="w-full h-48 bg-gray-100">
                                    <img src="{{ asset('storage/' . $election->pamphlet) }}" alt="{{ $election->name }}"
                                        class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-full h-48 bg-gradient-to-r from-green-500 to-emerald-500"></div>
                            @endif

                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-2">
                                    @php
                                        $orgType = $election->organization->organization_type;
                                        $badgeColor =
                                            $orgType === 'LT'
                                                ? 'bg-blue-100 text-blue-700'
                                                : ($orgType === 'HMJ'
                                                    ? 'bg-purple-100 text-purple-700'
                                                    : 'bg-amber-100 text-amber-700');
                                        $badgeText =
                                            $orgType === 'LT'
                                                ? 'Lembaga Tinggi'
                                                : ($orgType === 'HMJ'
                                                    ? 'Himpunan Jurusan'
                                                    : 'Unit Kegiatan');
                                    @endphp
                                    <span class="{{ $badgeColor }} text-xs font-semibold px-2 py-1 rounded">
                                        {{ $badgeText }}
                                    </span>
                                    @if ($election->user_has_voted)
                                        <span
                                            class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">
                                            ✓ Sudah Memilih
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $election->name }}</h3>
                                @if ($election->organization)
                                    <p class="text-sm text-gray-500 mb-3">{{ $election->organization->name }}</p>
                                @endif

                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                        {{ $election->candidates->count() }} Kandidat
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Berakhir: {{ $election->end_at->format('d M Y, H:i') }}
                                    </div>
                                </div>

                                @if ($election->user_has_voted)
                                    <button disabled
                                        class="w-full bg-gray-300 text-gray-600 font-semibold py-3 px-4 rounded-lg cursor-not-allowed">
                                        Sudah Memilih
                                    </button>
                                @else
                                    <a href="{{ route('vote.show', $election->id) }}" wire:navigate
                                        class="block w-full bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white text-center font-semibold py-3 px-4 rounded-lg transition duration-150 shadow-md hover:shadow-lg">
                                        Pilih Sekarang
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Upcoming Elections -->
        @if ($upcomingElections->count() > 0)
            <div class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-3xl font-bold text-gray-800">Pemilihan Mendatang</h2>
                        <p class="text-gray-600">Persiapkan diri Anda untuk pemilihan berikutnya</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($upcomingElections as $election)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden border-2 border-blue-100">
                            <!-- Pamflet Image -->
                            @if ($election->pamphlet)
                                <div class="w-full h-48 bg-gray-100">
                                    <img src="{{ asset('storage/' . $election->pamphlet) }}"
                                        alt="{{ $election->name }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-full h-48 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                            @endif

                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-2">
                                    @php
                                        $orgType = $election->organization->organization_type;
                                        $badgeColor =
                                            $orgType === 'LT'
                                                ? 'bg-blue-100 text-blue-700'
                                                : ($orgType === 'HMJ'
                                                    ? 'bg-purple-100 text-purple-700'
                                                    : 'bg-amber-100 text-amber-700');
                                        $badgeText =
                                            $orgType === 'LT'
                                                ? 'Lembaga Tinggi'
                                                : ($orgType === 'HMJ'
                                                    ? 'Himpunan Jurusan'
                                                    : 'Unit Kegiatan');
                                    @endphp
                                    <span class="{{ $badgeColor }} text-xs font-semibold px-2 py-1 rounded">
                                        {{ $badgeText }}
                                    </span>
                                    <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded">
                                        Akan Datang
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $election->name }}</h3>
                                @if ($election->organization)
                                    <p class="text-sm text-gray-500 mb-3">{{ $election->organization->name }}</p>
                                @endif

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                    <p class="text-sm font-semibold text-blue-800 mb-1">Dimulai pada:</p>
                                    <p class="text-lg font-bold text-blue-600">
                                        {{ $election->start_at->format('d M Y, H:i') }}</p>
                                </div>

                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    {{ $election->candidates->count() }} Kandidat
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Past Elections -->
        @if ($pastElections->count() > 0)
            <div class="mb-12">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-gray-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-3xl font-bold text-gray-800">Riwayat Pemilihan</h2>
                        <p class="text-gray-600">Lihat hasil pemilihan sebelumnya</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($pastElections as $election)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden opacity-90">
                            <!-- Pamflet Image -->
                            @if ($election->pamphlet)
                                <div class="w-full h-48 bg-gray-100">
                                    <img src="{{ asset('storage/' . $election->pamphlet) }}"
                                        alt="{{ $election->name }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-full h-48 bg-gray-500"></div>
                            @endif

                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-2">
                                    @php
                                        $orgType = $election->organization->organization_type;
                                        $badgeColor =
                                            $orgType === 'LT'
                                                ? 'bg-blue-100 text-blue-700'
                                                : ($orgType === 'HMJ'
                                                    ? 'bg-purple-100 text-purple-700'
                                                    : 'bg-amber-100 text-amber-700');
                                        $badgeText =
                                            $orgType === 'LT'
                                                ? 'Lembaga Tinggi'
                                                : ($orgType === 'HMJ'
                                                    ? 'Himpunan Jurusan'
                                                    : 'Unit Kegiatan');
                                    @endphp
                                    <span class="{{ $badgeColor }} text-xs font-semibold px-2 py-1 rounded">
                                        {{ $badgeText }}
                                    </span>
                                    <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2 py-1 rounded">
                                        Selesai
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $election->name }}</h3>
                                @if ($election->organization)
                                    <p class="text-sm text-gray-500 mb-3">{{ $election->organization->name }}</p>
                                @endif

                                @if ($election->user_vote)
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                        <p class="text-sm font-semibold text-green-800 mb-2">Pilihan Anda:</p>
                                        <p class="text-sm font-bold text-green-600">
                                            {{ $election->user_vote->candidate->ketua->name }}
                                            @if ($election->user_vote->candidate->wakil)
                                                & {{ $election->user_vote->candidate->wakil->name }}
                                            @endif
                                        </p>
                                    </div>
                                @else
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                        <p class="text-sm text-yellow-800">Anda tidak berpartisipasi dalam pemilihan
                                            ini</p>
                                    </div>
                                @endif

                                <div class="text-sm text-gray-600">
                                    Berakhir: {{ $election->end_at->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- No Elections Message -->
        @if ($activeElections->count() == 0 && $upcomingElections->count() == 0 && $pastElections->count() == 0)
            <div class="text-center py-20">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-200 rounded-full mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Pemilihan</h3>
                <p class="text-gray-500">Saat ini tidak ada pemilihan yang tersedia. Silakan cek kembali nanti.</p>
            </div>
        @endif
    </div>
</div>
