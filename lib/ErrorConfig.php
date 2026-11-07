<?php
class ErrorConfig
{
    private $koneksi;
    private $db_dns, $db_user, $db_pass, $db_dns2, $mode;

    public function __construct($mode,$user_id,$creator,$lokasi)
    {
       
        $this->mode = $mode;
        $this->user_id = $user_id;
        $this->creator = $creator;
        $this->lokasi = $lokasi;

    }

    public function error($msg, $params =[],$dev_msg=[])
    {
           $response = [];

           $response['msg']=$msg;
           $response['content']=$params;
           $response['success']=false;
           $response['user_id']=$this->user_id;
           $response['creator']=$this->creator;
           $response['lokasi']=$this->lokasi;

           $response['token']='valid';
           $response['dev_msg']=[];
           if($this->mode == 'dev'){
                $response['dev_msg']=$dev_msg;
           }

           return json_encode($response);


    }

     public function succes($msg, $params = [],$dev_msg=[])
    {
           $response = [];

           $response['msg']=$msg;
           $response['content']=$params;
           $response['success']=true;
           $response['user_id']=$this->user_id;
           $response['creator']=$this->creator;
           $response['token']='valid';
           $response['lokasi']=$this->lokasi;
           $response['dev_msg']=[];

           return json_encode($response);
    }

   
}
