<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TestUnit extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('unit_test');
        $this->load->model('Assignment_model');     //VIO & COCO
        $this->load->model('Hof_model');
        $this->load->model('Logs_model');
        $this->load->model('Notifications_model');  //REYNER
        $this->load->model('Queue_model');
        $this->load->model('Scoreboard_model');
        $this->load->model('Settings_model');
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
        $this->testGetSubmissionFalse();

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

        /** REYNER's FUNCTIONS HERE **/
        $this->addNotifications();
        $this->testGetAllNotifications();
        $this->testGetLatestNotifications();

        /** ENRICO's FUNCTIONS HERE **/

        /** VIO's FUNCTIONS HERE **/

        /** run report function here **/
        $this->report();

        /* ------------------------------------------------------------------ */

        /** --- INPUT YONATHAN's CODE HERE ---- **/

        //User_model.php model
        //method untuk membuat user untuk test jangan di pake kalo metod lain belom kelar












        /* ------------ END OF CODE ----------- */

        /** ---- INPUT ENRICO's CODE HERE ----- **/
        //testabcdsasdas
        /* ------------ END OF CODE ----------- */

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
     * Expected to return FALSE
     */
    public function testGetSubmissionFalse() {
        $test       = $this->Submit_model->get_submission('kippi123', 'PBO1', 'TestJava1', 1);
        $result     = FALSE;
        $testName   = "testGetSubmissionFalse";
        $testNote   = "Test get submission data that doesn't exists in db";
        $this->unit->run($test, $result, $testName, $testNote);
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
    $test=$this->Notifications_model->add_notification('notifikasi','Ada ujian');
    $result=$count+1;
    $testName='Test to add notification on judge';
    $testNote='Add notifications';
    $this->unit->run($test,$result,$testName,$testNote);
  }


    /** ----- INPUT ENRICO's CODE HERE ----- **/


    /** ----- INPUT VIO's CODE HERE ----- **/


}

?>
