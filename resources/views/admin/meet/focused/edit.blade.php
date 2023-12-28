@extends('admin.layouts.main')

@section('extraScripts')
<script>
    function convertDateTimeFormat(inputDateTime) {
        const parsedDateTime = new Date(inputDateTime);
        if (isNaN(parsedDateTime.getTime())) {
            console.error('Invalid date and time format');
            return null;
        }
        const formattedDate = parsedDateTime.toISOString().split('T')[0];

        return formattedDate;
    }

    function convertDateFormat(inputDate) {
        const parsedDate = new Date(inputDate);
        if (isNaN(parsedDate.getTime())) {
            console.error('Invalid date format');
            return null;
        }

        // Set the time to the current time
        parsedDate.setHours(new Date().getHours(), new Date().getMinutes());

        // Format the date as "yyyy-MM-dd HH:mm:ss"
        const formattedDateTime = parsedDate.toISOString().slice(0, 19).replace('T', ' ');

        return formattedDateTime;
    }

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

        let start_time_val = start_time.val();
        let end_time_val = end_time.val();
        if($(this).is(":checked")) {
            $($('input[name="start_time"]')[0]).attr("type", "date");
            $($('input[name="end_time"]')[0]).attr("type", "date");
            start_time.val(convertDateTimeFormat(start_time_val));
            end_time.val(convertDateTimeFormat(end_time_val));
            return;
        };
        $($('input[name="start_time"]')[0]).attr("type", "datetime-local");
        $($('input[name="end_time"]')[0]).attr("type", "datetime-local");
        start_time.val(convertDateFormat(start_time_val));
        end_time.val(convertDateFormat(end_time_val));
    });

    @if($meeting->all_days)
        $("#allDaysMeetingButton").click();
    @endif
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
                    {{ $meeting->meeting_id }}
                </button>
                <span class="badge badge-dark"> Current meeting name: {{ $meeting->meeting_name }} </span>
            </div>
            <div class="card-body">
                <form class="input-group flex-nowrap">
                    <input type="text" class="form-control submit-field" placeholder="Edit title" style="font-size:14px;
                        width: 650px" name="meeting_name" value="{{ $meeting->meeting_name }}">
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
                        <button class="btn btn-sm btn-info" id="editButton">
                            <i class="fas fa-save"></i>
                            Save
                        </button>

                        <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseManagingMembers">
                            <i class="fas fa-user"></i>
                            Managing Members
                        </button>          
                        <hr>

                        <div class="collapse" id="collapseManagingMembers">
                            <button class="btn btn-sm btn-success mb-2">
                                <i class="fas fa-user-plus"></i>
                                Add new member
                            </button>
                            <div class="email-list-item">
                                <div class="email-list-detail">
                                    <span class="date float-right">
                                        23 Jun -
                                        <span class="badge badge-light">Tuấn Phan VQ</span>
                                    </span>
                                    <small class="fw-bold">Tuấn Phan VQ</small>
                                </div>
                                <div class="btn-group ml-auto justify-content-center d-flex align-items-center">
                                    <i class="fas fa-edit me-2"></i>
                                    <i class="far fa-trash-alt me-2"></i>
                                    <span class="badge badge-primary">Accepted</span>
                                </div>   
                            </div>
                            <div class="email-list-item">
                                <div class="email-list-detail">
                                    <span class="date float-right">
                                        23 Jun -
                                        <span class="badge badge-light"></span>
                                    </span>
                                    <small class="fw-bold">tuantyler21@gmail.com</small>
                                </div>
                                <div class="btn-group ml-auto justify-content-center d-flex align-items-center">
                                    <i class="fas fa-edit me-2"></i>
                                    <i class="far fa-trash-alt me-2"></i>
                                    <span class="badge badge-light">Pending</span>
                                </div>   
                            </div>
                
                        </div>
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
                                <input type="datetime-local" class="submit-field form-control" name="start_time" value="{{ $meeting->start_time }}">
                            </div>

                            <div class="col-md-5">
                                <small class="fw-bold">End date</small>
                                <input type="datetime-local" class="submit-field form-control" name="end_time" value="{{ $meeting->end_time }}">
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
