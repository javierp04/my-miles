$(function () {
    $("#frmSponsor").validate({
        ignore: [],
        rules:
        {
            image_1 : "required",            
            image_2 : "required",            
        },
        highlight: function (input) {
            $(input).parents('.form-line').addClass('error');
        },
        unhighlight: function (input) {
            $(input).parents('.form-line').removeClass('error');
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "image_1") {
                $("#imgError_1").append(error); 
            } else if (element.attr("name") == "image_2") {
                $("#imgError_2").append(error); 
            } else {                    
               $(element).parents('.form-group').append(error);
            }
        },
        messages:
        {
            Channel_Id : {
                required: "Debe Seleccionar un Canal"
            },
            Sponsor_Name : {
                required: "Debe completar el nombre de la empresa"
            },
            PdfLink : {
                required: "Debe ingresar el link al PDF de la empresa"
            },
            WebLink : {
                required: "Debe ingresar el link a la web de la empresa"
            },
            image_1 : {
                required: "Debe seleccionar una imagen de logo"
            },
            image_2 : {
                required: "Debe seleccionar una imagen para banner"
            },

        },
        submitHandler: submitSponsor
      });  
});