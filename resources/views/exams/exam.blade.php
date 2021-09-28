@extends('layouts.app')

@section('content')
        <h2 class="mb-4">Exams Table</h2>
        @can('create-exam')
        <a type="button" class="btn btn-primary" href="{{ route('exam.add') }}">{{ __('Add New Exam') }}</a>
        <hr />
        @endcan
        <table style="width: 100%" class="table table-bordered yajra-datatable">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>date</th>
                <th>note</th>
                @if(Auth::user()->can('edit-exam') || Auth::user()->can('delete-exam'))
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
                ajax: "{{ route('exam.list') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'date', name: 'date'},
                    {data: 'note', name: 'note'},
                        @if(Auth::user()->can('edit-exam') || Auth::user()->can('delete-exam'))
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
