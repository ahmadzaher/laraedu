@extends('layouts.app')

@section('content')


        <h2 class="mb-4">Students Table</h2>
        @can('create-user')
        <a type="button" class="btn btn-primary" href="{{ route('student.add') }}">Add new student</a>
        <hr />
        @endcan
        <table style="width: 100%" class="table table-bordered yajra-datatable">
            <thead>
            <tr>
                <th>No</th>
                <th>Avatar</th>
                <th>Name</th>
                <th>Class</th>
                <th>Section</th>
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
                ajax: "{{ route('student.list') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'avatar', name: 'avatar'},
                    {data: 'name', name: 'name'},
                    {data: 'class_name', name: 'class_name'},
                    {data: 'section_name', name: 'section_name'},
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
                ],
                buttons: [
                    {
                        extend: "excel",                    // Extend the excel button
                        excelStyles: {                      // Add an excelStyles definition
                            cells: "2",                     // to row 2
                            style: {                        // The style block
                                font: {                     // Style the font
                                    name: "Arial",          // Font name
                                    size: "14",             // Font size
                                    color: "FFFFFF",        // Font Color
                                    b: false,               // Remove bolding from header row
                                },
                                fill: {                     // Style the cell fill (background)
                                    pattern: {              // Type of fill (pattern or gradient)
                                        color: "457B9D",    // Fill color
                                    }
                                }
                            }
                        },
                    },
                ],
            });

        })
    </script>
@endsection
