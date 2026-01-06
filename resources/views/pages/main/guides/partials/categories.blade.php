<div class="categories-menu">
    <ul class="categories-group">
        @php $title = "title_" .app()->getLocale(); @endphp
        @foreach(getguidecategories() as $guidecategory)
            <li><a class="@if(request()->query('category_id') == $guidecategory->id) active @endif" href="{{ route('guides') }}?category_id={{ $guidecategory->id }}">{{ $guidecategory->$title }}</a></li>
        @endforeach
    </ul>
</div>