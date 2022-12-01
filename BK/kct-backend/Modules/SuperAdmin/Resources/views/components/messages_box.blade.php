@if ($errors->any())
    <div class="alert alert-dismissible alert-danger" id="errorSection">
        {{--
        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
         --}}
        <ul class="pb-0 mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@elseif(session('messages'))
    <div class="alert alert-success">
        <ul class="pb-0 mb-0">
            @foreach (session("messages") as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

@endif
