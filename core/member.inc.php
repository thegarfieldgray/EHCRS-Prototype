<?php

function set_appointment($patient, $apptmentSetter, $hospital, $date, $time, $type = 'reg') {

    global $connect;

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_set_appointment (?, ?, ?, ?, ?, ?);");

    // bind string datatype to varaibles
    $stmt->bind_param("ssssss", $patient, $apptmentSetter, $hospital, $date, $time, $type);

    #executing and fetching he rows
    $update = $stmt->execute();

    // var_dump($connect->error);
    //var_dump($stmt->affected_rows);
    // die();

    if($stmt->affected_rows > 0) {
        #echo "string";
        $stmt->close();
        return true;
    }

    #echo "2222";
    return false;

}

function validate_user_from_session($id, $type, $session_id){

    global $connect;

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_validate_user_session (?, ?, ?);");

    // bind string datatype to varaibles
    $stmt->bind_param("sss", $id, $type, $session_id);

    #executing and fetching he rows
    $row = executeAndGetRowsFromSelectPreparedStatement($stmt);
    $stmt->close();

    if (array_key_exists('valid', $row)){

        $verified = (bool) $row['valid'];
        // var_dump($verified);
        # if successful
        if ($verified) {
            return true;
        }
    }

    return false;
}


function update_user_session($id, $session_id) {

        global $connect;

        // the prepare for update
        $stmt = $connect->prepare("CALL proc_update_session (?, ?);");

        // bind string datatype to varaibles
        $stmt->bind_param("ss", $id, $session_id);

        #executing and fetching he rows
        $update = $stmt->execute();

        #var_dump($connect->error);
        #var_dump($stmt->affected_rows);

        if($stmt->affected_rows > 0) {
            #echo "string";
            $stmt->close();
            return true;
        }

        #echo "2222";
        return false;

}


function sign_in_member($id, $email, $password, $type) {

    global $connect;

    if($type === 'Patient') {
        $query = 'CALL proc_sign_in_user (?, ?, ?, ?);';
    } else {
        $query = 'CALL proc_sign_in_user (?, ?, ?, ?);';
    }

    // the prepare for update
    $stmt = $connect->prepare($query);

    // bind string datatype to varaibles
    $stmt->bind_param("ssss", $id, $email, $password, $type);

    #executing and fetching he rows
    $row = executeAndGetRowsFromSelectPreparedStatement($stmt);
    $stmt->close();

    $signed_in = (bool) $row['signed_in'];

    # if sign in successful
    if ($signed_in) {
        log_user_sign($id, '', 'sign_in');
        return true;
    }

    log_user_sign($id, '', 'sign_in_attempt');
    return false;

}

function add_new_member(&$connect, $address_id, $firstName, $lasttName, $middleName,
                        $maidenName, $email, $trn, $password = '', $gender,
                        $dob = '0000-01-01', $tel_no, $age = '0'){
    if($connect == null) {
        echo "pateint.inc.php : global connect member";
        global $connect;
    }


    if (empty(trim($dob))) {
        # set default value
        $dob = '0000-01-01';
    }

    if (empty(trim($age))) {
        # set default value
        $age = '0';
    }

    if (empty(trim($gender))) {
        # set default value
        $gender = null;
    }
    // the prepare for update
    $stmt = $connect->prepare("CALL proc_enter_new_member (?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,? ,?);");

    // bind string datatype to varaibles
    $stmt->bind_param("isssssssssss", $address_id, $firstName, $lasttName, $middleName,
                            $maidenName, $email, $trn, $password, $gender, $dob, $tel_no, $age);

    $member_insert = $stmt->execute();

    #echo $connect->error;

    return $member_insert;
}

function generate_and_send_verification_code_by_email($id) {

    $code = generate_verification_code();

    if(update_verification_code($id, $code))
    {
        gen_send_mail('dlen366@gmail.com', 'Hello' , 'PLease Verify your account by entering the following code on the verfication page of our site: ' .$code );
        return true;
    }

    return false;
}

function confirm_verification($id, $code){
    global $connect;

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_confirm_verfication_code (?, ?);");

    // bind string datatype to varaibles
    $stmt->bind_param("ss", $id, $code);

    #executing and fetching he rows
    $row = executeAndGetRowsFromSelectPreparedStatement($stmt);
    $stmt->close();

    if (array_key_exists('verify',$row)){

        $verified = (bool) $row['verify'];

        # if successful
        if ($verified) {
            update_verification_code($id, null);
            return true;
        }
    }

    return false;

}

function generate_verification_code() {
    return rand(999,99999);
}

function update_verification_code($id, $code) {

        global $connect;

        // the prepare for update
        $stmt = $connect->prepare("CALL proc_update_verification_code (?, ?);");

        // bind string datatype to varaibles
        $stmt->bind_param("ss", $id, $code);

        /* execute statement */
        $stmt->execute();

        //var_dump($stmt->affected_rows);

        return (bool) $stmt->affected_rows;
}

function is_member_exist($id) {

        global $connect;

        // the prepare for update
    	$stmt = $connect->prepare("CALL proc_check_if_memebr_exist (?);");

        // bind string datatype to varaibles
        $stmt->bind_param("i", $id);


        #executing and fetching he rows
        $row = executeAndGetRowsFromSelectPreparedStatement($stmt);

        #var_dump($row);

        $stmt->close();
        #$connect->close();

        return (bool) $row['exist'];
}

function is_member_exist_by_email($email) {

        global $connect;

        // the prepare for update
    	$stmt = $connect->prepare("CALL proc_check_if_member_exist_by_email (?);");

        // bind string datatype to varaibles
        $stmt->bind_param("s", $email);


        #executing and fetching he rows
        $row = executeAndGetRowsFromSelectPreparedStatement($stmt);

        #var_dump($row);

        $stmt->close();
        #$connect->close();

        return (bool) $row['exist'];
}

function is_code_and_user_valid($email, $code) {

        global $connect;

        // the prepare for update
    	$stmt = $connect->prepare("CALL proc_validate_email_and_activation_code (?, ?);");

        // bind string datatype to varaibles
        $stmt->bind_param("ss", $email, $code);


        #executing and fetching he rows
        $row = executeAndGetRowsFromSelectPreparedStatement($stmt);

        #var_dump($row);

        $stmt->close();
        #$connect->close();

        return (bool) $row['exist'];
}

function is_member_active($id) {

    global $connect;

    # the prepare for update
	$stmt = $connect->prepare("CALL proc_check_if_member_is_active_by_id (?);");

    # bind string datatype to varaibles
    $stmt->bind_param("i", $id);

    #executing and fetching he rows
    $row = array();
    $row = executeAndGetRowsFromSelectPreparedStatement($stmt);

    #var_dump($row);

    $stmt->close();

    return !array_key_exists('active' , $row) ? false : (bool) $row['active'];
}

function active_account_by_email($email){
    global $connect;

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_activae_user_account (?);");

    // bind string datatype to varaibles
    $stmt->bind_param("s", $email);

    $activated = $stmt->execute();

    return $activated;

}








  ?>
