<?php

//dev
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH."vendor/autoload.php");

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class Payment_flow extends CI_Controller
{
  public function payment(){
    
   
    $this->load->model('Usermodel', 'user');
    $this->load->model('Categorymodel', 'category');
    $this->load->model('funder_model');
    $this->load->model('projectmodel','project');
    $this->load->model('Commonmodel','common'); //added by snehal hude
    $this->load->library('session');  //Load the Session 
    $this->load->library('Utility');
  
   // include APPPATH . 'libraries/Instamojo.php';
    
    // echo '<pre>';
    // print_r($_POST);
    // die();
    
    //$signup_next = array();
    //$reward_id = $this->session->userdata('reward_id');
                
    // if($this->session->userdata('rewq_reflogin')){
    //   $rew_quantity = $this->session->userdata('rewq_reflogin');
    //   $this->session->set_userdata('rew_quantity',$rew_quantity);
    // } else {
    //   $rew_quantity=1;
    // }
    
    //echo $campaign_id = $this->session->userdata('campaign_id');
    //echo $campaign_reward = $this->session->userdata('campaign_reward');

    $signup_data    =   array();
  
    $rsc            =   new Utility();

    if ($this->input->post()) {
      $email        =   trim($this->input->post('user_email_id'));

      $rotary_club_name        =   trim($this->input->post('rotary_club_name'));
      $rotary_reg_no        =   trim($this->input->post('rotary_reg_no'));
       

      $user_id            =   $this->input->post('user_id');
      $campaign_id        =   $this->input->post('cont_campaign_id');
      $contribute_amount  =   $this->input->post('contribute_amount');
      
      $anonymous          =   $this->input->post('anonymous');
      $anonymous          =   (!empty($anonymous)) ? 1 : 0;

      $user_org_name      =   $rsc->RemoveSpecialCharacter(trim($this->input->post('user_org_name')));
      $user_org_name      =   (!empty($user_org_name)) ? $user_org_name : "";

      $user_type          =   $this->input->post('reg_user_type');
      $user_type          =   (!empty($user_type)) ? $user_type : 1;


      $name               =   $rsc->RemoveSpecialCharacter(trim($this->input->post('name')));

      if($user_type == 5){
        $name             =   (!empty($name)) ? $name : $user_org_name;
      }
    
      $fl_shipping_address =  $rsc->RemoveSpecialCharacter($this->input->post('fl_shipping_address'));
      $fl_shipping_address =  (!empty($fl_shipping_address)) ? $fl_shipping_address : '';

      $address            =   $rsc->RemoveSpecialCharacter($this->input->post('address1'));
      $address            =   (!empty($address)) ? $address : '';

      // $address = $rsc->RemoveSpecialCharacter($this->input->post('address1'));
      
      $rew_quantity = $this->input->post('rew_quantity');
      $rew_quantity = (!empty($rew_quantity)) ? $rew_quantity : 1;
      
      $payment_type = $this->input->post('payment_type');

      //print_r($_POST);exit();
      /* code added by Shubham for Razorpay*/
      if($campaign_id == 73558){
        //$card_type    = (!empty($payment_type)) ? $payment_type : 6;
        $card_type    =  6;
      }else{
      //  $card_type    = (!empty($payment_type)) ? $payment_type : 5;
      $card_type    = 5;        
      } 
      /* code added by Shubham for Razorpay*/
   
     


      $nationality = $this->input->post('nationality');
      $nationality = (!empty($nationality)) ? $nationality : 80;
 
      $country_code = $this->input->post('country_code');
      $country_code = (!empty($country_code)) ? $country_code : 91;
      
      $phone = $this->input->post('phone');
      $phone = (!empty($phone)) ? $phone : '';

  ///////// for phone number + country code
        $searchForValueplus = '+'.$country_code;

        if( strpos($phone, $searchForValueplus) !== false ) {
             // echo "Found";
            $phonenumber_db = explode($searchForValueplus,$phone)[1];
             
        }else{
          $phonenumber_db = trim($phone);
        }


      
      $pin = $this->input->post('pin');
      $pin = (!empty($pin)) ? $pin : '';

      $nationality = $this->input->post('nationality');

     /* echo '<div class="shreesh" style="display:none;">';
      print_r($_POST); exit;
      echo '</div>';*/
      //added by snehal hude 02-11-2021 to save the country and state id
      $country_id = $this->input->post('country');
      $state_id = $this->input->post('state');
      $city = $this->input->post('city');
     //added by snehal hude 02-11-2021 to save the country and state id


      //product specifications
      $size_1 = (!empty($this->input->post('size_1'))) ? $this->input->post('size_1') : '';
      $size_2 = (!empty($this->input->post('size_2'))) ? $this->input->post('size_2') : '';
      $size_3 = (!empty($this->input->post('size_3'))) ? $this->input->post('size_3') : '';
      $size_4 = (!empty($this->input->post('size_4'))) ? $this->input->post('size_4') : '';
      $size_str = $size_1.','.$size_2.','.$size_3.','.$size_4;
      $size = rtrim($size_str,",");


      $material_1 = (!empty($this->input->post('material_1'))) ? $this->input->post('material_1') : '';
      $material_2 = (!empty($this->input->post('material_2'))) ? $this->input->post('material_2') : '';
      $material_3 = (!empty($this->input->post('material_3'))) ? $this->input->post('material_3') : '';
      $material_4 = (!empty($this->input->post('material_4'))) ? $this->input->post('material_4') : '';
      $material_str = $material_1.','.$material_2.','.$material_3.','.$material_4;
      $material = rtrim($material_str,",");
      
      // for($i =0; $i<=4; $i++){
      //   $color_.$i = (!empty($this->input->post('color_'.$i))) ? $this->input->post('color_'.$i) : '';
      // }

      $color_1 = (!empty($this->input->post('color_1'))) ? $this->input->post('color_1') : '';
      $color_2 = (!empty($this->input->post('color_2'))) ? $this->input->post('color_2') : '';
      $color_3 = (!empty($this->input->post('color_3'))) ? $this->input->post('color_3') : '';
      $color_4 = (!empty($this->input->post('color_4'))) ? $this->input->post('color_4') : '';
     
      $color_str = $color_1.','.$color_2.','.$color_3.','.$color_4;
      $color = rtrim($color_str,",");

      $passportNumber = $this->input->post('passportNumber');
      $passportNumber = (!empty($passportNumber)) ? $passportNumber : "";
      
      // $state = $this->input->post('state');
      // $state = (!empty($state)) ? $state : "";
      
      // $country = $this->input->post('country');
      // $country = (!empty($country)) ? $country : "";
      
      // $city = $this->input->post('city');
      // $city = (!empty($city)) ? $city : ""; 

      $hometown = $this->input->post('hometown');
      $hometown = (!empty($hometown)) ? $hometown : "";

      // $sap_str = explode(", ",$hometown);
      // $city = $sap_str[0];
      // $state = $sap_str[1];
      // $country = $sap_str[2];
      
      // if(empty($country)){
      //   $searchForValue = ',';

      //   if( strpos($hometown, $searchForValue) !== false ) {
      //        // echo "Found";
      //        $home_town = trim(explode(", ",$hometown)[0]);
      //   }else{
      //     $home_town = trim($hometown);
      //   }

      //   $countrydata = $this->getDataFromCity($home_town);


      //   // print_r($countrydata); 
      //   $country = $countrydata['Countryname'];
      //   $city = $countrydata['CityName'];
      //   $state = $countrydata['Statename'];

      //   $hometown = $city.', '.$state.', '.$country;
      // }

    /*  $hometownArray = explode(',', $hometown);
      $len = count($hometownArray);
      if($len == 3){
        $country = trim($hometownArray[$len-1]);
        $state = trim($hometownArray[$len-2]);
        $city = trim($hometownArray[$len-3]);
      }
      else if($len == 2){
        $country = trim($hometownArray[$len-1]);
        $state = trim($hometownArray[$len-2]);
        $city = trim($hometownArray[$len-2]);
      }
      else {
       $country = trim($hometownArray[$len-1]);
       $state = trim($hometownArray[$len-1]);
       $city = trim($hometownArray[$len-1]);
     }*/

 
      $material = (!empty($this->input->post('material'))) ? $this->input->post('material') : 0;
      $camp_reward_id = (!empty($this->input->post('camp_reward_id'))) ? $this->input->post('camp_reward_id') : 0;

      $pan_card = $rsc->RemoveSpecialCharacter($this->input->post('panno'));
      $pan_card = (!empty($pan_card)) ? $pan_card : '';

      $org_data = $this->db->query('SELECT fo.`id`,fu.`id` as user_id,fo.`is_no_pan`,fo.`is_pancard_mandate` FROM fad_organizations fo
              Left Join fad_front_users fu on fu.`organization_id` = fo.`id`
              Left Join fad_project fp on fp.`user_id` = fu.`id`
              WHERE fp.`id`="'.$this->input->post('cont_campaign_id').'"')->row_array();

      if(!empty($user_id) && ($user_id != 0)){
        
          $get_funder = $this->user->get_info($user_id);  
          if(!empty($get_funder) && $get_funder['email'] == $email){
            
            $user_id = $get_funder['id'];
            
            $user_info = array(
              'user_id'     => $user_id,
              'user_type'   => $get_funder['user_type']
              );
            $this->session->set_userdata($user_info);
            
            // $update_data = array(
            //   'pan_card'        => $pan_card
            // );

            $name = ($get_funder['fname'] == $name) ? $get_funder['fname'] : $name;
            
            $phonenumber_db = ($get_funder['mobile'] == $phonenumber_db) ? $get_funder['mobile'] : $phonenumber_db;

             // print_r($phone); die;
            
            $country_code = ($get_funder['country_code'] == $country_code) ? $get_funder['country_code'] : $country_code;
            
            $address = (strcmp($address,$get_funder['address']) == 0) ? $get_funder['address'] : $address;
            
            $user_org_name = ($get_funder['ex_real_name'] == $user_org_name) ? $get_funder['ex_real_name'] : $user_org_name;
            
            if($org_data['is_pancard_mandate'] == 0 && empty($pan_card)){
              $pan_card = $get_funder['pan_card'];
            }else{
              $pan_card = ($get_funder['pan_card'] == $pan_card) ? $get_funder['pan_card'] : $pan_card;
            }
           
            $nationality = ($get_funder['nationality'] == $nationality) ? $get_funder['nationality'] : $nationality;
            
            // $hometown = ($get_funder['hometown'] == $hometown) ? $get_funder['hometown'] : $hometown;
            $country = ($get_funder['country'] == $country) ? $get_funder['country'] : $country;

            
           // $city = ($get_funder['city'] == $city) ? $get_funder['city'] : $city;

              if(!empty($city)){
                $city = ($get_funder['city'] == $city) ? $get_funder['city'] : $city;
              }else{
                $city = $_POST['city'];
              }
            
            
            $state = ($get_funder['state'] == $state) ? $get_funder['state'] : $state;
            
           // $hometown = $city.', '.$state.', '.$country;
            
            $passportNumber = ($get_funder['passport'] == $passportNumber) ? $get_funder['passport'] : $passportNumber;
            
            $pin = ($get_funder['pin'] == $pin) ? $get_funder['pin'] : $pin;

            //ADDED by snehal hude
            $getCountryName = $this->common->getData("fad_all_countries","name","id='".$country_id."'");
            $getStateName = $this->common->getData("fad_all_states","name","id='".$state_id."'");
            //ADDED by snehal hude

              $update_data          =   array(
                  'mobile'          =>  $phonenumber_db,
                  'fname'           =>  $name,
                  'country_code'    =>  $country_code,
                  'address'         =>  $address,
                  'ex_real_name'    =>  $user_org_name,
                  // 'pan_card'        =>  $pan_card,
                  'nationality'     =>  $nationality,
                  'hometown'        =>  ucwords($hometown),//updated by snehal hude
                   'country'         =>  $getCountryName['0']->name,//ADDED by snehal hude
                  //'city'            =>  $city,
                  'city'            =>  ucwords($hometown),//updated by snehal hude
                   'state'           =>  $getStateName['0']->name,//ADDED by snehal hude
                  'passport'        =>  $passportNumber,
                  'pin'             =>  $pin,
                  'rotary_club_name'=>  $rotary_club_name,
                  'rotary_reg_no'   =>  $rotary_reg_no,
                  'country_id'   =>  $country_id, //ADDED by snehal hude
                  'state_id'   =>  $state_id, //ADDED by snehal hude
                );


              if($org_data['is_no_pan'] == 0){
                $update_data['pan_card'] = $pan_card;
              }

             // print_r($get_funder); 
             // print_r($update_data);  die();
            $this->user->update_user_info($user_id,$update_data);
          }else{
            redirect('home/campaign/'.$campaign_id);
          }
      }else{

        if ($this->user->unique_email($email)) {
          
          $user_name = explode('@', $email)[0];
          $fname = $this->input->post('name');

          $str_code = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
          $verification_key = substr(str_shuffle(str_repeat($str_code, 5)), 0, 22);
            
          // $verification_key = rand(23456,975655);
          // $verification_key = md5($email);

          //ADDED by snehal hude
           $getCountryName = $this->common->getData("fad_all_countries","name","id='".$country_id."'");
           $getStateName = $this->common->getData("fad_all_states","name","id='".$state_id."'");
         //ADDED by snehal hude
         
          $signup_data = array(
            'email'          => $email,
            'mobile'         => $phonenumber_db,
            'fname'          => $name,
            //'rotary_reg_no'  => $this->input->post('rotary_reg_no'),
            'user_name'      => $user_name,
            'login_ip'       => $_SERVER['REMOTE_ADDR'],
            'user_pwd'       => md5($this->input->post('new_apswd')),
            'user_type'      => $user_type,
            'verification_key'=> $verification_key,
            'is_effect'       => 1,
            'point'           => 1,
            'account_verify'  => 1,
            'country_code'  => $country_code,
            'address'       => $address,
            'hometown'      => ucwords($hometown),//updated by snehal hude
            'pin'           => $pin,
            'country'         =>  $getCountryName['0']->name,//ADDED by snehal hude
            /*'city'          => $city,*/
            'city'          => ucwords($hometown),//updated by snehal hude
            'state'           =>  $getStateName['0']->name,//ADDED by snehal hude
            'ex_real_name'    => $user_org_name,
            'pan_card'        => $pan_card,
            'passport'      => $passportNumber,
            'nationality'     => $nationality,
            'device_info'     => json_encode($_SERVER),
            'is_new_user' =>1,
            'rotary_club_name'=>  $rotary_club_name,
            'rotary_reg_no'   =>  $rotary_reg_no,
            'country_id'   =>  $country_id,  //ADDED by snehal hude
            'state_id'   =>  $state_id, //ADDED by snehal hude
          );
          // print_r($signup_data); die;

          // if($this->input->post('close') == 1){
          //    if($email && $this->input->post('phone') && $name){
          //       $user_id = $this->funder_model->funder_singup_data($signup_data);
          //    } 
          // }else{
          //   $user_id = $this->funder_model->funder_singup_data($signup_data);
          // }
  
          $user_id = $this->funder_model->funder_singup_data($signup_data);

          $_POST['user_name'] = $signup_data['fname'];
          $_POST['ins_id'] = $user_id;
          $_POST['verification_key'] = $verification_key;
          
          $user_info = array(
            'user_id'     => $user_id,
            'user_type'   => $user_type
            );
          $this->session->set_userdata($user_info);
          
          //Send Email
          $email_template = $this->load->view('Emails/user-welcome', $_POST, TRUE);
          $this->load->config('email');
          $this->load->library('email');
          $this->email->clear();
          $this->email->initialize(array('mailtype' => 'html'));
          $this->email->set_newline("\r\n");
          $this->email->from("getintouch@fueladream.com", "Fueladream");
          $this->email->to($email);
          $this->email->subject("Welcome to Fueladream!");
          $this->email->message($email_template);
          $email_sent =  $this->email->send();
          
        } else{
          //echo "test";exit();
          redirect('home/campaign/'.$campaign_id);
        }
      }

      $data_user = array(
        'user_id'     => $user_id,
        'user_type'   => $user_type
      ); 
      
      $email_arr = array(
        'email_vars'   => $fname.'|'.$user_id.'|'.$verification_key,
        'email' => $email
      );

     

      if(!empty($user_id)){   
        //echo "test1";exit();
          $data = array(
            "fl_funder_unique_user_id"   => $user_id,
            
            "fl_anonymous" => $anonymous,
            "fl_shipping_address"   => $fl_shipping_address,
            "fl_card_type"   => $card_type,
            "fl_project_id" => $campaign_id,
            "fl_funded_amount" => $contribute_amount,
            "fl_size"     => $size,
            "fl_color"    => $color,
            "fl_meterial"  => $material,
            "fl_reward_id" => $camp_reward_id,
            "fl_rew_count" => $rew_quantity,
            // "fl_passport_no"  => $passport,
            "fl_nationality"  => $nationality
          );


     

        $this->new_contribute($data);
      }else{
       
        redirect('home/campaign/'.$campaign_id);
      }
      
    }else{ 
      /*Name of the Developer: Shubham
      The purpose for updates: added comment to echo to block the msg
      Date of Update: 29/03/2022
      Requested by Dept: Technical
      Approved by: Harish
      */
      //echo "something went wrong";
      exit();
    }
  }

  ////
  public function new_contribute($data = array()){



    $this->load->library('session');
    $this->load->model('UserModel','user');
    $this->load->model('Projectmodel','project');
    $this->load->model('funder_model','funder');
  
   
    if($data['fl_project_id']){
      $check_status = $this->db->query("SELECT is_effect,end_time,is_crowdfavourite,cate_id FROM fad_project WHERE id='".$data['fl_project_id']."'")->result_array();

      $end_date = $check_status[0]['end_time'];
      
      if(($check_status[0]['is_crowdfavourite']==0)){    
        if(strtotime(date("d-m-Y"))>strtotime(stripslashes("$end_date"))){
          redirect('/paymenterror/error_page');
        }
      }

    }
 
   if($data['fl_funded_amount']){
      
      $amount = $data['fl_funded_amount'];
      $card_type = $data['fl_card_type'];

      $funder_array = $data;
          
       $funder_array['fl_payment_confirmation'] = 0;
       $funder_array['device_info'] = json_encode($_SERVER);

    
      $ins_funder_details = $this->funder->add_funded_details($funder_array);
      

      if(!empty($ins_funder_details)){
        
        $payment_id = array(
          'funder_payment_id' => $ins_funder_details
        );
        
        $this->session->set_userdata($payment_id);
        
        $data['funder_payment_id'] = $ins_funder_details; 
        $data['reward_count'] = 1; 


        /*ini_set ('display_errors', 1);  
        ini_set ('display_startup_errors', 1);  
        error_reporting (E_ALL);  */


        switch($card_type) {
          case 1:
            $this->hdfc_call($data);
            break;
          case 2:
            $this->load->model('Instamojo_model', 'InstamojoModel');
            $this->InstamojoModel->instamojo($data);
            break;
          case 3:
            $this->paytm($data);
            break;
          case 4:
            /* code added by snehal hude for paypal*/
            $this->load->model('Paypal_model');
            $this->Paypal_model->create_paypal_payment_url($data);
            /* code added by snehal hude for paypal*/
            break;
          case 5:
            /* code added by Shubham for Razorpay*/
            $this->Razerpay($data);
            /* code added by Shubham for Razorpay*/
              break;
          case 6:
            /* code added by Shubham for Razorpay*/
            $this->PayU($data);
            /* code added by Shubham for Razorpay*/
              break;
          default:
            redirect("/home/campaign/".$data['fl_project_id']);
        }
    
      }else{
        // echo "dsfdsfd";die();
        redirect('home/campaign/'.$data['fl_project_id']);
      }
    }else{
      // echo "dfdfdfewe";die();
      redirect('home/campaign/'.$data['fl_project_id']);
    }
  }

  public function PayU($data = array()){

    $this->load->model('Funder_model', 'Funder');
    $this->config->load('payu');
    $pre_funding_details = $this->Funder->get_pre_funding_data_detail($data['funder_payment_id']);

    // Print data for debugging (optional)
    print_r($data);

    // Set the API endpoint URL
    $apiEndpoint = $merchantKey = $this->config->item('payu_base_url');

    // Set the merchant key and salt
    $merchantKey = $this->config->item('payu_merchant_key');      //"ccdWW5";
    $salt = $this->config->item('payu_merchant_key');

    // Set the order details
    $amount = $data['fl_rew_count'] * $pre_funding_details['fl_funded_amount'];
    $productInfo = "Test Product";
    $firstName = $pre_funding_details['fname'].' '.$pre_funding_details['lname'];
    $email = $pre_funding_details['email'];
    $phone = $pre_funding_details['mobile'];
    $txnId = "TXN" . time();
    $surl = base_url()."payment_flow/response_payu_success/";
    $furl = base_url()."payment_flow/response_payu_fail/";

    // Create a map of parameters to pass to the PayU API
    $params = array(
        "key" => $merchantKey,
        "txnid" => $txnId,
        "amount" => $amount,
        "productinfo" => $productInfo,
        "firstname" => $firstName,
        "email" => $email,
        "phone" => $phone,
        "surl" => $surl,
        "furl" => $furl,
        "service_provider" => "payu_paisa", // Mandatory for certain integrations
        "udf1" => "",
        "udf2" => "",
        "udf3" => "",
        "udf4" => "",
        "udf5" => ""
    );

    // Generate the hash
    $params["hash"] = $this->generateHash($params, $salt);

    // Generate the HTML form for submission
    $this->generatePaymentForm($apiEndpoint, $params);
  }

  private function generateHash($params, $salt)
  {
    // Create the hash string
    $hashString = $params["key"] . "|" .
                  $params["txnid"] . "|" .
                  $params["amount"] . "|" .
                  $params["productinfo"] . "|" .
                  $params["firstname"] . "|" .
                  $params["email"] . "|" .
                  $params["udf1"] . "|" .
                  $params["udf2"] . "|" .
                  $params["udf3"] . "|" .
                  $params["udf4"] . "|" .
                  $params["udf5"] . "||||||" . $salt;

    // Generate and return the hash
    return strtolower(hash("sha512", $hashString));
  }

  private function generatePaymentForm($action, $params){

    // Generate an auto-submitting HTML form
    echo '<form id="payuForm" method="POST" action="' . $action . '">';
    foreach ($params as $key => $value) {
        echo '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '">';
    }
    echo '<script>document.getElementById("payuForm").submit();</script>';
    echo '</form>';
  }

  public function response_payu_success(){
    $Payu_Reaponce = $_POST;
    $merchantSalt = "aJqPPCxiB2QQ5YTuj79PekTbtYvg7UeG"; // Use config value

    $hashSequence = $merchantSalt . '|' . $response['status'] . '||||||||||' .
        $response['udf5'] . '|' . $response['udf4'] . '|' . $response['udf3'] . '|' .
        $response['udf2'] . '|' . $response['udf1'] . '|' . $response['email'] . '|' .
        $response['firstname'] . '|' . $response['productinfo'] . '|' .
        $response['amount'] . '|' . $response['txnid'] . '|' . $response['key'];

    $computedHash = strtolower(hash('sha512', $hashSequence));

    if ($computedHash !== $response['hash']) {
        // Log error, redirect to failure
        redirect('payment/failed');
        return;
    }

    // Validate transaction details (e.g., check txnId and amount in your database)
    $this->load->model('Funder_model', 'Funder');
    $isValid = $this->Funder->validate_transaction($response['txnid'], $response['amount']);

    if (!$isValid) {
        redirect('payment/invalid');
        return;
    }

    // Process successful payment
    // Update database, send confirmation, etc.

    redirect('payment/success'); // Redirect to a thank-you page
    
    print_r($Payu_Reaponce);
    print_r('pass');
  }

  public function response_payu_fail(){
    $Payu_Reaponce = $_POST;
    print_r($Payu_Reaponce);
    print_r('Fail');
  }

  public function Razerpay($data = array()){
    $this->load->model('Funder_model', 'Funder');
    require_once(APPPATH."/third_party/razorpay/config.php");
    $api = new Api($keyId, $keySecret);
    
    $pre_funding_details = $this->funder->get_pre_funding_data_detail($data['funder_payment_id']);

    $campaignid        = $data["fl_project_id"];
    $transaction_amount = $data['fl_rew_count'] * $pre_funding_details['fl_funded_amount'];
    $name               = $pre_funding_details['fname'].' '.$pre_funding_details['lname'];
    $email              = $pre_funding_details['email'];
    $fl_id              = $pre_funding_details['fl_id'];
    $phone              = $pre_funding_details['mobile'];
    
    // Create order data
    $orderData = [
        'receipt'         => 'Paying for '. $campaignid,
        'amount'          => $transaction_amount * 100, 
        'currency'        => 'INR',
        'payment_capture' => 1
    ];

    // Create order using Razorpay library
    $razorpay_order = $api->order->create($orderData);
    $ORDER_ID       = $razorpay_order['id'];
    $call_url       = base_url()."payment_flow/responce_razorpay/".$ORDER_ID;
    //print_r($razorpay_order);

    if($razorpay_order['status'] == 'created')
    {
      $upd_data['fl_instamojo_payment_request_id'] = $ORDER_ID;
      $this->Funder->update_pre_funding_data_by_id($pre_funding_details['fl_id'],$upd_data);
      
    }else{
    redirect('home/campaign/'.$data['fl_project_id']);
    }

    echo ('
      <script>
      function onScriptLoad(){
        var options = {
          "key": "'. $keyId . '", // Enter the Key ID generated from the Dashboard
          "amount": "'. $transaction_amount . '", // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
          "currency": "INR",
          "name": "FUELADREAM ONLINE VENTURES PRIVATE LIMITED", //your business name
          "description": "'. $campaignid . '",  // Add the campaign id here for referrence
          "image": "https://fadcdn.s3.amazonaws.com/assets/img/fadlogo-tm.png",
          "order_id": "'. $ORDER_ID.'", //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
          "callback_url": "'.$call_url.'",
          "modal": {
            "ondismiss": function(){
                // Redirect to another URL on popup close
                window.location.href = "https://www.fueladream.com/home/campaign/'. $campaignid. '";
            }
          },
          "prefill": { //We recommend using the prefill parameter to auto-fill customer contact information especially their phone number
              "name": "'.$name.'", //your customer name
              "email": "'.$email.'",
              "contact": "'.$phone.'" //Provide the customer phone number for better conversion rates 
          },
          "theme": {
            "color": "#3399cc"
          }
        };

        var rzp1 = new Razorpay(options);
        rzp1.open();
        e.preventDefault();
      }
		</script>
    <script src="https://checkout.razorpay.com/v1/checkout.js" onload="onScriptLoad();"></script>');

    
	}

  public function responce_razorpay($ORDER_ID){
    require_once(APPPATH."/third_party/razorpay/config.php");
    $api = new Api($keyId, $keySecret);
    $this->load->model('Funder_model', 'Funder');

    $paymentId = $_POST['razorpay_payment_id'];
    $razorpayorderId   = $_POST['razorpay_order_id'];
    $signature = $_POST['razorpay_signature'];

    $order     = $api->order->fetch($ORDER_ID);
    
    $amount    = $order['amount'];
    $orderId = $ORDER_ID;
    $attributes = array(
      'amount'=>$amount,
      'currency' => 'INR'
    );
    $response = $api->payment->fetch($paymentId);
    $rdata =  json_encode($response);
    

    if($response['status'] == "captured") {
      unset($_POST);
      $pre_funding_details = $this->Funder->get_pre_funding_data_by_request_id($orderId);
      if(count($pre_funding_details) != 0)
      { 
        if ($this->verifyPaymentSignature($paymentId, $orderId, $signature))
        {
          
          $payment_status = 1;
          
          $_POST['fpi_card_type'] = $pre_funding_details['fl_card_type'];
          $_POST['fpi_amount_funded'] = $response['amount']/100;
          $_POST['fpi_payment_id'] = $response['id'];
          $_POST['fpi_payment_mode'] = $response['method'];
          $_POST['fpi_transaction_status'] = $payment_status;
          $_POST['fpi_merchant_id'] = $pre_funding_details['fl_project_id'];
          $_POST['fpi_email'] = $response['email'];
          $_POST['fpi_mobile'] = $response['contact'];
          $_POST['fpi_user_id'] = $pre_funding_details['fl_funder_unique_user_id'];
          $_POST['fpi_project_id'] = $pre_funding_details['fl_project_id'];
          $_POST['fpi_receipt_pdf_link'] = 'CallBack';
          $_POST['device_info'] = json_encode($_SERVER);
          $_POST['fpi_hash_payu'] = substr($this->getToken(),0,-8).'-'.substr($this->getToken(),0,-8).'-'.substr($this->getToken(),0,-8);
          $_POST['fpi_transaction_id_gen'] = $this->session->userdata('funder_payment_id');  
          $_POST['fpi_matching_partner_amount'] = 0;
          
          $value = $this->Funder->payment_id_duplicate_check($response['id']);
           
          
          if($value['result_count'] == 0){
            //$value['fpi_id'] = $this->Funder->add_confirmed_funding_details($_POST);
          }

          if($payment_status == 1)
          {
            
            $data_array_fl['fl_payment_confirmation'] = $payment_status;
            //$data_array_fpi['fpi_transaction_status'] = $payment_status;
            $this->db->update('fad_funders_list', $data_array_fl, array('fl_id' => $this->session->userdata('funder_payment_id')));
            
            //$this->db->update('fad_funder_payment_information', $data_array_fpi, array('fpi_id' => $value['fpi_id']));

            $data['reward_details'] = $this->get_claimed_rewards_rz($this->session->userdata('funder_payment_id'));
            
            $rew_quantity = ( !empty($data['reward_details']['fl_rew_count']) ? $data['reward_details']['fl_rew_count'] : 0 );
            $reward_id = ( !empty($data['reward_details']['fl_reward_id']) ? $data['reward_details']['fl_reward_id'] : 0 );

            if($reward_id)
            {
              if($rew_quantity >= 1)
              {
                $last_claimed_user_id = $data['reward_details']['fl_funder_unique_user_id'];
                $rew_details = $this->Funder->get_reward_details_rewardwise($reward_id);
                if($rew_details['applicable_person'] >= $rew_details['claimed_reward'])
                {
                  $up_arr  = array(
                    'claimed_reward' => $rew_details['claimed_reward']+$rew_quantity,
                    'last_claimed_user_id' => $last_claimed_user_id 
                  );                
                  
                  $res_rew = $this->Funder->update_rewards_data_by_id($reward_id,$up_arr);
                }
              }
            }
            redirect('payment_flow/razorpay_payment_confirmation/'.$pre_funding_details['fl_project_id']);
            
          }else{
          
            redirect('Error/rejected/'.$pre_funding_details['fl_project_id']);
          }
          
        }
        else{
          show_error();
        }
      }
    }
  } 

  public function verifyPaymentSignature($paymentId, $orderId, $signature) {
    require(APPPATH."/third_party/razorpay/config.php");
    $api = new Api($keyId, $keySecret);

    $attributes = array(
      'razorpay_order_id' => $orderId,
      'razorpay_payment_id' => $paymentId,
      'razorpay_signature' => $signature
    );
    try {
        $api->utility->verifyPaymentSignature($attributes);
        return true;
    } catch (SignatureVerificationError $e) {
        return false;
    }
  }


  public function get_claimed_rewards_rz($flID = null){

    try{
      $query = $this->db->query('SELECT * FROM `fad_funders_list` WHERE `fl_id` = "'.$flID.'"');

      if($query->num_rows() > 0){
        return $query->row_array();
      }
    }catch(Exception $e){
      log_message('error',$e->getMessage());
    }
    return array();
  }

  public function razorpay_payment_confirmation($project_id) {
 
    $this->load->model('Projectmodel','project');
    $this->load->model('Funder_model','funder');
    $this->load->model('Usermodel','user');
    $data = array();
        
    if (!$this->session->userdata('user_id')) {
        $data['user_id'] = $this->input->cookie('anony_id');
        $data['project_id'] = $project_id;
        $data['project_info'] = $this->project->get_project_info($project_id); 
        $data['payment_details'] = $this->funder->get_payment_details($project_id,$data['user_id']); 
        $data['reward_details'] = $this->funder->get_claimed_rewards($project_id,$data['user_id']);
        $data['rating']  = $this->funder->get_rating($project_id,$data['user_id']);
        delete_cookie('funder_payment_id');
        delete_cookie('anony_id');
        delete_cookie('amount_funded');
    } else 
    {
      
      $data['user_id'] = $this->session->userdata('user_id');
      $data['user_type'] = $this->session->userdata('user_type');
      $data['project_id'] = $project_id;
    
      $data['user_info'] = $this->user->get_user_info($data['user_id']);
      
      $fname = $data['user_info']['fname'];
      $lname = $data['user_info']['lname'];
      
      $to_title = trim($data['user_info']['email']);
  
      $data['project_info'] = $this->project->get_project_details($project_id); 
      
      $campaign_title = strtoupper($data['project_info']['name']);
      $camp_id = $data['project_info']['id'];
  
      $data['payment_details'] = $this->funder->get_payment_details($project_id,$data['user_id']);
  
      $email_array = array(
        'fname'                 => $fname,
        'lname'                 => $lname,
        'campaign_title'        => $campaign_title,
        'camp_id'               => $project_id,
        'cate_id'               => $data['project_info']['cate_id'],  
        'fpi_hash_payu'         => $data['payment_details']['fpi_hash_payu'],
        'size'                  => $data['payment_details']['fl_size'],
        'meterial'              => $data['payment_details']['fl_meterial'],
        'color'                 => ucwords(str_replace('_',' ',$data['payment_details']['fl_color']))
        ); 
  
      $data['rating']  = $this->funder->get_rating($project_id,$data['user_id']);
    
      if($project_id == 89 && $this->session->userdata('reward_id') != 525 && $this->session->userdata('reward_id') != 561){
        $data['display_link_linen'] = $this->funder->get_display_link($project_id,$this->session->userdata('reward_id'));
      }
      
      // echo '<pre>'; print_r($data); die;
  
      delete_cookie('funder_payment_id');
      delete_cookie('anony_id');
      delete_cookie('amount_funded');
    
      // payment_id_exists  
      /* Reason : For duplicate Check */
      $value = $this->payment_id_exists($data['payment_details']['fpi_payment_id']);
    
      if($value['result_count'] == 0){
      
        /*** SEND CONFIRMATION OTP MESSAGE **/
        
        $user_id = $data['user_id'];
        $code = $data['user_info']['country_code'];
        $mobile_number = $data['user_info']['mobile'];
        $email = $data['user_info']['email'];
        $xmobile_number = $code.$mobile_number;
        
        if ($data['project_info']['cate_id']==12 || $data['project_info']['cate_id']==66) {
          $altermsgtext ="pre-order";
        }
        else {
          $altermsgtext ="contribution";
        }
  
        $message = "Your $altermsgtext on Fueladream.com has been received towards $campaign_title! Thank you for making a difference!";
        $bcc = "admin@fueladream.com";
        /*** Check if campaign title empty **/
        /*
          Updated by Neha
          Updated On : 14/10/2020
          Reason : Avoid Repeating SMS and Email confirmation
        */
        if(!empty($campaign_title)){
          //preorder / contribution based on cate_id,reward existence
          $emailvar1 = 'contribution';
          
          if(isset($_SESSION['reward_id']))
          {
            if($data['project_info']['cate_id']==13 || $data['project_info']['cate_id']==12 || $data['project_info']['cate_id']==66)
            {
              $emailvar1 = 'pre-order';
              $email_array['is_product']=1;
              
              $email_array['comobileno'] = $this->funder->get_shipping_contact_info($data['project_id']);
              $email_array['comobno'] = $email_array['comobileno']['shipping_contact'];
            }
          }
  
          $this->load->config('email');
          $email_template = $this->load->view('Emails/thanks-contribution', $email_array, TRUE);
          $this->load->library('email');
          $this->email->clear();
          $this->email->initialize(array('mailtype' => 'html'));
          $this->email->set_newline("\r\n");
          $this->email->from("getintouch@fueladream.com", "Fueladream");
          $this->email->to($email);
          $this->email->bcc($bcc);
          $this->email->subject("Thank you for your $emailvar1");
          $this->email->message($email_template);
          $this->email->send();
          
          $this->funder->update_payment_flag($data['payment_details']['fpi_payment_id']);
          
        }
      }
    }
  
    $data['step_name'] = "Payment confirmation-Contribute";
    $data['title'] = $data['project_info']['pname'];
    $data['meta_tags'] = 1;
  
    /** END  CONFIRMATION OTP MESSAGE */
    $this->load->view('elements/header_success',$data);
    $this->load->view('elements/navigation',$data);
    $this->load->view('funder/rz-confirmation',$data);
    $this->load->view('elements/footer_front',$data);
  }
  
//PAYPAL PAYMENT CONFIRMATION BY SNEHAL HUDE 30-10-2020


public function paypal_payment_confirmation($project_id) {

    $this->load->model('funder_model','funder');
    $rep_url = $_SERVER['REQUEST_URI'];
    

    $parseurl = explode('?', $rep_url); 
  
    $sep = explode('&',$parseurl[1]);
  
    $paymentId = explode('=',$sep[0])[1];
    $token = explode('=',$sep[1])[1];
    $PayerID = explode('=',$sep[2])[1];
    
     $this->session->set_userdata('paymentId', $paymentId);
     $this->session->set_userdata('token', $token);
     $this->session->set_userdata('PayerID', $PayerID);
   
      if($this->session->userdata('paypal')==1)
      {

       // $rew_quantity = $this->session->userdata('rew_quantity');

        $CCRescode = $this->get_paypal_payment_data();
       
        // print_r($CCRescode);
        // die;
        if(empty($CCRescode))
          die("Error: No response.");
        else
        {


           foreach($CCRescode->transactions as $key) {
          
            if($key->related_resources[0]->sale->state == 'completed'){

              $payment_id = $key->related_resources[0]->sale->id;
              // print_r($payment_id); die;
                
                $this->funder->update_fad_funder_list(array('fl_payment_confirmation'=>1),array('fl_project_id' => $project_id,'fl_id'=>$this->session->userdata('funder_payment_id') ));

                
                $this->funder->update_fad_funder_payment_information(array('fpi_transaction_status'=>1,'fpi_payment_id'=>$payment_id),array('fpi_user_id' => $this->session->userdata('user_id'),'fpi_transaction_id_gen'=>$this->session->userdata('funder_payment_id')));


            
            }else{

            }
          }
        }
        // die;
      }

  /*$project_id = $this->session->userdata('project_id');*/
  $this->load->model('Projectmodel','project');
  $this->load->model('Funder_model','funder');
  $this->load->model('Usermodel','user');
  $data = array();
        //Common Code
        //Check if user is logged in or not

  if (!$this->session->userdata('user_id')) {

   
    $data['user_id'] = $this->input->cookie('anony_id');
    $data['project_id'] = $project_id;
    $data['project_info'] = $this->project->get_project_info($project_id); 
    $data['payment_details'] = $this->funder->get_payment_details($project_id,$data['user_id']); 
    $data['reward_details'] = $this->funder->get_claimed_rewards($project_id,$data['user_id']);
    $data['rating']  = $this->funder->get_rating($project_id,$data['user_id']);
    delete_cookie('funder_payment_id');
    delete_cookie('anony_id');
    delete_cookie('amount_funded');
  } else { 
    $data['user_id'] = $this->session->userdata('user_id');  
    $data['user_type'] = $this->session->userdata('user_type');
    $data['project_id'] = $project_id;
    $data['user_info'] = $this->user->get_info($data['user_id']); 
    $fname = $data['user_info']['fname'];
    $lname = $data['user_info']['lname'];
    $to_title = trim($data['user_info']['email']);
    $data['project_info'] = $this->project->get_project_info($project_id); 
    $campaign_title = strtoupper($data['project_info']['name']);

    $camp_id = $data['project_info']['id'];


    $data['payment_details'] = $this->funder->get_payment_details($project_id,$data['user_id']);

    $email_array = array(
      'fname'                 => $fname,
      'lname'                 => $lname,
      'campaign_title'        => $campaign_title,
      'camp_id'               => $project_id,
      'cate_id'               => $data['project_info']['cate_id'],  
      'fpi_hash_payu'         => $data['payment_details']['fpi_hash_payu'],
      'size'                  => $data['payment_details']['fl_size'],
      'meterial'              => $data['payment_details']['fl_meterial'],
      'rew_quantity'          =>$rew_quantity,
      'color'                 => ucwords(str_replace('_',' ',$data['payment_details']['fl_color']))
      ); 

   
    
    $data['rating']  = $this->funder->get_rating($project_id,$data['user_id']);
    if($project_id == 89 && $this->session->userdata('reward_id') != 525 && $this->session->userdata('reward_id') != 561){
      $data['display_link_linen'] = $this->funder->get_display_link($project_id,$this->session->userdata('reward_id'));
    }
    
    delete_cookie('funder_payment_id');
    delete_cookie('anony_id');
    delete_cookie('amount_funded');
    
    // payment_id_exists
   
    $value = $this->payment_id_exists($data['payment_details']['fpi_payment_id']);


    
    if($value['result_count'] == 0){

      /*** SEND CONFIRMATION OTP MESSAGE **/
      
      $user_id = $this->session->userdata('user_id');
      $code = $data['user_info']['country_code'];

      $mobile_number = $data['user_info']['mobile'];
      $email = $data['user_info']['email'];
      $xmobile_number = $code.$mobile_number;
      
      if ($data['project_info']['cate_id']==12 || $data['project_info']['cate_id']==13 || $data['project_info']['cate_id']==66) {
        $altermsgtext ="pre-order";
      }
      else {
        $altermsgtext ="contribution";
      }
      $message = "Your $altermsgtext on Fueladream.com has been received towards $campaign_title! Thank you for making a difference!";
      $bcc = "neha@fueladream.com";
      /*** Check if campaign title empty **/
    
        if(!empty($campaign_title)){
       
         //preorder / contribution based on cate_id,reward existence
         $emailvar1 = 'contribution';
         if(isset($_SESSION['reward_id']))
         {
            if($data['project_info']['cate_id']==13 || $data['project_info']['cate_id']==12 || $data['project_info']['cate_id']==66)
            {
                $emailvar1 = 'pre-order';
                $email_array['is_product']=1;
                $email_array['comobileno'] = $this->funder->get_shipping_contact_info($data['project_id']);
                $email_array['comobno'] = $email_array['comobileno']['shipping_contact'];
            }
         }
         $this->load->config('email');
         $email_template = $this->load->view('Emails/thanks-contribution', $email_array, TRUE);
         $this->load->library('email');
         $this->email->clear();
         $this->email->initialize(array('mailtype' => 'html'));
         $this->email->set_newline("\r\n");
         $this->email->from("getintouch@fueladream.com", "Fueladream");
         $this->email->to($email);
         // $this->email->to($email);
        // $this->email->cc("nishanth@fueladream.com");
         $this->email->bcc($bcc);
         $this->email->subject("Thank you for your $emailvar1");
         $this->email->message($email_template);
         $this->email->send();
         $this->funder->update_payment_flag($data['payment_details']['fpi_payment_id']);
         

         $_POST['claim_status'] = 2;
       
          $_POST['last_claimed_user_id'] = $this->session->userdata('user_id');

           $rew_quantity = ( !empty($this->session->userdata('rew_quantity')) ? $this->session->userdata('rew_quantity') : 0 );
          $reward_id = ( !empty($this->session->userdata('reward_id')) ? $this->session->userdata('reward_id') : 0 );

          log_message('error',$_POST['claimed_reward'].'Payment Confirmation Call');
       
            if( $rew_quantity >1)
            {
                for($num=1;$num<= $rew_quantity;$num++)
                {
                  $get_reward_details = $this->funder->get_reward_details_auto($project_id,$reward_id);  
                 
                  $_POST['claimed_reward'] = $get_reward_details['claimed_reward'] + 1;
                  $this->project->update_reward($reward_id);
                }
            }
            else
            {
                $get_reward_details = $this->funder->get_reward_details_auto($project_id,$reward_id);  
                $_POST['claimed_reward'] = $get_reward_details['claimed_reward'] + 1;
                $this->project->update_reward($reward_id);
            }
          $data['reward_details'] = $this->funder->get_claimed_rewards($project_id,$data['user_id']);
        }
      }
    }
    $data['step_name'] = "Payment confirmation-Contribute";
    $data['title'] = $data['project_info']['pname'];
    $data['meta_tags'] = 1;

    // print_r($data); die;

  //   /** END  CONFIRMATION OTP MESSAGE */
    $this->load->view('elements/header_success',$data);
    $this->load->view('elements/navigation',$data);
    $this->load->view('funder/fc-confirmation',$data);
    $this->load->view('elements/footer_front',$data);
  }

function get_paypal_payment_data()
{
  $this->load->library('session');
  // autenticazione per ottenere user token
       
       //header('Content-Type: application/json'); // Specify the type of data

       $paymentId = $this->session->userdata('paymentId');
       $PayerID = $this->session->userdata('PayerID');
       $PayPal_Request_Id = 'FAD-'.rand(10000,99999999); // Prepare the authorisation token
       // print_r($paymentId.'---');
       // print_r($PayerID);
       // die;
      
       // sandbox URL
       $ch = curl_init('https://api.sandbox.paypal.com/v1/payments/payment/'.$paymentId.'/execute'); // Initialise cURL

       // Live URL
       // $ch = curl_init('https://api.paypal.com/v1/payments/payment/'.$paymentId.'/execute'); // Initialise cURL
       
       $post = '{
                    "payer_id":"'.$PayerID.'"
                }';

       //$post = json_encode($post); // Encode the data array into a JSON string
       $authorization = $this->session->userdata('token_key'); // Prepare the authorisation token
       curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization,'PayPal-Request-Id : '.$PayPal_Request_Id )); // Inject the token into the header
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
       curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST
       curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
       $result = curl_exec($ch); // Execute the cURL statement
       curl_close($ch); // Close the cURL connection
       //print_r(json_decode($result)); // Return the received data
  if(empty($result))
    {
      die("Error: No response.");
    }
  else
  {
    $obj=json_decode( $result );
     // print_r($obj);
    return $obj;
  }

}

//PAYPAL PAYMENT CONFIRMATION BY SNEHAL HUDE 30-10-2020

///////////////// NEW ////////////////
public function response_instamojo_webhook($rew_quantity=1){
   $this->load->library('session');
   $this->load->model('Funder_model', 'Funder');
   $this->load->model('Instamojo_model', 'Instamojomodel');
   $this->load->library('Utility');
   $util = new Utility();

   $insta_payment_request_id = $_GET['payment_request_id'];
   $insta_payment_id = $_GET['payment_id'];

   $curl = curl_init();
  
   // LIVE
   curl_setopt( $curl, CURLOPT_URL, 'https://www.instamojo.com/api/1.1/payments/'.$insta_payment_id.'/' );

   // TEST
   // curl_setopt( $curl, CURLOPT_URL, 'https://test.instamojo.com/api/1.1/payments/'.$insta_payment_id.'/' );
   
   curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
   curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
   
   // LIVE
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'X-Api-Key: f1c9b5ac69b1fcd0f5f562b6d7fdad47',
    'X-Auth-Token: 2618be3887c814801d67403ca838a206'
  ));

   // TEST
   // curl_setopt($curl, CURLOPT_HTTPHEADER, array(
   //    'X-Api-Key: 6796bce7692d0698e8a9059c02c63039',
   //    'X-Auth-Token: 73a560fb387bfd8faf27518977d8e70c'
   //  ));
   
   $response = curl_exec( $curl );
   
   curl_close( $curl );

   $res = json_decode($response, true);

 if($res['success'] == 1){
 
  $pre_funding_details = $this->Funder->get_pre_funding_data_by_request_id($insta_payment_request_id);
  // print_r($pre_funding_details);
  
  if(count($pre_funding_details) != 0){
      //Update the Funder List Table
      unset($_POST);
      // $data_array['fl_payment_confirmation'] = 1;
      $payment_status = 0;
      
        if(!empty($res['payment'])){
          $payment_status = ($res['payment']['status'] == "Credit") ? 1 : 0;
        }
      
      $_POST['fpi_card_type'] = $pre_funding_details['fl_card_type'];
      $_POST['fpi_amount_funded'] = $pre_funding_details['fl_funded_amount'];
      $_POST['fpi_payment_id'] = $res['payment']['payment_id'];
      $_POST['fpi_payment_mode'] = 'CC';
      $_POST['fpi_transaction_status'] = $payment_status;
      $_POST['fpi_merchant_id'] = $pre_funding_details['fl_project_id'];
      $_POST['fpi_email'] = $res['payment']['buyer_email'];
      $_POST['fpi_mobile'] = $res['payment']['buyer_phone'];
      $_POST['fpi_user_id'] = $pre_funding_details['fl_funder_unique_user_id'];
      $_POST['fpi_project_id'] = $pre_funding_details['fl_project_id'];
      $_POST['device_info'] = json_encode($_SERVER);
      $_POST['fpi_hash_payu'] = substr($this->getToken(),0,-8).'-'.substr($this->getToken(),0,-8).'-'.substr($this->getToken(),0,-8);
      $_POST['fpi_transaction_id_gen'] = $this->session->userdata('funder_payment_id');
      
      $_POST['fpi_matching_partner_amount'] = add_matching_partner_contribution($pre_funding_details['fl_project_id'], $pre_funding_details['fl_funded_amount']);
    
      // echo '<pre>'; print_r($res); print_r($_POST); print_r($this->session->userdata()); 

      $value = $this->Funder->payment_id_duplicate_check($_POST['fpi_payment_id']);
    
      if($value['result_count'] == 0){
         $value['fpi_id'] = $this->Funder->add_confirmed_funding_details($_POST);
      }
       // print_r($value); die();
      // else{
      //     $this->Funder->update_confirmed_funding_details($value['fpi_id'],$_POST['fpi_payment_id']);
      // }
   
      if($payment_status == 1){

        $data_array_fl['fl_payment_confirmation'] = $payment_status;
        $data_array_fpi['fpi_transaction_status'] = $payment_status;

        $this->db->update('fad_funders_list', $data_array_fl, array('fl_id' => $this->session->userdata('funder_payment_id')));
        
        $this->db->update('fad_funder_payment_information', $data_array_fpi, array('fpi_id' => $value['fpi_id']));

        $data['reward_details'] = $this->Instamojomodel->get_claimed_rewards($this->session->userdata('funder_payment_id'));

         $rew_quantity = ( !empty($data['reward_details']['fl_rew_count']) ? $data['reward_details']['fl_rew_count'] : 0 );
         $reward_id = ( !empty($data['reward_details']['fl_reward_id']) ? $data['reward_details']['fl_reward_id'] : 0 );

        if($reward_id){
          if($rew_quantity >= 1){

            // $rew_id = $data['fl_reward_id'];
            $last_claimed_user_id = $data['reward_details']['fl_funder_unique_user_id'];

            $rew_details = $this->Funder->get_reward_details_rewardwise($reward_id);
           
            if($rew_details['applicable_person'] >= $rew_details['claimed_reward']){

                $up_arr  = array(
                                'claimed_reward' => $rew_details['claimed_reward']+$rew_quantity,
                                'last_claimed_user_id' => $last_claimed_user_id 
                               );
                
                // print_r($up_arr); die;
                $res_rew = $this->Funder->update_rewards_data_by_id($reward_id,$up_arr);
            }
          }

        }
        
        log_message('error',$_POST['claimed_reward'].'Payment Confirmation Call');

        // $this->funder->update_fad_funder_list(array('fl_payment_confirmation'=>1),array('fl_project_id' => $this->session->userdata('project_id'),'fl_id'=>$this->session->userdata('funder_payment_id') ));
        
        // $this->funder->update_fad_funder_payment_information(array('fpi_transaction_status'=>1),
        //   array('fpi_user_id' => $this->session->userdata('user_id'),'fpi_transaction_id_gen'=>$this->session->userdata('funder_payment_id'),'fpi_id'=> $value['fpi_id']));

        redirect('payment_flow/payment_confirmation/'.$pre_funding_details['fl_project_id']);
      } else{
        redirect('Error/rejected/'.$pre_funding_details['fl_project_id']);
      }
    
    }else{
      show_error();
    }
  }
 
}

public function payment_confirmation($project_id) {
 // echo '<pre>'; print_r($_SESSION);
 //  print_r($this->input->cookie());
 //  echo $project_id;die();
 
    $this->load->model('Projectmodel','project');
    $this->load->model('Funder_model','funder');
    $this->load->model('Usermodel','user');
    $data = array();
       //Check if user is logged in or not
    if (!$this->session->userdata('user_id')) {
        $data['user_id'] = $this->input->cookie('anony_id');
        $data['project_id'] = $project_id;
        $data['project_info'] = $this->project->get_project_info($project_id); 
        $data['payment_details'] = $this->funder->get_payment_details($project_id,$data['user_id']); 
        $data['reward_details'] = $this->funder->get_claimed_rewards($project_id,$data['user_id']);
        $data['rating']  = $this->funder->get_rating($project_id,$data['user_id']);
        delete_cookie('funder_payment_id');
        delete_cookie('anony_id');
        delete_cookie('amount_funded');
    } else {
    
    $data['user_id'] = $this->session->userdata('user_id');
    $data['user_type'] = $this->session->userdata('user_type');
    $data['project_id'] = $project_id;
    
    $data['user_info'] = $this->user->get_user_info($data['user_id']);
    
    $fname = $data['user_info']['fname'];
    $lname = $data['user_info']['lname'];
    
    $to_title = trim($data['user_info']['email']);

    $data['project_info'] = $this->project->get_project_details($project_id); 
    
    $campaign_title = strtoupper($data['project_info']['name']);
    $camp_id = $data['project_info']['id'];

    $data['payment_details'] = $this->funder->get_payment_details($project_id,$data['user_id']);

    $email_array = array(
      'fname'                 => $fname,
      'lname'                 => $lname,
      'campaign_title'        => $campaign_title,
      'camp_id'               => $project_id,
      'cate_id'               => $data['project_info']['cate_id'],  
      'fpi_hash_payu'         => $data['payment_details']['fpi_hash_payu'],
      'size'                  => $data['payment_details']['fl_size'],
      'meterial'              => $data['payment_details']['fl_meterial'],
      'color'                 => ucwords(str_replace('_',' ',$data['payment_details']['fl_color']))
      ); 

    $data['rating']  = $this->funder->get_rating($project_id,$data['user_id']);
   
    if($project_id == 89 && $this->session->userdata('reward_id') != 525 && $this->session->userdata('reward_id') != 561){
      $data['display_link_linen'] = $this->funder->get_display_link($project_id,$this->session->userdata('reward_id'));
    }
    
    // echo '<pre>'; print_r($data); die;

    delete_cookie('funder_payment_id');
    delete_cookie('anony_id');
    delete_cookie('amount_funded');
    
    // payment_id_exists  
    /* Reason : For duplicate Check */
    $value = $this->payment_id_exists($data['payment_details']['fpi_payment_id']);
    
    if($value['result_count'] == 0){
    
      /*** SEND CONFIRMATION OTP MESSAGE **/
      
      $user_id = $data['user_id'];
      $code = $data['user_info']['country_code'];
      $mobile_number = $data['user_info']['mobile'];
      $email = $data['user_info']['email'];
      $xmobile_number = $code.$mobile_number;
      
      if ($data['project_info']['cate_id']==12 || $data['project_info']['cate_id']==66) {
        $altermsgtext ="pre-order";
      }
      else {
        $altermsgtext ="contribution";
      }

      $message = "Your $altermsgtext on Fueladream.com has been received towards $campaign_title! Thank you for making a difference!";
      $bcc = "admin@fueladream.com";
      /*** Check if campaign title empty **/
      /*
        Updated by Neha
        Updated On : 14/10/2020
        Reason : Avoid Repeating SMS and Email confirmation
      */
    if(!empty($campaign_title)){
         //preorder / contribution based on cate_id,reward existence
         $emailvar1 = 'contribution';
         
         if(isset($_SESSION['reward_id']))
         {
            if($data['project_info']['cate_id']==13 || $data['project_info']['cate_id']==12 || $data['project_info']['cate_id']==66)
            {
                $emailvar1 = 'pre-order';
                $email_array['is_product']=1;
               
                $email_array['comobileno'] = $this->funder->get_shipping_contact_info($data['project_id']);
                $email_array['comobno'] = $email_array['comobileno']['shipping_contact'];
            }
         }

         $this->load->config('email');
         $email_template = $this->load->view('Emails/thanks-contribution', $email_array, TRUE);
         $this->load->library('email');
         $this->email->clear();
         $this->email->initialize(array('mailtype' => 'html'));
         $this->email->set_newline("\r\n");
         $this->email->from("getintouch@fueladream.com", "Fueladream");
         $this->email->to($email);
         $this->email->bcc($bcc);
         $this->email->subject("Thank you for your $emailvar1");
         $this->email->message($email_template);
         $this->email->send();
         
         $this->funder->update_payment_flag($data['payment_details']['fpi_payment_id']);
         
        }
      }
    }

    $data['step_name'] = "Payment confirmation-Contribute";
    $data['title'] = $data['project_info']['pname'];
    $data['meta_tags'] = 1;

    /** END  CONFIRMATION OTP MESSAGE */
    $this->load->view('elements/header_success',$data);
    $this->load->view('elements/navigation',$data);
    $this->load->view('funder/fc-confirmation',$data);
    $this->load->view('elements/footer_front',$data);
  }

/////////////////////

public function response_instamojo($rew_quantity=1){
 $this->load->library('Utility');
 $util = new Utility();
 $insta_payment_request_id = $_GET['payment_request_id'];
 $insta_payment_id = $_GET['payment_id'];
 $curl = curl_init();
 

 //curl_setopt( $curl, CURLOPT_URL, 'https://test.instamojo.com/api/1.1/payments/'.$insta_payment_id.'/' );
 

 curl_setopt( $curl, CURLOPT_URL, 'https://www.instamojo.com/api/1.1/payments/'.$insta_payment_id.'/' );
 
 curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
 curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
 
//  TEST
 // curl_setopt($curl, CURLOPT_HTTPHEADER, array(
 //    'X-Api-Key: 6796bce7692d0698e8a9059c02c63039',
 //    'X-Auth-Token: 73a560fb387bfd8faf27518977d8e70c'
 //  ));


 //  LIVE
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
  'X-Api-Key: f1c9b5ac69b1fcd0f5f562b6d7fdad47',
  'X-Auth-Token: 2618be3887c814801d67403ca838a206'
    ));

 $response = curl_exec( $curl );
 curl_close( $curl );
 $res = json_decode($response, true);
 // echo '<pre>'; print_r($response);die();
    //Check for the payment request id
 $this->load->model('Funder_model', 'Funder');
 $pre_funding_details = $this->Funder->get_pre_funding_data_by_request_id($insta_payment_request_id);

 // echo '<pre>'; print_r($pre_funding_details); die();
 if(count($pre_funding_details) != 0){
      //Update the Funder List Table
  unset($_POST);
  $data_array['fl_payment_confirmation'] = 1;
     $payment_status = 0;
    if(!empty($res['payment'])){
      $payment_status = ($res['payment']['status'] == "Credit") ? 1 : 0;
    }
    $data_array['fl_payment_confirmation'] = $payment_status;
    
  unset($_POST);
          //  //Insert Data in Funder Payment Information

  $_POST['fpi_card_type'] = $pre_funding_details['fl_card_type'];
  $_POST['fpi_amount_funded'] = $pre_funding_details['fl_funded_amount'];
  $_POST['fpi_payment_id'] = $res['payment']['payment_id'];
  $_POST['fpi_payment_mode'] = 'CC';
  $_POST['fpi_transaction_status'] = $payment_status;
  $_POST['fpi_merchant_id'] = $pre_funding_details['fl_project_id'];
  $_POST['fpi_email'] = $res['payment']['buyer_email'];
  $_POST['fpi_mobile'] = $res['payment']['buyer_phone'];
  $_POST['fpi_user_id'] = $pre_funding_details['fl_funder_unique_user_id'];
  $_POST['fpi_project_id'] = $pre_funding_details['fl_project_id'];
  $_POST['device_info'] = json_encode($_SERVER);
  //$_POST['fpi_hash_payu'] = $util->get_alphanumeric(4).'-'.$util->get_alphanumeric(4).'-'.$util->get_alphanumeric(4);
  $_POST['fpi_hash_payu'] = substr($this->getToken(),0,-8).'-'.substr($this->getToken(),0,-8).'-'.substr($this->getToken(),0,-8);
  $_POST['fpi_matching_partner_amount'] = add_matching_partner_contribution($this->session->userdata('project_id'), $pre_funding_details['fl_funded_amount']);
 
        $this->db->update('fad_funders_list', $data_array, array('fl_id' => $this->session->userdata('funder_payment_id')));
        $_POST['fpi_transaction_id_gen'] = $this->session->userdata('funder_payment_id');
        $value = $this->payment_id_exists($_POST['fpi_payment_id']);
 // print_r($value); die();
        if($value == 2){
          $this->Funder->add_confirmed_funding_details($_POST);
        }
    // }
  // print_r($payment_status);die();
  if($payment_status == 1){
    redirect('funder/payment_confirmation/'.$pre_funding_details['fl_project_id']);
  } else{
    redirect('Error/rejected/'.$pre_funding_details['fl_project_id']);
  }
}else{
  show_error();
}
}

//////////// END ///////////

  public function paytm($data = array())
  {
    // following files need to be included
    require_once APPPATH."third_party/paytmlib/lib/config_paytm.php";
    require_once APPPATH."third_party/paytmlib/lib/encdec_paytm.php";

// echo '<pre>'; print_r($data); die();

    $paytmData = array(
    'CUST_ID' => $data['fl_project_id']."_".date("md").date("His"),
    'ORDER_ID' => ORDER_ID
    );
    
    $this->session->set_userdata('paytmArray', $paytmData); 
    
    $payment_id = $data['funder_payment_id'];
    $reward_id = $data['fl_reward_id'];

    $sess_ch_pay_id = $this->db->query("SELECT fpi_id FROM fad_funder_payment_information WHERE fpi_transaction_id_gen='$payment_id'")->result_array();

    if( !isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], "funder/about_contribution") === -1 ) {
    redirect('/page404');
    }
    if($sess_ch_pay_id){
      redirect('/funder/rewards_management/'.$data['fl_project_id'].'/'.$fl_reward_id);
    }

    $this->load->model('funder_model','funder');
    header("Pragma: no-cache");
    header("Cache-Control: no-cache");
    header("Expires: 0");

    $checkSum = "";
    $paramList = array();
   
    $funding_details = $this->funder->get_pre_funding_data_detail($payment_id);
    $payment_details =$this->funder->get_fad_front_users_details(array("id"=>$data['fl_funder_unique_user_id']));
    
    $ORDER_ID = $this->session->userdata['paytmArray']['ORDER_ID'];
    $CUST_ID = $this->session->userdata['paytmArray']['CUST_ID'];
    $TXN_AMOUNT = $data['fl_rew_count'] * $funding_details['fl_funded_amount'];
    //$TXN_AMOUNT = $funding_details['fl_funded_amount'];
    // Create an array having all required parameters for creating checksum.
    //print_r($_SESSION);die();
    $paramList["MID"] = PAYTM_MERCHANT_MID;
    $paramList["ORDER_ID"] = $ORDER_ID;
    $paramList["CUST_ID"] = $CUST_ID;
    $paramList["INDUSTRY_TYPE_ID"] = INDUSTRY_TYPE_ID;
    $paramList["CHANNEL_ID"] = CHANNEL_ID;
    $paramList["TXN_AMOUNT"] = $TXN_AMOUNT;
    $paramList["MSISDN"] = $payment_details[0]['mobile'];
    $paramList["EMAIL"] = $payment_details[0]['email'];
    $paramList["WEBSITE"] = PAYTM_MERCHANT_WEBSITE;
    $paramList["MERC_UNQ_REF"] = $payment_details[0]['email']."||".$data['fl_project_id']."||".$payment_details[0]['mobile'];
    $paramList["VERIFIED_BY"] = "EMAIL";
    $paramList["IS_USER_VERIFIED"] = "YES";
    $paramList["CALLBACK_URL"] = base_url()."funder/payment_confirmation/".$data['fl_project_id'];
    // $paramList["MSISDN"] = $MSISDN; //Mobile number of customer
    // $paramList["EMAIL"] = $EMAIL; //Email ID of customer
    // $paramList["VERIFIED_BY"] = "EMAIL"; //
    // $paramList["IS_USER_VERIFIED"] = "YES"; //

    $this->load->library('Utility');
    $util = new Utility();

    $user_id = $data['fl_funder_unique_user_id'];
    //$user_type = $this->session->userdata('user_type');
    $user_type = 1;
    $proj_id = $data['fl_project_id'];
    $data = array();
    //Common Code
    //Check if user is logged in or not
    $this->load->model('UserModel','user');
    $this->load->model('Projectmodel','project');
    $this->load->model('funder_model','funder');

    $data['user_id'] = $user_id;
    //$data['user_type'] = $this->session->userdata('user_type');
    $data['user_type'] = 1;
    $data['user_info'] = $this->user->get_info($user_id);
    $data['get_project_info'] = $this->project->get_project_info_fund($proj_id);
  
    /** Get Nationalities */
    $data['country_list'] = $this->funder->get_nationalities();
   
    $funders_details = $this->funder->get_fad_funders_list_details(array("fl_funder_unique_user_id"=>$user_id));
  
     // TODO update response and the order's payment status as SUCCESS in to database
    $result_array = array(
      'fpi_email'                    => $payment_details[0]['email'],
      'fpi_mobile'                   => $payment_details[0]['mobile'],
      'fpi_user_id'                  => $user_id,
      'fpi_payment_mode'             => "paytm",
      'fpi_transaction_key'          => "1",
      'fpi_transaction_id_payu'      => "1",
      //'fpi_hash_payu'                => $util->get_alphanumeric(4).'-'.$util->get_alphanumeric(4).'-'.$util->get_alphanumeric(4),
      'fpi_hash_payu'                => substr($this->getToken(),0,-8).'-'.substr($this->getToken(),0,-8).'-'.substr($this->getToken(),0,-8),
      'fpi_payment_id'               => $ORDER_ID,
      'fpi_merchant_id'              => $proj_id,
      'fpi_project_id'               => $proj_id,
      'fpi_amount_funded'            => $funding_details['fl_funded_amount'],
      'fpi_transaction_status'       => 0,
      'device_info' => json_encode($_SERVER),
      'fpi_matching_partner_amount'  => add_matching_partner_contribution($proj_id, $TXN_AMOUNT),
      //'fpi_transaction_id_gen'       => $funders_details[0]['fl_id']
    ); 

    $this->load->model('funder_model','funder');
    $this->load->model('Projectmodel','project');
    $this->load->model('Usermodel','user');

    $data = array();

    $data['funder_payment_id'] = $data['funder_payment_id'];
    
    $data['user_id'] = $user_id;
    //$data['user_type'] = $this->session->userdata('user_type');
    $data['user_type'] = 1;
    
    // User data
    $user_data = $this->user->get_info($user_id);
    
    
    if($this->session->userdata('user_type') == 5){
      $name =  "FAD";
    } else {
      $name =  $user_data['user_name'];
    }
    
    $value = $this->payment_id_exists($result_array['fpi_payment_id']);
    //$rew_quantity = $this->session->userdata('rew_quantity');
    $rew_quantity = 1;
    
    if($value == 2){
      if($rew_quantity>1){
        $result_array['fpi_transaction_id_gen']  = $data['funder_payment_id'];
        $add_payumoney_details = $this->funder->add_payumoney_details($result_array);
      }else{
        $result_array['fpi_transaction_id_gen']  = $data['funder_payment_id'];
        $add_payumoney_details = $this->funder->add_payumoney_details($result_array);
      }
    }
    //Here checksum string will return by getChecksumFromArray() function.
    $checkSum = getChecksumFromArray($paramList,PAYTM_MERCHANT_KEY);
    echo "<html>
            <head>
              <title>Merchant Check Out Page</title>
            </head>
            <body>
              <center><h1>Please do not refresh this page...</h1></center>
              <form method='post' action='".PAYTM_TXN_URL."' name='f1'>
                <table border='1'>
                  <tbody>";
                    foreach($paramList as $name => $value) {
                      echo '<input type="hidden" name="' . $name .'" value="' . $value .'">';
                    }
                    echo "<input type='hidden' name='CHECKSUMHASH' value='". $checkSum . "'>
                  </tbody>
                </table>
                <script type='text/javascript'>
                  document.f1.submit();
                </script>
              </form>
            </body>
          </html>";
    $this->session->set_userdata('paytm',true);       
  }

  public function hdfc_call($data=array()){
    $this->load->model('Funder_model','funder');
    // SECURE PARAMETERS TO BE SAVED IN CONFIG File
    $HASHING_METHOD = 'sha512'; // md5,sha1
    $payment = false;
    $account_id = '19356';
    $secret_key = '946e73afba3c62ee27c5da514afcdea5';
    
    // Get funding data
    $funding_data = $this->funder->get_pre_funding_data($data['funder_payment_id']);
    $transaction_amount = $data['reward_count'] * $funding_data['fl_funded_amount'];
    $transaction_number = $data['campaign_id'];
    $encrypted_transaction_number = $this->encryption->encrypt($transaction_number);
    
    // Add transaction number to session
    // TODO: store transaction reference into database
    $this->session->set_userdata('transaction_no',$encrypted_transaction_number);
    $check = explode(',',$funding_data['hometown']);
    
    if(count($check) > 2){
      //3 Words
    }else{
      //2 Words or less
      $check[2] = $check[count($check)-1];
    }
          
    $channel = "10";
    $mode = "LIVE";
    $currency = "INR";
    $key = "FuelaDream";
    $country = "IND";
    $return_url = base_url().'payment/response';

    $params = array(
      'channel'           => $channel,
      'account_id'        => $account_id,
      'reference_no'      => $transaction_number,
      'amount'            => $transaction_amount,
      'currency'          => $currency,
      'description'       => "FUELADREAM",
      'return_url'        => $return_url,
      'mode'              => $mode,
      'name'              => $funding_data['fname'],
      'address'           => $funding_data['address'],
      'city'              => substr($check[0],0,32),
      'state'             => substr($check[1],0,32),
      'postal_code'       => $funding_data['pin'],
      'country'           => substr($check[2],0,3),
      'email'             => $funding_data['email'],
      'phone'             => $funding_data['mobile'],
      'ship_name'         => $funding_data['fname'],
      'ship_address'      => $funding_data['address'],
      'ship_city'         => substr($check[0],0,32),
      'ship_state'        => substr($check[1],0,32),
      'ship_postal_code'  => $funding_data['pin'],
      'ship_country'      => strtoupper(substr($funding_data['country'],0,3)),
      'ship_phone'        => $funding_data['mobile']
    );
          
    ksort($params);
    $hash_data = $secret_key;
    
    foreach ($params as $key => $value){
      if (strlen($value) > 0) {
        $hash_data .= '|'.$value;
      }
    }
    
    $secure_hash = "";
    if (strlen($hash_data) > 0) {
      $secure_hash = strtoupper(hash($HASHING_METHOD, $hash_data));
    }
    
    $data = array(
      "params" => $params,
      "hash_data" => $secure_hash
    );
        
    $this->load->view('payment/payment_post', $data);
    $payment = true;
  }


  function string_sanitize($s) {
    $result = preg_replace("/[~!`$%^*()_{}]+/", "", html_entity_decode($s, ENT_QUOTES));
    $unquoted = str_replace('"', "", $result);
    $res = str_replace("'", "", $unquoted);
    return $res;
  }

  public function getToken(){
    $token = '';
    $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
    $codeAlphabet .= '0123456789';
    $max = strlen($codeAlphabet); // edited

    for ($i = 0; $i < 4; ++$i) {
      $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max - 1)];
      $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max - 1)];
      $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max - 1)];
    }

    return $token;
  }

  public function crypto_rand_secure($min, $max)
  {
    $range = $max - $min;
    if ($range < 1) {
        return $min;
    } // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd > $range);

    return $min + $rnd;
  }

  private function payment_id_exists($id){
    $this->load->model('Funder_model','funder');
    $get_payment_bool = $this->funder->unique_payment_id($id);
    /*print_r($get_payment_bool);exit();*/
    return $get_payment_bool;
  }



public function getDataFromCity($city) {
      $search_item = $city;

      try{
          if($search_item){
            $query = $this->db->query('SELECT `fcity`.`name` AS CityName,`fstate`.`name` AS Statename,
                                      `fcountry`.`name` AS Countryname
                                      FROM `fad_all_cities` `fcity`
                                      LEFT JOIN `fad_all_states` `fstate` on `fstate`.`id` = `fcity`.`state_id`
                                      LEFT JOIN `fad_all_countries` `fcountry` on `fcountry`.`id` = `fstate`.`country_id`
                                      WHERE CONCAT_WS("",`fcity`.`name`) LIKE "%'.$search_item.'%"');
          }
          if($query){
              if($query->num_rows() > 0){
                $res = $query->row_array();
              }
          }else{
             return;
          } 
          
        }catch(Exception $e){
          log_message('error',$e->getMessage());
        }

      // if($search_item){
      //   $res = $this->Backendupdate_model->getDataFromCity($search_item); 
      //   }
      // echo json_encode($res);

        return $res;
  
  }

}
