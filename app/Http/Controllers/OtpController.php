<?php

namespace App\Http\Controllers;

use App\Enums\Otp\OtpTypeEnum;
use App\Mail\Otp\OtpCodeMail;
use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    public function store(): Otp
    {
        request()->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type' => ['required', Rule::enum(OtpTypeEnum::class)],
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ]);

        $user_id = request()->input('user_id');
        $type = request()->input('type');
        $email = request()->input('email');

        $otp = Otp::where('user_id', $user_id)
            ->where('type', $type)
            // ->whereNull('used_at')
            // ->whereFuture('expires_at')
            ->latest()
            ->first();

        if ($otp) {
            $cooldownPeriod = $otp->created_at->addMinutes(5);

            if (now()->isBefore($cooldownPeriod)) {
                $timeLeft = now()->diffForHumans($cooldownPeriod);
                throw ValidationException::withMessages([
                    'code' => "Request after $timeLeft",
                ]);
            }
        }

        $code = Str::random(6);
        $codeHash = Hash::make($code);

        $otp = new Otp();
        $otp->user_id = $user_id;
        $otp->expires_at = now()->addMinutes(5);
        $otp->code = $codeHash;
        $otp->type = $type;
        $otp->save();

        if (!$otp) {
            throw ValidationException::withMessages([
                'otp' => "Otp failed",
            ]);
        }

        Mail::to($email)->send(new OtpCodeMail(code: $code));

        return $otp;
    }

    public function verify(): Otp
    {
        request()->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type' => ['required', Rule::enum(OtpTypeEnum::class)],
            'code' => 'required|string',
        ]);


        $user_id = request()->input('user_id');
        $type = request()->input('type');
        $code = request()->input('code');

        $otp = Otp::where('user_id', $user_id)
            ->where('type', $type)
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

        $otp->attempts = $otp->attempts + 1;
        $otp->save();

        if (!Hash::check($code, $otp->code)) {

            throw ValidationException::withMessages([
                'otp' => "Invalid Otp",
            ]);
        }

        $otp->used_at = now();
        $otp->save();

        return $otp;
    }

    public function expireAll()
    {
        request()->validate([
            'otp_id' => 'sometimes|integer|exists:otps,id',
            'user_id' => 'required|integer|exists:users,id',
            'type' => ['required', Rule::enum(OtpTypeEnum::class)],
        ]);

        $user_id = request()->input('user_id');
        $type = request()->input('type');

        Otp::where('user_id', $user_id)
            ->where('type', $type)
            ->whereNotIn('id', [request()->input('otp_id')])
            // ->where('attempts', '<', 5)
            // ->whereFuture('expires_at')
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
                'expires_at' => now(),
            ]);
    }
}
