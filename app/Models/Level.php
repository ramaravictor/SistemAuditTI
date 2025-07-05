<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $table = 'levels';

    protected $fillable = ['nama_level', 'kategori_id', 'level_number'];

    // --- RELASI YANG DITAMBAHKAN ---

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function quisioners()
    {
        return $this->hasMany(Quisioner::class);
    }

    public function jawabans()
    {
        return $this->hasMany(Jawaban::class);
    }

    public function resubmissionRequests()
    {
        return $this->hasMany(ResubmissionRequest::class);
    }

    // --- HELPER METHOD (SUDAH BENAR) ---

    public function isCompletedByUser($user)
    {
        if (!$user) return false;
        $answeredCount = $this->jawabans()->where('user_id', $user->id)->count();
        return $answeredCount > 0 && $answeredCount >= $this->quisioners()->count();
    }

    public function isFullyAchievedByUser($user)
    {
        if (!$user || !$this->isCompletedByUser($user)) {
            return false;
        }
        $fullyAchievedCount = $this->jawabans()->where('user_id', $user->id)->where('jawaban', 'F')->count();
        return $fullyAchievedCount === $this->quisioners()->count();
    }

    public function hasActiveResubmissionRequest($user)
    {
        if (!$user) return false;
        return $this->resubmissionRequests()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();
    }

    public function isApprovedForResubmission($user)
    {
        if (!$user) return false;
        return $this->resubmissionRequests()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->exists();
    }
}
