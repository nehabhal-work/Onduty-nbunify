<?php

namespace App\Services;

use App\Models\OtDuty;
use Illuminate\Support\Facades\DB;

class OtDutyService
{
    public function create(array $data): OtDuty
    {
        return DB::transaction(function () use ($data) {
            return OtDuty::create($data);
        });
    }

    public function update(OtDuty $otDuty, array $data): OtDuty
    {
        return DB::transaction(function () use ($otDuty, $data) {
            $otDuty->update($data);

            return $otDuty->refresh();
        });
    }

    public function delete(OtDuty $otDuty): void
    {
        DB::transaction(function () use ($otDuty) {
            $otDuty->delete();
        });
    }
}
