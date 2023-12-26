@extends('admin.layouts.main')

@section('extraScripts')
    <script>
        let currentMeetingID;
        $("#changeMeetingNameForm").submit(function(e){
            e.preventDefault();
            let postData = $(this).serialize();
            postData += '&' + $.param({"meeting_id": currentMeetingID});
            $.ajax({
                type: "POST",
                url: "{{ route('meet.update.name') }}",
                data: postData,
                success: function (response) {
                    $("#EditRoomNameModal").modal("hide");
                    $.notify(response);

                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                },
                error: function (error) {
                    console.error("Error:", error);
                }
            });
        });

        $(".btn-edit-room-name").click(function() {
            const parentTr = $($(this).closest('tr')[0]);
            currentMeetingID = parentTr.data("meetingid");
            $("#meetingIDPlaceholder").html(currentMeetingID);
            $("#EditRoomNameModal").modal("show");
            $($("#EditRoomNameModal").find(".form-control")[0]).val(parentTr.data("meetingname"));
        });

        function seeMeetingParticipants() {
            fetch(
                "/api/meet/list/participants/9ae86e45-f9e0-4fbc-9687-260a00172520"
            )
            .then(response => response.json())
            .then(data => {
                renderUserTable(data);
            })
            .catch(error => console.error('Error fetching data:', error));
        }

        function renderUserTable(data) {
            const tableBody = document.getElementById('userTableBody');
            tableBody.innerHTML = "";
            data.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${item.user.name}</td>
                    <td>${item.created_at}</td>
                    <td><button class='btn btn-sm btn-success'><i class="fas fa-eye"></i></button></td>
                `;
                tableBody.appendChild(row);
            });
            $("#SeeFullListParticipants").modal("show");
        }
    </script>
@endsection

@section('content')
<!-- Edit Room Name Modal -->
<div class="modal fade" id="EditRoomNameModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Meeting: <span id="meetingIDPlaceholder"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="changeMeetingNameForm">
                    @csrf
                    @method("PUT")
                    <input class="form-control" placeholder="Enter New Room Name" name="meeting_name">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="changeMeetingNameForm" class="btn btn-primary">Update</button>
            </div> 
        </div>
    </div>
</div>


<!-- List Participants Modal -->
<div class="modal fade" id="SeeFullListParticipants" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Participants</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="bg-light">
                                    <tr class="border-0">
                                        <th class="border-0">#</th>
                                        <th class="border-0">User Name</th>
                                        <th class="border-0">Room Enter Time</th>
                                        <th class="border-0">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid  dashboard-content">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <h3 class="mb-2">List Meeting</h3>
                <p class="pageheader-text">The meeting you've just arranged.</p>
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">The meeting you've just arranged.</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
            <div class="card border-3 border-top border-top-primary">
                <div class="card-body">
                    <h5 class="text-muted">Total Meeting Arranged</h5>
                    <div class="metric-value d-inline-block">
                        <h1 class="mb-1">12099</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
            <div class="card border-3 border-top border-top-primary">
                <div class="card-body">
                    <h5 class="text-muted">Total Participants</h5>
                    <div class="metric-value d-inline-block">
                        <h1 class="mb-1">1245</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Meetings</h5>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead class="bg-light">
                        <tr class="border-0">
                            <th class="border-0">#</th>
                            <th class="border-0">Meeting ID</th>
                            <th class="border-0">Meeting Name</th>
                            <th class="border-0">Participants</th>
                            <th class="border-0">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($meetings as $index => $meet)
                        <tr data-meetingid="{{ $meet->meeting_id }}" data-meetingname="{{ $meet->meeting_name }}">
                            <td>{{ $index + 1}}</td>
                            <td>
                                {{ $meet->meeting_id }}
                            </td>
                            <td>
                                <span>{{ $meet->meeting_name }}</span>
                                <i class="btn-edit-room-name fas fa-edit"></i>
                            </td>
                            <td>{{ $meet->meetings_log_count }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary">
                                    Re-enter
                                </button>
                                <button class="btn btn-sm btn-warning">
                                    Notify
                                </button>
                                <button class="btn btn-sm btn-success" onclick="seeMeetingParticipants()">
                                    See List Participants
                                </button>
                                <button class="btn btn-sm btn-danger">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
