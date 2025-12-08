@props(['items' => [], 'light' => false])

@if(!empty($items))
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm flex-wrap">
            @foreach($items as $index => $item)
                <li class="flex items-center">
                    @if($index > 0)
                        <svg class="w-4 h-4 {{ $light ? 'text-slate-300' : 'text-gray-400' }} mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    @endif
                    @if(isset($item['url']) && $index < count($items) - 1)
                        <a href="{{ $item['url'] }}" class="{{ $light ? 'text-slate-200 hover:text-white' : 'text-gray-500 hover:text-indigo-600' }} transition">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="{{ $light ? 'text-white' : 'text-gray-900' }} font-medium" aria-current="page">
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif

