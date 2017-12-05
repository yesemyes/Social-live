jQuery(document).ready(function($)
{
    $('.deleteAccount').on('click', function(e) {
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
                "id": dataId
            },
            success: function( msg ) {
                if ( msg.status === 'success' ) {
                    /*setInterval(function() {
                        window.location.reload();
                    }, 1000);*/
                    $(".user_detalis_provider_"+dataId).html('<h3>Connect '+dataProvider+'</h3><span class="circle-gray"></span><span class="grayText ml5">offline</span>');
                }
            },
            error: function( data ) {

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

    var block = $("#sharePost").find(".b2s-post-item");
    $(document).on("click", ".noconnected", function()
    {
        var noconnected = $(this).val();
        $(this).attr("class", "connected");
        $(this).attr("name", "connected[]");
        $( block ).each(function( index ) {
            $("textarea[data-post-title='"+noconnected+"']").attr("name", "postTitle[]");
            $("input[data-post-content='"+noconnected+"']").attr("name", "postContent[]");
            $("input[data-soc='"+noconnected+"']").attr("name", "token_soc[]");
            $("input[data-soc-sec='"+noconnected+"']").attr("name", "token_soc_sec[]");
            $("input[data-img='"+noconnected+"']").attr("name", "images[]");
            $("input[data-link='"+noconnected+"']").attr("name", "url[]");
            if( noconnected == "linkedin" || noconnected == "reddit" ){
                $("input[data-link='"+noconnected+"']").attr('required', true);
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
            $("textarea[data-post-title='"+connected+"']").attr("name", "asd[]");
            $("input[data-post-content='"+connected+"']").attr("name", "asd[]");
            $("input[data-soc='"+connected+"']").attr("name", "asd[]");
            $("input[data-soc-sec='"+connected+"']").attr("name", "asd[]");
            $("input[data-img='"+connected+"']").attr("name", "asd[]");
            $("input[data-link='"+connected+"']").attr("name", "asd[]");
            if( connected == "linkedin" || connected == "reddit" ){
                $("input[data-link='"+connected+"']").attr('required', false);
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

    function readURL(input) {

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
});