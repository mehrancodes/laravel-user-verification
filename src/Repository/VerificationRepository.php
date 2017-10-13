<?php

namespace Rasulian\UserVerification\Repository;

use Carbon\Carbon;
use Rasulian\UserVerification\Modal\Verification;

/**
 * Class VerificationRepository
 *
 * @package \Rasulian\UserVerification\Repository
 */
class VerificationRepository
{
    public function createVerificationToken($user)
    {
        $verification = $this->getVerificationByUser($user);

        if (!$verification) {
            list($token) = $this->createVerification($user);
            return $token;
        }

        list($token) = $this->regenerateVerification($user);
        return $token;
    }

    public function createVerificationCode($user)
    {
        $verification = $this->getVerificationByUser($user);

        if (!$verification) {
            list($code) = $this->createVerification($user);
            return $code;
        }

        list($code) = $this->regenerateVerification($user);
        return $code;
    }

    public function getVerificationByUser($user)
    {
        return Verification::where('user_id', $user->id)->first();
    }

    public function getVerificationByToken($token)
    {
        return Verification::where('token', $token)->first();
    }

    public function getVerificationByCode($code)
    {
        return Verification::where('code', $code)->first();
    }

    public function deleteVerification($value)
    {
        Verification::where('token', $value)->orWhere('code', $value)->delete();
    }

    /**
     * Create a new verification for the specified user
     *
     * @param $user
     * @return array
     */
    private function createVerification($user)
    {
        // Make the user unverified
        $user->verified = false;
        $user->save();

        $token = $this->getToken();
        $code = $this->getCode();

        Verification::insert([
            'user_id' => $user->id,
            'token' => $token,
            'code' => $code,
            'created_at' => new Carbon()
        ]);

        return [$token, $code];
    }

    /**
     * Update the verification specified by user
     *
     * @param $user
     * @return array
     */
    private function regenerateVerification($user)
    {
        $token = $this->getToken();
        $code = $this->getCode();

        Verification::where('user_id', $user->id)->update([
            'token' => $token,
            'code' => $code,
            'created_at' => new Carbon(),
            'updated_at' => new Carbon()
        ]);

        return [$token, $code];
    }

    private function getToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

    private function getCode()
    {
        return random_int(00000000, 99999999);
    }
}
