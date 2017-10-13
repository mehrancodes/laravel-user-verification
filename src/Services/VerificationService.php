<?php

namespace Rasulian\UserVerification\Services;

use App\User;
use Rasulian\UserVerification\Exceptions\VerifyCodeMismatchException;
use Rasulian\UserVerification\Exceptions\VerifyTokenMismatchException;
use Rasulian\UserVerification\Exceptions\UserIsVerifiedException;
use Rasulian\UserVerification\Repository\VerificationRepository;

/**
 * Class VerificationService
 *
 * @package \Rasulian\UserVerification\Services
 */
class VerificationService
{
    protected $verifyRepo;

    public function __construct(VerificationRepository $verifyRepo)
    {
        $this->verifyRepo = $verifyRepo;
    }

    /**
     * Get the verification token to be sent by email
     *
     * @param $user
     * @throws UserIsVerifiedException
     * @return string
     */
    public function getVerificationToken($user)
    {
        if ($user->verified || !$this->shouldSend($user)) {
            throw new UserIsVerifiedException;
        }

        $token = $this->verifyRepo->createVerificationToken($user);
        return $token;
    }

    /**
     * Get the verification code to be sent by SMS
     *
     * @param $user
     * @throws UserIsVerifiedException
     * @return int
     */
    public function getVerificationCode($user)
    {
        if ($user->verified || !$this->shouldSend($user)) {
            throw new UserIsVerifiedException;
        }

        $code = $this->verifyRepo->createVerificationCode($user);
        return $code;
    }

    /**
     * Verify the user by token
     *
     * @param $token
     * @throws VerifyTokenMismatchException
     * @return \App\User
     */
    public function verifyUserByToken($token)
    {
        $activation = $this->verifyRepo->getVerificationByToken($token);

        if ($activation === null) {
            throw new VerifyTokenMismatchException;
        }
        $user = User::find($activation->user_id)->update(['verified' => true]);
        $this->verifyRepo->deleteVerification($token);

        return $user;
    }

    /**
     * Verify the user by code
     *
     * @param $code
     * @throws VerifyCodeMismatchException
     * @return \App\User
     */
    public function verifyUserByCode($code)
    {
        $activation = $this->verifyRepo->getVerificationByCode($code);

        if ($activation === null) {
            throw new VerifyCodeMismatchException;
        }

        $user = User::find($activation->user_id)->update(['verified' => true]);
        $this->verifyRepo->deleteVerification($code);

        return $user;
    }

    /**
     * Check if any activation code/token has been created in the specified time for the specified user
     *
     * @param $user
     * @return bool
     */
    private function shouldSend($user)
    {
        $verification = $this->verifyRepo->getVerificationByUser($user);
        $generate_after = config('verification.generate_after');

        return $verification === null || strtotime($verification->created_at) + 60 * 60 * $generate_after < time();
    }
}
