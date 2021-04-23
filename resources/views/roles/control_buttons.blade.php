

<div class="d-flex flex-row">
    @can('edit-role')
    <div class="p-1">
        <a href="{{ route('role.edit', $role_id)}}" class="edit btn btn-success btn-sm">{{ __('Edit') }}</a>
    </div>
    @endcan
    @can('delete-role')
    <div class="p-1">
        <form action="{{ route('role.destroy', $role_id)}}" method="post">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this role?');" type="submit">{{ __('Delete') }}</button>
        </form>
    </div>
    @endcan
</div>
