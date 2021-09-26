

<div class="d-flex flex-row">
    @can('edit-teacher')
    <div class="p-1">
        <a href="{{ route('allocation.edit', $id)}}" class="edit btn btn-success btn-sm">{{ __('Edit') }}</a>
    </div>
    @endcan
    @can('delete-teacher')
    <div class="p-1">
        <form action="{{ route('allocation.destroy', $id)}}" method="post">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');" type="submit">{{ __('Delete') }}</button>
        </form>
    </div>
    @endcan
</div>
