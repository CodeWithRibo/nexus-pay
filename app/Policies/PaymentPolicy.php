<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Payment $payment): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Payment $payment): bool
    {
    }

    public function delete(User $user, Payment $payment): bool
    {
    }

    public function restore(User $user, Payment $payment): bool
    {
    }

    public function forceDelete(User $user, Payment $payment): bool
    {
    }
}
