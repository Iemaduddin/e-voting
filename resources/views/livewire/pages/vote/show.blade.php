<?php

use App\Models\Election;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.vote', ['subtitle' => 'Pilih Kandidat'])] class extends Component {
    public Election $election;
    public $selectedCandidate = null;
    public $showConfirmModal = false;
    public $showSuccessModal = false;
    public $hasVoted = false;

    public function selectCandidate($candidateId)
    {
        $this->selectedCandidate = $candidateId;
        $this->showConfirmModal = true; // Langsung tampilkan modal konfirmasi
    }

    public function confirmVote()
    {
        $this->showConfirmModal = false;
        $this->submitVote();
    }

    public function cancelVote()
    {
        $this->showConfirmModal = false;
        $this->selectedCandidate = null;
    }

    public function mount($id)
    {
        $this->election = Election::with(['candidates.ketua', 'candidates.wakil', 'organization.user', 'organization.members'])->findOrFail($id);
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        // Check if user has role 'voter' dan aktif
        if (!$user->hasRole('Voter') || !$user->is_active) {
            notyf()->duration(4000)->position('x', 'right')->position('y', 'top')->addError('Anda tidak memiliki akses untuk voting.');
            return $this->redirect(route('vote.index'), navigate: true);
        }

        // Check if election is active
        if (!$this->election->isActive()) {
            if ($this->election->hasEnded()) {
                notyf()->duration(4000)->position('x', 'right')->position('y', 'top')->addError('Pemilihan ini sudah berakhir.');
            } else {
                notyf()->duration(4000)->position('x', 'right')->position('y', 'top')->addError('Pemilihan ini belum dimulai.');
            }
            return $this->redirect(route('vote.index'), navigate: true);
        }

        // Check authorization based on organization type
        $orgType = $this->election->organization->organization_type;
        $userJurusanId = $user->jurusan_id;
        $canVote = false;

        if ($orgType === 'LT') {
            // LT: Semua user dengan role 'voter' yang aktif dapat memilih
            $canVote = true;
        } elseif ($orgType === 'HMJ') {
            // HMJ: Cek jurusan_id dari organization creator dengan user login
            $orgJurusanId = $this->election->organization->user->jurusan_id;
            $canVote = $orgJurusanId && $orgJurusanId === $userJurusanId;
        } elseif ($orgType === 'UKM') {
            // UKM: Hanya user yang ada di organization_members dengan organization_id yang sama
            $canVote = $this->election->organization->members->where('user_id', $user->id)->isNotEmpty();
        }

        if (!$canVote) {
            notyf()->duration(4000)->position('x', 'right')->position('y', 'top')->addError('Anda tidak memiliki akses untuk memilih dalam pemilihan ini.');
            return $this->redirect(route('vote.index'), navigate: true);
        }

        // Check if user has already voted (only for mahasiswa)
        if ($mahasiswa) {
            $this->hasVoted = Vote::where('election_id', $this->election->id)->where('mahasiswa_id', $mahasiswa->id)->exists();

            if ($this->hasVoted) {
                notyf()->duration(4000)->position('x', 'right')->position('y', 'top')->addError('Anda sudah memberikan suara pada pemilihan ini.');
                return $this->redirect(route('vote.index'), navigate: true);
            }
        }
    }

    public function submitVote()
    {
        if (!$this->selectedCandidate) {
            notyf()->duration(3000)->position('x', 'right')->position('y', 'top')->addError('Silakan pilih kandidat terlebih dahulu.');
            return;
        }

        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            notyf()->duration(4000)->position('x', 'right')->position('y', 'top')->addError('Hanya mahasiswa yang dapat memberikan suara.');
            return;
        }

        try {
            Vote::create([
                'election_id' => $this->election->id,
                'candidate_id' => $this->selectedCandidate,
                'mahasiswa_id' => $mahasiswa->id,
            ]);

            // Show success modal and set hasVoted
            $this->hasVoted = true;
            $this->showSuccessModal = true;
            // Keep selectedCandidate for display purpose
        } catch (\Exception $e) {
            notyf()->duration(4000)->position('x', 'right')->position('y', 'top')->addError('Terjadi kesalahan. Silakan coba lagi.');
        }
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('vote.index') }}" wire:navigate
                class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Pemilihan
            </a>
        </div>

        <!-- Banner Header -->
        @if ($election->banner)
            <div class="relative mb-8 h-80 overflow-hidden rounded-xl shadow-2xl">
                <img src="{{ asset('storage/' . $election->banner) }}" alt="Banner"
                    class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/30 to-black/70"></div>
                <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                    <h2 class="text-4xl font-bold mb-3">{{ $election->name }}</h2>
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="font-semibold">{{ $election->organization->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center gap-2 bg-green-500/80 backdrop-blur-sm px-4 py-2 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $election->start_at->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex items-center gap-2 bg-red-500/80 backdrop-blur-sm px-4 py-2 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $election->end_at->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Pamphlet & Description Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Pamphlet (Left Side) -->
                @if ($election->pamphlet)
                    <div class="lg:col-span-1">
                        <div class="sticky top-6">
                            <div
                                class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 overflow-hidden shadow-md hover:shadow-xl transition-all duration-300">
                                <img src="{{ asset('storage/' . $election->pamphlet) }}" alt="Pamflet"
                                    class="w-full h-auto object-contain">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Description (Right Side) -->
                <div class="{{ $election->pamphlet ? 'lg:col-span-3' : 'lg:col-span-4' }}">
                    <div class="prose prose-lg max-w-none text-gray-700">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center border-b pb-3">
                            <svg class="w-7 h-7 mr-3 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Tentang Pemilihan
                        </h3>
                        {!! $election->description ?? '<p class="text-gray-400 italic">Tidak ada deskripsi</p>' !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Instruction -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg mb-8">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">Petunjuk Pemilihan:</h3>
                    <ul class="list-disc list-inside text-blue-700 space-y-1">
                        <li>Pilih SATU kandidat dengan mengklik tombol "PILIH KANDIDAT"</li>
                        <li>Pastikan pilihan Anda sudah benar sebelum menekan tombol "Kirim Suara"</li>
                        <li>Keputusan Anda bersifat FINAL dan tidak dapat diubah</li>
                        <li>Suara Anda bersifat rahasia dan terenkripsi</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Candidates Section -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border border-purple-200 shadow-sm p-8">
            <div class="text-center mb-8">
                <h3 class="text-3xl font-bold text-gray-900 mb-2 flex items-center justify-center">
                    <svg class="w-8 h-8 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Daftar Kandidat
                </h3>
                <p class="text-gray-600">Pilih kandidat favorit Anda</p>
            </div>

            @if ($election->candidates && $election->candidates->count() > 0)
                <!-- Real Candidates -->
                <div class="grid grid-cols-1 gap-6">
                    @foreach ($election->candidates as $index => $candidate)
                        <div
                            class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden hover:border-purple-400 hover:shadow-2xl transition-all duration-300 group {{ $selectedCandidate == $candidate->id ? 'ring-4 ring-green-500 border-green-500' : '' }}">
                            <div class="md:flex">
                                <!-- Photo Section -->
                                <div class="md:w-2/5 bg-gradient-to-br from-purple-100 to-pink-100 p-6">
                                    @php
                                        $photoData = is_array($candidate->photo)
                                            ? $candidate->photo
                                            : json_decode($candidate->photo, true);
                                        $ketuaPhoto = $photoData['ketua'] ?? null;
                                        $wakilPhoto = $photoData['wakil'] ?? null;
                                    @endphp

                                    @if ($wakilPhoto)
                                        <!-- Grid 2 kolom untuk Ketua & Wakil -->
                                        <div class="grid grid-cols-2 gap-4">
                                            <!-- Ketua -->
                                            <div class="text-center">
                                                @if ($ketuaPhoto)
                                                    <div class="relative group/photo">
                                                        <img src="{{ asset('storage/' . $ketuaPhoto) }}"
                                                            alt="Foto Ketua"
                                                            class="w-full aspect-square object-cover rounded-xl shadow-lg mb-3 border-2 border-purple-300 group-hover/photo:scale-105 transition-transform duration-300">
                                                    </div>
                                                @else
                                                    <div
                                                        class="w-full aspect-square rounded-xl bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center text-white text-4xl font-bold mb-3 shadow-lg">
                                                        {{ substr($candidate->ketua->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <span
                                                    class="inline-block bg-purple-500 text-white text-xs font-semibold px-3 py-1 rounded-full mb-1">Ketua</span>
                                                <p class="text-sm font-semibold text-gray-800 mb-1">
                                                    {{ $candidate->ketua->name }}</p>
                                                <p class="text-xs text-gray-600">{{ $candidate->ketua->nim }}</p>
                                            </div>

                                            <!-- Wakil -->
                                            <div class="text-center">
                                                @if ($wakilPhoto)
                                                    <div class="relative group/photo">
                                                        <img src="{{ asset('storage/' . $wakilPhoto) }}"
                                                            alt="Foto Wakil"
                                                            class="w-full aspect-square object-cover rounded-xl shadow-lg mb-3 border-2 border-pink-300 group-hover/photo:scale-105 transition-transform duration-300">
                                                    </div>
                                                @else
                                                    <div
                                                        class="w-full aspect-square rounded-xl bg-gradient-to-br from-pink-400 to-purple-400 flex items-center justify-center text-white text-4xl font-bold mb-3 shadow-lg">
                                                        {{ substr($candidate->wakil->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <span
                                                    class="inline-block bg-pink-500 text-white text-xs font-semibold px-3 py-1 rounded-full mb-1">Wakil</span>
                                                <p class="text-sm font-semibold text-gray-800 mb-1">
                                                    {{ $candidate->wakil->name }}</p>
                                                <p class="text-xs text-gray-600">{{ $candidate->wakil->nim }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Solo Ketua -->
                                        <div class="text-center">
                                            @if ($ketuaPhoto)
                                                <div class="relative group/photo mx-auto max-w-xs">
                                                    <img src="{{ asset('storage/' . $ketuaPhoto) }}" alt="Foto Ketua"
                                                        class="w-full aspect-square object-cover rounded-xl shadow-lg mb-4 border-2 border-purple-300 group-hover/photo:scale-105 transition-transform duration-300">
                                                </div>
                                            @else
                                                <div
                                                    class="w-48 h-48 mx-auto rounded-xl bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center text-white text-5xl font-bold mb-4 shadow-lg">
                                                    {{ substr($candidate->ketua->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <span
                                                class="inline-block bg-purple-500 text-white text-xs font-semibold px-3 py-1 rounded-full mb-2">Ketua</span>
                                            <p class="text-lg font-bold text-gray-800 mb-1">
                                                {{ $candidate->ketua->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $candidate->ketua->nim }}</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Content Section -->
                                <div class="md:w-3/5 p-6">
                                    <!-- Header with Number Badge -->
                                    <div class="flex items-center gap-3 mb-4">
                                        <div
                                            class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xl font-bold shadow-lg">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">
                                                {{ $candidate->ketua->name }}
                                                @if ($candidate->wakil)
                                                    & {{ $candidate->wakil->name }}
                                                @endif
                                            </h3>
                                            <p class="text-sm text-gray-600">Kandidat {{ $index + 1 }}</p>
                                        </div>
                                    </div>

                                    <!-- Visi Accordion -->
                                    <details class="group/visi mb-3">
                                        <summary
                                            class="flex items-center justify-between cursor-pointer list-none bg-gradient-to-r from-purple-50 to-pink-50 p-3 rounded-lg hover:from-purple-100 hover:to-pink-100 transition-all duration-200">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-purple-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span class="font-bold text-purple-700">VISI</span>
                                            </div>
                                            <svg class="w-5 h-5 text-purple-600 transition-transform duration-200 group-open/visi:rotate-180"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </summary>
                                        <div class="mt-2 p-4 bg-white rounded-lg border border-purple-200">
                                            <p class="text-gray-700 leading-relaxed text-justify">
                                                {{ $candidate->visi }}
                                            </p>
                                        </div>
                                    </details>

                                    <!-- Misi Accordion -->
                                    <details class="group/misi mb-4">
                                        <summary
                                            class="flex items-center justify-between cursor-pointer list-none bg-gradient-to-r from-pink-50 to-purple-50 p-3 rounded-lg hover:from-pink-100 hover:to-purple-100 transition-all duration-200">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-pink-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                                </svg>
                                                <span class="font-bold text-pink-700">MISI</span>
                                            </div>
                                            <svg class="w-5 h-5 text-pink-600 transition-transform duration-200 group-open/misi:rotate-180"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </summary>
                                        <div class="mt-2 p-4 bg-white rounded-lg border border-pink-200">
                                            @php
                                                $misiData = is_array($candidate->misi)
                                                    ? $candidate->misi
                                                    : json_decode($candidate->misi, true);
                                            @endphp
                                            @if ($misiData && is_array($misiData))
                                                <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                                    @foreach ($misiData as $misiItem)
                                                        <li class="leading-relaxed">{{ $misiItem }}</li>
                                                    @endforeach
                                                </ol>
                                            @endif
                                        </div>
                                    </details>

                                    <!-- Action Buttons -->
                                    <div class="space-y-2">
                                        @php
                                            $cvData = is_array($candidate->cv)
                                                ? $candidate->cv
                                                : json_decode($candidate->cv, true);
                                            $ketuaCv = $cvData['ketua'] ?? null;
                                            $wakilCv = $cvData['wakil'] ?? null;
                                        @endphp

                                        @if ($ketuaCv || $wakilCv || $candidate->link)
                                            <div class="grid grid-cols-2 gap-2 mb-3">
                                                @if ($ketuaCv)
                                                    <a href="{{ asset('storage/' . $ketuaCv) }}" target="_blank"
                                                        onclick="event.stopPropagation()"
                                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg text-sm font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        CV Ketua
                                                    </a>
                                                @endif

                                                @if ($wakilCv)
                                                    <a href="{{ asset('storage/' . $wakilCv) }}" target="_blank"
                                                        onclick="event.stopPropagation()"
                                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg text-sm font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        CV Wakil
                                                    </a>
                                                @endif

                                                @if ($candidate->link)
                                                    <a href="{{ $candidate->link }}" target="_blank"
                                                        onclick="event.stopPropagation()"
                                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg text-sm font-semibold hover:from-purple-600 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg {{ $ketuaCv && $wakilCv ? 'col-span-2' : '' }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                        </svg>
                                                        Link Profil
                                                    </a>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- PILIH KANDIDAT Button -->
                                        <button type="button" wire:click="selectCandidate('{{ $candidate->id }}')"
                                            @disabled($hasVoted)
                                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg font-bold hover:from-green-600 hover:to-emerald-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:scale-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            @if ($hasVoted)
                                                SUDAH MEMILIH
                                            @else
                                                PILIH KANDIDAT
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- No Candidates Message -->
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="text-gray-500 font-semibold">Belum ada kandidat yang terdaftar</p>
                </div>
            @endif
        </div>

        <!-- Confirmation Modal -->
        @if ($showConfirmModal && $selectedCandidate)
            @php
                $candidate = $election->candidates->firstWhere('id', $selectedCandidate);
            @endphp
            <div x-data="{ show: @entangle('showConfirmModal') }" x-show="show" x-transition:enter="transition ease-out duration-300"
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
                        <!-- Warning Icon -->
                        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-amber-100 mb-6">
                            <svg class="h-12 w-12 text-amber-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>

                        <!-- Confirmation Message -->
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">
                            Konfirmasi Pilihan Anda
                        </h3>

                        <div class="bg-blue-50 rounded-lg p-4 mb-6">
                            <p class="text-gray-700 mb-2">Anda akan memilih:</p>
                            <p class="text-lg font-bold text-gray-900">
                                {{ $candidate->ketua->name }}
                                @if ($candidate->wakil)
                                    & {{ $candidate->wakil->name }}
                                @endif
                            </p>
                        </div>

                        <p class="text-gray-600 mb-8 text-sm">
                            <strong class="text-red-600">PERHATIAN:</strong> Pilihan Anda bersifat
                            <strong>FINAL</strong> dan <strong>TIDAK DAPAT DIUBAH</strong> setelah dikonfirmasi.
                        </p>

                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button wire:click="cancelVote"
                                class="flex-1 bg-gray-100 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-200 transition-all duration-200">
                                Batal
                            </button>
                            <button wire:click="confirmVote" wire:loading.attr="disabled"
                                class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold py-3 px-6 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50">
                                <span wire:loading.remove wire:target="confirmVote">Ya, Yakin</span>
                                <span wire:loading wire:target="confirmVote">Mengirim...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Success Modal -->
        @if ($showSuccessModal)
            <div x-data="{ show: @entangle('showSuccessModal') }" x-show="show" x-transition:enter="transition ease-out duration-300"
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
                            <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                        </div>

                        <!-- Success Message -->
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">
                            Suara Berhasil Terkirim!
                        </h3>

                        @if ($selectedCandidate)
                            @php
                                $votedCandidate = $election->candidates->firstWhere('id', $selectedCandidate);
                            @endphp
                            <div class="bg-green-50 rounded-lg p-4 mb-4">
                                <p class="text-gray-700 text-sm mb-1">Anda telah memilih:</p>
                                <p class="text-lg font-bold text-gray-900">
                                    {{ $votedCandidate->ketua->name }}
                                    @if ($votedCandidate->wakil)
                                        & {{ $votedCandidate->wakil->name }}
                                    @endif
                                </p>
                            </div>
                        @endif

                        <p class="text-gray-600 mb-8">
                            Terima kasih telah berpartisipasi dalam pemilihan ini. Suara Anda telah tercatat dengan
                            aman.
                        </p>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-3">
                            <button wire:click="$set('showSuccessModal', false)"
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
        @endif
    </div>
</div>
