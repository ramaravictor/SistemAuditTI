<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Level;
use App\Models\ResubmissionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JawabanController extends Controller
{
    public function store(Request $request, $levelId)
    {
        // Validasi input jawaban
        $validated = $request->validate([
            'jawaban.*' => 'required|in:N,P,L,F',
        ]);

        $userId = Auth::id();

        // Menyimpan atau update jawaban ke database
        foreach ($validated['jawaban'] as $quisionerId => $jawaban) {
            Jawaban::updateOrCreate(
                [
                    'user_id' => $userId,
                    'quisioner_id' => $quisionerId,
                    'level_id' => $levelId,
                ],
                ['jawaban' => $jawaban]
            );
        }

        // --- PERBAIKAN DIMULAI DI SINI ---
        // Setelah jawaban disimpan, cari permintaan 'approved' yang PALING BARU dan konsumsi.
        $approvedRequest = ResubmissionRequest::where('user_id', $userId)
            ->where('level_id', $levelId)
            ->where('status', 'approved')
            ->latest('requested_at') // <-- Tambahkan ini untuk mengambil yang terbaru
            ->first();

        if ($approvedRequest) {
            // Ubah statusnya menjadi 'completed' agar tidak bisa digunakan lagi.
            $approvedRequest->update(['status' => 'completed']);
        }
        // --- PERBAIKAN SELESAI ---

        // Ambil level untuk redirect
        $level = Level::with(['kategori.cobitItem'])->findOrFail($levelId);

        // Redirect kembali ke halaman daftar level dengan pesan sukses.
        return redirect()->route('audit.showLevels', [
            'cobitItem' => $level->kategori->cobitItem->id,
            'kategori' => $level->kategori->id,
        ])->with('success', 'Jawaban berhasil disimpan!');
    }
}
