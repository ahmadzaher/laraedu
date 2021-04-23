@extends('layouts.app')

@section('content')
        <h2 class="mb-4">Rolls Table</h2>
        @can('create-role')
        <a type="button" class="btn btn-primary" href="{{ route('role.add') }}">{{ __('Add new role') }}</a>
        <hr />
        @endcan
        <table style="width: 100%" class="table table-bordered yajra-datatable">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Slug</th>
                @if(Auth::user()->can('edit-role') || Auth::user()->can('delete-role'))
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
                ajax: "{{ route('role.list') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'slug', name: 'slug'},
                        @if(Auth::user()->can('edit-role') || Auth::user()->can('delete-role'))
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
