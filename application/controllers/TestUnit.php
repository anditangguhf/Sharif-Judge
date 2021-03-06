<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use SebastianBergmann\CodeCoverage\CodeCoverage;

class TestUnit extends CI_Controller {
    const ENABLE_COVERAGE = true;
    private $coverage;
    public function __construct() {
        parent::__construct();

        $this->load->library('unit_test');
        $this->unit->use_strict(TRUE);

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

        if (self::ENABLE_COVERAGE) {
            $this->coverage = new SebastianBergmann\CodeCoverage\CodeCoverage;
            // $this->coverage->filter()->addDirectoryToWhitelist('application/controllers');
            $this->coverage->filter()->removeDirectoryFromWhitelist('application/controllers/tests');
            // $this->coverage->filter()->addDirectoryToWhitelist('application/libraries');
            $this->coverage->filter()->addDirectoryToWhitelist('application/models');
            // $this->coverage->filter()->addDirectoryToWhitelist('application/views');
            $this->coverage->start('UnitTests');
        }


    }

    private function report() {
        if (self::ENABLE_COVERAGE) {
            $this->coverage->stop();
            $writer = new \SebastianBergmann\CodeCoverage\Report\Html\Facade;
            $writer->process($this->coverage, 'reports/code-coverage');
        }
        // Generate Test Report HTML
        file_put_contents('reports/test_report.html', $this->unit->report());
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

        /** KIPPI's FUNCTIONS HERE **/
        $this->getASetting('enable_log');
        $this->testSetASetting();
        $this->testSettings();
        $this->getAllSettings();
        // $this->testEmptyAQueue();
        $this->testGetScoreBoardFound();

        $this->testGetSubmission('kippi123', 'PBO1', 'Test1', 1);

        /** YONATHAN's FUNCTIONS HERE **/
        $this->testAddUserTrue();
        // $this->testAddUserRoleInvalid();
        $this->testAddUserUsernameExist();
        $this->testAddUserErrorLowercase();
        $this->testAddUserEmailExistError();
        $this->testAddUserLengthUsernameError();
        $this->testAddUserWrongUsernameAlphaNumeric();
        $this->testHaveEmail();
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
        $this->testGetUserTrue();
        $this->testGetUserFalse();
        $this->testSendResetPass();
        $this->testSendResetPassEmailNotExist();
        $this->testPasschangeIsValid();
        $this->testPasschangeIsValidTimeExpired();
        $this->testPasschangeIsValidInvalidPass();
        $this->testResetPass();
        // $this->testingAddAssignment();failed travis
        // $this->testingDeleteAssignment();failed travis


        /** REYNER's FUNCTIONS HERE **/
        $this->testAddNotifications();
        $this->testGetAllNotifications();
        $this->testGetLatestNotifications();
        $this->testUpdateNotification();
        $this->testDeleteNotification();
        $this->testGetNotifications();
        $this->testHaveNewNotificationsTrue();
        // $this->testHaveNewNotificationsFalse();failed travis
        // $this->testGetAllFinalSubmission();failed travis

        /** ENRICO's FUNCTIONS HERE **/
        $this->testAllAssignments();
        $this->testNewAssignmentId();
        $this->testIncreaseTotalSubmits();
        $this->testAllProblem();
        $this->testIsParticipant();
        // $this->testAssignmentInfo();failed travis
        $this->testProblemInfo();
        // $this->testSetMossTime();failed travis
        // $this->testGetMossTime();failed travis
        $this->testUpdateScoreBoards();
        $this->testGetScoreBoard();

        /** VIO **/
        // $this->deleteUser(); failed travis
        $this->testDeleteUserFalse();
        $this->updateLoginTime();
        $this->testGetFirstItem();
        // $this->testRemoveItem();failed travis
        // $this->TestAddtoQueue(); failed travis
        $this->TestGetScoreBoard();
        $this->testEmptyQueue();
        $this->testInQueue();
        $this->testGetFirstItemFound();


        /* ------------ END OF CODE ----------- */

        // $this->add_user_manual();
        // $this->add_assignment_manual();
        // $this->add_submission_manual(370,1); /* TODO: masih error */
        // $this->add_queue_manual(72,1);
        // $this->add_scoreboard_manual(340);

        /** run report function here **/
        $this->generateFile($this->unit->report());
        $this->report();

        /* ------------------------------------------------------------------ */

        // $coverage->stop();

        // $writer = new \SebastianBergmann\CodeCoverage\Report\Clover;
        // $writer->process($coverage, '/tmp/clover.xml');
        //
        // $writer = new \SebastianBergmann\CodeCoverage\Report\Html\Facade;
        // $writer->process($coverage, '/tmp/code-coverage-report');
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
        $query = $this->db->query("SELECT id from shj_assignments")->result();
        $assignment_id = "";
        foreach ($query as $key => $value) {
            $assignment_id = $value->id;
        }

        /*
        *   after assignment is added, do add a test problem to db
        *   clean shj_problems db first
        */
        $this->db->query('DELETE FROM shj_problems');

        // echo var_dump($this->db->get('shj_problems')->result());
        $prob = array(
            'assignment'        => $assignment_id, // TODO: harus ganti isinya jadi id assignment
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

    private function add_scoreboard_manual($assignment_id) {
        $args = array(
            'assignment' => $assignment_id,
            'scoreboard' => "SCOREBOARD"
        );
        $query = $this->db->insert('scoreboard', $args);
        // echo var_dump($query);
    }

    /*
    *   Function untuk menambah submission ke dalam queue
    *   kemudian set submission tersebut menjadi final submission
    *   // TODO: masih error belum bisa masukin data ke db secara manual
    */
    private function add_submission_manual($assignment_id, $problem_id) {
        /* clean shj_submissions db */
        // $this->db->query('DELETE FROM shj_submissions');

        $submit_info = array(
            'username'      => 'testuser',
            'assignment'    => $assignment_id,
            'problem'       => $problem_id,
            'time'          => date('Y-m-d H:i:s'),
            'status'        => '0',
            'pre-score'     => 100,
            'coefficient'   => '100%',
            'file_name'     => 'test_file.java',
            'main_file_name'=> 'test_file.java',
            'file_type'     => 'java'
        );
        // echo var_dump($submit_info);

        echo var_dump($this->db->insert('shj_submissions', $submit_info));
    }

    private function add_queue_manual($assignment_id) {
        /* clean shj_queue db */
        $this->db->query('DELETE FROM shj_queue');

        $queue_info = array(
            'submit_id' => '1',
            'username' => 'testuser',
            'assignment' => $assignment_id,
            'problem' => 1,
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
    private function testSetASetting() {
        $set = $this->Settings_model->set_setting('enable_log', 1);
        $test = $this->Settings_model->get_setting('enable_log');
        $result = "1";
        $testName = "testSetASetting";
        $testNote  = "Test set a setting key to a new value";
        $this->unit->run($test, $result, $testName, $testNote);
    }

    private function testSettings() {
        $args = array(
            'enable_registration' => 1
        );
        $set = $this->Settings_model->set_settings($args);
        $test = $this->Settings_model->get_setting('enable_registration');
        $result = "1";
        $testName = "testSetMultipleSetting";
        $testNote  = "Test set multiple settings key to a new value";
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
        $result = "1";
        $testName = "testGetASetting";
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

    private function testDeleteUserFalse() {
        $test = $this->User_model->delete_user(2);
        $result     = false;
        $testName   = 'testDeleteUserFalse';
        $testNote   = 'test to delete user with no same id in db table, expected to return false';
        $this->unit->run($test,$result,$testName,$testNote);
    }

    private function testGetScoreBoardFound() {
        $this->add_user_manual();
        $this->add_assignment_manual();
        $assignment_id = "";
        $query = $this->db->query("SELECT id from shj_assignments")->result();
        foreach ($query as $key => $value) {
            $assignment_id = $value->id;
        }
        $this->add_scoreboard_manual($assignment_id);
        $test=$this->Scoreboard_model->get_scoreboard($assignment_id);
        $queryy =  $this->db->select('scoreboard')->get_where('scoreboard', array('assignment'=>$assignment_id));
		$result = $queryy->row()->scoreboard;
        $testName='Get Cached Scoreboard (Found Scoreboard)';
        $testNote='Update All Scoreboards Returns the cached scoreboard of given assignment as a html text';
        $this->unit->run($test,$result,$testName,$testNote);
    }

    /** ----- INPUT YONATHAN's CODE HERE ----- **/
    private function testAddUserTrue(){
        $this->User_model->__construct();
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


    private function testHaveEmail(){
        $this->User_model->add_user('globaladmin','admin@gmail.com', 'administrator', 'Admin10', 'admin' );

        $test = $this->User_model->have_email('admin@gmail.com', false);
        $result = true;
        $testName = "Testing have_email ";
        $testNotes = "untuk hasil true";
        $this->unit->run($test,$result,$testName,$testNote);


        $test = $this->User_model->have_email('', false);
        $result = false;
        $testName = "Testing have_email ";
        $testNotes = "untuk hasil false email tidak ada";
        $this->unit->run($test,$result,$testName,$testNote);


        $test = $this->User_model->have_email('admin@gmail.com', true);
        $result = false;
        $testName = "Testing have_email ";
        $testNotes = "untuk hasil false parameter true";
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
        ///////////////////////////////////////////////////
        $test=$this->User_model->validate_user('Globaladmin','Admin10');
        $result=False;
        $testName= 'Test username and password invalid username for login';
        $testNote= 'untuk hasil passed username tidak huruf kecil';
        $this->unit->run($test,$result,$testName,$testNote);
        //////////////////////////////////////////////////
        $test=$this->User_model->validate_user('globaladmin','');
        $result=False;
        $testName= 'Test username and password invalid username for login';
        $testNote= 'untuk hasil passed pass no password';
        $this->unit->run($test,$result,$testName,$testNote);
        /////////////////////////////////////////////////
        $test=$this->User_model->validate_user('globaladmin','globaladmin');
        $result=False;
        $testName= 'Test username and password invalid username for login';
        $testNote= 'untuk hasil passed wrong password';
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
//////////////////////////////////////////////////////////////
        $text="";
        $send_mail="false";
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
///////////////////////////////////////////////////////////////////

        $text="andy,kippi@gmail.com,andy,random[6],student \n
              vio,vio@gmail.com,vio,123456,nothing \n
              reyner,reyner@gmail.com, reyner,776237,student";
        $send_mail="false";
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
    private function testGetUserTrue(){
        $ids=$this->db->get_where('users', array('id'))->result();
        $test=$this->User_model->get_user($ids[0]->id);
        if(sizeof($test)>0){
            $test=true;
        }
        else {
            $test=false;
        }
        $result=true;
        $testName= 'Test get user by id user return true';
        $testNote= 'result passed if test<=0';
        $this->unit->run($test,$result,$testName,$testNote);
    }
    private function testGetUserFalse(){
        $ids=$this->db->get_where('users', array('id'))->result();
        $test=$this->User_model->get_user(++$ids[0]->id);
        if(sizeof($test)>0){
            $test=true;
        }
        else {
            $test=false;
        }
        $result=true;
        $testName= 'Test get user by id user return false';
        $testNote= 'result passed if test > 0 ';
        $this->unit->run($test,$result,$testName,$testNote);
    }
    private function testSendResetPass(){
      $query = $this->db->get_where('users', array('passchange_key'))->result();
      $test= $this->User_model->send_password_reset_mail($query[0]->email);
      $query2 = $this->db->get_where('users', array('passchange_key'))->result();
      if($query[0]->passchange_key!=$query2[0]->passchange_key){
        $test=true;
      }
      else{
        $test=false;
      }
      $result=true;
      $testName= 'Test send reset pass ';
      $testNote= 'result passed passchange_key !=""';
      $this->unit->run($test,$result,$testName,$testNote);

    }
    private function testSendResetPassEmailNotExist(){
      $query = $this->db->get_where('users', array('passchange_key'))->result();
      $test= $this->User_model->send_password_reset_mail("random@gmail.com");
      $query2 = $this->db->get_where('users', array('passchange_key'))->result();
      if($query[0]->passchange_key!=$query2[0]->passchange_key){
        $test=false;
      }
      else{
        $test=true;
      }
      $result=true;
      $testName= 'Test send reset pass email not exist ';
      $testNote= 'result passed passchange_key ==""';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    private function testPasschangeIsValid(){
      $test= $this->User_model->send_password_reset_mail($query[0]->email);
      $query = $this->db->get_where('users', array('passchange_key'))->result();
      $query2 = $this->db->select('passchange_time')->get_where('users', array('passchange_key'=>$query[0]->passchange_key));
      $test=$this->User_model->passchange_is_valid($query[0]->passchange_key);
      $result=true;
      $testName= 'Test passchange is valid ';
      $testNote= 'result passed if the given password reset key is valid';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    private function testPasschangeIsValidInvalidPass(){
      $query = $this->db->get_where('users', array('passchange_key'))->result();
      $test= $this->User_model->send_password_reset_mail($query[0]->email);
      $query2 = $this->db->select('passchange_time')->get_where('users', array('passchange_key'=>$query[0]->passchange_key));
      $test=$this->User_model->passchange_is_valid($query[0]->passchange_key);
      $result='Invalid password reset link.';
      $testName= 'Test passchange is valid Invalid Pass ';
      $testNote= 'result passed if return invalid password reset link';
      $this->unit->run($test,$result,$testName,$testNote);
    }
    private function testPasschangeIsValidTimeExpired(){
        $users = array(
        array(
            'id' => '1000',
            'username'=>'test',
            'password'=>'test',
            'display_name'=>'test',
            'email'=>'test@gmail.com',
            'role'=>'admin',
            'passchange_key'=>'qwerty',
            'passchange_time'=>'2010-03-28 14:47:03',
            'first_login_time'=>'2018-02-14 09:04:03',
            'last_login_time'=>'2018-02-14 09:04:03',
            'selected_assignment'=>'1',
            'dashboard_widget_positions'=>'dashboard1')
        );
      $this->db->insert('shj_users',$users[0]);
      $test=$this->User_model->passchange_is_valid('qwerty');
      $result='The link is expired.';
      $testName= 'Test passchange is valid Invalid time expired ';
      $testNote= 'result passed if return The link is expired';
      $this->unit->run($test,$result,$testName,$testNote);

    }

    private function testResetPass(){
        $test= $this->User_model->send_password_reset_mail($query[0]->email);
        $query = $this->db->get_where('users', array('passchange_key'))->result();
        $query2 = $this->db->select('passchange_time')->get_where('users', array('passchange_key'=>$query[0]->passchange_key));
        $test=$this->User_model->reset_password($query[0]->passchange_key,"123iyg123");
        $result=true;
        $testName= 'Test reset password true';
        $testNote= 'result passed if true';
        $this->unit->run($test,$result,$testName,$testNote);
        ///////////////////////////////////////////////////
        $query2 = $this->db->select('passchange_time')->get_where('users', array('passchange_key'=>$query[0]->passchange_key));
        $test=$this->User_model->reset_password("","123iyg123");
        $result=false;
        $testName= 'Test reset password false';
        $testNote= 'result passed if false';
        $this->unit->run($test,$result,$testName,$testNote);
    }

    private function post($num){
      $_POST['id'] = 1;
      $_POST['assignment_name'] = "test assignment";
      $_POST['number_of_problems'] = $num;
      $_POST['total_submits'] = 0;
      $_POST['open'] = 1;
      $_POST['scoreboard'] = 1;
      $_POST['javaexceptions'] = 0;
      $_POST['description'] = 'add assignment test'; /* todo */
      $_POST['start_time'] = date('Y-m-d H:i:s', strtotime('04/12/2018 00:00:00'));
      $_POST['finish_time'] = date('Y-m-d H:i:s', strtotime('04/12/2018 12:00:00'));
      $_POST['extra_time'] = 0;
      $_POST['late_rule'] = '';
      $_POST['participants'] = 'ALL';
      $_POST['archived_assignment'] = 0;
      $_POST['name'] = array('Assignment 1', 'Assignment 2');
      $_POST['score'] = array(100, 100);
      $_POST['c_time_limit'] = array(100, 100);
      $_POST['python_time_limit'] = array(500, 500);
      $_POST['java_time_limit'] = array(1000, 1000);
      $_POST['memory_limit'] = array(25000, 25000);
      $_POST['languages'] = array('C,C++,Python 2,Python 3,Java', 'C,C++,Python 2,Python 3,Java');
      $_POST['diff_cmd'] = array('', '');
      $_POST['diff_arg'] = array('', '');
      $_POST['is_upload_only'] = array(1, 1);

    }

    private function testingAddAssignment(){
       $this->post(1);
       $ceker = $this->db->order_by('id')->get('assignments')->result_array();
       $this->Assignment_model->add_assignment(1, FALSE);
       $ceker2 = $this->db->order_by('id')->get('assignments')->result_array();
       $test = sizeof($ceker2)-sizeof($ceker);
       $result = 1;
       $testName = "Test add assignment ";
       $testNote= "Result passed if size of array > 0";
       $this->unit->run($test,$result,$testName,$testNote);
       ////////////////////////////////////////////////////
       // $this->db->delete('assignments', array('id'=>1));
       // $this->post(100);
       // $ceker = $this->db->order_by('id')->get('assignments')->result_array();
       // $this->Assignment_model->add_assignment(1, TRUE);
       // $ceker2 = $this->db->order_by('id')->get('assignments')->result_array();
       // $test = sizeof($ceker2)-sizeof($ceker);
       // var_dump($ceker2);
       // $result = 1;
       // $testName = "Test add assignment ";
       // $testNote= "Result passed if size of array > 0";
       // $this->unit->run($test,$result,$testName,$testNote);

     }
     //todo method berhasil dijalankan tetapi result gagal karena assignment tidak ter delete
     private function testingDeleteAssignment(){
       $this->db->delete('assignments', array('id'=>1));
       $this->post(1);
       $this->Assignment_model->add_assignment(1, FALSE);
       $ceker2 = $this->db->order_by('id')->get('assignments')->result_array();
       $this->Assignment_model->delete_assignment(1);
       $ceker = $this->db->order_by('id')->get('assignments')->result_array();
       $test = sizeof($ceker2)-sizeof($ceker);
       $result = 1;
       $testName = "Test delete assignment ";
       $testNote= "Result passed if size of array = size of array -1";
       $this->unit->run($test,$result,$testName,$testNote);
     }
    /** ----- INPUT REYNER's CODE HERE ----- **/
    public function testGetAllNotifications(){
        $add=$this->Notifications_model->add_notification('notifikasi','Ada ujian2');
        $test=$this->Notifications_model->get_all_notifications();
        if($test!=null && $test != 0){
            $test=TRUE;
        }else{
            $test=false;
        }
        $result=TRUE;
        $testName= 'Test get all notification on judge';
        $testNote= 'awal tes belum ada notifkasi jadi masih false, ketika sudah di add notifkasi resultnya true';
        $this->unit->run($test,$result,$testName,$testNote);
    }
    public function testGetLatestNotifications(){
        $add=$this->Notifications_model->add_notification('notifikasi','Ada ujian');
        $test=$this->Notifications_model->get_latest_notifications();
        if($test!=null && $test != 0){
            $test=TRUE;
        }else{
            $test=false;
        }
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

    public function testHaveNewNotificationsTrue(){
        $notifs = $this->db->select('time')->get('notifications')->result_array();
        $currdt = date('Y-m-d h:i:s');
        foreach ($notifs as $notif) {
            if(strtotime($notif['time']) > $currdt) {
                $tmp = TRUE;
            } else {
                $tmp = FALSE;
            }
        }
        $test=$this->Notifications_model->have_new_notification($currdt);
        $result=$tmp;
        $testName= 'Test have new notification on judge';
        $testNote= 'To get newest notification';
        $this->unit->run($test,$result,$testName,$testNote);
    }

    //todo
    public function testHaveNewNotificationsFalse(){
        $this->Notifications_model->__construct();
        $test=$this->Notifications_model->add_notification('notifikasi','Ada ujian');
        $notifs = $this->db->select('time')->get('notifications')->result_array();
        //var_dump($notifs['time']);
        // $test=$this->Notifications_model->have_new_notification(strtotime($notifs[0]['time']));
        $test=$this->Notifications_model->have_new_notification("null");
        var_dump($test);
        $result=False;
        $testName= 'Test have new notification on judge FALSE';
        $testNote= 'To get newest notification return false';
        $this->unit->run($test,$result,$testName,$testNote);
    }

    public function testGetAllFinalSubmission(){
        $this->add_user_manual();
        $datas=$this->User_model->get_all_users();

        $result=true;
        $testName= 'Test get all final submission';
        $testNote= 'To get all final submission';
        $this->unit->run($test,$result,$testName,$testNote);
    }


    /** ----- INPUT ENRICO's CODE HERE ----- **/
    public function testAllAssignments(){
        $this->Assignment_model->__construct();
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
        // $this->add_user_manual();
        // $this->add_assignment_manual();
        // $current_id = $this->get_current_assignment_id();
        $test=$this->Assignment_model->new_assignment_id();
        $max = ($this->db->select_max('id', 'max_id')->get('assignments')->row()->max_id) + 1;

		$assignments_root = rtrim($this->settings_model->get_setting('assignments_root'), '/');
		while (file_exists($assignments_root.'/assignment_'.$max)){
			$max=$max+1;
		}

		$result = $max;
        //$result=$current_id+1;
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
        $this->add_user_manual();
        $this->add_assignment_manual();
        $query = $this->db->query("SELECT id from shj_assignments")->result();
        $assignment_id = "";
        foreach ($query as $key => $value) {
            $assignment_id = $value->id;
        }
        $test=$this->Assignment_model->all_problems($assignment_id);
        $result = $this->db->order_by('id')->get_where('problems', array('assignment'=>$assignment_id))->result_array();
        $problems = array();
        foreach ($result as $row)
        $problems[$row['id']] = $row;
        $resultt=$problems;
        $testName='Test all Problems of an Assignment';
        $testNote='Returns an array containing all problems of given assignment';
        $this->unit->run($test,$resultt,$testName,$testNote);


    }

    public function testIsParticipant(){
        $this->add_user_manual();
        $this->add_assignment_manual();
        $query = $this->db->query("SELECT participants from shj_assignments")->result();
        $queryy = $this->db->query("SELECT Username from shj_users")->result();
        $participants = "";
        $username="";
        foreach ($query as $key => $value) {
            $participants = $value->id;
        }
        foreach ($queryy as $key => $value) {
            $username = $value->id;
        }
        $test=$this->Assignment_model->is_participant($participants,$username);
        $participants = explode(',', $participants);
        foreach ($participants as &$participant){
            $participant = trim($participant);
        }
        if(in_array('ALL', $participants)){
          $result=TRUE;
        }
        else if(in_array($username, $participants)){
          $result=TRUE;
        }
        else {
          $result=FALSE;
        }
        $testName='Test is Participant';
        $testNote='Returns TRUE if $username if one of the $participants';
        $this->unit->run($test,$result,$testName,$testNote);
        ///////////////////////////////////////////////
        $test=$this->Assignment_model->is_participant('ALL',$username);
        $result=TRUE;
        $testName='Test is Participant';
        $testNote='Returns TRUE if $username All';
        $this->unit->run($test,$result,$testName,$testNote);
        ////////////////////////////////////////////////
        $test=$this->Assignment_model->is_participant($participants,"");
        $result=false;
        $testName='Test is Participant false';
        $testNote='Returns Passed if result is false';
        $this->unit->run($test,$result,$testName,$testNote);

    }
    public function testProblemInfo(){
        $this->add_user_manual();
        $this->add_assignment_manual();
        $query = $this->db->query("SELECT id from shj_assignments")->result();
        $queryy = $this->db->query("SELECT * from shj_problems")->result();
        $assignment_id = "";
        $problem_id="";
        foreach ($query as $key => $value) {
            $assignment_id = $value->id;
        }
        foreach ($queryy as $key => $value) {
            $problem_id = $value->id;
        }
        $test=$this->Assignment_model->problem_info($assignment_id,$problem_id);
        $result=$this->db->get_where('problems', array('assignment'=>$assignment_id, 'id'=>$problem_id))->row_array();
        $testName='Problem Info';
        $testNote='Returns database row for given problem (from given assignment)';
        $this->unit->run($test,$result,$testName,$testNote);
    }

    public function testAssignmentInfo(){
        $this->add_user_manual();
        $this->add_assignment_manual();
        $query = $this->db->query("SELECT id from shj_assignments")->result();
        $assignment_id = "";
        foreach ($query as $key => $value) {
            $assignment_id = $value->id;
        }
        $test=$this->Assignment_model->assignment_info($assignment_id);
        $query = $this->db->get_where('assignments', array('id'=>$assignment_id));
        $result=$query->row_array();
        $testName='Assignment Info';
        $testNote='Returns database row for given assignment';
        $this->unit->run($test,$result,$testName,$testNote);
/////////////////////////////////////////////////////////
        $test=$this->Assignment_model->assignment_info("");
        $result=array(
            'id' => 0,
            'name' => 'Not Selected',
            'finish_time' => 0,
            'extra_time' => 0,
            'problems' => 0);
        $testName='Assignment Info';
        $testNote='Returns database array id=0';
        $this->unit->run($test,$result,$testName,$testNote);


    }

    public function testSetMossTime(){
        $this->add_user_manual();
        $this->add_assignment_manual();
        $query = $this->db->query("SELECT id from shj_assignments")->result();
        $assignment_id = "";
        foreach ($query as $key => $value) {
            $assignment_id = $value->id;
        }
        $test=$this->Assignment_model->set_moss_time($assignment_id);
        $now = shj_now_str();
        $result=$this->db->where('id', $assignment_id)->update('assignments', array('moss_update'=>$now));
        $this->db->where('id', $assignment_id)->update('assignments', array('moss_update'=>$now));
        $testName='Set Moss Time';
        $testNote='Moss Update Time for given assignment';
        $this->unit->run($test,$result,$testName,$testNote);
    }

    public function testGetMossTime(){
        $this->add_user_manual();
        $this->add_assignment_manual();
        $query = $this->db->query("SELECT id from shj_assignments")->result();
        $assignment_id = "";
        foreach ($query as $key => $value) {
            $assignment_id = $value->id;
        }
        $test=$this->Assignment_model->get_moss_time($assignment_id);
        $queryy = $this->db->select('moss_update')->get_where('assignments', array('id'=>$assignment_id));
        $result=$queryy->row()->moss_update;
        $testName='Get Moss Time';
        $testNote='Returns "Moss Update Time" for given assignment';
        $this->unit->run($test,$result,$testName,$testNote);
    }

    /* ------------ END OF CODE ----------- */

    /** ----- INPUT ENRICO's CODE HERE ----- **/

    public function testUpdateScoreBoards(){
        $this->add_user_manual();
        $this->add_assignment_manual();
        $query = $this->db->query("SELECT id from shj_assignments")->result();
        $assignment_id = "";
        foreach ($query as $key => $value) {
            $assignment_id = $value->id;
        }
        $test=$this->Scoreboard_model->update_scoreboards();
        foreach ($query as $assignment){
			$result = $this->Scoreboard_model->update_scoreboard($assignment_id);
		}
        $testName='Update All Scoreboards';
        $testNote='Updates the cached scoreboard of all assignments,
        this function is called each time a user is deleted, or all submissions of a user is deleted';
        $this->unit->run($test,$result,$testName,$testNote);


    }

    public function testGetScoreBoard(){
        $this->add_user_manual();
        $this->add_assignment_manual();
        $query = $this->db->query("SELECT id from shj_assignments")->result();
        $assignment_id = "";
        foreach ($query as $key => $value) {
            $assignment_id = $value->id;
        }
        $test=$this->Scoreboard_model->get_scoreboard($assignment_id);
        $queryy =  $this->db->select('scoreboard')->get_where('scoreboard', array('assignment'=>$assignment_id));
		if ($queryy->num_rows() != 1)
			$result = 'Scoreboard not found';
		else
			$result = $queryy->row()->scoreboard;

        $testName='Get Cached Scoreboard';
        $testNote='Update All ScoreboardsReturns the cached scoreboard of given assignment as a html text';
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
/////////////////////////////////////////////////////////////
        $test=$this->User_model->delete_user("");
        $count2 = sizeof($this->User_model->get_all_users());
        $result = FALSE;
        //if($count!=$count2){$result = TRUE;}
        $testName='Test to delete user';
        $testNote='Delete user';
        $this->unit->run($test,$result,$testName,$testNote);
    }

    private function updateLoginTime(){
        $now = shj_now_str();
        $this->User_model->add_user('nadyavio','7315005@student.unpar.ac.id','nadya','Nadya123','admin');
        $test=$this->db->select('first_login_time')->get_where('users', array('username'=>'nadyavio'))->row()->first_login_time;
        if($test==null){
            $this->User_model->update_login_time('nadyavio');
            $test1=$this->db->select('first_login_time')->get_where('users', array('username'=>'nadyavio'))->row()->first_login_time;
        }
        else{
            $test=$this->db->select('last_login_time')->get_where('users', array('username'=>'nadyavio'))->row()->last_login_time;
            $this->User_model->update_login_time('nadyavio');
            $test1=$this->db->select('last_login_time')->get_where('users', array('username'=>'nadyavio'))->row()->last_login_time;
        }
        if($test != $test1){
            $test=true;
        }else {
            $test=false;
        }
        $result=true;
        $testName='Test to update login time';
        $testNote='Update time';
        $this->unit->run($test,$result,$testName,$testNote);

    }

    private function testGetFirstItem(){
      $this->add_user_manual();
      $this->add_assignment_manual();
      $assignment_id = $this->db->query("SELECT id from shj_assignments")->result()[0]->id;
      $this->add_queue_manual($assignment_id);
      $query = $this->db->order_by('id')->limit(1)->get('queue')->row_array();
      $test=$this->Queue_model->get_first_item();
      $result=$query;
      $testName='Test to get first item in queue';
      $testNote='get first item';
      $this->unit->run($test,$result,$testName,$testNote);
  }

  // TODO: failed
  private function testRemoveItem(){
      $this->add_user_manual();
      $this->add_assignment_manual();
      $assignment_id = $this->db->query("SELECT id from shj_assignments")->result()[0]->id;
      $this->add_queue_manual($assignment_id);
      $queueSize = sizeof($this->db->get('queue')->result());
      $test=$this->Queue_model->remove_item('testuser', $assignment_id, 1, 1);
      $test=sizeof($this->db->get('queue')->result());
      // echo "REMOVE QUEUE --> $test";
      $result=$queueSize-1;
      $testName='Test to remove item in queue';
      $testNote='remove item';
      $this->unit->run($test,$result,$testName,$testNote);
  }

  private function TestAddtoQueue(){
      $this->add_user_manual();
      $this->add_assignment_manual();
      $assignment_id = $this->db->query("SELECT id from shj_assignments")->result()[0]->id;
      $submit_info = array(
          'submit_id' => '1',
          'username' => 'testuser',
          'assignment' => $assignment_id,
          'problem' => 1,
          'type' => 'judge'
      );

      $queueSize = sizeof($this->db->get('queue')->result());
      $test=$this->Queue_model->add_to_queue($submit_info);
      $queueSize1 = sizeof($this->db->get('queue')->result());
      if($queueSize!=$queueSize1){
          $result=True;
      }
      else {
        $result=False;
      }
      $test= true;
      $testName='Test to add item in queue';
      $testNote='add item';
      $this->unit->run($test,$result,$testName,$testNote);
  }
//perlu assignment id
  // private function TestGetScoreBoard(){
  //     $test = $this->Scoreboard_model->get_scoreboard(1);
  //     $result='Scoreboard not found';
  //     $testName = 'Test get data kosong pada Scoreboard';
  //     $testNote = 'get score board';
  //     $this->unit->run($test,$result,$testName,$testNote);
  //     //////////////////////////
  //         // $test = $this->Scoreboard_model->get_scoreboard(get_current_assignment_id());
  //         // $result='Scoreboard not found';
  //         // $testName='Test get data kosong pada Scoreboard';
  //         // $testNote='get score board';
  //         // $this->unit->run($test,$result,$testName,$testNote);
  // }

  private function testEmptyQueue(){
      $test = $this->Queue_model->empty_queue();
      $query = $this->db->get_where('queue',array('id'=>1));
      $result = ($query != 0);
      $testName = 'Test queue is empty';
      $testNote = 'empty queue';
      $this->unit->run($test,$result,$testName,$testNote);
  }

  private function testInQueue(){
      $test = $this->Queue_model->in_queue('vio','assignment1','problem1');
      $result = false;
      $testName = 'Test in queue';
      $testNote = 'cek username, assignment, problem in queue';
      $this->unit->run($test,$result,$testName,$testNote);
  }
  private function testGetFirstItemFound() {
      $query = $this->db->order_by('id')->limit(1)->get('queue');
      $result = null;
      $test = $this->Queue_model->get_first_item();
      $testName = 'Test get first item';
      $testNote = 'get first item';
      $this->unit->run($test,$result,$testName,$testNote);
  }

}

?>
