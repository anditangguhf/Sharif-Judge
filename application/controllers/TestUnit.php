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
        $this->load->model('Scoreboard_model');
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

        $this->db->empty_table('shj_assignments');
        // // $this->db->empty_table('shj_logins');
        $this->db->empty_table('shj_notifications');
        $this->db->empty_table('shj_problems');
        $this->db->empty_table('shj_queue');
        $this->db->empty_table('shj_scoreboard');
        // $this->db->empty_table('shj_sessions');
        // $this->db->empty_table('shj_settings');
        $this->db->empty_table('shj_submissions');
        //only for 'shj_users' table, only delete records other than id = 1 (root)
        // $this->db->query('DELETE FROM shj_users WHERE id != 1');

        /* ------------------------------------------------------------------ */

        /** KIPPI's FUNCTIONS HERE **/
        // $this->testGetSubmission('kippi123', 'PBO1', 'Test1', 1);
        // $this->testSetASetting('enable_log', 1);
        // $this->testEmptyAQueue();
        $this->testAddQueue();

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

        /** REYNER's FUNCTIONS HERE **/
        // $this->addNotifications();
        // $this->testGetAllNotifications();
        // $this->testGetLatestNotifications();

        /** ENRICO's FUNCTIONS HERE **/

        /** VIO's FUNCTIONS HERE **/

        /** run report function here **/
        $this->report();

        /* ------------------------------------------------------------------ */

        /** ------ INPUT VIO's CODE HERE ------ **/
        $test=$this->Assignment_model->add_assignment('DAA1', FALSE);
        $result=FALSE;
        $testName= '';
        $testNote= '';
        $this->unit->run($test,$result,$testName,$testNote);

        $test=$this->Assignment_model->add_assignment('DAA2', TRUE);
        $result=TRUE;
        $testName= '';
        $testNote= '';
        $this->unit->run($test,$result,$testName,$testNote);

        $test=$this->Assignment_model->delete_assignment('DAA1');
        $result='';
        $testName= '';
        $testNote= '';
        $this->unit->run($test,$result,$testName,$testNote);

    }

    /** ----- INPUT KIPPI's CODE HERE ----- **/

    /*
     * Testing function get_submission di file Submit_model.php
     */
    private function testGetSubmission($username, $assignment, $problem, $submit_id) {
        $test       = $this->Submit_model->get_submission($username, $assignment, $problem, $submit_id);
        $result     = FALSE;
        $testName   = "testGetSubmissionFalse";
        $testNote   = "Test get submission data that doesn't exists in db";
        $this->unit->run($test, $result, $testName, $testNote);
    }

    /*
    *   Testing function to get submission after a submission is added to db
    *   Expected to return a table row of the added submission
    */
    private function testGetSubmissionAfterAdd($username, $assignment, $problem, $submit_id) {
        // do add submission first

        // do test get submission here
        testGetSubmission($username, $assignment, $problem, $submit_id);
    }

    /*
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

    /*
    *   QUEUE_MODEL
    *   Testing function to empty a queue
    *   Expected to return true, meaning test succeed
    */
    private function testEmptyAQueue() {
        $totalQueue = sizeof($this->Queue_model->get_queue());
        $test       = $this->Queue_model->empty_queue();
        $result     = true;
        $testName   = 'testEmptyAQueue';
        $testNote   = 'test to empty queue table from db, expected to return true';
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
  public function addNotifications(){
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

  }






    /** ----- INPUT ENRICO's CODE HERE ----- **/


    /** ----- INPUT VIO's CODE HERE ----- **/


}

?>
