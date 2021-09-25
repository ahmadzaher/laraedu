@extends('layouts.app')

@section('content')
        <h2 class="mb-4">Subjects Table</h2>
        @can('create-subject')
        <a type="button" class="btn btn-primary" href="{{ route('subject.add') }}">{{ __('Add new subject') }}</a>
        <hr />
        @endcan
        <table style="width: 100%" class="table table-bordered yajra-datatable">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Code</th>
                <th>Author</th>
                <th>Type</th>
                @if(Auth::user()->can('edit-subject') || Auth::user()->can('delete-subject'))
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
                ajax: "{{ route('subject.list') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'code', name: 'code'},
                    {data: 'author', name: 'author'},
                    {data: 'type', name: 'type'},
                        @if(Auth::user()->can('edit-subject') || Auth::user()->can('delete-subject'))
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
