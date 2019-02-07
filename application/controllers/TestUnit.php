<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TestUnit extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('unit_test');
        $this->load->model('Assignment_model');
        $this->load->model('Hof_model');
        $this->load->model('Logs_model');
        $this->load->model('Notifications_model');
        $this->load->model('Queue_model');
        $this->load->model('Scoreboard_model');
        $this->load->model('Settings_model');
        $this->load->model('Submit_model');
        $this->load->model('User_model');
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
        //  echo $this->unit->run($testSum, $sumResult, $testName);

        /*
        *   Taruh code test di tempat masing2
        *   untuk menghindari adanya conflict di git!
        */

        /** ----- INPUT KIPPI's CODE HERE ----- **/

        /* ------------ END OF CODE ----------- */

        /** --- INPUT YONATHAN's CODE HERE ---- **/

        //User_model.php model
        $test=$this->User_model->have_user('reyner');
        $result=True;
        $testName= 'Test Have User Name by Username on judge';
        $testNote= 'untuk hasil true jadi user sudah ada dalam database judge';
        echo $this->unit->run($test,$result,$testName,$testNote);


        $test=$this->User_model->have_user('reyner');
        $result=True;
        $testName= 'Test Have User Name by Username on judge';
        $testNote= 'untuk hasil false jadi username tidak ada dalam database judge';
        echo $this->unit->run($test,$result,$testName,$testNote);


        $test=$this->User_model->user_id_to_username('1');
        $result='reyner';
        $testName= 'Test get user name by user id on database';
        $testNote= 'untuk hasil passed id dan username ada dalam database';
        echo $this->unit->run($test,$result,$testName,$testNote);


        $test=$this->User_model->user_id_to_username('asdf');
        $result=FALSE;
        $testName= 'Test get user name by user id on database';
        $testNote= 'untuk hasil failed id yang diinput bukan numeric';
        echo $this->unit->run($test,$result,$testName,$testNote);


        $test=$this->User_model->user_id_to_username('2');
        $result=FALSE;
        $testName= 'Test get user name by user id on database';
        $testNote= 'untuk hasil failed id tidak ada dalam database';
        echo $this->unit->run($test,$result,$testName,$testNote);

        /* ------------ END OF CODE ----------- */

        /** ---- INPUT REYNER's CODE HERE ----- **/
        $test=$this->Notifications_model->get_all_notifications();
        $result=FALSE;
        $testName= 'Test get all notification on judge';
        $testNote= 'untuk hasil true jadi user sudah ada dalam database judge';
        echo $this->unit->run($test,$result,$testName,$testNote);
        /* ------------ END OF CODE ----------- */

        /** ---- INPUT ENRICO's CODE HERE ----- **/

        /* ------------ END OF CODE ----------- */

        /** ------ INPUT VIO's CODE HERE ------ **/


    }
}

?>
