<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
		'api/login',
		'api/register',
		'api/get_user',
		'api/facebook/login',
		'api/google/login',
		'api/users',
		'api/account/delete',
		'api/twitter',
		'api/facebook',
		'api/linkedin',
		'api/instagram',
		'api/google',
		'api/reddit',
		'api/instagram',
		'api/pinterest',
		'api/get_boards',
		'api/get_subreddits',
    ];
}
