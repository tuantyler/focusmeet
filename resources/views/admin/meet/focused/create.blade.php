@extends('admin.layouts.main')

@section('extraScripts')
<script>
    var descriptionEditor = new Quill('#description-editor', {
        theme: 'snow'
    });

    var submitFields = {};
    $("#createButton").click(function() {
        $('.submit-field').each(function() {
            $(this).removeClass('is-invalid');
            var name = $(this).prop('name');

            switch(name) {
                case "allow_freely_join":
                case "enable_member_list_view": 
                case "enable_member_focus_view":
                case "all_days":
                    var value = $(this).is(":checked")
                    break;
                default:
                    var value = $(this).val();
                    break;
            }
   
            submitFields[name] = value;
        });

        $.ajax({
            url: '{{ route("api.meet.focused.create") }}',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(submitFields),
            success: function(response) {
                $.notify(response.message, {type: 'success'});
                setTimeout(function() {
                    window.location.href = "{{ route('meet.focused.edit', '') }}" + "/" + response.meeting_id;
                }, 1000);
            },
            error: function(error) {
                let errorResponse = error.responseJSON;
                console.log(errorResponse);
                for (var field in errorResponse.errors) {
                    if (errorResponse.errors.hasOwnProperty(field)) {
                        var errorMessage = errorResponse.errors[field][0];
                        $.notify(errorMessage, {type: 'danger'});
                        $($('input[name="' + field + '"]')[0]).addClass("is-invalid");
                    }
                }
            }
        });

    });

    $("#allDaysMeetingButton").click(function() {
        let start_time = $($('input[name="start_time"]')[0]);
        let end_time = $($('input[name="end_time"]')[0]);
        if($(this).is(":checked")) {
            $($('input[name="start_time"]')[0]).attr("type", "date");
            $($('input[name="end_time"]')[0]).attr("type", "date");
            return;
        };
        $($('input[name="start_time"]')[0]).attr("type", "datetime-local");
        $($('input[name="end_time"]')[0]).attr("type", "datetime-local");
    });  
</script>
@endsection

@section('content')
<div class="card p-2 container">
    <div class="card-header">
        <h2 class="pageheader-title">Create a focused meet</h2>
        <span>Take a first step on enhancing your conferenece experience</span>
        <hr>
        <div class="card shadow">
            <div class="card-header">
                <button class="btn btn-sm btn-light">
                    <i class="fas fa-handshake"></i>
                    Provide a title for your conference meeting.
                </button>
                
            </div>
            <div class="card-body">
                <form class="input-group flex-nowrap">
                    <input type="text" class="form-control submit-field" placeholder="Add title" style="font-size:14px;
                        width: 650px" name="meeting_name">
                </form>
              
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header">
                        Action
                    </div>
                    <div class="card-body">
                        <button class="btn btn-sm btn-success" id="createButton">
                            <i class="fas fa-plus-circle"></i>
                            Create
                        </button>
                        <hr>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header">
                        Member Settings
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input
                                class="form-check-input submit-field"
                                type="checkbox"
                                role="switch"
                                name="allow_freely_join"
                                
                            >
                            <label class="form-check-label">
                                Let members join freely, even if they are not on the list.
                            </label>
                        </div>
                        <div class="form-check form-switch">
                            <input
                                class="form-check-input submit-field"
                                type="checkbox"
                                role="switch"
                                name="enable_member_list_view"    
                            >
                            <label class="form-check-label">
                                Permit members to view the list of participants.
                            </label>
                        </div>

                        <div class="form-check form-switch">
                            <input
                                class="form-check-input submit-field"
                                type="checkbox"
                                role="switch"
                                name="enable_member_focus_view"
                            >
                            <label class="form-check-label">
                                Enable members to review the focus statuses of participants.
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header">
                        Schedule Settings
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <small class="fw-bold">Start date</small>
                                <input type="datetime-local" class="submit-field form-control" name="start_time">
                            </div>

                            <div class="col-md-5">
                                <small class="fw-bold">End date</small>
                                <input type="datetime-local" class="submit-field form-control" name="end_time">
                            </div>

                            <div class="col-md-2">
                                <small class="fw-bold">All days?</small>
                                <div class="form-check form-switch mt-2">
                                    <input
                                        class="form-check-input submit-field"
                                        type="checkbox"
                                        role="switch"
                                        name="all_days"
                                        id="allDaysMeetingButton"
                                        style="width: 100%"
                                    >
                                </div>
                            </div>
                        </div>
           
                        <hr>

                        <select class="form-control submit-field" name="repeat_pattern">
                            @foreach (config('constants.REPEAT_PATTERNS') as $key => $pattern)
                                <option value="{{ $key }}">{{ $pattern }}</option>
                            @endforeach
                        </select>

                        <hr>

                        <label class="pb-2 fw-bold">Meeting Description</label>
                        <div id="description-editor"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
