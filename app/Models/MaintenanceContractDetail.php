<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class   MaintenanceContractDetail extends Model
{
    use HasFactory;

    // تعديل fillable لإضافة حقل status
    protected $fillable = [
        'installation_contract_id',
        'maintenance_contract_id',
        'maintenance_type',
        'client_id',
        'user_id',
        'start_date',
        'end_date',
        'visits_count',
        'cost',
        'paid_amount',
        'notes',
        'remaining_visits',
        'cancellation_allowance',
        'payment_status',
        'receipt_attachment',
        'contract_attachment',
        'cancellation_attachment',
        'cancellation_note',
        'status'
    ];

    // تعريف الثوابت لحالات العقد
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';

    // Boot method لتسجيل الـ events
    protected static function boot()
    {
        parent::boot();

        // تشغيل الدالة قبل كل عملية حفظ
        static::saving(function ($contract) {
            $contract->checkAndUpdateStatus();
        });
    }

    // دالة فحص وتحديث حالة العقد
    public function checkAndUpdateStatus()
    {
        $today = Carbon::now();
        $endDate = Carbon::parse($this->end_date);

        if ($endDate->lt($today) && $this->remaining_visits == 0) {
            $this->status = self::STATUS_EXPIRED;

            // تسجيل العملية في السجلات
            $this->logs()->create([
                'action' => 'contract_expired',
                'description' => 'تم تحديث حالة العقد إلى منتهي تلقائياً'
            ]);

            return true;
        }

        return false;
    }

    // دالة للتحقق من حالة انتهاء العقد
    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    // Relations...
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function visits()
    {
        return $this->hasMany(MaintenanceVisit::class);
    }

    public function logs()
    {
        return $this->morphMany(GeneralLog::class, 'loggable');
    }

    public function getExpiredContracts()
    {
        $today = Carbon::today();
        return $this->where('end_date', '<', $today)->where('remaining_visits', '<', 1)->get();
    }

    // contract
    public function contract()
    {
        return $this->belongsTo(MaintenanceContract::class, 'maintenance_contract_id');
    }

    // reports
    public function reports()
    {
        return $this->hasMany(MaintenanceReport::class, 'maintenance_contract_details_id');
    }


    // get last active report
    public function lastReport()
    {
        return $this->reports()
            ->where('status', '!=', 'approved')
            ->orderBy('created_at', 'desc');
    }


    public function getCompletedVisitsCountAttribute()
    {
        return $this->visits()->where('status', 'completed')->count() ?? 0;
    }
}