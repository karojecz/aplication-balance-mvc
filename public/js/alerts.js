
				

				
				
				
				
				//this is js validation of text area in income and expense file
				

document.getElementById("submit_btn").addEventListener("click", function(event){

//var comment=document.getElementById('Textarea').value;
var comment=document.getElementById("Textarea").value;

const regex=/^[A-Za-z0-9 ,.]*$/;

if(!regex.test(comment)){
	
	

	document.getElementById("text_area_alert").innerHTML = "You can only use letters and numbers. Special characters are not allowed." ;
	$('#text_area_alert').show();
	
	
	setTimeout(function () {
				$( "#text_area_alert" ).hide('fade');
				},3000);
	
  event.preventDefault();
  
  }
  
});
				
				