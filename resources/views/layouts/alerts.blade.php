@if(session()->get('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
    <hr />
@elseif(session()->get('warning'))
    <div class="alert alert-warning">
        {{ session()->get('warning') }}
    </div
    <hr />
@endif
