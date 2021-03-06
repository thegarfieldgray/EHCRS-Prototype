<?php

require_once(dirname(__FILE__) . '/../init.php');
require_once(dirname(__FILE__) . '/../member.inc.php');

function verify_user_and_password($id, $password){

    global $connect;

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_sign_and_register (?, ?);");

    // bind string datatype to varaibles
    $stmt->bind_param("ss", $id, $password);

    #executing and fetching he rows
    $row = executeAndGetRowsFromSelectPreparedStatement($stmt);
    $stmt->close();

    #var_dump($connect->error);
    #die();

    if (array_key_exists('signed_in', $row)){

        $verified = (bool) $row['signed_in'];
        // var_dump($verified);
        # if successful
        if ($verified) {
            return true;
        }
    }

    return false;

}


function search_for_patient($search_query){

    global $connect;

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_search_for_patient (?);");

    // bind string datatype to varaibles
    $stmt->bind_param("s", $search_query);

    #executing and fetching he rows
    $select = $stmt->execute();

    if (!$select) {
        echo 'Not selected';
    }

    $resultSet = $stmt->get_result();

    if ($resultSet == null) {
		/*  Handle error */
		echo "ResultSet == null" ;

	 }

    # var_dump($connect->error);
     return  $resultSet;
}

function get_physician_work_place($id){

    global $connect;

    $stmt = $connect->prepare("CALL proc_get_physician_working_hosptial (?);");

    $stmt->bind_param("s", $id);

    #executing and fetching he rows
    $row = executeAndGetRowsFromSelectPreparedStatement($stmt);
    $stmt->close();

    #var_dump($row);
    //die();


    if ($row !== null && array_key_exists('hospital', $row)){
        return $row['hospital'];
    }

    return null;
}

function get_hospital_id_from_name($hospital) {

    global $connect;

    $stmt = $connect->prepare("CALL proc_get_hospital_id_by_name (?);");

    $stmt->bind_param("s", $hospital);

    #executing and fetching he rows
    $row = executeAndGetRowsFromSelectPreparedStatement($stmt);
    $stmt->close();

    #var_dump($row);
    //die();


    if ($row !== null && array_key_exists('hospital', $row)){
        return $row['hospital'];
    }

    return null;

}

function get_appointment_for_hospital_by_id($id) {

    global $connect;

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_get_appointments_for_hospital (?);");

    // bind string datatype to varaibles
    $stmt->bind_param("s", $id);

    #executing and fetching he rows
    $select = $stmt->execute();

    if (!$select) {
        echo 'Not selected';
    }

    $resultSet = $stmt->get_result();

    if ($resultSet == null) {
        /*  Handle error */
        echo "ResultSet == null" ;
    }

     return  $resultSet;
}

function change_appointment_status($id, $status){

    global $connect;

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_change_appointment_status (?, ?);");

    // bind string datatype to varaibles
    $stmt->bind_param("ss", $id, $status);

    #executing and fetching he rows
    $update = $stmt->execute();

    if($stmt->affected_rows > 0) {
        #echo "string";
        $stmt->close();
        return true;
    }

    #echo "2222";
    return false;
}

function get_emergency_count($id) {

    global $connect;

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_get_em_and_inc_em_count (?);");

    // bind string datatype to varaibles
    $stmt->bind_param("s", $id);

    #executing and fetching he rows
    $select = $stmt->execute();

    if (!$select) {
        echo 'Not selected';
    }

    $resultSet = $stmt->get_result();

    if ($resultSet == null) {
        /*  Handle error */
        echo "ResultSet == null" ;

     }

    # var_dump($connect->error);
     return  $resultSet;
}


function get_all_patient_and_id(){

    global $connect;

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_get_all_patient_name_and_id ();");

    // bind string datatype to varaibles
    #$stmt->bind_param("s", $id);

    #executing and fetching he rows
    $select = $stmt->execute();

    if (!$select) {
        echo 'Not selected';
    }

    $resultSet = $stmt->get_result();

    if ($resultSet == null) {
        /*  Handle error */
        echo "ResultSet == null" ;

     }

    # var_dump($connect->error);
     return  $resultSet;

}

function add_patient_medical_hisory($physician_id, $patient_id, $hospital_id, $condition, $signs, $height,
                                    $weight, $temp, $pulse, $bp, $resp, $urinalysis){

    global $connect;
    $connect->autocommit(false);


    if(create_new_medical_record($connect, $patient_id, $hospital_id, $physician_id)) {
        # get last insert id from address table
        $med_id = getLastInsertedId($connect);

        $status = enter_new_conditions($connect, $condition, $med_id);

        if(!$status) {
            $state['error'] = 'condition insert failed';
            return $state;
        }

        $status = enter_new_vitals($connect, $med_id, $physician_id, $height, $weight, $temp,
                                    $pulse, $resp, $bp, $urinalysis);

        if(!$status) {
            $state['error'] = 'vitals insert failed';
            return $state;
        }

        $status = add_new_sign_and_sympton($med_id, $physician_id, $signs);

        if(!$status) {
            $state['error'] = 'signs insert failed';
            return $state;
        } else {
            // $connect->commit();
            $state['success'] = true;
            return $state;
        }

    }

}


function add_new_sign_and_sympton($med_id, $physician_id, $sign){

    global $connect;

    if(!is_all_empty($sign)) {

        // the prepare for update
        $stmt = $connect->prepare("CALL proc_enter_new_sign_and_symptom (?, ?, ?);");

        // bind string datatype to varaibles
        $stmt->bind_param("sss", $med_id, $physician_id, $sign);

        #executing and fetching he rows
        $insert = $stmt->execute();

        // var_dump($stmt->error);
        // var_dump($stmt->affected_rows);
        // die();

        if($stmt->affected_rows > 0) {
            #echo "string";
            $stmt->close();
            return true;
        }

        #echo "2222";
        return false;

    } else {

        return true;
    }
}

function create_new_medical_record(&$connect = null, $pateint_id, $hospital_id, $clerk_id) {

    if($connect == null) {
        echo "pateint.inc.php : global connect medical_history";
        global $connect;
    }

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_create_new_medical_record (?, ?, ?);");

    // bind string datatype to varaibles
    $stmt->bind_param("sss", $pateint_id, $hospital_id, $clerk_id);

    $stmt->execute();

    // echo $connect->error;

    if($stmt->affected_rows > 0) {
        $stmt->close();
        return true;
    }
    return false;
}


function enter_new_pateint(&$connect = null,$member_id, $emp_info_id, $pet_name,
                            $kin, $relationship, $religion, $father_name,
                            $mother_name, $birth_place, $birth_parish, $union) {

    if($connect == null) {
        echo "pateint.inc.php : global connect patient";
        global $connect;
    }

    if (empty(trim($union))) {
        # set default value
        $union = null;
    }

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_enter_new_pateint (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");

    // bind string datatype to varaibles
    $stmt->bind_param("issssssssss", $member_id, $emp_info_id, $pet_name, $kin,
                            $relationship, $religion, $father_name, $mother_name, $birth_place,
                    $birth_parish, $union);

    $patient_insert = $stmt->execute();

    // echo $connect->error;
    if($stmt->affected_rows > 0) {
        $stmt->close();
        return true;
    }
    return false;

    // return $patient_insert;
}

function register_pateint(&$connect = null, $pateint_id, $clerk_id, $hospital_id, $physician_id) {

    if($connect == null) {
        echo "pateint.inc.php : global connect register";
        global $connect;
    }

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_register_patient (?, ?, ?, ?);");

    // bind string datatype to varaibles
    $stmt->bind_param("iiii", $pateint_id, $clerk_id, $hospital_id, $physician_id);

    $register_insert = $stmt->execute();

    // echo $connect->error;
    // return $register_insert;
    if($stmt->affected_rows > 0) {
        $stmt->close();
        return true;
    }
    return false;
}


function enter_new_vitals(&$connect = null, $medical_id, $who_recorded, $height = '', $weight = '', $temp  = '',
                            $pulse = '', $resp = '', $bp = '', $urinalysis = '') {

    if(!is_all_empty($height, $weight, $temp, $pulse, $resp, $bp, $urinalysis)) {

        if($connect == null) {
            echo "pateint.inc.php : global connect vitals";
            global $connect;
        }

        // the prepare for update
        $stmt = $connect->prepare("CALL proc_enter_new_vitals (?, ?, ?, ?, ?, ?, ?, ?, ?);");

        // bind string datatype to varaibles
        $stmt->bind_param("sssssssss", $medical_id, $who_recorded, $height, $weight, $temp, $pulse, $resp, $bp, $urinalysis);

        $vitals_insert = $stmt->execute();

        echo $connect->error;

        // return $vitals_insert;

        if($stmt->affected_rows > 0) {
            $stmt->close();
            return true;
        }
        return false;

    } else {

        return true;
    }
}

function enter_new_conditions(&$connect = null, $condition, $medical_id) {

    if(!is_all_empty($condition)) {

        if($connect == null) {
            echo "pateint.inc.php : global connect emp-info";
            global $connect;
        }

        // the prepare for update
        $stmt = $connect->prepare("CALL proc_enter_new_condition (?, ?);");

        // bind string datatype to varaibles
        $stmt->bind_param("ss", $condition, $medical_id);

        $emp_insert = $stmt->execute();


        // return $emp_insert;
        if($stmt->affected_rows > 0) {
            $stmt->close();
            return true;
        }
        return false;

    } else {
        return true;
    }

}

function enter_new_employee_info(&$connect = null, $address_id = null, $insurance_id = null, $emp_name, $occupation, $tel_no, $policy) {

    if(!is_all_empty($insurance_id, $emp_name, $occupation, $tel_no, $policy)) {

        if($connect == null) {
            echo "pateint.inc.php : global connect emp-info";
            global $connect;
        }

        if (empty(trim($address_id))) {
            # set default value
            $address_id = null;
        }

        if (empty(trim($insurance_id))) {
            # set default value
            $insurance_id = null;
        }

        #var_dump($address_id);

        // the prepare for update
        $stmt = $connect->prepare("CALL proc_enter_new_empployment_info (?, ?, ?, ?, ?, ?);");

        // bind string datatype to varaibles
        $stmt->bind_param("ssssss", $address_id, $insurance_id, $emp_name, $occupation, $tel_no, $policy);

        $emp_insert = $stmt->execute();

        #echo $connect->error;
        // return $emp_insert;
        if($stmt->affected_rows > 0) {
            $stmt->close();
            return true;
        }

        return false;


    } else {
        return $error['error'] = 'Empty: Didnt Insert';
    }

}

function enter_new_address(&$connect = null, $street_name = '', $parish = '', $country = '') {

    if(!is_all_empty($street_name, $parish, $country)) {

        if($connect == null) {
            echo "pateint.inc.php : global connect Address";
            global $connect;
        }

        $street_name = is_empty_set_default($street_name, null);


        // the prepare for update
        $stmt = $connect->prepare("CALL proc_enter_new_address (?, ?, ?);");

        // bind string datatype to varaibles
        $stmt->bind_param("sss", $street_name, $parish, $country);

        $address_insert = $stmt->execute();

        // return $address_insert;
        if($stmt->affected_rows > 0) {
            $stmt->close();
            return true;
        }
        return false;

    } else {
        return $error['error'] = 'Empty: Didnt Insert';
    }
}


function get_patient_general_info_for_pyhsician($id){

    global $connect;

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_get_patient_general_info_for_physician (?);");

    // bind string datatype to varaibles
    $stmt->bind_param("s", $id);

    #executing and fetching he rows
    $select = $stmt->execute();

    if (!$select) {
        echo 'Not selected';
        return null;
    }

    $resultSet = $stmt->get_result();

    if ($resultSet == null) {
        /*  Handle error */
        echo "ResultSet == null" ;
        return null;

     }

    # var_dump($connect->error);
     return  $resultSet->fetch_assoc();;
}

function get_latest_patient_vital($id){

    global $connect;

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_get_the_lastest_vital_for_patient (?);");

    // bind string datatype to varaibles
    $stmt->bind_param("s", $id);

    #executing and fetching he rows
    $select = $stmt->execute();

    if (!$select) {
        echo 'Not selected';
        return null;
    }

    $resultSet = $stmt->get_result();

    if ($resultSet == null) {
        /*  Handle error */
        echo "ResultSet == null" ;
        return null;
     }

    # var_dump($connect->error);
     return  $resultSet->fetch_assoc();
}

function get_patient_condition_signs_treatment_medication($id){

    global $connect;

    // the prepare for update
    $stmt = $connect->prepare("CALL proc_get_patient_condition_signs_medication_treatment (?);");

    // bind string datatype to varaibles
    $stmt->bind_param("s", $id);

    #executing and fetching he rows
    $select = $stmt->execute();

    if (!$select) {
        echo 'Not selected';
        return null;
    }

    $resultSet = $stmt->get_result();

    if ($resultSet == null) {
        /*  Handle error */
        echo "ResultSet == null" ;
        return null;
     }

    # var_dump($connect->error);
     return  $resultSet;
}

 ?>
