$('document').ready(function()
{ 
  /* validation */
  $("#login-form").validate({
    rules:
    {
      password: {
        required: true,
      },
      user_email: {
        required: true,
        email: true
      },
    },
    messages:
    {
      password:{
        required: "please enter your password"
      },
      user_email: "please enter your email address",
    },
    submitHandler: submitForm 
  });  
});