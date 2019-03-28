<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TestUnit extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('unit_test');
        $this->load->model('Assignment_model');     //VIO & COCO
        $this->load->model('Hof_model');            //REYNER
        $this->load->model('Logs_model');           //YONATHAN
        $this->load->model('Notifications_model');  //REYNER
        $this->load->model('Queue_model');          //KIPPI
        $this->load->model('Scoreboard_model');     //COCO
        $this->load->model('Settings_model');       //KIPPI
        $this->load->model('Submit_model');         //KIPPI
        $this->load->model('User_model');           //YONATHAN
        $this->load->model('User');
    }

    private function report() {
        // if (self::ENABLE_COVERAGE) {
        //     $this->coverage->stop();
        //     $writer = new \SebastianBergmann\CodeCoverage\Report\Html\Facade;
        //     $writer->process($this->coverage, '../reports/code-coverage');
        // }
        // Generate Test Report HTML
        file_put_contents('../reports/test_report.html', $this->unit->report());
        // Output result to screen
        $statistics = [
            'Pass' => 0,
            'Fail' => 0
        ];
        $results = $this->unit->result();
        foreach ($results as $result) {
            echo "=== " . $result['Test Name'] . " ===\n";
            foreach ($result as $key => $value) {
                echo "$key: $value\n";
            }
            echo "\n";
            if ($result['Result'] === 'Passed') {
                $statistics['Pass']++;
            } else {
                $statistics['Fail']++;
            }
        }
        echo "==========\n";
        foreach ($statistics as $key => $value) {
            echo "$value test(s) $key\n";
        }
        if ($statistics['Fail'] > 0) {
            exit(1);
        }
    }

    public function index() {

        /*
        *   Clean sharifjudge's database tables by emptying the table
        */

        // $this->db->empty_table('shj_assignments');
        // // // $this->db->empty_table('shj_logins');
        // $this->db->empty_table('shj_notifications');
        // $this->db->empty_table('shj_problems');
        // $this->db->empty_table('shj_queue');
        // $this->db->empty_table('shj_scoreboard');
        // // $this->db->empty_table('shj_sessions');
        // // $this->db->empty_table('shj_settings');
        // $this->db->empty_table('shj_submissions');
        // only for 'shj_users' table, only delete records other than id = 1 (root)
        // $this->db->query('DELETE FROM shj_users WHERE id != 1');

        /* ------------------------------------------------------------------ */

        /** KIPPI's FUNCTIONS HERE **/
        $this->getASetting('enable_log');
        $this->testSetASetting('enable_log', 1);
        $this->getAllSettings();
        $this->testEmptyAQueue();

        // $this->testGetSubmission('kippi123', 'PBO1', 'Test1', 1);
        // $this->testAddQueue();

        /** YONATHAN's FUNCTIONS HERE **/
        $this->testAddUserTrue();
        $this->testAddUserRoleInvalid();
        $this->testAddUserUsernameExist();
        $this->testAddUserErrorLowercase();
        $this->testAddUserEmailExistError();
        $this->testAddUserLengthUsernameError();
        $this->testAddUserWrongUsernameAlphaNumeric();
        $this->testHaveUserTrue();
        $this->testhaveUserFalse();
        $this->testUsernameToUserId();
        $this->testUsernameToUserIdFalse();
        $this->testUserIdToUsernameTrueId();
        $this->testUserIdToUsernameFalseIdNotfound();
        $this->testUserIdToUsernameFalseIdAlphanumeric();
        $this->testInsertToLogs();
        $this->testValidateUserTrue();
        $this->testValidateUserFalseInvalidUsername();
        $this->testGetNames();
        $this->testAddUsers();
        $this->testGetAllUsers();
        $this->testGetUser();

        /** REYNER's FUNCTIONS HERE **/
        $this->testAddNotifications();
        $this->testGetAllNotifications();
        $this->testGetLatestNotifications();
        $this->testUpdateNotification();
        $this->testDeleteNotification();
        $this->testGetNotifications();

        // /** ENRICO's FUNCTIONS HERE **/
        $this->testAllAssignments();
        $this->testNewAssignmentId();
        $this->testIncreaseTotalSubmits();
        $this->testAllProblem();
        $this->testIsParticipant();

        /** VIO **/
       $this->deleteUser();

    /* ------------ END OF CODE ----------- */

        // $this->add_user_manual();
        // $this->add_assignment_manual();
        // $this->add_submission_manual(); /* TODO: masih error */
        // $this->add_queue_manual();

        /** run report function here **/
        $this->generateFile($this->unit->report());
        $this->report();
        /* ------------------------------------------------------------------ */
    }
    /* GLOBAL FUNCTIONS FOR TESTING */

    /**
    *   function to get current assignment id
    */
    private function get_current_assignment_id() {
        return $this->db->select_max('id', 'max_id')->get('assignments')->row()->max_id;
    }

    /*
    *   Function untuk add user menggunakan mysql $query
    */
    private function add_user_manual() {

        /* clean shj_users db table first. clean all row except UID = 1 */
        $this->db->query('DELETE FROM shj_users WHERE id != 1');

        $data = array(
            'username'  => 'testuser',
            'password'  => '$2a$08$ZVY15Ckd5JpQjD6hViEP/OOto/mTjGPKJGtSz9.8TV5ofUoblsk2W',
            'display_name'  => 'TestUser',
            'email' => 'tu@mail.com',
            'role'  => 'admin',
        );
        // echo var_dump($this->db->insert('shj_users',$data));
        $this->db->insert('shj_users',$data);
    }

    /*
    *   Function untuk add assignment menggunakan mysql $query
    */
    private function add_assignment_manual() {

        /* clean shj_assignments table first */
        $this->db->query('DELETE FROM shj_assignments');

        $data =array(
            'name'  => 'Testing Assignment',
            'problems'  => '1',
            'total_submits' => '0',
            'open'  => '0',
            'scoreboard'    => '0',
            'javaexceptions' => '0',
			'description' => 'Testing',
			'start_time' => '2019-02-21 00:00:00',
			'finish_time' => '2019-07-22 00:00:00',
			'extra_time' => '300',
			'late_rule' => '/*
         * Put coefficient (from 100) in variable $coefficient.
         * You can use variables $extra_time and $delay.
         * $extra_time is the total extra time given to users
         * (in seconds) and $delay is number of seconds passed
         * from finish time (can be negative).
         *  In this example, $extra_time is 172800 (2 days):
         */

        if ($delay<=0)
          // no delay
          $coefficient = 100;

        elseif ($delay<=3600)
          // delay less than 1 hour
          $coefficient = ceil(100-((30*$delay)/3600));

        elseif ($delay<=86400)
          // delay more than 1 hour and less than 1 day
          $coefficient = 70;

        elseif (($delay-86400)<=3600)
          // delay less than 1 hour in second day
          $coefficient = ceil(70-((20*($delay-86400))/3600));

        elseif (($delay-86400)<=86400)
          // delay more than 1 hour in second day
          $coefficient = 50;

        elseif ($delay > $extra_time)
          // too late
          $coefficient = 0;',
			'participants' => 'ALL',
            'moss_update'   => 'Never',
			'archived_assignment' => '0',
        );

        // echo var_dump($this->db->insert('shj_assignments',$data));
        $this->db->insert('shj_assignments',$data);

        /*
        *   after assignment is added, do add a test problem to db
        *   clean shj_problems db first
        */
        $this->db->query('DELETE FROM shj_problems');

        // echo var_dump($this->db->get('shj_problems')->result());
        $prob = array(
            'assignment'        => '1',
            'id'                => '1',
            'name'              => 'Test Problem',
            'score'             => '100',
            'is_upload_only'    => '0',
            'c_time_limit'      => '500',
            'python_time_limit' => '1500',
            'java_time_limit'   => '2000',
            'memory_limit'      => '50000',
            'allowed_languages' => 'C,C++,Python 2, Python 3, Java',
            'diff_cmd'          => 'diff',
            'diff_arg'          => '-bB'
        );
        // echo var_dump($prob);
        // echo var_dump($this->db->insert('shj_problems', $prob));
        $this->db->insert('shj_problems', $prob);
    }

    /*
    *   Function untuk menambah submission ke dalam queue
    *   kemudian set submission tersebut menjadi final submission
    *   // TODO: masih error belum bisa masukin data ke db secara manual
    */
    private function add_submission_manual() {
        /* clean shj_submissions db */
        // $this->db->query('DELETE FROM shj_submissions');


        $submit_info = array(
            'submit_id'     => '1',
            'username'      => 'testuser',
            'assignment'    => '1',
            'problem'       => '1',
            'is_final'      => 1,
            'time'          => date('Y-m-d H:i:s'),
            'status'        => '0',
            'pre-score'     => 100,
            'coefficient'   => '100%',
            'file_name'     => 'test_file.java',
            'main_file_name'=> 'test_file.java',
            'file_type'     => 'java'
        );
        // echo var_dump($submit_info);

        //add to submission db
        echo var_dump($this->db->insert('shj_submissions', $submit_info));
    }

    private function add_queue_manual() {
        /* clean shj_queue db */
        $this->db->query('DELETE FROM shj_queue');

        $queue_info = array(
            'submit_id' => '1',
			'username' => 'testuser',
			'assignment' => '1',
			'problem' => '1',
			'type' => 'judge'
        );

        //add to queue db
        // echo var_dump($this->db->insert('shj_queue', $queue_info));
        $this->db->insert('shj_queue', $queue_info);
    }



    /** ----- INPUT KIPPI's CODE HERE ----- **/

    /**
     * Testing function get_submission di file Submit_model.php
     */
    private function testGetSubmission($username, $assignment, $problem, $submit_id) {
        $test       = $this->Submit_model->get_submission($username, $assignment, $problem, $submit_id);
        $result     = FALSE;
        $testName   = "testGetSubmissionFalse";
        $testNote   = "Test get submission data that doesn't exists in db";
        $this->unit->run($test, $result, $testName, $testNote);
    }

    /**
    *   Testing function to get submission after a submission is added to db
    *   Expected to return a table row of the added submission
    */
    private function testGetSubmissionAfterAdd($username, $assignment, $problem, $submit_id) {
        // do add submission first

        // do test get submission here
        testGetSubmission($username, $assignment, $problem, $submit_id);
    }

    /**
    *   SETTINGS_MODEL
    *   Testing function to set single setting
    *   Expected to return a different value than the setting before
    *   (compare the old and new, expect to return FALSE since the old is
    *   not the same as the new setting)
    *   @param $key : the setting name
    */
    private function testSetASetting($key, $value) {
        $currentSettingValue = $this->Settings_model->get_setting($key);

        $test = $this->Settings_model->set_setting($key, $value);

        $updatedSettingValue = $this->Settings_model->get_setting($key);

        ($currentSettingValue != $updatedSettingValue) ? $result = FALSE : $result = TRUE;
        $testName = "testSetASetting";
        $testNote  = "Test set a setting key to a new value";
        $this->unit->run($test, $result, $testName, $testNote);
    }

    /**
    *   SETTINGS_MODEL
    *   Testing function to get a setting
    *   Expected to return a setting value
    *   @param $key : the setting name
    */
    private function getASetting($key) {
        //set the setting to a value first, and get the value to be tested
        $this->Settings_model->set_setting($key, 1);

        $test = $this->Settings_model->get_setting($key);
        $result = 1;
        $testName = "testSetASetting";
        $testNote  = "Test get a setting value after update value";
        $this->unit->run($test, $result, $testName, $testNote);
    }

    /**
    *   SETTINGS MODEL
    *   Testing function to get all settings
    *   by comparing expected settings row with get_all_settings() function
    */
    private function getAllSettings() {
        $count = $this->db->get('shj_settings')->num_rows();
        // echo var_dump();
        $test = sizeof($this->Settings_model->get_all_settings());
        $result = $count;
        $testName = "testSetAllSettings";
        $testNote  = "Test get all setting by comparing row count, expected to return 26 rows";
        $this->unit->run($test, $result, $testName, $testNote);
    }

    /*
    *   QUEUE_MODEL
    *   Testing function to empty a queue
    *   Expected to return true, meaning test succeed
    */
    private function testEmptyAQueue() {
        $this->add_user_manual();
        $this->add_assignment_manual();
        $this->add_queue_manual();

        $test       = $this->Queue_model->empty_queue();
        $totalQueue = sizeof($this->Queue_model->get_queue());
        $result     = ($totalQueue == 0) ? true : false;
        $testName   = 'testEmptyAQueue';
        $testNote   = 'test to empty queue table from db, expected to return true if totalQueue == 0';
        $this->unit->run($test,$result,$testName,$testNote);
    }

    /*
    *   QUEUE_MODEL
    *   Testing function to add a submission to submission table and queue table
    *   Expected to return rows+1 on both submission & queue table
    *   var $submit_info contains submit_id, username, assignment, and problem
    */
    private function testAddQueue() {

        /*
        *   flow: get available assignment id->add new assignment->add to queue
        */
        // $this->add_user_manual();
        // $this->add_assignment_manual();
        // $this->add_queue_manual();
        //
        // $current_queue = sizeof($this->Queue_model->get_queue());
        //
        // $queue_info = array(
        //     'submit_id' => '1',
        //     'username' => 'testuser',
        //     'assignment' => '1',
        //     'problem' => '1',
        //     'type' => 'judge'
        // );
        //
        // $test       = $this->Queue_model->add_to_queue($queue_info);
        // $result     = $current_queue+1;
        // $testName   = 'testAddQueue';
        // $testNote   = 'test to add queue to db, expected to return queue_count + 1';
        // $this->unit->run($test,$result,$testName,$testNote);
    }

    /** ----- INPUT YONATHAN's CODE HERE ----- **/
    private function testAddUserTrue(){
      $test=$this->User_model->add_user('globaladmin','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
      $result=true;
      $testName= 'Test Add User on judge';
      $testNote= 'create new user admin';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    private function testAddUserWrongUsernameAlphaNumeric(){
      $test=$this->User_model->add_user('globaladmin!!!','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
      $result='Username may only contain alpha-numeric characters.';
      $testName= 'Test Add User on judge';
      $testNote= 'create new user admin';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    private function testAddUserLengthUsernameError(){
      $test=$this->User_model->add_user('glo','admin@gmail.com', 'administrator', 'Ad', 'admin' );
      $result='Username or password length error.';
      $testName= 'Test Add User on judge';
      $testNote= 'create new user admin';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    private function testAddUserUsernameExist(){
      $this->User_model->add_user('globaladmin','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
      $test=$this->User_model->add_user('globaladmin','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
      $result='User with this username exists.';
      $testName= 'Test Add User on judge';
      $testNote= 'create new user admin';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    private function testAddUserEmailExistError(){
      $this->User_model->add_user('globalnakskd','admin@gmail.com', 'iahsundkaso', 'Admin10', 'admin' );
      $test=$this->User_model->add_user('globalasdasd','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
      $result= 'User with this email exists.';
      $testName= 'Test Add User on judge';
      $testNote= 'create new user admin';
      $this->unit->run($test,$result,$testName,$testNote);

    }
    private function testAddUserErrorLowercase(){
      $test=$this->User_model->add_user('GlobalAdmin','aasadasd@gmail.com', 'administrator', 'Admin10', 'admin' );
      $result='Username must be lowercase.';
      $testName= 'Test Add User on judge';
      $testNote= 'create new user admin';
      $this->unit->run($test,$result,$testName,$testNote);

    }
    private function testAddUserRoleInvalid(){
      $id= $this->User_model->username_to_user_id('globaladmin');
      $this->User_model->delete_user($id);
      $test=$this->User_model->add_user('globaladmin','admin@gmail.com', 'administrator', 'Admin10', '' );
      $result='Users role is not valid.';
      $testName= 'Test Add User on judge';
      $testNote= 'create new user admin';
      $this->unit->run($test,$result,$testName,$testNote);

    }

    private function testHaveUserTrue(){
      $test=$this->User_model->have_user('globaladmin');
      $result=True;
      $testName= 'Test Have User Name by Username on judge';
      $testNote= 'untuk hasil true jadi user sudah ada dalam database judge input ("globaladmin")';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    private function testhaveUserFalse(){
      $test=$this->User_model->have_user('yonathan');
      $result=False;
      $testName= 'Test Have User Name by Username on judge';
      $testNote= 'untuk hasil false jadi username tidak ada dalam database judge input ("yonathan")';
      $this->unit->run($test,$result,$testName,$testNote);
    }

    private function testUserIdToUsernameTrueId(){
      $id= $this->User_model->username_to_user_id('globaladmin');
      $test=$this->User_model->user_id_to_username($id);
      $result='globaladmin';
      $testName= 'Test get user name by user id on database';
      $testNote= 'untuk hasil passed id dan username ada dalam database input("1")';
      $this->unit->run($test,$result,$testName,$testNote);
    }

    private function testUserIdToUsernameFalseIdAlphanumeric(){
      $test=$this->User_model->user_id_to_username('asdf');
      $result=False;
      $testName= 'Test get user name by user id on database';
      $testNote= 'untuk hasil failed id yang diinput bukan numeric input ("asdf")';
      $this->unit->run($test,$result,$testName,$testNote);

    }

    private function testUserIdToUsernameFalseIdNotfound(){
      $user=$this->User_model->get_all_users();
      $id=$user[sizeof($user)-1]['id'];
      $test=$this->User_model->user_id_to_username($id+1);
      $result=False;
      $testName= 'Test get user name by user id on database';
      $testNote= 'untuk hasil failed id tidak ada dalam database input ("2")';
      $this->unit->run($test,$result,$testName,$testNote);

    }

    private function testUsernameToUserId(){
      $this->User_model->add_user('globaladmin','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
      $id=$this->User_model->username_to_user_id('globaladmin');
      $test=$this->User_model->username_to_user_id('globaladmin');
      $result=$id;
      $testName= 'Test get id by username on database';
      $testNote= 'untuk hasil passed username ada dalam database input ("globaladmin")';
      $this->unit->run($test,$result,$testName,$testNote);
    }

    private function testUsernameToUserIdFalse(){
      $test=$this->User_model->username_to_user_id('yonathan');
      $result=FALSE;
      $testName= 'Test get id by username on database';
      $testNote= 'untuk hasil passed username tidak ada dalam database input ("yonathan")';
      $this->unit->run($test,$result,$testName,$testNote);

    }

    private function testInsertToLogs(){
      $size= sizeof($this->Logs_model->get_all_logs());
      $test=$this->Logs_model->insert_to_logs('globaladmin','192.168.12.2');
      $sizee= sizeof($this->Logs_model->get_all_logs());
      if($size!=$sizee){
        $test=true;
      }else{
        $test=false;
      }
      $result=TRUE;
      $testName= 'Test insert logs';
      $testNote= 'dilakukan dengan membandingkan log sebelumnya dengan log sesudah di insert';
      $this->unit->run($test,$result,$testName,$testNote);
    }

    private function generateFile($test){
      $myfile = fopen("TestFile.html", "w") or die("Unable to open file!");
      fwrite($myfile, $test);
      fclose($myfile);
    }

    //delete submissions
    //selected assignment
    //update profile
    //send password reset mail
    //pass change is valid
    //reset passwords
    //update login time
    private function testValidateUserTrue(){
      $this->User_model->add_user('globaladmin','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
      $test=$this->User_model->validate_user('globaladmin','Admin10');
      $result=True;
      $testName= 'Test username and password valid for login';
      $testNote= 'untuk hasil passed username dan password ada dalam database';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    private function testValidateUserFalseInvalidUsername(){
      $this->User_model->add_user('globaladmin','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
      $test=$this->User_model->validate_user('globaladminnnn','Admin10');
      $result=False;
      $testName= 'Test username and password invalid username for login';
      $testNote= 'untuk hasil passed username tidak ada dalam database';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    private function testGetNames(){
      $test=$this->User_model->get_names();
      if(sizeof($test)>0){
        $test=true;
      }
      else{
        $test=false;
      }
      $result=true;
      $testName= 'Test to get names ';
      $testNote= 'if return test > 0 test passed else failed';
       $this->unit->run($test,$result,$testName,$testNote);
    }

    private function testAddUsers(){
      $text="andy \r\n reyner";
      $send_mail="7315016@student.unpar.ac.id";
      $delay="10";

      $test=$this->User_model->add_users($text,$send_mail,$delay);
      if(sizeof($test)>0){
        $test=true;
      }
      else{
        $test=false;
      }
      $result=true;
      $testName= 'Test add users ';
      $testNote= 'result passed if test > 0 and failed if test<=0';
      $this->unit->run($test,$result,$testName,$testNote);

    }
    private function testGetAllUsers(){
      $text="andy \r\n reyner";
      $send_mail="7315016@student.unpar.ac.id";
      $delay="10";
      $this->User_model->add_users($text,$send_mail,$delay);
      $test=$this->User_model->get_all_users();
      if(sizeof($test)>0){
        $test=true;
      }
      else {
        $test=false;
      }
      $result=true;
      $testName= 'Test get users ';
      $testNote= 'result passed if test > 0 and failed if test<=0';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    private function testGetUser(){
      $ids=$this->db->get_where('users', array('id'))->result();
      $test=$this->User_model->get_user($ids[0]->id);
      if(sizeof($test)>0){
        $test=true;
      }
      else {
        $test=false;
      }
      $result=true;
      $testName= 'Test get user by id user ';
      $testNote= 'result passed if test > 0 and failed if test<=0';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    /** ----- INPUT REYNER's CODE HERE ----- **/
    public function testGetAllNotifications(){
      $test=$this->Notifications_model->get_all_notifications();
      $result=TRUE;
      $testName= 'Test get all notification on judge';
      $testNote= 'awal tes belum ada notifkasi jadi masih false, ketika sudah di add notifkasi resultnya true';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    public function testGetLatestNotifications(){
      $test=$this->Notifications_model->get_latest_notifications();
      $result=TRUE;
      $testName= 'Test get latest notification on judge';
      $testNote= 'awal tes belum ada notifkasi jadi masih false, ketika sudah di add notifkasi resultnya true';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    public function testAddNotifications(){
      $count= sizeof($this->Notifications_model->get_all_notifications());
      $test=$this->Notifications_model->add_notification('notifikasi','Ada ujian');
      $countt= sizeof($this->Notifications_model->get_all_notifications());
      if($count!=$countt){
        $test=true;
      }else{
        $test=false;
      }
      $result=true;
      $testName='Test to add notification on judge';
      $testNote='Add notifications';
      $this->unit->run($test,$result,$testName,$testNote);
    }

    public function testUpdateNotification(){
      $test=$this->Notifications_model->get_latest_notifications();
      $this->Notifications_model->update_notification($test[0]['id'],'notifikasi 1','ada ujian lagi');
      $test3=$this->Notifications_model->get_notification($test[0]['id']);
      if($test != $test3){
        $test3=true;
      }else {
        $test3=false;
      }
      $result=true;
      $testName='Test to update notification on judge';
      $testNote='Update notifications';
      $this->unit->run($test3,$result,$testName,$testNote);
    }

    public function testDeleteNotification(){
      $test=$this->Notifications_model->get_all_notifications();
      $add=$this->Notifications_model->add_notification('notifikasi','Ada ujian');
      $count= sizeof($this->Notifications_model->get_all_notifications());
      $testt=$this->Notifications_model->delete_notification($test[0]['id']);
      $countt= sizeof($this->Notifications_model->get_all_notifications());
      if($count !=$countt){
        $testt=true;
      }else{
        $testt=false;
      }
      $result=true;
      $testName='Test to delete notification on judge';
      $testNote='Delete notifications';
      $this->unit->run($testt,$result,$testName,$testNote);
    }

    public function testGetNotifications(){
      $add=$this->Notifications_model->add_notification('notifikasi','Ada ujian');
      $all=$this->Notifications_model->get_all_notifications();
      $test=$this->Notifications_model-> get_notification($add[0]['id']);
      if($test == false){
        $test=true;
      }
      $result=TRUE;
      $testName= 'Test get notification on judge';
      $testNote= 'get specific notification';
      $this->unit->run($test,$result,$testName,$testNote);
    }

    // public function testHaveNewNotifications(){
    //
    // }



    /** ----- INPUT ENRICO's CODE HERE ----- **/
    public function testAllAssignments(){
      $test=$this->Assignment_model->all_assignments();
      $result = $this->db->order_by('id')->get('assignments')->result_array();
  		$resultt = array();
  		foreach ($result as $item)
  		{
  			$resultt[$item['id']] = $item;
  		}
      $testName='Test all assignments';
      $testNote='Returns a list of all assignments and their information';
      $this->unit->run($test,$resultt,$testName,$testNote);
    }

    public function testNewAssignmentId(){
        $this->add_user_manual();
        $this->add_assignment_manual();
        $current_id = $this->get_current_assignment_id();
        $test=$this->Assignment_model->new_assignment_id();
        $result=$current_id+1;
        $testName='Test new assignment id';
        $testNote='Finds the smallest integer that can be uses as id for a new assignment';
        $this->unit->run($test,$result,$testName,$testNote);
    }

    public function testIncreaseTotalSubmits(){
        $this->add_user_manual();
        $this->add_assignment_manual();
        $this->Assignment_model->increase_total_submits('');
        $test=$this->Assignment_model->increase_total_submits('T15062');
        $result=$total+1;
        $testName='Test increase total submits';
        $testNote='Increases number of total submits for given assignment by one';
        $this->unit->run($test,$result,$testName,$testNote);
    }

    public function testAllProblem(){
      $test=$this->Assignment_model->all_problems('T15062');
      $testName='Test all Problems of an Assignment';
      $testNote='Returns an array containing all problems of given assignment';
      $this->unit->run($test,$result,$testName,$testNote);


    }

    public function testIsParticipant(){
      $this->Assignment_model->is_participant('user1','i15062');
      $test=$this->Assignment_model->is_participant('ALL','i15062');
      $result=TRUE;
      $testName='Test is Participant';
      $testNote='Returns TRUE if $username if one of the $participants';
      $this->unit->run($test,$result,$testName,$testNote);


    }


    /* ------------ END OF CODE ----------- */


    /** ----- INPUT VIO's CODE HERE ----- **/
private function deleteUser(){
    $this->User_model->add_user('nadyavio','7315005@student.unpar.ac.id','nadya','Nadya123','admin');
    $user=$this->User_model->get_all_users();
    $count = sizeof($user);
    $test=$this->User_model->delete_user($user[$count-1]['id']);
    $count2 = sizeof($this->User_model->get_all_users());
    $result = FALSE;
    if($count!=$count2){$result = TRUE;}
    $testName='Test to delete user';
    $testNote='Delete user';
    $this->unit->run($test,$result,$testName,$testNote);
}

}

?>
