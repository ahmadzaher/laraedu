@extends('layouts.app')

@section('content')


    <h2 class="mb-4">Assign Class Teacher</h2>
    @can('create-user')
        <a type="button" class="btn btn-primary" href="{{ route('allocation.add') }}">Assign new class teacher</a>
        <hr />
    @endcan
    <table style="width: 100%" class="table table-bordered yajra-datatable">
        <thead>
        <tr>
            <th>No</th>
            <th>Class Teacher</th>
            <th>Class</th>
            <th>Section</th>
            @if(Auth::user()->can('edit-user') || Auth::user()->can('delete-user'))
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
                ajax: "{{ route('allocation.list') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'class_name', name: 'class_name'},
                    {data: 'section_name', name: 'section_name'},
                        @if(Auth::user()->can('edit-user') || Auth::user()->can('delete-user'))
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true
                    },
                    @endif
                ]
            });

        })
    </script>
@endsection
