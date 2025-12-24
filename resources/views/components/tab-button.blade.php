@props([
    'name' => '',
    'label' => '',
    'icon' => null,
    'active' => false,
])

<button type="button" @click="activeTab = '{{ $name }}'"
    :class="{
        'bg-blue-50 border-blue-500 text-blue-700': activeTab === '{{ $name }}',
        'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50': activeTab !== '{{ $name }}'
    }"
    class="group inline-flex items-center gap-2 py-3 px-4 border-b-3 font-medium text-sm transition-all duration-200 rounded-t-lg"
    {{ $attributes }}>
    @if ($icon)
        <span
            :class="{
                'text-blue-600': activeTab === '{{ $name }}',
                'text-gray-400 group-hover:text-gray-600': activeTab !== '{{ $name }}'
            }"
            class="transition-colors duration-200">
            {!! $icon !!}
        </span>
    @endif
    <span class="font-semibold">{{ $label }}</span>
</button>
