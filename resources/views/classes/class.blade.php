@extends('layouts.app')

@section('content')
        <h2 class="mb-4">Class Table</h2>
        @can('create-class')
        <a type="button" class="btn btn-primary" href="{{ route('class.add') }}">{{ __('Add new class') }}</a>
        <hr />
        @endcan
        <table style="width: 100%" class="table table-bordered yajra-datatable">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Name numeric</th>
                @if(Auth::user()->can('edit-class') || Auth::user()->can('delete-class'))
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
                ajax: "{{ route('class.list') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'name_numeric', name: 'name_numeric'},
                        @if(Auth::user()->can('edit-class') || Auth::user()->can('delete-class'))
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
