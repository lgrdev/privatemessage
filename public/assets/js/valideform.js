(function () {
  "use strict";
  var form = document.getElementById("myForm");

  form.addEventListener("keyup", function (event) {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }

    // Password validation
    let passwordInput = document.getElementById("validationPassword");
    let passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[#!=\$]).{12,32}$/;
    
    // Password validation
    if (passwordInput != null) {
      if (!passwordRegex.test(passwordInput.value)) {
        passwordInput.setCustomValidity("Password must be 12-32 characters long and contain at least one uppercase letter, one lowercase letter, and one number.");
      } else {
        passwordInput.setCustomValidity("");
      }
    }

    // repeatPassword validation
    let confirmPasswordInput = document.getElementById("repeatPassword");
    if (confirmPasswordInput != null) {
      if (passwordInput.value && confirmPasswordInput.value !== passwordInput.value) {
        confirmPasswordInput.setCustomValidity("Passwords do not match.");
      } else {
        confirmPasswordInput.setCustomValidity("");
      }
    }

    form.classList.add("was-validated");
  });

})();


  function Toggle_Password(idpassword){

    $(idpassword).parent().find('i').toggleClass('bi-eye bi-eye-slash');
    
    if(idpassword.type == "password"){
      idpassword.type = "text";
    }
    else{
      idpassword.type = "password";
    }
  }

  function Set_Form(){
    
    $(this).find('#error').addClass('invisible');

    var form = document.getElementById("myForm");
    if (!form.checkValidity()) {
      return;
    }

    try{
      let Datas = new FormData();
          Datas.append("module", $( "#module" ).val());
          Datas.append("csrf", $( "#csrf_token" ).val());
          Datas.append("login", $( "#login" ).val());
          Datas.append("password",$( "#validationPassword" ).val() );
  
      let request =
        $.ajax({
          type: "POST", 
          url: "ajax/ajax.php",
          data:Datas,
          dataType: 'json',
          timeout: 60000, // 1 Minute
          cache: false,
          contentType: false,
          processData: false,
          beforeSend: function () {
            //Code à jouer avant l'appel ajax en lui même
          }
        });
  
        request.done(function (output_success) {
            //Code à jouer en cas d'éxécution sans erreur du script du PHP
            window.location.href = "/account";
        });
        
        request.fail(function (http_error) {
          //Code à jouer en cas d'éxécution en erreur du script du PHP

           let server_msg = obj = JSON.parse(http_error.responseText);
           let code = http_error.status;
           $('#liveToastMsg').text("Erreur "+code+" : "  + server_msg.message);
           $("#liveToast").toast("show");
        });
  
        request.always(function () {
           //Code à jouer après done OU fail dans tous les cas 
        });
  
    }
    catch(e){
      $('#liveToastMsg').text(e);
      $("#liveToast").toast("show");
    }
  }