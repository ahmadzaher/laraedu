

<div class="d-flex flex-row">
    @can('edit-section')
    <div class="p-1">
        <a href="{{ route('section.edit', $section_id)}}" class="edit btn btn-success btn-sm">{{ __('Edit') }}</a>
    </div>
    @endcan
    @can('delete-section')
    <div class="p-1">
        <form action="{{ route('section.destroy', $section_id)}}" method="post">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this section?');" type="submit">{{ __('Delete') }}</button>
        </form>
    </div>
    @endcan
</div>
