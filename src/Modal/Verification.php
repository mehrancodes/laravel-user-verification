<?php

namespace Rasulian\UserVerification\Modal;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $fillable = ['token', 'code'];

    public function __construct()
    {
        $this->table = config('verification.table_names.user_verifications');
    }

    /**
     * Every verification is associate with one user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
