<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasCompanyScope;

    // Heures journalières normales (base légale Maroc = 44h/semaine ÷ 5.5j)
    private const NORMAL_HOURS_PER_DAY = 8.0;

    protected $fillable = [
        'company_id',
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'worked_hours',
        'overtime_hours',
    ];

    protected $casts = [
        'date'           => 'date',
        'worked_hours'   => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if ($model->check_in && $model->check_out) {
                $in  = Carbon::parse($model->check_in);
                $out = Carbon::parse($model->check_out);

                // Gérer le cas nuit (ex: 22h → 06h)
                if ($out->lt($in)) {
                    $out->addDay();
                }

                $totalHours           = round($in->floatDiffInHours($out), 2);
                $model->worked_hours  = $totalHours;
                $model->overtime_hours = max(0, round($totalHours - self::NORMAL_HOURS_PER_DAY, 2));
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
