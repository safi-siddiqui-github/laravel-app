<?php

use Illuminate\Support\Facades\App;

use function Livewire\Volt\{state};

$changeLocale = function () {
    App::isLocale('en') ? App::setLocale('es') : App::setLocale('en');
    session(['currentLocale' => App::currentLocale()]);
};

?>

<div class="flex flex-col gap-2">
    <p class="">App Language: {{app()->currentLocale()}}</p>

    <button wire:click="changeLocale" class="bg-black text-white text-sm px-2 py-1 rounded w-fit">Switch between en/es</button>

</div>