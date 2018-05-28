$.fn.extend({
  animateCss: function(animationName, callback) {
    var animationEnd = (function(el) {
      var animations = {
        animation: 'animationend',
        OAnimation: 'oAnimationEnd',
        MozAnimation: 'mozAnimationEnd',
        WebkitAnimation: 'webkitAnimationEnd',
      };

      for (var t in animations) {
        if (el.style[t] !== undefined) {
          return animations[t];
        }
      }
    })(document.createElement('div'));

    this.addClass('animated ' + animationName).one(animationEnd, function() {
      $(this).removeClass('animated ' + animationName);

      if (typeof callback === 'function') callback();
    });

    return this;
  },
});

$(document).ready(function(){
    updateSelects();
});

function updateSelects(){
    $('select:not(.select>select)').each(function(){
        var $this = $(this), numberOfOptions = $(this).children('option').length;

        $this.addClass('select-hidden');
        $this.wrap('<div class="select"></div>');
        $this.after('<div class="select-styled"></div>');

        var $styledSelect = $this.next('div.select-styled');
        $styledSelect.text($this.children('option').eq(0).text());

        var $list = $('<ul />', {
            'class': 'select-options'
        }).insertAfter($styledSelect);

        for (var i = 0; i < numberOfOptions; i++) {
            $('<li />', {
                text: $this.children('option').eq(i).text(),
                rel: $this.children('option').eq(i).val()
            }).appendTo($list);
        }

        var $listItems = $list.children('li');

        $styledSelect.click(function(e) {
            e.stopPropagation();
            $('div.select-styled.active').not(this).each(function(){
                $(this).removeClass('active').next('ul.select-options').hide();
            });
            $(this).toggleClass('active').next('ul.select-options').toggle();
        });

        $listItems.click(function(e) {
            e.stopPropagation();
            $styledSelect.text($(this).text()).removeClass('active');
            $this.val($(this).attr('rel'));
            $list.hide();
        });

        $(document).click(function() {
            $styledSelect.removeClass('active');
            $list.hide();
        });

    });
}

function focusIn(element){
    let id = element.id;
    $("label[data-to='"+id+"']").addClass("active").css("color","#26a69a");
}

function openPanel(){
    $(".addContainer").css("display","block");
    $(".addPanel").css("display","block");
    $(".addPanel").animateCss("zoomIn");
}

function focusOut(element){
    let id = element.id;
    if($(element.target).val() == ""){
        $("label[data-to='"+id+"']").removeClass("active");
    }
    $("label[data-to='"+id+"']").css("color","#9e9e9e");
}
function updateMaterial(){
    var iterate = function(){
        if($(this).val() != ""){
            focusIn(this);
        } else {
            focusOut(this);
        }
    };
    $(".input-field>input[type=text]").each(iterate);
    $(".input-field>input[type=number]").each(iterate);
    $(".input-field>input[type=email]").each(iterate);
}

$('.input-field').on('focusin','input',function(element){
    focusIn(element.target);
});

$('.input-field').on('focusout','input',function(element){
    focusOut(element.target);
});

$(".newElement").click(function(){
    openPanel();
});

function clearForm(){
    $('input[type=number]').val('');
    $('input[type=text]').val('');
    $('select').prop('selectedIndex', 0);
    $('.select-options').each(function(index,element){
    	var msg = $(element).children().html();
		$(element).parent().children('.select-styled').html(msg);
    });
}

$("#clearForm").click(function(){
    clearForm();
});

function closePanel(){
    $(".addPanel").animateCss("zoomOut",function(){
        $(".addPanel").css("display","none");
        $(".addContainer").css("display","none");
    });
}

$(".closePanel").click(function(){
    closePanel();
});
