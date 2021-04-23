@extends('layouts.app')

@section('content')


        <h2 class="mb-4">Users Table</h2>
        @can('create-user')
        <a type="button" class="btn btn-primary" href="{{ route('user.add') }}">Add new user</a>
        <hr />
        @endcan
        <table style="width: 100%" class="table table-bordered yajra-datatable">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
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
                ajax: "{{ route('user.list') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
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
