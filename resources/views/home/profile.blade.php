<x-layout>
    <div class="flex flex-col gap-5 p-5">

        <h2 class="text-lg">Profile</h2>

        <div class="flex flex-col gap-2">
            <div class="flex gap-2 items-center">
                <p class="font-medium">Name:</p>
                <p class="">{{auth()->user()->name}}</p>
            </div>

            <div class="flex gap-2 items-center">
                <p class="font-medium">Username:</p>
                <p class="">{{auth()->user()->username}}</p>
            </div>

            <div class="flex gap-2 items-center">
                <p class="font-medium">Email:</p>
                <p class="">{{auth()->user()->email}}</p>
            </div>

        </div>

        <livewire:profile.avatar />

    </div>

</x-layout>