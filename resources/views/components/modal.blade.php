@props(['id', 'maxWidth', 'title', 'zIndex'])

@php
    $id = $id ?? md5($attributes->wire('model'));
    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl'
    ][$maxWidth ?? '2xl'];

    // z-index
    $zIndex = $zIndex ?? 9999;
@endphp

<div
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-[9999] px-4 py-6 overflow-y-auto sm:px-0"
    style="display: none;"
    >
    <div class="fixed inset-0 transform transition-all" x-on:click="show = false">
        <div class="absolute inset-0 bg-gray-200 opacity-30"></div>
    </div>

    <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto"
        x-show="show"
        x-trap.inert.noscroll="show"
    >
        <div class="px-3 py-3 bg-slate-500 text-white flex justify-between">
            <div class="text-lg font-medium font-noto_thai">{{ $title }}</div>
            <button class="focus:outline-none" @click="show = false">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="px-3 py-3 text-gray-800">
            {{ $slot }}
        </div>
    </div>
</div>

