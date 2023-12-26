<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>QuickMeet Room</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo e(asset('meet_resources/quickmeet/public/css/style.css')); ?>">
    <script src="https://kit.fontawesome.com/6510466b6c.js" crossorigin="anonymous"></script>
    <script>
        const username = "<?php echo e(auth()->user()->name); ?>";
        const savePath = "User_<?php echo e(auth()->id()); ?>";
        const params = new URLSearchParams(location.search);
        if (!params.get("room")) location.href = "/";
    </script>
</head>

<body>
    
    <canvas id="canvasElement" style="display: none;"></canvas>

    <div class="overlay" id="overlay">
        <div class="box">
            <div class="head-name">Enter a Name</div>
            <input type="text" class="name-field" placeholder="Type here.." id="name-field"></input><br>
            <button class="continue-name">Continue</button>

        </div>
    </div>
    <div class="container-room">
        <div class="left-cont">

            <div class="video-cont-single" id="vcont">
                <div class="video-box">
                    <video class="video-frame" id="vd1" autoplay playsinline>
                    </video>
                    <div class="nametag" id="myname">yourname</div>
                    <div class="mute-icon" id="mymuteicon"><i class="fas fa-microphone-slash"></i></div>
                    <div class="video-off" id="myvideooff">Video Off</div>
                </div>
            </div>

            <div class="whiteboard-cont"><canvas id="whiteboard" height="1000" width="1000"></canvas>
                <div class="colors-cont">
                    <div class="black" onclick="setColor('black')"></div>
                    <div class="red" onclick="setColor('#e74c3c')"></div>
                    <div class="yellow" onclick="setColor('#f1c40f')"></div>
                    <div class="green" onclick="setColor('#badc58')"></div>
                    <div class="blue" onclick="setColor('#3498db')"></div>
                    <div class="orange" onclick="setColor('#e67e22')"></div>
                    <div class="purple" onclick="setColor('#9b59b6')"></div>
                    <div class="pink" onclick="setColor('#fd79a8')"></div>
                    <div class="brown" onclick="setColor('#834c32')"></div>
                    <div class="grey" onclick="setColor('gray')"></div>
                    <div class="eraser" onclick="setEraser()"><i class="fas fa-eraser"></i></div>
                    <div class="clearboard" onclick="clearBoard()"><i class="fas fa-trash-alt"></i></div>
                </div>
            </div>

            <div class="footer">
                <div class="utils">

                    <div class="audio">
                        <i class="fas fa-microphone"></i>
                    </div>
                    <div class="novideo">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="screenshare tooltip">
                        <i class="fas fa-desktop"></i>
                        <span class="tooltiptext">Share Screen</span>
                    </div>
                    <div class="board-icon tooltip">
                        <i class="fas fa-chalkboard"></i>
                        <span class="tooltiptext">Whiteboard</span>
                    </div>
                    <div class="cutcall tooltip">
                        <i class="fas fa-phone-slash"></i>
                        <span class="tooltiptext">Leave Call</span>
                    </div>


                </div>
                <div class="copycode-cont">
                    <div class="roomcode"></div>
                    <button class="copycode-button" onclick="CopyClassText()">Copy Code</button>
                </div>
            </div>


        </div>

        <div class="right-cont">
            <div class="head-title">

                <div class="chats"><i class="fas fa-comment-alt mr-1"></i>Chats</div>
                <div class="attendies"><i class="fas fa-users mr-1"></i>Attendies</div>


            </div>

            <div class="chat-cont">

            </div>
            <div class="chat-input-cont">
                <div class="ci-cont"><input type="text" class="chat-input" placeholder="Type chat here.."></div>
                <div class="ci-send"><button class="chat-send">Send</button></div>
            </div>
        </div>
    </div>

    <script src="<?php echo e(asset('meet_resources/quickmeet/node_modules/socket.io-client/dist/socket.io.js')); ?>"></script>
    <script src="<?php echo e(asset('meet_resources/quickmeet/public/js/room.js')); ?>"></script>

</body>

</html>
<?php /**PATH /var/www/html/resources/views/admin/meetrooms/index.blade.php ENDPATH**/ ?>