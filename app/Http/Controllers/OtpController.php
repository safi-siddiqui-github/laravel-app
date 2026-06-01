<?php

namespace App\Http\Controllers;

use App\Enums\OtpType;
use App\Mail\Otp\OtpCodeMail;
use App\Models\Otp;
use App\Models\User;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Number;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    use ResponseTrait;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type' => ['required', Rule::enum(OtpType::class)],
        ]);

        $otp = Otp::where('user_id', $validated['user_id'])
            ->where('type', $validated['type'])
            ->whereNull('used_at')
            ->whereFuture('expires_at')
            ->latest()
            ->first();

        $expires_at = now()->addMinutes(5);
        $code = Str::random(6);
        $codeHash = Hash::make($code);

        if ($otp) {
            $cooldownPeriod = $otp->created_at->addMinutes(5);

            if (now()->isBefore($cooldownPeriod)) {
                $timeLeft = now()->diffForHumans($cooldownPeriod);
                throw ValidationException::withMessages([
                    'code' => "Request after $timeLeft",
                ]);
            }
        }

        if (!$otp) {
            $otp = new Otp();
            $otp->user_id = $validated['user_id'];
            $otp->expires_at = $expires_at;
            $otp->code = $codeHash;
            $otp->type = $validated['type'];
            $otp->save();
        }

        if (!$otp) {
            throw ValidationException::withMessages([
                'otp' => "Otp failed",
            ]);
        }

        Otp::where('user_id', $validated['user_id'])
            ->where('type', $validated['type'])
            ->whereNull('used_at')
            ->wherePast('expires_at')
            ->whereNotIn('id', [$otp->id])
            ->update([
                'used_at' => now()
            ]);

        Mail::to($request->input('email'))->send(new OtpCodeMail(code: $code));
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type' => ['required', Rule::enum(OtpType::class)],
            'code' => 'required|string',
        ]);

        $otp = Otp::where('user_id', $validated['user_id'])
            ->where('type', $validated['type'])
            ->where('attempts', '<', 5)
            ->whereNull('used_at')
            ->whereFuture('expires_at')
            ->latest()
            ->first();

        if (!$otp) {
            throw ValidationException::withMessages([
                'otp' => "Invalid Otp",
            ]);
        }

        if (!Hash::check($validated['code'], $otp->code)) {

            $otp->attempts = $otp->attempts + 1;
            $otp->save();

            throw ValidationException::withMessages([
                'otp' => "Invalid Otp",
            ]);
        }

        $otp->used_at = now();
        $otp->save();
    }

    public function expireAll(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type' => ['required', Rule::enum(OtpType::class)],
        ]);

        Otp::where('user_id', $validated['user_id'])
            ->where('type', $validated['type'])
            ->where('attempts', '<', 5)
            ->whereNull('used_at')
            ->whereFuture('expires_at')
            ->update([
                'used_at' => now()
            ]);
    }
}
