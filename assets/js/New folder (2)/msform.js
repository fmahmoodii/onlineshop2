$(document).ready(function () {
    $('.registration-form fieldset:first-child').fadeIn('slow');

    $('.registration-form .requair').on('focus', function () {
        $(this).removeClass('input-error');
    });
    // next step
    $('.registration-form .btn-next').on('click', function () {
        var parent_fieldset = $(this).parents('fieldset');
        var next_step = true;
        parent_fieldset.find('.requair').each(function () {
            if ($(this).val() == "") {
                $(this).addClass('input-error');
                next_step = false;
            }
            else if($(this).attr('id')=="area"){
                if(parseInt($(this).val())){return true;}
                else{
                    $(this).addClass('input-error');
                    next_step = false;
                }
            }else if($(this).attr('id')=="account_number"){
                if(parseInt($(this).val())){return true;}
                else{
                    $(this).addClass('input-error');
                    next_step = false;
                }
            }else if($(this).attr('id')=="account_number_owner"){
                if(parseInt($(this).val())){
                    $(this).addClass('input-error');
                    next_step = false;
                }
            }
            else if($(this).attr('id')=="weekend_price"){
                if($(this).val()!="0"){
                    if(parseInt($(this).val())){return true;}
                    else{
                        $(this).addClass('input-error');
                        next_step = false;
                    }
                }
            }
            else if($(this).attr('id')=="p_per_holyday"){
                if($(this).val()!="0"){
                    if(parseInt($(this).val())){return true;}
                    else{
                        $(this).addClass('input-error');
                        next_step = false;
                    }
                }
            }
            else if($(this).attr('id')=="p_per_nourooz"){
                if($(this).val()!="0"){
                    if(parseInt($(this).val())){return true;}
                    else{
                        $(this).addClass('input-error');
                        next_step = false;
                    }
                }
            }
            else if($(this).attr('id')=="price_night"){
                if($(this).val()!="0"){
                    if(parseInt($(this).val())){return true;}
                    else{
                        $(this).addClass('input-error');
                        next_step = false;
                    }
                }
            }
            else if($(this).attr('id')=="p_per_person"){
                if($(this).val()!="0"){
                    if(parseInt($(this).val())){return true;}
                    else{
                        $(this).addClass('input-error');
                        next_step = false;
                    }
                }
            }

            else {
                $(this).removeClass('input-error');
            }
        });
        /*if(!($( "li" ).hasClass( "select2-selection__choice" ))){
          
            toastr["warning"]("امکانات اقامتگاه را انتخاب کنید");
            next_step = false;}*/
        if($(this).attr("id")=="nxtbtn"){
            parent_fieldset.find('div.upimg').each(function () {
                var cat_array=[];
                $(".upimg").each(function() {cat_array.push(this.id);});
                $('#pic').val(cat_array);
            });
            var child3 = parent_fieldset.find('div.upimg');
            if(child3.length < 1) {
              
                toastr["warning"]("حداقل یک عکس از واحد اقامتی خود را باید اپلود کنید");
                next_step = false;
            }
            parent_fieldset.find('a.cpi').each(function () {
                var cat_array="";
                $(".selectedimg").each(function() {cat_array=this.id; });
                $('#cover_pic').val(cat_array);
            });
        }
        if (next_step) {
            parent_fieldset.fadeOut(400, function () {
                $(this).next().fadeIn();
            });
        }

    });
    // previous step
    $('.registration-form .btn-previous').on('click', function () {
        $(this).parents('fieldset').fadeOut(400, function () {
            $(this).prev().fadeIn();
        });
    });

    $('.registration-form .submit-btn').on('click', function () {
        var parent_fieldset = $(this).parents('fieldset');
        var next_step = true;
        parent_fieldset.find('.requair').each(function () {
            if ($(this).val() == "") {
                $(this).addClass('input-error');
                next_step = false;
            }
        });
        if (next_step) {
            var numItems = $('.input-error').length;
            if(numItems==0){
                $("#addlisting").submit();
            }
        }
    });
    // submit
    // $('.registration-form').on('submit', function (e) {

    // });
});