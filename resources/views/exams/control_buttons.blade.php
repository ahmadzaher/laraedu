

<div class="d-flex flex-row">
    @can('edit-exam')
    <div class="p-1">
        <a href="{{ route('exam.edit', $exam_id)}}" class="edit btn btn-success btn-sm">{{ __('Edit') }}</a>
    </div>
    @endcan
    @can('delete-exam')
    <div class="p-1">
        <form action="{{ route('exam.destroy', $exam_id)}}" method="post">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this exam?');" type="submit">{{ __('Delete') }}</button>
        </form>
    </div>
    @endcan
</div>
