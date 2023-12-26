@extends('admin.layouts.main')

@section('content')
<!-- Create New Meeting Modal -->
<div class="modal fade" id="createNewMeetModal" tabindex="-1" role="dialog" aria-labelledby="createNewMeetModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createNewMeetModalLabel">Create a new meeting</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>


<link rel="stylesheet" href="{{ asset('meet_resources/quickmeet/public/css/style.css') }}">
<div class="main">
    <div class="create-join">
        <div class="text">
            <div class="head">Revolutionize your virtual meetings with a single click!</div>

        </div>
        <button id="createroom" class="createroom-butt unselectable">Create Room</button><br>
        <input type="text" name="room" spellcheck="false" placeholder="Enter Room Code" id="roomcode"
            class="roomcode"><br>
        <div class="joinroom unselectable" id="joinroom">Join Room</div>
    </div>
    <div class="video-cont">
        <video class="video-self" autoplay muted playsinline></video>
        <div class="settings">
            <div class="device" id="mic"><i class="fas fa-microphone"></i></div>
            <div class="device" id="webcam"><i class="fas fa-video"></i></div>
        </div>
    </div>
</div>

<script src="{{ asset('meet_resources/quickmeet/public/js/landing.js') }}"></script>
@endsection
