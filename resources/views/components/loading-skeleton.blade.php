@props(['type' => 'card'])

@if($type === 'card')
    <div class="bg-white border-2 border-gray-200 rounded-xl p-8 animate-pulse">
        <div class="h-16 w-16 bg-gray-200 rounded-lg mb-6"></div>
        <div class="h-6 bg-gray-200 rounded w-3/4 mb-4"></div>
        <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
        <div class="h-4 bg-gray-200 rounded w-5/6"></div>
    </div>
@elseif($type === 'blog')
    <article class="bg-white border-2 border-gray-200 rounded-xl overflow-hidden animate-pulse">
        <div class="h-48 bg-gray-200"></div>
        <div class="p-6">
            <div class="h-4 bg-gray-200 rounded w-1/3 mb-3"></div>
            <div class="h-6 bg-gray-200 rounded w-3/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
        </div>
    </article>
@elseif($type === 'text')
    <div class="animate-pulse space-y-3">
        <div class="h-4 bg-gray-200 rounded w-full"></div>
        <div class="h-4 bg-gray-200 rounded w-5/6"></div>
        <div class="h-4 bg-gray-200 rounded w-4/5"></div>
    </div>
@elseif($type === 'button')
    <div class="h-12 bg-gray-200 rounded-lg w-32 animate-pulse"></div>
@endif

