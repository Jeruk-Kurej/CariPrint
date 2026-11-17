<?php

namespace App\Services\Auth;

use RuntimeException;

/**
 * The Google account cannot be used to sign in. The message is safe to show to the visitor.
 */
class GoogleLoginRejected extends RuntimeException {}
