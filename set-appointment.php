<?php
include 'patient-header.php';
require_once 'session-validation.php';

if(get_user_id_from_session() !== null && get_current_user_type() !== null && get_current_user_type() === 'Patient') {
	$user_id = get_user_id_from_session();
} else {
	echo "Session not set: logout user";
	header('Location: login-redirect.php');
}
 ?>
<div id="wrapper">
	<div class="main-content-wrapper" id="p-info">
		<div class="main-content">
			  <div class="row">
			  	<div class="panel panel-default" id="pad-panel">
			  		<div class="panel-body">
			  			<br>
			  			<div align="center"><h2><b>Set Appointment</b></h2></div>
			  			<hr style="width: 98%">
			  			<div class="col-md-8 col-md-offset-2">

			    			<div id='calendar'></div>

			  			</div>
			  		</div>
			  	</div>
			  </div>
		</div>
	</div>
</div>

<?php include 'footer.php' ?>
