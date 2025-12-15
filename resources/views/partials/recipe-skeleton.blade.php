{{-- resources/views/partials/recipe-skeleton.blade.php --}}
{{-- Skeleton loader for recipes while loading --}}

@for($i = 0; $i < 4; $i++)
<div class="flex-shrink-0 w-90 bg-white rounded-2xl shadow-md animate-pulse">
    <div class="h-24 rounded-t-2xl bg-gray-300"></div>
    <div class="p-3 space-y-2">
        <div class="h-4 bg-gray-300 rounded w-3/4"></div>
        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
        <div class="h-3 bg-gray-200 rounded w-2/3"></div>
        <div class="flex items-center justify-between pt-2">
            <div class="h-3 bg-gray-300 rounded w-1/3"></div>
            <div class="h-3 bg-gray-300 rounded w-1/4"></div>
        </div>
    </div>
</div>
@endfor