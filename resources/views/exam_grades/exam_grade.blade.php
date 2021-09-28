@extends('layouts.app')

@section('content')
        <h2 class="mb-4">Exam Grades Table</h2>
        @can('create-exam-grade')
        <a type="button" class="btn btn-primary" href="{{ route('exam_grade.add') }}">{{ __('Add New Exam Grade') }}</a>
        <hr />
        @endcan
        <table style="width: 100%" class="table table-bordered yajra-datatable">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Point</th>
                <th>Mark From</th>
                <th>Mark Upto</th>
                <th>Note</th>
                @if(Auth::user()->can('edit-exam-grade') || Auth::user()->can('delete-exam-grade'))
                <th>Action</th>
                @endif
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
@endsection
@section('script')
    <script type="text/javascript">
        $(function () {

            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: "{{ route('exam_grade.list') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'point', name: 'point'},
                    {data: 'mark_from', name: 'mark_from'},
                    {data: 'mark_upto', name: 'mark_upto'},
                    {data: 'note', name: 'note'},
                        @if(Auth::user()->can('edit-exam-grade') || Auth::user()->can('delete-exam-grade'))
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true
                    },
                    @endif
                ]
            });

        });
    </script>
@endsection
