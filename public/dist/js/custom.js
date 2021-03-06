jQuery(document).ready(function($)
{
    var current_date = new Date();
    var timezone = -current_date.getTimezoneOffset() / 60;
    if($("#timezone")){
        $("#timezone").val(timezone);
    }
    if( $('.datetimepicker1') ){
        $(function () {
            $('.datetimepicker1').datetimepicker({
                defaultDate: null,
                inline: true,
                sideBySide: true,
                icons: {
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down"
                }
            });
        });
    }

    $('.deleteAccount').on('click', function(e)
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var dataId = $(this).attr('data-id');
        var dataProvider = $(this).attr('data-provider');
        dataProvider = dataProvider.charAt(0).toUpperCase() + dataProvider.substr(1).toLowerCase();

        $.ajax({
            url: '/account/delete/'+dataId,
            type: 'POST',
            data: {
                "id": dataId,
                "provider": dataProvider
            },
            success: function( msg ) {
                if ( msg.status === 'success' ) {
                    setInterval(function() {
                        $(".user_detalis_provider_"+dataId).html('<h3>Connect '+dataProvider+'</h3><span class="circle-gray"></span><span class="grayText ml5">offline</span>');
                        window.location.reload();
                    }, 1000);
                }else if(msg.status === "schedule"){
                    $.confirm({
                        title: 'Warning!',
                        content: 'There is a scheduled post on this social account , are you sure you want to delete it?',
                        buttons: {
                            confirm: function () {
                                $.ajax({
                                    url: '/account/delete/'+dataId,
                                    type: 'POST',
                                    data: {
                                        "id": dataId,
                                        "provider": dataProvider,
                                        "schedule": "schedule"
                                    },
                                    success: function(req) {
                                        if ( req.status === 'success' ) {
                                            setInterval(function() {
                                                $(".user_detalis_provider_"+dataId).html('<h3>Connect '+dataProvider+'</h3><span class="circle-gray"></span><span class="grayText ml5">offline</span>');
                                                window.location.reload();
                                            }, 1000);
                                        }
                                    },
                                    error: function( req_data ) {
                                        $.alert('Error!');
                                    }
                                });
                            },
                            cancel: function () {
                                $.alert('Canceled!');
                            }
                        }
                    });
                }
            },
            error: function( data ) {
                console.log(data);
            }
        });

        return false;
    });
    $('.del-post-img').on('click', function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var postID = $(this).attr('data-del-post-img-id');
        var postImgUrl = $(this).attr('data-del-post-img-url');
        $.ajax({
            url: '/image/delete/'+postID,
            type: 'POST',
            data: {
                "id": postID,
                "img_url": postImgUrl
            },
            success: function( msg ) {

                if ( msg == 'success' ) {
                    setInterval(function() {
                        window.location.reload();
                    }, 1000);
                }
            },
            error: function( data ) {
                console.log(data);
            }
        });

        return false;

    });
    $('.del-post').on('click', function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var postID = $(this).attr('data-del-post-id');
        $.ajax({
            url: '/deletePost/'+postID,
            type: 'POST',
            data: {
                "id": postID
            },
            success: function( msg ) {
                if ( msg == 'success' ) {
                    $(".post_"+postID).fadeOut();
                }else{
                    $(".post_"+postID).append("<p>Not Deleted!</p>");
                }
            },
            error: function( data ) {
                console.log(data);
            }
        });
        return false;
    });

    var block = $(".publishBlock");
    $(document).on("click", ".noconnected", function()
    {
        var noconnected = $(this).val();
        $(this).attr("class", "connected");
        $(this).attr("name", "connected[]");
        $( block ).each(function( index ) {
            $("textarea[data-post-content='"+noconnected+"']").attr("name", "postTitle[]");
            $("input[data-post-title='"+noconnected+"']").attr("name", "postContent[]");
            $("input[data-soc='"+noconnected+"']").attr("name", "token_soc[]");
            $("input[data-soc-sec='"+noconnected+"']").attr("name", "token_soc_sec[]");
            $("input[data-soc-id='"+noconnected+"']").attr("name", "prov_user_id[]");
            $("input[data-img='"+noconnected+"']").attr("name", "images[]");
            $("input[data-link='"+noconnected+"']").attr("name", "url[]");
            if( noconnected == "linkedin" || noconnected == "reddit" ){
                $("input[data-link='"+noconnected+"']").attr('required', true);
            }
            if(noconnected == "reddit"){
                $("select[data-cat='"+noconnected+"']").attr("name", "subreddits_id");
            }
            if(noconnected == "pinterest"){
                $("select[data-cat='"+noconnected+"']").attr("name", "boards_id");
            }
            $("div[data-network-name='"+noconnected+"']").css({"display":"block"});
        });
        var connected_check = $("#sharePost").find(".connected");
        if( connected_check.length == 0 ){
            $(".error-result").html('<span class="alert alert-danger text-danger"> Please choose social network </span>');
            $(".share-button").fadeOut();
        }else{
            $(".error-result").html('');
            $(".share-button").fadeIn();
        }
    });

    $(document).on("click", ".connected", function()
    {
        var connected = $(this).val();
        $(this).attr("class", "noconnected");
        $(this).attr("name", "noconnected[]");
        $( block ).each(function( index ) {
            $("textarea[data-post-content='"+connected+"']").attr("name", "asd[]");
            $("input[data-post-title='"+connected+"']").attr("name", "asd[]");
            $("input[data-soc='"+connected+"']").attr("name", "asd[]");
            $("input[data-soc-sec='"+connected+"']").attr("name", "asd[]");
            $("input[data-soc-id='"+connected+"']").attr("name", "asd[]");
            $("input[data-img='"+connected+"']").attr("name", "asd[]");
            $("input[data-link='"+connected+"']").attr("name", "asd[]");
            if( connected == "linkedin" || connected == "reddit" ){
                $("input[data-link='"+connected+"']").attr('required', false);
            }
            if(connected == "reddit"){
                $("select[data-cat='"+connected+"']").attr("name", "asd[]");
            }
            if(connected == "pinterest"){
                $("select[data-cat='"+connected+"']").attr("name", "asd[]");
            }
            $("div[data-network-name='"+connected+"']").css({"display":"none"});
        });
        var connected_check = $("#sharePost").find(".connected");
        if( connected_check.length == 0 ){
            $(".error-result").html('<span class="alert alert-danger text-danger"> Please choose social network </span>');
            $(".share-button").fadeOut();
        }
    });

    $(document).on("click", ".share-button", function()
    {
        var connected_check = $("#sharePost").find(".connected");
        if( connected_check.length == 0 ){
            $(".error-result").html('<span class="alert alert-danger text-danger"> Please choose social network </span>');
            return false;
        }
    });

    function readURL(input)
    {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#blah').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#imgInp").change(function() {
        readURL(this);
    });

    $(document).on("click", "label[id^='ablah-']", function(){
        var a = this.id.split('-')[1];
        console.log(a);
        function readURLnew(input)
        {
            if (input.files && input.files[0]) {
                var reader_new = new FileReader();
                reader_new.onload = function(e) {
                    $("#blah-"+a).attr('src', e.target.result);
                }
                reader_new.readAsDataURL(input.files[0]);
            }
        }
        $("#imgInp"+a).change(function() {
            $("#blah-"+a).show();
            readURLnew(this);
        });
    });

    $(".share_button_color").click(function(){
        $(".loader").show();
    });


    $("#ins-form").on("click", function(e){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        e.preventDefault();
        var username = $("#ins-username").val();
        var password = $("#ins-password").val();
        $.ajax({
            url: '/instagram/login/',
            type: 'GET',
            data: {
                "username": username,
                "password": password,
            },
            success: function( msg ) {
                if(msg === "ok" ){
                    location.reload();
                }else{
                    var obj = jQuery.parseJSON( msg );
                    if(obj.error_type === "bad_password" ){
                        $(".ins-error").html("<p>"+obj.message+"</p>");
                    }
                    else if(obj.error_type === "invalid_user" ){
                        $(".ins-error").html("<p>"+obj.message+"</p>");
                    }
                    else if(obj.status === "fail" ){
                        $(".ins-error").html("<p><a href='"+obj.checkpoint_url+"' target='_blank'>We Detected An Unusual Login Attempt</a></p>");
                    }
                }
            },
            error: function( data ) {
                $(".ins-error").html("<p>Error! Try again</p>");
            }
        });
        return false;
    });

    $("#ins-username, #ins-password").keypress(function(e) {
        var key = e.which;
        if (key == 13) // the enter key code
        {
            var username = $("#ins-username").val();
            var password = $("#ins-password").val();
            $.ajax({
                url: '/instagram/login/',
                type: 'GET',
                data: {
                    "username": username,
                    "password": password,
                },
                success: function( msg ) {
                    if(msg === "ok" ){
                        location.reload();
                    }else{
                        var obj = jQuery.parseJSON( msg );
                        if(obj.error_type === "bad_password" ){
                            $(".ins-error").html("<p>"+obj.message+"</p>");
                        }
                        else if(obj.error_type === "invalid_user" ){
                            $(".ins-error").html("<p>"+obj.message+"</p>");
                        }
                        else if(obj.status === "fail" ){
                            $(".ins-error").html("<p><a href='"+obj.checkpoint_url+"' target='_blank'>We Detected An Unusual Login Attempt</a></p>");
                        }
                    }
                },
                error: function( data ) {
                    $(".ins-error").html("<p>Error! Try again</p>");
                }
            });
            return false;
        }
    });

    $(document).on("click","#delete_posted",function(e)
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        e.preventDefault();
        var post_id = $("#post_id").val();
        var post = $("#post").val();
        $.ajax({
            url: '/deletePosted/'+post_id,
            type: 'POST',
            data: {
                "id": post_id,
                "post": post,
            },
            success: function( msg ) {
                if(msg === "success" ){
                    location.reload();
                }else{
                    console.log('error');
                }
            },
            error: function( data ) {
                console.log(data);
            }
        });
    });

    $(document).on("click","#delete_post",function(e)
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        e.preventDefault();
        var post_id = $("#post_id").val();
        var post = $("#post").val();
        $.ajax({
            url: '/deletePost/'+post_id,
            type: 'POST',
            data: {
                "id": post_id,
                "post": post,
            },
            success: function( msg ) {
                if(msg === "success" ){
                    location.reload();
                }else{
                    console.log('error');
                }
            },
            error: function( data ) {
                console.log(data);
            }
        });
    });

    $(document).on("click","#myBtn_schedule",function(e)
    {
        e.preventDefault();
    });

    $(document).on("click",".disable_schedule",function(e)
    {
        e.preventDefault();
        $("#myModalSchedule").css({"display":"none"});
    });

}); // end ready function


// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the modal
var modal_schedule = document.getElementById('myModalSchedule');

// Get the button that opens the modal
var btn_schedule = document.getElementById("myBtn_schedule");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];
var span_new = document.getElementsByClassName("close_schedule")[0];

// When the user clicks on the button, open the modal
if(btn)
{
    btn.onclick = function() {
        modal.style.display = "block";
    }
    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}
if(btn_schedule)
{
    btn_schedule.onclick = function() {
        modal_schedule.style.display = "block";
    }
    // When the user clicks on <span> (x), close the modal
    span_new.onclick = function() {
        modal_schedule.style.display = "none";
    }
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal_schedule) {
            modal_schedule.style.display = "none";
        }
    }
}

function PopupCenter(url, title, w, h) {
    // Fixes dual-screen position                         Most browsers      Firefox
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
}

function oldPassword() {
    var x_old = document.getElementById("old_password");
    if (x_old.type === "password") {
        x_old.type = "text";
    } else {
        x_old.type = "password";
    }
}
function newPassword() {
    var x_new = document.getElementById("new_password");
    if (x_new.type === "password") {
        x_new.type = "text";
    } else {
        x_new.type = "password";
    }
}
function confirmPassword() {
    var x_confirm = document.getElementById("confirm_password");
    if (x_confirm.type === "password") {
        x_confirm.type = "text";
    } else {
        x_confirm.type = "password";
    }
}