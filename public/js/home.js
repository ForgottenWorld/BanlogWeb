$(document).ready(function(){
		
	$("#add_ban").click(function(){
		$(".new_ban_modal").modal('show');
	});
	
	$("#add_server").click(function(){
		$(".new_server_modal").modal('show');
	});
	
	$("#editUser").click(function(){
		$(".edit_user_modal").modal('show');
	});
	
	$("#newPasswordForm").submit(function(event) {
		let newPassword = $("input[name=new_password]").val();
		let confirmNewPassword = $("input[name=confirm_password]").val();
		
		if(newPassword != confirmNewPassword) {
			event.preventDefault();
			alert('Le due password devono coincidere!');
		}
	});	
	
	$(".is_perma").checkbox({
		onChange: function(){
			let checked = this.checked;
			if(checked) {
				$(".end_date").slideUp(400);
			} else {
				$(".end_date").slideDown(400);
			}
		}
	});

});