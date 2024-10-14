<?php

use Illuminate\Support\Facades\Auth;

$logout = function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    session()->flash('status', 'Logged Out');
    $this->redirectRoute('auth.login');
}

?>

<button wire:click="logout" class="hover:underline text-neutral-500">Logout</button>