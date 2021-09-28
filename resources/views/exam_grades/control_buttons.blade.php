

<div class="d-flex flex-row">
    @can('edit-exam-grade')
    <div class="p-1">
        <a href="{{ route('exam_grade.edit', $exam_grade_id)}}" class="edit btn btn-success btn-sm">{{ __('Edit') }}</a>
    </div>
    @endcan
    @can('delete-exam-grade')
    <div class="p-1">
        <form action="{{ route('exam_grade.destroy', $exam_grade_id)}}" method="post">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this exam_grade?');" type="submit">{{ __('Delete') }}</button>
        </form>
    </div>
    @endcan
</div>
