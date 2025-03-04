<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
form controller 
ADDED BY : NEHA
ADDED ON : 07-06-2018
PURPOSE : registration form automation for all (school,college,corporate and others)
*/
require APPPATH.'libraries/aws/aws-autoloader.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\Sts\StsClient;


class Form extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model(array("Form_model","Usermodel","Commonmodel"));
		$this->load->library(array('form_validation','session','Cdn','gps_auth')); // load form lidation libaray & session library
        $this->load->helper(array('url','html','form'));  // load url,html,form helpers optional
	}

	public function index($id=null){

		
	// echo $id;	
		if ($id) {
				$data['school'] = $this->Form_model->get_form_info($id);
				// echo '<pre>'; print_r($data['school']); die;
				$data['is_new'] = $data['school']['is_new'];
				// $data['banner_img'] = $data['school']['banner_img'];
				
				$str = $data['school']['name'];
			    $ret = '';
			    foreach(explode(' ', $str) as $word){
			        $ret .= strtoupper($word[0]);
			    	}
				$data['school_initials'] = $ret;
				$_POST['document_number'] = $this->input->post('document_number').'StudentsDoc';
				$data['about_me'] = $data['school']['about_co'];
				$type = $data['school']['type'];

				/*Name of the Developer:Snehal Hude
				The purpose for updates: Not storing the NGO address for the volunteers. Remove the address associa
				Date of Update: 03-12-2021
				Requested by Dept: Tech Team
				Approved by: Harish Nanjudaswamy */
				if($type == '6'){
					$address = '';
					$city = '';
					$pin = '';
					$country = '';
					$state = '';
					$city = '';
				}else{
					$address = $this->input->post('address');
					$city = $this->input->post('city');
					$pin = $this->input->post('pin');
					$country = $this->input->post('country');
					$state = $this->input->post('state');
					$city = $this->input->post('city');
				}
				//print_r($type);exit();
				if ($_POST){


					            $_POST['login_ip'] = $_SERVER['REMOTE_ADDR'];
					            $_POST['user_type'] = 4;
					            $username_array = explode("@", $_POST['email']);
					            $_POST['user_name'] = $username_array[0];
					            $_POST['verification_key'] = md5($_POST['email']);
					            $_POST['dob'] = "";
					           // $_POST['address'] = $this->input->post('address');
					            $_POST['address'] = $address;
					            $_POST['hometown'] = $city;
					            $_POST['pin'] = $pin;
					            $_POST['country'] = $country;
					            $_POST['country_code'] = 91;
					            $_POST['nationality'] = 80;
					            $_POST['state'] = $state;
					            $_POST['city'] = $city;
					            $_POST['intro'] = nl2br(htmlspecialchars($this->input->post('intro')));
					            $_POST['cmcenter'] = nl2br(htmlspecialchars($this->input->post('cmcenter')));
					            $_POST['section'] = (!empty($_POST['section'])) ? $_POST['section'] : "";

					            $formType = $_POST['type'];
					            unset($_POST['type']);

					            switch ($formType) {
										case '1':
											$_POST['occupation'] = "Student at ".$this->input->post('occupation')."";
											break;
										case '2':
											$_POST['occupation'] = "Student at ".$this->input->post('occupation')."";
											break;
										case '3':
											$_POST['occupation'] = $this->input->post('occupation');
											break;
									  	default:
											$_POST['occtual_pathcupation'] = $this->input->post('occupation');
											break;
								}

						 		if(isset($_FILES) && $_FILES['image']['name'] != ''){

									$file = "image";
									if($_FILES['image']['name'] != ''){
										$file = $file;
									}

									$uploadedFile = $_FILES['image']['tmp_name'];


									
									/*Name of the Developer: Shubham
									The purpose for updates: Image path was overrriding so we added the randomizer.
									Date of Update: 06/10/2023
									Requested by Dept: Technical
									Approved by: Harish
									*/
									// Randomizer Code Starts Here ////////////////////////////////////////////////
									$original_filename = $_FILES['image']['name'];
									$file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
									
									$randomNumber1 = mt_rand(1,getrandmax());
									$randomNumber2 = mt_rand(0,getrandmax());
									$randomNumber3 = mt_rand(1,getrandmax());
									
									$new_filename = $id."_".$randomNumber1."_".$randomNumber2."_".$randomNumber3."_".$original_filename ;

									// Randomizer Code ends Here ////////////////////////////////////////////////

									/*Name of the Developer: Shubham
									The purpose for updates: Upload images to s3 bucket.
									Date of Update: 05/03/2025
									Requested by Dept: Technical
									Approved by: Harish
									*/
									/////////////////////// Code START ////////////////////////////////
									$this->config->load('s3');

									$s3 = new S3Client([
										'version'     => 'latest',
										'region'      => $this->config->item('access_region'),
										'credentials' => [
											'key'    => $this->config->item('access_key'),
											'secret' => $this->config->item('secret_key'),
										],
										'http' => [
											'verify' => $this->config->item('verify_peer')
										]
									]);

									try {
										// Upload to S3
										$result = $s3->putObject([
											'Bucket'      => 'nginxs3mappedbucket', // Consider moving this to config too
											'Key'         => "public/user_assets/registeredStudents/{$new_filename}",
											'Body'        => fopen($_FILES['image']['tmp_name'], 'rb'),
											'ACL'         => 'public-read',
											'ContentType' => $_FILES['image']['type']
										]);

										// Get permanent URL
										$pic_name = $result['ObjectURL'];
								
										$_POST['profile_image'] = $pic_name;
								
									} catch (Aws\S3\Exception\S3Exception $e) {
										echo "Error uploading file: " . $e->getMessage();
										exit();
									}					
									/////////////////////// Code END ////////////////////////////////
								}

							$email = $this->input->post('email');
			            	$mobile = $this->input->post('mobile');
			            	$_POST['device_info'] = json_encode($_SERVER);
			            	
			            	if($_POST['campaignId']){

					            switch ($formType) {
										case '1':
											$updateData = array('age'=>$_POST['age'],'sex'=>$_POST['sex'],'class'=>$_POST['class'],'section'=>$_POST['section'],'intro'=>$_POST['intro'],'occupation'=>$_POST['occupation'],
												    'bulk_frm_id'=>$_POST['bulk_frm_id'],'document_number'=>$_POST['document_number'],'profile_image'=>$_POST['profile_image'],'device_info' => $_POST['device_info']
												);
											break;
										case '2':
											$updateData = array('class'=>$_POST['class'],'sex'=>$_POST['sex'],'section'=>$_POST['section'],'intro'=>$_POST['intro'],'occupation'=>$_POST['occupation'],
												    'bulk_frm_id'=>$_POST['bulk_frm_id'],'document_number'=>$_POST['document_number'],'profile_image'=>$_POST['profile_image'],'device_info' => $_POST['device_info']
												);
											break;
										case '3':
											$updateData = array('intro'=>$_POST['intro'],'occupation'=>$_POST['occupation'],
												    'bulk_frm_id'=>$_POST['bulk_frm_id'],'document_number'=>$_POST['document_number'],'profile_image'=>$_POST['profile_image'],'device_info' => $_POST['device_info']
												);
											break;
									  	default:
											$updateData = array('age'=>$_POST['age'],'sex'=>$_POST['sex'],'class'=>$_POST['class'],'section'=>$_POST['section'],'intro'=>$_POST['intro'],'occupation'=>$_POST['occupation'],
												    'bulk_frm_id'=>$_POST['bulk_frm_id'],'document_number'=>$_POST['document_number'],'profile_image'=>$_POST['profile_image'],'device_info' => $_POST['device_info']
												);
											break;
								}
							
								// print_r($updateData); die;

								if($this->Usermodel->email_match_Cid($email,$_POST['campaignId'])){
									$updated_id = $this->Usermodel->registerForExisting($email,$updateData); //Returns the updated Id
									redirect('home/newregister_success');
								}else{
									// this campaign id does not exist with this email id
									  // $data['msg'] = '<div class="alert alert-danger">Error! This email does not exist with given campaign Id.</div>';
									  $data['msg'] = "<script>
											alert('Error! This email does not exist with given campaign Id.');
											window.location.href='';
											</script>";
									  // $this->form_validation->set_rules('campaignId', 'Error! This email does not exist with given campaign Id.');
						              // redirect('form/' . $id);
								}
							}else{
								unset($_POST['campaignId']);
								
					                //Check if Email is Present Or Not
					            	// if ($this->Usermodel->unique_email($email)) {
												// BY ARUN - REMOVED VALIDATION - 31/07/2021
					            	if (($email)) {	
					              
						                	if ($_POST['password_1'] == $_POST['password_2']) {
							                    $_POST['user_pwd'] = md5($_POST['password_1']);
							                   
							                    $_POST['document_type'] = 4;
							                    // $_POST['document_number'] = $this->input->post('document_number').'StudentsDoc';

							                    $_POST['is_effect'] = 0;
							                    $_POST['point'] = 1;
							                    $_POST['is_new_user'] = 1;
							                    $_POST['device_info'] = json_encode($_SERVER);
							                    
							                        $this->form_validation->set_rules('fname', 'Name', 'required|min_length[3]|max_length[30]');
											        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
											        $this->form_validation->set_rules('mobile', 'Phone Number', 'required|numeric|max_length[15]');
											         // password field with confirmation field matching
											        $this->form_validation->set_rules('password_1', 'Password', 'required|matches[password_2]');
											        $this->form_validation->set_rules('password_2', 'Password Confirmation', 'required');
											        if (empty($_FILES['image']['name']))
													{
													    $this->form_validation->set_rules('image', 'Image', 'required');
													}
													if($_POST['profile_image'] == '' || $_POST['profile_image'] == null){
											        	$this->form_validation->set_rules('image', 'Image size is too small');
											        }
											        
													 // if(filesize($_FILES['image']['tmp_name']) > 20000) {
											   //          $this->form_validation->set_message('image', 'The Image file size shoud not exceed 2MB!');
											   //          // $check = FALSE;
											   //      }
											        
											        // print_r($_POST['profile_image']); die;
											        // hold error messages in div
											        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

													if ($this->form_validation->run() == FALSE) {
											          // do nothing
													}else{
											        	 unset($_POST['password_1']);
							                    		 unset($_POST['password_2']);

											            $ins_id = $this->Usermodel->register(); //Returns the Inserted Id
							                    		$_POST['ins_id'] = $ins_id;

							                    		  if($ins_id)
										                    {
										                        //Send Email
										                        $email_template = $this->load->view('Emails/user-welcome_campaign_owner_indv', $_POST, TRUE);
										                        $this->load->config('email');
										                        $this->load->library('email');
										                        $this->email->clear();
										                        $this->email->initialize(array('mailtype' => 'html'));
										                        $this->email->set_newline("\r\n");
										                        $this->email->from("getintouch@fueladream.com", "Fueladream");
										                        $this->email->to($_POST["email"]);
										                        //$this->email->bcc("chandan@fueladream.com");
										                        $this->email->subject("Welcome to Fueladream.com!");
										                        $this->email->message($email_template);
										                        $this->email->send();
										                        redirect('home/newregister_success');
										                    }
									                	

											        }
											  } else {
									            $data['msg'] = '<div class="alert alert-danger">Error! Confirm password doesn\'t match.</div>';
									      	}
		    
						            } else {
						            		// BY ARUN - REMOVED VALIDATION ERROR MESSAGE - 31/07/2021
						                // $data['msg'] = '<div class="alert alert-danger">Error! This email is already registered.</div>';
						            	
						                // redirect('form/' . $id, 'refresh');
						            }
				            }
				        }
				} else{
					echo "Oops! The School Registration form does not exit!";
				}
			///customization for Chinmaya by Chandan on 14/07/2021///
      $state = $data['school']['state_linked'];
      //echo $state;
      
      if($state){
        	$data['cmcenters'] = $this->Form_model->get_allCMCenterData($state);
    }

			//$data['cmcenters'] = $this->Form_model->get_allCMCenterData($state);
			//print_r($data['cmcenters']);						

    	// Fueladream the best online crowdfunding platform in India | Thank you for being an agent of change.The world needs more people like you .

    	//Change by: snehal hude 
      //Dated on: 29-10-2021 
    	//Change: $data['title'] = $data['school']['name']." Registration Form"; 
      //to 
      //$data['title'] = $data['school']['name']." Registration Form | Thank you for being an agent of change. The world needs more people like you.";
      //Purpose: Requested by Ranga to better message to volunteers.
	    $data['title'] = $data['school']['name']." Registration Form | Thank you for being an agent of change. The world needs more people like you.";
	    $data['description'] = $data['school']['about'];
	    $data['keywords'] = "Crowdfunding, Crowdfund, Crowdfunding in India, Indian Crowdfunding website, Social causes in India, Charities in India, Innovation in India";

	    //Load Up the View
        $this->load->view('elements/header_temp',$data);
        $this->load->view('elements/navigation', $data);
        // print_r($type); die;
        switch ($type) {
        	case '1':
        		$this->load->view('form/school_form', $data);
        		break;
        	case '2':
        		$this->load->view('form/collage_form', $data);
        		break;
        	case '3':
        		$this->load->view('form/corporate_form', $data);
        		break;
        	default:
        		$this->load->view('form/others_form', $data);
        		break;
        }
        
        $this->load->view('elements/footer_front',$data);
	}

	public function search_cate_data(){
      $search_item = $this->input->post('term');
      // print_r($search_item); die;
      if($search_item){
        $data['var']= $this->Form_model->search_cate_field($search_item); 
    }
    // print_r($data['var']); die;
      echo json_encode($data['var']);
   }

   public function fetch_CampaignId(){
   		echo 123; die;
   		// $email = $this->input->post('email');
   		// echo $email; die;
		   //    if($email){
		   //      $res = $this->Form_model->fetchCampaignId($email);
		   //      print_r($res); die; 
		   //  }
		   //    echo json_encode($res);
   }

   
   public function search_sch_data(){
      $search_item = $this->input->post('term');
      if($search_item){
        $data['var']= $this->Form_model->search_sch_data($search_item); 
    }
      echo json_encode($data['var']);
   }

    public function search_org_data(){
      $search_item = $this->input->post('term');
      if($search_item){
        $data['var']= $this->Form_model->search_org_data($search_item); 
    }
      echo json_encode($data['var']);
   }

   public function search_city_data(){
      $search_item = $this->input->post('term');
      if($search_item){
        $data['var']= $this->Form_model->search_city_data($search_item); 
    }
      echo json_encode($data['var']);
   }
   public function getDetails(){
      $city = $this->input->post('city');
      if($city){
        $result = $this->Form_model->getDetails($city); 
    }
      echo json_encode($result);
   }


// updated by neha
public function form_summary() {

// echo CI_VERSION; die;
    //Common Code
    $this->load->library('gps_auth');
    $this->load->model('Form_model', 'Form');

    if (!$this->gps_auth->is_logged_in()) {
        redirect('admin/index', 'refresh');
    }

    //Load Up the View
    $this->load->view('elements/header_admin');
    $this->load->view('elements/navigation_admin', $data);
    $this->load->view('elements/sidebar_admin', $data);
    $this->load->view('form/form_summary', $data);
    $this->load->view('elements/footer_admin');
}


public function get_FormSummaryData(){ 

           $this->load->model('Form_model', 'Form');
           $fetch_data = $this->Form->make_datatables1();  
           $data = array(); 
           $i = 1;


    /*Name of the Developer: Shubham
    The purpose for updates: added if condition to block the url from public
    Date of Update: 06/04/2022
    Requested by Dept: Technical
    Approved by: Harish
    */

    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest'){
           foreach($fetch_data as $row)  
           {  
            
                $sub_array = array();  
                $sub_array[] = $i;  
                // $sub_array[] = "<a href='".base_url("/form/".$row->Registration_Form_ID."")."' onclick='window.open(document.URL, "'_blank'", "'location=yes,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no, status=yes'");' >".$row->Registration_Form_ID."</a>";  
                $sub_array[] = '<strong>'.$row->Registration_Form_ID.'</strong>';  
                $sub_array[] = '<strong>'.$row->Total_Registration.'</strong>';  
                $sub_array[] = $row->NGO_Name;  
                $sub_array[] = $row->Institute_Name;  
                $sub_array[] = $row->Cause;  
                $sub_array[] = $row->City;  
                $sub_array[] = $row->Last_Registered_Time;  
                $sub_array[] = '<a href="'.base_url().'form/download_Data/'.$row->Registration_Form_ID.'" title="Download" class="btn btn-xs btn-default"><i class="fa fa-download"></i></a>';  
                $data[] = $sub_array; 
                $i++; 
           }  
           $output = array(  
                "draw"             =>     intval($_POST["draw"]),  
                "recordsTotal"     =>      $this->Form->get_all_data1(),  
                "recordsFiltered"  =>     $this->Form->get_filtered_data1(),  
                "data"             =>     $data  
           );  
           echo json_encode($output);  
        }
      }

   public function download_Data($param1 = null){
   		
          $this->load->dbutil();
          $this->load->helper('file');
          $this->load->helper('download');
          $this->load->helper('fad_date_helper');
          
            $result = array();
            $final = array();
            $temp = array();
            
                    $delimiter = ",";
                    $newline = "\r\n";
                    $filename = "Crowdfunding_Registrations_".($param1)."_".date('Y-m-d h:i:s').".csv";
             
        			$query = 'SELECT @a:=@a+1 AS "SL No.", `fu`.`fname` AS Name, (CASE fu.`sex` WHEN 1 THEN "Male" WHEN 2 THEN "Female" ELSE "" END) AS Gender, fu.`mobile` AS Mobile, fu.`email` AS Email, fu.`class` AS Class, fu.`age` AS Age,fu.`cmcenter` AS Center, fu.`bulk_frm_id` AS "Registration_Form_ID"
                   		FROM (SELECT @a:= 0) AS a, fad_front_users fu
         				WHERE fu.`bulk_frm_id`="'.$param1.'"
        				ORDER BY fu.`create_time` DESC';

                    $result = $this->db->query($query);
                    $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
                    force_download($filename, $data);
              

   } 

//Code added by snehal hude 02-11-2021 to fetch the states data

public function get_state()
{
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest'){	
		/*	print_r("hii");exit;*/
		$getStatesData = $this->Commonmodel->getData("fad_all_states","id,name","country_id='".$this->input->post('country_id')."'");

		$html = '<select class="form-control" name="state" id="state">';
		$html .= '<option value="">Select State</option>';
		foreach($getStatesData as $rowState){
			$html .= '<option value="'.$rowState->id.'">'.$rowState->name.'</option>';
		}
		$html .= '</select>';

		echo $html;
		exit();
	}	
}
//Code added by snehal hude 02-11-2021 to fetch the states data

}