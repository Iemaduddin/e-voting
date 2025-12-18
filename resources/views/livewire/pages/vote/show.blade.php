<?php

use App\Models\Election;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.vote', ['subtitle' => 'Pilih Kandidat'])] class extends Component {
    public Election $election;
    public $selectedCandidate = null;

    public function mount($id)
    {
        $this->election = Election::with(['candidates.ketua', 'candidates.wakil', 'organization'])->findOrFail($id);

        // Check if election is active
        if (!$this->election->isActive()) {
            if ($this->election->hasEnded()) {
                notyf()->duration(4000)->position('x', 'right')->position('y', 'top')->addError('Pemilihan ini sudah berakhir.');
            } else {
                notyf()->duration(4000)->position('x', 'right')->position('y', 'top')->addError('Pemilihan ini belum dimulai.');
            }
            return $this->redirect(route('vote.index'), navigate: true);
        }

        // Check if user has already voted
        $hasVoted = Vote::where('election_id', $this->election->id)->where('user_id', Auth::id())->exists();

        if ($hasVoted) {
            notyf()->duration(4000)->position('x', 'right')->position('y', 'top')->addError('Anda sudah memberikan suara pada pemilihan ini.');
            return $this->redirect(route('vote.index'), navigate: true);
        }
    }

    public function submitVote()
    {
        if (!$this->selectedCandidate) {
            notyf()->duration(3000)->position('x', 'right')->position('y', 'top')->addError('Silakan pilih kandidat terlebih dahulu.');
            return;
        }

        try {
            Vote::create([
                'election_id' => $this->election->id,
                'candidate_id' => $this->selectedCandidate,
                'user_id' => Auth::id(),
            ]);

            notyf()->duration(4000)->position('x', 'right')->position('y', 'top')->addSuccess('Terima kasih! Suara Anda berhasil tersimpan.');

            return $this->redirect(route('vote.index'), navigate: true);
        } catch (\Exception $e) {
            notyf()->duration(4000)->position('x', 'right')->position('y', 'top')->addError('Terjadi kesalahan. Silakan coba lagi.');
        }
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-12">
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

        <!-- Election Header -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <div class="flex items-start justify-between">
                <div>
                    <span
                        class="bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full mb-3 inline-block">
                        🔴 LIVE
                    </span>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">{{ $election->name }}</h1>
                    @if ($election->organization)
                        <p class="text-lg text-gray-600 mb-4">{{ $election->organization->name }}</p>
                    @endif
                    <p class="text-gray-600 mb-4">{{ $election->description }}</p>
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Dimulai: {{ $election->start_at->format('d M Y, H:i') }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Berakhir: {{ $election->end_at->format('d M Y, H:i') }}
                        </div>
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
                        <li>Pilih SATU kandidat dengan mengklik kartu kandidat</li>
                        <li>Pastikan pilihan Anda sudah benar sebelum menekan tombol "Kirim Suara"</li>
                        <li>Keputusan Anda bersifat FINAL dan tidak dapat diubah</li>
                        <li>Suara Anda bersifat rahasia dan terenkripsi</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Candidates Grid -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Pilih Kandidat Anda</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($election->candidates as $candidate)
                    <div wire:click="$set('selectedCandidate', '{{ $candidate->id }}')"
                        class="bg-white rounded-xl shadow-lg overflow-hidden cursor-pointer transform transition duration-300 hover:scale-105 hover:shadow-2xl {{ $selectedCandidate == $candidate->id ? 'ring-4 ring-indigo-500 shadow-2xl' : '' }}">

                        <!-- Photo -->
                        <div class="relative">
                            <img src="{{ Storage::url($candidate->photo) }}" alt="{{ $candidate->ketua->name }}"
                                class="w-full h-64 object-cover">

                            @if ($selectedCandidate == $candidate->id)
                                <div class="absolute top-4 right-4 bg-indigo-600 text-white rounded-full p-2">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="p-6">
                            <div class="mb-4">
                                <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $candidate->ketua->name }}</h3>
                                @if ($candidate->wakil)
                                    <p class="text-gray-600">& {{ $candidate->wakil->name }}</p>
                                @endif
                            </div>

                            <!-- Visi -->
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    Visi
                                </h4>
                                <p class="text-sm text-gray-600 line-clamp-3">{{ $candidate->visi }}</p>
                            </div>

                            <!-- Misi -->
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                                        </path>
                                    </svg>
                                    Misi
                                </h4>
                                <p class="text-sm text-gray-600 line-clamp-3">{{ $candidate->misi }}</p>
                            </div>

                            <!-- Additional Links -->
                            @if ($candidate->link || $candidate->cv)
                                <div class="flex gap-2 mt-4">
                                    @if ($candidate->link)
                                        <a href="{{ $candidate->link }}" target="_blank"
                                            onclick="event.stopPropagation()"
                                            class="flex-1 text-center bg-blue-100 text-blue-700 text-xs font-semibold py-2 px-3 rounded-lg hover:bg-blue-200 transition duration-150">
                                            Detail
                                        </a>
                                    @endif
                                    @if ($candidate->cv)
                                        <a href="{{ Storage::url($candidate->cv) }}" target="_blank"
                                            onclick="event.stopPropagation()"
                                            class="flex-1 text-center bg-purple-100 text-purple-700 text-xs font-semibold py-2 px-3 rounded-lg hover:bg-purple-200 transition duration-150">
                                            CV
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Submit Button -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="max-w-2xl mx-auto">
                @if ($selectedCandidate)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-green-800 font-semibold">
                                Kandidat dipilih:
                                {{ $election->candidates->firstWhere('id', $selectedCandidate)->ketua->name }}
                                @if ($election->candidates->firstWhere('id', $selectedCandidate)->wakil)
                                    & {{ $election->candidates->firstWhere('id', $selectedCandidate)->wakil->name }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endif

                <button wire:click="submitVote" wire:loading.attr="disabled"
                    class="w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-lg shadow-lg text-lg font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                    {{ !$selectedCandidate ? 'disabled' : '' }}>

                    <!-- Loading Spinner -->
                    <svg wire:loading wire:target="submitVote" class="animate-spin h-6 w-6 mr-3"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>

                    <span wire:loading.remove wire:target="submitVote">
                        <svg class="w-6 h-6 mr-2 inline-block" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Kirim Suara Saya
                    </span>
                    <span wire:loading wire:target="submitVote">Mengirim...</span>
                </button>

                <p class="text-center text-sm text-gray-500 mt-4">
                    Dengan menekan tombol di atas, Anda menyetujui bahwa pilihan Anda adalah final dan tidak dapat
                    diubah.
                </p>
            </div>
        </div>
    </div>
</div>
