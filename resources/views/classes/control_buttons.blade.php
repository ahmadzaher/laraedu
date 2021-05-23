

<div class="d-flex flex-row">
    @can('edit-class')
    <div class="p-1">
        <a href="{{ route('class.edit', $class_id)}}" class="edit btn btn-success btn-sm">{{ __('Edit') }}</a>
    </div>
    @endcan
    @can('delete-class')
    <div class="p-1">
        <form action="{{ route('class.destroy', $class_id)}}" method="post">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this class?');" type="submit">{{ __('Delete') }}</button>
        </form>
    </div>
    @endcan
</div>
