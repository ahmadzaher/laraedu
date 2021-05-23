@extends('layouts.app')

@section('content')
        <h2 class="mb-4">Section Table</h2>
        @can('create-section')
        <a type="button" class="btn btn-primary" href="{{ route('section.add') }}">{{ __('Add new section') }}</a>
        <hr />
        @endcan
        <table style="width: 100%" class="table table-bordered yajra-datatable">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Capacity</th>
                @if(Auth::user()->can('edit-section') || Auth::user()->can('delete-section'))
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
                ajax: "{{ route('section.list') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'capacity', name: 'capacity'},
                        @if(Auth::user()->can('edit-section') || Auth::user()->can('delete-section'))
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
