<div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold text-gray-900">Manajemen Kandidat</h3>
    <a href="{{ route('candidates.create', ['electionId' => $election->id]) }}" wire:navigate
        class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Kandidat
    </a>
</div>

@if ($election->candidates && $election->candidates->count() > 0)
    <!-- Candidates Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        No
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Foto
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ketua
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Wakil
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Visi
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($election->candidates as $index => $candidate)
                    @php
                        $photoData = is_array($candidate->photo)
                            ? $candidate->photo
                            : json_decode($candidate->photo, true);
                        $ketuaPhoto = $photoData['ketua'] ?? null;
                        $wakilPhoto = $photoData['wakil'] ?? null;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if ($ketuaPhoto)
                                    <img src="{{ asset('storage/' . $ketuaPhoto) }}" alt="Foto Ketua"
                                        class="w-10 h-10 rounded-full object-cover border-2 border-blue-500">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                        K
                                    </div>
                                @endif

                                @if ($candidate->wakil && $wakilPhoto)
                                    <img src="{{ asset('storage/' . $wakilPhoto) }}" alt="Foto Wakil"
                                        class="w-10 h-10 rounded-full object-cover border-2 border-cyan-500">
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $candidate->ketua->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $candidate->ketua->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($candidate->wakil)
                                <div class="text-sm font-medium text-gray-900">{{ $candidate->wakil->name }}</div>
                                <div class="text-xs text-gray-500">{{ $candidate->wakil->email ?? '-' }}</div>
                            @else
                                <span class="text-sm text-gray-400 italic">Tidak ada wakil</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 line-clamp-2">{{ Str::limit($candidate->visi, 100) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('candidates.edit', ['id' => $candidate->id]) }}" wire:navigate
                                    class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button wire:click="deleteCandidate({{ $candidate->id }})"
                                    wire:confirm="Apakah Anda yakin ingin menghapus kandidat ini?"
                                    class="text-red-600 hover:text-red-900 p-2 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <!-- Empty State -->
    <x-empty-state>
        <x-slot:icon>
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </x-slot:icon>
        <x-slot:title>Belum Ada Kandidat</x-slot:title>
        <x-slot:description>Mulai dengan menambahkan kandidat pertama untuk pemilihan ini. Klik tombol "Tambah
            Kandidat" di atas.</x-slot:description>
    </x-empty-state>
@endif
