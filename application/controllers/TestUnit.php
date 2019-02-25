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

        // $this->db->empty_table('shj_assignments');
        // // // $this->db->empty_table('shj_logins');
        // $this->db->empty_table('shj_notifications');
        // $this->db->empty_table('shj_problems');
        // $this->db->empty_table('shj_queue');
        // $this->db->empty_table('shj_scoreboard');
        // // $this->db->empty_table('shj_sessions');
        // // $this->db->empty_table('shj_settings');
        // $this->db->empty_table('shj_submissions');
        //only for 'shj_users' table, only delete records other than id = 1 (root)
        // $this->db->query('DELETE FROM shj_users WHERE id != 1');

        /* ------------------------------------------------------------------ */

        /** KIPPI's FUNCTIONS HERE **/
        // $this->testGetSubmission('kippi123', 'PBO1', 'Test1', 1);
        // $this->testSetASetting('enable_log', 1);
        // $this->testEmptyAQueue();
        // $this->testAddQueue();

        /** YONATHAN's FUNCTIONS HERE **/
         $this->testAddUserTrue();
        // $this->testAddUserRoleInvalid();
        // $this->testAddUserUsernameExist();
        // $this->testAddUserErrorLowercase();
        // $this->testAddUserEmailExistError();
        // $this->testAddUserLengthUsernameError();
        // $this->testAddUserWrongUsernameAlphaNumeric();
        // $this->testHaveUserTrue();
        // $this->testhaveUserFalse();
        // $this->testUsernameToUserId();
        // $this->testUsernameToUserIdFalse();
        // $this->testUserIdToUsernameTrueId();
        // $this->testUserIdToUsernameFalseIdNotfound();
        // $this->testUserIdToUsernameFalseIdAlphanumeric();
        // $this->testInsertToLogs();

        /** REYNER's FUNCTIONS HERE **/
        $this->addNotifications();
        $this->testGetAllNotifications();
        $this->testGetLatestNotifications();

        /** ENRICO's FUNCTIONS HERE **/
        $this->testAllAssignments();
        $this->testNewAssignmentId();
        $this->testIncreaseTotalSubmits();


    /* ------------ END OF CODE ----------- */

         $this->add_user_manual();
        //$this->add_assignment_manual();
        $this->deleteUser();

        /** run report function here **/
        $this->report();

    }

    /* GLOBAL FUNCTIONS FOR TESTING */

    /*
    *   Function untuk add user menggunakan mysql $query
    */
    private function add_user_manual(){
        $data = array(
            'username'  => 'testuser',
            'password'  => '$2a$08$ZVY15Ckd5JpQjD6hViEP/OOto/mTjGPKJGtSz9.8TV5ofUoblsk2W',
            'display_name'  => 'adminn',
            'email' => 'adminn@gmail.com',
            'role'  => 'admin',
        );
        echo var_dump($this->db->insert('shj_users',$data));
    }
    /*
    *   Function untuk add assignment menggunakan mysql $query
    */
    // private function add_assignment_manual(){
    //     // echo var_dump($this->db->get('shj_assignments')->result());
    //
    //     $data =array(
    //         'name'  => 'cobaaa',
    //         'problmes'  => '1',
    //         'total_submits' => '0',
    //         'open'  => '0',
    //         'scoreboard'    => '0',
    //         'javaexceptions' => '0',
	// 		'description' => 'cobacobacoba',
	// 		'start_time' => '2019-02-21 00:00:00',
	// 		'finish_time' => '2019-02-22 00:00:00',
	// 		'extra_time' => '300',
	// 		'late_rule' => '/*
        //  * Put coefficient (from 100) in variable $coefficient.
        //  * You can use variables $extra_time and $delay.
        //  * $extra_time is the total extra time given to users
        //  * (in seconds) and $delay is number of seconds passed
        //  * from finish time (can be negative).
        //  *  In this example, $extra_time is 172800 (2 days):
        //  */
        //
        // if ($delay<=0)
        //   // no delay
        //   $coefficient = 100;
        //
        // elseif ($delay<=3600)
        //   // delay less than 1 hour
        //   $coefficient = ceil(100-((30*$delay)/3600));
        //
        // elseif ($delay<=86400)
        //   // delay more than 1 hour and less than 1 day
        //   $coefficient = 70;
        //
        // elseif (($delay-86400)<=3600)
        //   // delay less than 1 hour in second day
        //   $coefficient = ceil(70-((20*($delay-86400))/3600));
        //
        // elseif (($delay-86400)<=86400)
        //   // delay more than 1 hour in second day
        //   $coefficient = 50;
        //
        // elseif ($delay > $extra_time)
        //   // too late
        //   $coefficient = 0;',
			// 'participants' => 'ALL',
            // 'moss_update'   => 'Never',
			// 'archived_assignment' => '0',
        // );
        // echo var_dump("test");
        // echo var_dump($this->db->insert('shj_assignments',$data));
    // }

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
      $test=$this->User_model->add_user('GlobalAdmin','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
      $result='Username must be lowercase.';
      $testName= 'Test Add User on judge';
      $testNote= 'create new user admin';
      $this->unit->run($test,$result,$testName,$testNote);

    }
    private function testAddUserRoleInvalid(){
      $test=$this->User_model->add_user('globaladmin','admin@gmail.com', 'administrator', 'Admin10', '' );
      $result='Users role is not valid.';
      $testName= 'Test Add User on judge';
      $testNote= 'create new user admin';
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
    // $count=$this->query('SELECT COUNT (id) FROM shj_notifications');
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


    /** ----- INPUT ENRICO's CODE HERE ----- **/
    public function testAllAssignments(){
      $test=$this->Assignment_model->all_assignments();
      $result=$assignments;
      $testName='Test all assignments';
      $testNote='Returns a list of all assignments and their information';
      $this->unit->run($test,$result,$testName,$testNote);
    }

    public function testNewAssignmentId(){
        $test=$this->Assignment_model->new_assignment_id();
        $result=$max;
        $testName='Test new assignment id';
        $testNote='Finds the smallest integer that can be uses as id for a new assignment';
        $this->unit->run($test,$result,$testName,$testNote);
    }

    public function testIncreaseTotalSubmits(){
        $this->Assignment_model->increase_total_submits('');
        $test=$this->Assignment_model->increase_total_submits('T15062');
        $result=$total+1;
        $testName='Increse total submits';
        $testNote='Increases number of total submits for given assignment by one';
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

?>
