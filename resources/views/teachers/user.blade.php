@extends('layouts.app')

@section('content')


        <h2 class="mb-4">teachers Table</h2>
        @can('create-user')
        <a type="button" class="btn btn-primary" href="{{ route('teacher.add') }}">Add new teacher</a>
        <hr />
        @endcan
        <table style="width: 100%" class="table table-bordered yajra-datatable">
            <thead>
            <tr>
                <th>No</th>
                <th>Avatar</th>
                <th>Name</th>
                <th>Department</th>
                <th>Username</th>
                <th>Phone number</th>
                <th>Email</th>
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
                ajax: "{{ route('teacher.list') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'avatar', name: 'avatar'},
                    {data: 'name', name: 'name'},
                    {data: 'department_name', name: 'department_name'},
                    {data: 'username', name: 'username'},
                    {data: 'number', name: 'number'},
                    {data: 'email', name: 'email'},
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
