<?php
class Response {
    public $success;
    public $data;
    
    function __construct($success, $data) {
        $this->success = $success;
        $this->data = $data;
        $this->send();
    }

    function send() {
        echo json_encode($this);
    }
}


?>