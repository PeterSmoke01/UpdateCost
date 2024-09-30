$(document).ready(function() {
   $('.select2').select2();

   // $('select').on('change', function() {
   //    $(this).valid();
   //    $(this).removeClass('is-invalid').addClass('is-valid');
   // });
   
   $(".datepicker").datepicker({ 
      autoclose: true, 
      todayHighlight: true,
      format:'dd-mm-yyyy',
      language: 'th',
   }).datepicker();


   // $('#table-result-upload').DataTable();

   $(".datepicker").keypress(function(event) {
      event.preventDefault();
   });

   $(function(){
      var today = new Date();
      var minYear = today.getFullYear()-10;
      var maxYear = today.getFullYear()+2;
      // $('.combodate').combodate({
      //    minYear: minYear,
      //    maxYear: maxYear,
      //    customClass: 'form-control',
      // });    
   });

   //auto size
   autosize($('.autosize'));

   $(function () {
      $('[data-toggle="tooltip"]').tooltip();
   });

   $('.modal').on('shown.bs.modal', function () {
         var textarea = document.querySelectorAll('textarea');
         autosize(textarea);
         autosize.update(textarea);
    });

   $(window).scroll(function() {
        var window_top = $(window).scrollTop() + 1;
        if (window_top > 300) {
            $('.service-menu').addClass('sticky-action sticky');
            $('.service-menu').removeClass('card-menu');
        } else {
            $('.service-menu').removeClass('sticky-action sticky');
            $('.service-menu').addClass('card-menu');
        }
    });

   $(function() {
        $('.hide-show-login').show();
        $('.hide-show-login i').addClass('show')
        $('.hide-show-login i').click(function() {
            if ($(this).hasClass('show')) {
                $(this).removeClass('fa-eye');
                $(this).addClass('fa-eye-slash');
                $('input[name="password"]').attr('type', 'text');
                $(this).removeClass('show');
            } else {
                $(this).removeClass('fa-eye-slash');
                $(this).addClass('fa-eye');
                $('input[name="password"]').attr('type', 'password');
                $(this).addClass('show');
            }
        });
        $('form button#userLogin').on('click', function() {
            $('.hide-show-login i').addClass('show', 'fa-eye');
            $('.hide-show-login').parent().find('input[name="password"]').attr('type', 'password');
        });
    });

   $(function() {
        $('.hide-show-db').show();
        $('.hide-show-db i').addClass('show')
        $('.hide-show-db i').click(function() {
            if ($(this).hasClass('show')) {
                $(this).removeClass('fa-eye');
                $(this).addClass('fa-eye-slash');
                $('input[name="db_password"]').attr('type', 'text');
                $(this).removeClass('show');
            } else {
                $(this).removeClass('fa-eye-slash');
                $(this).addClass('fa-eye');
                $('input[name="db_password"]').attr('type', 'password');
                $(this).addClass('show');
            }
        });
        $('form button#userLogin').on('click', function() {
            $('.hide-show-db i').addClass('show', 'fa-eye');
            $('.hide-show-db').parent().find('input[name="db_password"]').attr('type', 'password');
        });
    });

   $(function() {
        $('.hide-show-repass').show();
        $('.hide-show-repass i').addClass('show')
        $('.hide-show-repass i').click(function() {
            if ($(this).hasClass('show')) {
                $(this).removeClass('fa-eye');
                $(this).addClass('fa-eye-slash');
                $('input[name="reset_password"]').attr('type', 'text');
                $(this).removeClass('show');
            } else {
                $(this).removeClass('fa-eye-slash');
                $(this).addClass('fa-eye');
                $('input[name="reset_password"]').attr('type', 'password');
                $(this).addClass('show');
            }
        });
        $('form button#changePasswordByUser').on('click', function() {
            $('.hide-show-repass i').addClass('show', 'fa-eye');
            $('.hide-show-repass').parent().find('input[name="reset_password"]').attr('type', 'password');
        });
    });

    $(function() {
        $('.hide-show-confirmpass').show();
        $('.hide-show-confirmpass i').addClass('show')
        $('.hide-show-confirmpass i').click(function() {
            if ($(this).hasClass('show')) {
                $(this).removeClass('fa-eye');
                $(this).addClass('fa-eye-slash');
                $('input[name="reset_password_confirm"]').attr('type', 'text');
                $(this).removeClass('show');
            } else {
                $(this).removeClass('fa-eye-slash');
                $(this).addClass('fa-eye');
                $('input[name="reset_password_confirm"]').attr('type', 'password');
                $(this).addClass('show');
            }
        });
        $('form button#changePasswordByUser').on('click', function() {
            $('.hide-show-confirmpass i').addClass('show', 'fa-eye');
            $('.hide-show-confirmpass').parent().find('input[name="reset_password_confirm"]').attr('type', 'password');
        });
    });

    // Reset Password
    $('#reset_password').on('keyup', function() {
        let textElement = $(this).val()
        let strength = 0

        if (textElement.length > 0) {
            let sizeElements = textElement.length

            if (sizeElements > 10) {

                strength += 30

            } else {
                let calcMath = (sizeElements * 2)

                strength += calcMath

            }

        }

        let lowerCase = new RegExp(/[a-z]/)
        if (lowerCase.test(textElement)) {
            strength += 16
        }

        let upperCase = new RegExp(/[A-Z]/)
        if (upperCase.test(textElement)) {
            strength += 18
        }

        let regularNumber = new RegExp(/[0-9]/i)
        if (regularNumber.test(textElement)) {
            strength += 16
        }

        let specialChars = new RegExp(/[!@#$&*]/i)
        if (specialChars.test(textElement)) {
            strength += 20
        }

        //============end Business rules==============
        //======Results Rendering=====================
        if (strength < 21) {
            //red very weak password
            $('#strengthResult1').html(`
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: ${strength}%" aria-valuenow="${strength}" aria-valuemin="0" aria-valuemax="100"><span style="font-size: 14px;">${strength}%</span></div>
                </div>
                <p class="text-danger" style="font-style: italic; font-size: 12px;">Very Weak</p>`)
        } else
        if (strength > 20 && strength < 41) {
            //orange weak password
            $('#strengthResult1').html(`
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: ${strength}%" aria-valuenow="${strength}" aria-valuemin="0" aria-valuemax="100"><span style="font-size: 14px;">${strength}%</span></div>
                    </div>
                    <p class="text-warning" style="font-style: italic; font-size: 12px;">Weak</p>`)
        } else
        if (strength > 40 && strength < 61) {
            //medium password
            $('#strengthResult1').html(`
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-secondary" role="progressbar" style="width: ${strength}%" aria-valuenow="${strength}" aria-valuemin="0" aria-valuemax="100"><span style="font-size: 14px;">${strength}%</span></div>
                    </div>
                    <p class="text-secondary" style="font-style: italic; font-size: 12px;">Medium </p>`)
        } else
        if (strength > 60 && strength < 81) {
            // strong password
            $('#strengthResult1').html(`
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: ${strength}%" aria-valuenow="${strength}" aria-valuemin="0" aria-valuemax="100"><span style="font-size: 14px;">${strength}%</span></div>
                    </div>
                    <p class="text-info" style="font-style: italic; font-size: 12px;">Strong</p>`)
        } else {
            //very strong password
            $('#strengthResult1').html(`
                <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: ${strength}%" aria-valuenow="${strength}" aria-valuemin="0" aria-valuemax="100"><span style="font-size: 14px;">${strength}%</span></div>
                    </div>
                    <p class="text-success" style="font-style: italic; font-size: 12px;">Very Strong </p>`)
        }
        //======Results Rendering=====================
        
        //======Hide the div containing the result====
        if (strength == 0) {
            $('#strengthResult1').addClass('showHidden')
        } else {
            $('#strengthResult1').removeClass('showHidden')
        }
    });
});

$(document)
    .one('focus.textarea', '.auto-expand', function(){
        var savedValue = this.value;
            this.value = '';
            this.baseScrollHeight = this.scrollHeight;
            this.value = savedValue;
    })
    .on('input.textarea', '.auto-expand', function(){
        var minRows = this.getAttribute('data-min-rows')|1,
            rows;
            this.rows = minRows;
            rows = Math.ceil((this.scrollHeight - this.baseScrollHeight) / 20);
            this.rows = minRows + rows;
});

//=========================================//
// Logout
//=========================================//
$(document).ready(function () {
    $('#user_logout').click(function() {
        var LOGOUT_URL = $('#logout_url').val();
        var CURRENT_URL = window.location.href.split("#")[0];

        Swal.fire({
            title: "ออกจากระบบ",
            text: "ท่านต้องการออกจากระบบใช่หรือไม่",
            icon: "info",
            showCancelButton: true,
            cancelButtonText: "ไม่ใช่",
            confirmButtonColor: "#1690ed",
            confirmButtonText: "ใช่",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = LOGOUT_URL;
            }
        });
    });

    $('#dropdown_user_logout').click(function() {
        var LOGOUT_URL = $('#logout_url').val();
        var CURRENT_URL = window.location.href.split("#")[0];
        
        Swal.fire({
            title: "ออกจากระบบ",
            text: "ท่านต้องการออกจากระบบใช่หรือไม่",
            icon: "info",
            showCancelButton: true,
            cancelButtonText: "ไม่ใช่",
            confirmButtonColor: "#1690ed",
            confirmButtonText: "ใช่",
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = LOGOUT_URL;
            }
        });
    });
});

function autoFormat(obj, typeCheck) {
    if(typeCheck == "phone"){
        var pattern = new String("__-____-____");// กำหนดรูปแบบในนี้
        var pattern_ex = new String("-");// กำหนดสัญลักษณ์หรือเครื่องหมายที่ใช้แบ่งในนี้     
    }
    else if (typeCheck == "id_card") {
        var pattern = new String("_-____-_____-_-__");
        var pattern_ex = new String("-"); 
    }
    else{
        var pattern = new String("__-____-____");
        var pattern_ex = new String("-");                 
    }

    var returnText = new String("");
    var obj_l = obj.value.length;
    var obj_l2 = obj_l - 1;

    for(i=0;i<pattern.length;i++){           
        if(obj_l2 == i && pattern.charAt(i+1) == pattern_ex){
            returnText += obj.value + pattern_ex;
            obj.value = returnText;
        }
    }

    if(obj_l >= pattern.length){
        obj.value = obj.value.substr(0, pattern.length);           
    }
}