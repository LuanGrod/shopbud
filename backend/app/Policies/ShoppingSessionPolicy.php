<?php

namespace App\Policies;

use App\Models\ShoppingSession;
use App\Models\User;

class ShoppingSessionPolicy
{
    public function cancel(User $user, ShoppingSession $shoppingSession): bool
    {
        return $shoppingSession->user_id === $user->id
            && $shoppingSession->status === 'active'
            && $shoppingSession->expires_at->isFuture();
    }

    public function finish(User $user, ShoppingSession $shoppingSession): bool
    {
        return $shoppingSession->user_id === $user->id
            && $shoppingSession->status === 'active'
            && $shoppingSession->expires_at->isFuture();
    }
}
