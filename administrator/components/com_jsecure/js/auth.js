Joomla.submitbutton = function(pressbutton){
	var submitForm = document.adminForm;
	
	if(pressbutton=="help"){
		submitForm.task.value=pressbutton;
		submitForm.submit();
		return true;
	}	
	
	if(submitForm.master_password.value == ""){
		alert("The password you've supplied is not correct.");
		submitForm.key.focus();
		return false;
	}
	
	if(submitForm.master_password.value != submitForm.ret_master_password.value){
		alert("Master Password and Verify Master Password do not match");
		submitForm.key.focus();
		return false;
	}


	submitForm.task.value=pressbutton;
	submitForm.submit();
}
