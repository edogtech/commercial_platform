<?php
namespace Admin\Controller;
use Think\Controller;
class ManagementController extends Controller {
    public function mindex(){
        $this->display('management/mindex');
    }
}
