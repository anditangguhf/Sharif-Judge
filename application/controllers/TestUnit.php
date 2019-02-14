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

    /* COBA METHOD TEST 1 */
    private function sum($a, $b) {
        return $a+$b;
    }

    public function index() {
        // $testSum = $this->sum(4, 3);
        // $sumResult = 7;
        // $testName = 'SUM';
        //   $this->unit->run($testSum, $sumResult, $testName);

        /*
        *   Taruh code test di tempat masing2
        *   untuk menghindari adanya conflict di git!
        */

        /** ----- INPUT KIPPI's CODE HERE ----- **/

        /*
         * Testing function get_submission di file Submit_model.php
         * Expected to return FALSE
         */
        $test       = $this->Submit_model->get_submission('kippi123', 'PBO1', 'TestJava1', 1);
        $result     = FALSE;
        $testName   = "testGetSubmissionFalse";
        $testNote   = "Test get submission data that doesn't exists in db";
        $this->unit->run($test, $result, $testName, $testNote);
        // print_r($this->unit->result());

        /* ------------ END OF CODE ----------- */

        /** --- INPUT YONATHAN's CODE HERE ---- **/

        //User_model.php model
        //method untuk membuat user untuk test jangan di pake kalo metod lain belom kelar
        $test=$this->User_model->add_user('globaladmin','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
        $result=true;
        $testName= 'Test Add User on judge';
        $testNote= 'create new user admin';
         $this->unit->run($test,$result,$testName,$testNote);
        //1
        $test=$this->User_model->add_user('globaladmin!!!','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
        $result='Username may only contain alpha-numeric characters.';
        $testName= 'Test Add User on judge';
        $testNote= 'create new user admin';
         $this->unit->run($test,$result,$testName,$testNote);
        //2
        $test=$this->User_model->add_user('glo','admin@gmail.com', 'administrator', 'Ad', 'admin' );
        $result='Username or password length error.';
        $testName= 'Test Add User on judge';
        $testNote= 'create new user admin';
         $this->unit->run($test,$result,$testName,$testNote);
        //3
        $test=$this->User_model->add_user('globaladmin','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
        $result='User with this username exists.';
        $testName= 'Test Add User on judge';
        $testNote= 'create new user admin';
         $this->unit->run($test,$result,$testName,$testNote);
        //4
        $test=$this->User_model->add_user('globaladmin','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
        $result= 'User with this email exists.';
        $testName= 'Test Add User on judge';
        $testNote= 'create new user admin';
         $this->unit->run($test,$result,$testName,$testNote);
        //5
        $test=$this->User_model->add_user('GlobalAdmin','admin@gmail.com', 'administrator', 'Admin10', 'admin' );
        $result='Username must be lowercase.';
        $testName= 'Test Add User on judge';
        $testNote= 'create new user admin';
         $this->unit->run($test,$result,$testName,$testNote);
        //6
        $test=$this->User_model->add_user('globaladmin','admin@gmail.com', 'administrator', 'Admin10', '' );
        $result='Users role is not valid.';
        $testName= 'Test Add User on judge';
        $testNote= 'create new user admin';
         $this->unit->run($test,$result,$testName,$testNote);
        //7
        $test=$this->User_model->add_user('globaladminn','admin1@gmail.com', 'administrator', 'Admin10', 'admin' );
        $result=true;
        $testName= 'Test Add User on judge';
        $testNote= 'create new user admin';
         $this->unit->run($test,$result,$testName,$testNote);

        $test=$this->User_model->have_user('globaladmin');
        $result=True;
        $testName= 'Test Have User Name by Username on judge';
        $testNote= 'untuk hasil true jadi user sudah ada dalam database judge input ("globaladmin")';
         $this->unit->run($test,$result,$testName,$testNote);


        $test=$this->User_model->have_user('yonathan');
        $result=False;
        $testName= 'Test Have User Name by Username on judge';
        $testNote= 'untuk hasil false jadi username tidak ada dalam database judge input ("yonathan")';
         $this->unit->run($test,$result,$testName,$testNote);


        $id= $this->User_model->username_to_user_id('globaladmin');
        $test=$this->User_model->user_id_to_username($id);
        $result='globaladmin';
        $testName= 'Test get user name by user id on database';
        $testNote= 'untuk hasil passed id dan username ada dalam database input("1")';
         $this->unit->run($test,$result,$testName,$testNote);


        $test=$this->User_model->user_id_to_username('asdf');
        $result=False;
        $testName= 'Test get user name by user id on database';
        $testNote= 'untuk hasil failed id yang diinput bukan numeric input ("asdf")';
         $this->unit->run($test,$result,$testName,$testNote);


        $test=$this->User_model->user_id_to_username('2');
        $result=False;
        $testName= 'Test get user name by user id on database';
        $testNote= 'untuk hasil failed id tidak ada dalam database input ("2")';
         $this->unit->run($test,$result,$testName,$testNote);


        $test=$this->User_model->username_to_user_id('globaladmin');
        $result=$id;
        $testName= 'Test get id by username on database';
        $testNote= 'untuk hasil passed username ada dalam database input ("globaladmin")';
         $this->unit->run($test,$result,$testName,$testNote);

        $test=$this->User_model->username_to_user_id('yonathan');
        $result=FALSE;
        $testName= 'Test get id by username on database';
        $testNote= 'untuk hasil passed username tidak ada dalam database input ("yonathan")';
         $this->unit->run($test,$result,$testName,$testNote);

        //have email
        $test=$this->User_model->have_email('admin@gmail.com');
        $result=FALSE;
        $testName= 'Test have email on database';
        $testNote= '';
         $this->unit->run($test,$result,$testName,$testNote);


        $test=$this->User_model->have_email('admin@gmail.com','adminglobal');
        $result=FALSE;
        $testName= 'Test have email on database';
        $testNote= '';
         $this->unit->run($test,$result,$testName,$testNote);


        $test=$this->User_model->have_email('admin@gmail.com','adminasdasda');
        $result=True;
        $testName= 'Test have email on database';
        $testNote= '';
         $this->unit->run($test,$result,$testName,$testNote);
        /* ------------ END OF CODE ----------- */

        /** ---- INPUT REYNER's CODE HERE ----- **/
        $test=$this->Notifications_model->get_all_notifications();
        $result=TRUE;
        $testName= 'Test get all notification on judge';
        $testNote= 'awal tes belum ada notifkasi jadi masih false, ketika sudah di add notifkasi resultnya true';
        $this->unit->run($test,$result,$testName,$testNote);

        $test=$this->Notifications_model->get_latest_notifications();
        $result=TRUE;
        $testName= 'Test get latest notification on judge';
        $testNote= 'awal tes belum ada notifkasi jadi masih false, ketika sudah di add notifkasi resultnya true';
        $this->unit->run($test,$result,$testName,$testNote);

        // $test=$this->Notifications_model->add_notification('notifikasi','Ada ujian');
        // $result=;
        // $testName='Test to add notification on judge'
        // $testNote='notifikasi harus di add terlebih dahulu'
        // echo $this->unit->run($test,$result,$testName,$testNote);

        /* ------------ END OF CODE ----------- */

        /** ---- INPUT ENRICO's CODE HERE ----- **/

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
}

?>
