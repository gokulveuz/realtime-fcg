@if (count($attribute) > 0)
    <ul>
        @foreach ($attribute as $attr)
            <li>{{ $attr->name ?? "" }}</li>
        @endforeach
    </ul>
@else
    <div>
        <button onclick="createNew({{ $search_value }})">Create {{ $search_value }} </button>
    </div>
@endif
