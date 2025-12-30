<?php

namespace App\Livewire;

use App\Models\Election;
use App\Models\ElectionExtendedLog;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;

class ExtendElection extends Component
{
    public $showModal = false;
    public $electionId;
    public $election;
    public $newEndDate;
    public $newEndTime;
    public $reason;

    public function rules()
    {
        return [
            'newEndDate' => 'required|date|after:' . now()->format('Y-m-d'),
            'newEndTime' => 'required|date_format:H:i',
            'reason' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'newEndDate.required' => 'Tanggal berakhir baru harus diisi',
            'newEndDate.date' => 'Format tanggal tidak valid',
            'newEndDate.after' => 'Tanggal berakhir harus setelah hari ini',
            'newEndTime.required' => 'Waktu berakhir harus diisi',
            'newEndTime.date_format' => 'Format waktu tidak valid (HH:MM)',
            'reason.max' => 'Alasan maksimal 500 karakter',
        ];
    }

    #[On('openExtendModal')]
    public function openModal($electionId)
    {
        $this->reset(['newEndDate', 'newEndTime', 'reason']);
        $this->resetValidation();

        $this->electionId = $electionId;
        $this->election = Election::findOrFail($electionId);

        // Set default values
        $this->newEndDate = $this->election->end_at->format('Y-m-d');
        $this->newEndTime = $this->election->end_at->format('H:i');

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['electionId', 'election', 'newEndDate', 'newEndTime', 'reason']);
        $this->resetValidation();
    }

    public function extendElection()
    {
        $this->validate();

        $newEndAt = Carbon::parse($this->newEndDate . ' ' . $this->newEndTime);

        // Validasi: waktu baru harus setelah waktu berakhir saat ini
        if ($newEndAt->lte($this->election->end_at)) {
            $this->addError('newEndDate', 'Waktu berakhir baru harus setelah waktu berakhir saat ini (' .
                $this->election->end_at->locale('id')->translatedFormat('d F Y H:i') . ')');
            return;
        }

        // Validasi: waktu baru harus setelah sekarang
        if ($newEndAt->lte(now())) {
            $this->addError('newEndDate', 'Waktu berakhir baru harus setelah waktu sekarang');
            return;
        }

        try {
            // Simpan log perpanjangan
            ElectionExtendedLog::create([
                'election_id' => $this->election->id,
                'old_end_at' => $this->election->end_at,
                'new_end_at' => $newEndAt,
                'reason' => $this->reason,
                'extended_by' => auth()->user()->id,
            ]);

            // Update waktu berakhir election
            $this->election->update([
                'end_at' => $newEndAt,
            ]);

            flash()->success('Berhasil memperpanjang waktu pemilihan');

            $this->closeModal();
            $this->dispatch('pg:eventRefresh-electionTable');
        } catch (\Exception $e) {
            flash()->error('Gagal memperpanjang waktu pemilihan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.dashboard.elections.partials.extend-election');
    }
}
