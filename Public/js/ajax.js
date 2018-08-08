$(function(){


    // 黑板报评论
    $(".comment-box").delegate("#AddComBnt","click",function() {
            var bid = $('input[name=Sbid]');
            var content = $('textarea[name=content]');
            if (content.val()=='') {
                $('#info').show();
                $('#info').html('<i class="fa fa-remove"></i> 内容不能为空!');
                setTimeout("$('#info').hide()", 2000 );
                content.focus();
                return;
            }
           $("#AddComBnt").val("正在处理...");
           $("#AddComBnt").attr("disabled", "disabled");
        $.post(AddComUrl, {bid:bid.val(), content:content.val()}, function(data){
            if (data.status) {
                $('#info').show();
                $('#info').html(data.info);
                setTimeout("$('#info').hide()", 2000 );

                $(".comment-box").load(location.href+" .comment-box");
            }else{
                $('#info').show();
                $('#info').html(data.info);
                setTimeout("$('#info').hide()", 2000 );
                $("#AddComBnt").removeAttr("disabled");
                $("#AddComBnt").val("提  交");
            };

        },'json');

    });


    // 留言
    $(".cgx-txt").delegate("#AddMsgBnt","click",function() {
            var content = $('textarea[name=content]');
            if (content.val()=='') {
                $('#info').show();
                $('#info').html('<i class="fa fa-remove"></i> 内容不能为空!');
                setTimeout("$('#info').hide()", 2000 );
                content.focus();
                return;
            }
           $("#AddMsgBnt").val("正在处理...");
           $("#AddMsgBnt").attr("disabled", "disabled");
        $.post(AddMsgUrl, {content:content.val()}, function(data){
            if (data.status) {
                $('#info').show();
                $('#info').html(data.info);
                setTimeout("$('#info').hide()", 2000 );

                $(".cgx-txt").load(location.href+" .cgx-txt");
            }else{
                $('#info').show();
                $('#info').html(data.info);
                setTimeout("$('#info').hide()", 2000 );
                $("#AddMsgBnt").removeAttr("disabled");
                $("#AddMsgBnt").val("提  交");
            };

        },'json');

    });

    //添加日志
    $(".cgx-txt").delegate("#AddLogBnt","click",function() {
            var prid = $('input[name=prid]');
            var content = $('textarea[name=content]');
            if (content.val()=='') {
                $('#info').show();
                $('#info').html('<i class="fa fa-remove"></i> 内容不能为空!');
                setTimeout("$('#info').hide()", 2000 );
                content.focus();
                return;
            }
           $("#AddMsgBnt").val("正在处理...");
           $("#AddMsgBnt").attr("disabled", "disabled");
        $.post(AddLogUrl, {prid:prid.val(),content:content.val()}, function(data){
            if (data.status) {
                $('#info').show();
                $('#info').html(data.info);
                setTimeout("$('#info').hide()", 2000 );
                $(".cgx-txt").load(location.href+" .cgx-txt");
            }else{
                $('#info').show();
                $('#info').html(data.info);
                setTimeout("$('#info').hide()", 2000 );
                $("#AddMsgBnt").removeAttr("disabled");
                $("#AddMsgBnt").val("提  交");
            };

        },'json');

    });







})