var codeHtml = '<div class="loadingWrap">\
                    <div class="loadingContainer">\
                        <div class="circle__wrapper">\
                            <div class="circle__1"></div>\
                            <div class="circle__2"></div>\
                            <div class="circle__3"></div>\
                            <div class="circle__4"></div>\
                        </div>\
                    </div>\
                </div>';

$(document).ready(function(){
        $(document).on({
            ajaxStart: function(){
                if($('.loadingWrap').length == 0){
                    $('body').append(codeHtml);
                }
                $('.loadingWrap').css('display','block');
                $('.loadingWrap').animateCss('fadeIn');

            },
            ajaxStop: function(){
                $('.loadingWrap').remove();
            }
        });
});
