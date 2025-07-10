@props(['title', 'value', 'icon'])

<div class="bg-white p-4 rounded shadow flex flex-col items-center">
    <div class="text-3xl mb-2">
        <!-- Replace with your icon system -->
        <span class="inline-block">[{{ $icon }}]</span>
    </div>
    <div class="text-2xl font-bold">{{ $value }}</div>
    <div class="text-gray-600">{{ $title }}</div>
</div> 