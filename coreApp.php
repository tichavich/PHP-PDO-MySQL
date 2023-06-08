<?php
//copyright 2022
//dev by: tichavichmos@gmail.com

// SQL Management
class SQLmanage{
  protected $_dbConnect;
  protected $_dateThai;
  protected $_yearThai;
  function __construct($db_way) {
      date_default_timezone_set('Asia/Bangkok');  //default timezone bangkok
      $this->_dbConnect = ($db_way === 0) ? null : $this->ConnectMySQL($db_way);        
      $this->_dateThai = (date('y')+43).date('m').date('d');
      $this->_yearThai = date('y')+43;  //ปีแบบไทยๆ                   
  } 
  // init Database connection
  function ConnectMySQL($db_route){
    $conf = parse_ini_file("config.ini",true);       //get configuration file
    try{
        $host = $conf[$db_route]["db_host"];
        $dbname = $conf[$db_route]["db_name"];
        $o_con = new PDO("mysql:host=$host;dbname=$dbname",$conf[$db_route]["db_user"],$conf[$db_route]["db_pass"]);
    }catch(PDOException $e){
        die("การเชื่อมต่อฐานข้อมูลเกิดความผิดพลาด!".$e->getMessage());        
    }
    //setcharactor for THAI FONT
    $o_con->query("SET character_set_results=utf8");
    $o_con->query("SET character_set_client=utf8");
    $o_con->query("SET character_set_connection=utf8");
    return $o_con;
  }

  public function PrepareDML($dml,$arr){
      //$dml = call queryCompareStore(:attribute)
      //$arr = ["attribute"=>data]
      try{
      $res = $this->_dbConnect->prepare($dml);
      return $res->execute($arr);
      //result = 1 หมายถึงบันทึกข้อมูลสำเร็จ
      }catch(Exception $e){
          echo 'Error: '.$e->getMessage();
      }
  }
  public function ExecuteDML($SQLCommand){
    //Support Storage Engine "InnoDB" by MySQL
      try{
          $this->_dbConnect->beginTransaction();
          $this->_dbConnect->exec($SQLCommand);
          $this->_dbConnect->commit();    
          return 1;        
      } catch (Exception $ex) {
          echo 'Error: '.$ex->getMessage();
          $this->_dbConnect->rollBack();
          return 0;
      }
  }
    //Query all database
    public function QueryFetchAllNum($QrySelect){
        $OutputQry = $this->_dbConnect->prepare($QrySelect);
        $OutputQry->execute();
        return $OutputQry->fetchAll(PDO::FETCH_NUM);
    }
    public function QueryFetchAllNumByPrepare($QrySelect,$Array){
    $OutputQry = $this->_dbConnect->prepare($QrySelect);
    $OutputQry->execute($Array);
    return $OutputQry->fetchAll(PDO::FETCH_NUM);
    }
    public function QueryFetchAllAssoc($QrySelect){
        $OutputQry = $this->_dbConnect->prepare($QrySelect);
        $OutputQry->execute();
        return $OutputQry->fetchAll(PDO::FETCH_ASSOC);
    }
    public function QueryFetchAllAssocByPrepare($QrySelect,$Array){
        $OutputQry = $this->_dbConnect->prepare($QrySelect);
        $OutputQry->execute($Array);
        return $OutputQry->fetchAll(PDO::FETCH_ASSOC);
    }

    //Query once database
    public function QueryFetchNum($QrySelect){
        $OutputQry = $this->_dbConnect->prepare($QrySelect);
        $OutputQry->execute();
        return $OutputQry->fetch(PDO::FETCH_NUM);            
    }
    public function QueryFetchNumByPrepare($QrySelect,$Array){
    $OutputQry = $this->_dbConnect->prepare($QrySelect);
    $OutputQry->execute($Array);
    return $OutputQry->fetch(PDO::FETCH_NUM);            
    }
    public function QueryFetchAssoc($QrySelect){
        $OutputQry = $this->_dbConnect->prepare($QrySelect);
        $OutputQry->execute();
        return $OutputQry->fetch(PDO::FETCH_ASSOC);
    }
    public function QueryFetchAssocByPrepare($QrySelect,$Array){
        $OutputQry = $this->_dbConnect->prepare($QrySelect);
        $OutputQry->execute($Array);
        return $OutputQry->fetch(PDO::FETCH_ASSOC);
    }

  public function StoreProcedure($SQLCommand,$ArrayInput){
      //$SQLCommand = call queryCompareStore(:Array)
      //$ArrayInput = ["attribute"=>data]
      $OutputQry = $this->_dbConnect->prepare($SQLCommand);
      $OutputQry->execute($Array); 
      return $OutputQry->fetchALL(PDO::FETCH_NUM); 
  }
  function __destruct() {
      unset($this->_dbConnect);
  }
}
class BackEnd extends SQLmanage{
  public $_your_URL;
  public $_count_row;
  public $_data_query;
  public $_row_per_page;
  function __construct($db_way){
    parent::__construct($db_way);
  }
  public function GetyourURL(){
    $this->_your_URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";
  }
  public function CountRowDB($dql){
    $this->_count_row = (int)$this->QueryFetchNum($dql)[0];
  }
  public function GETMethod(){
    $array_get = array();
    foreach (explode('&',$_SERVER["QUERY_STRING"]) as $index => $value){
      list($key, $item) = explode("=", $value);
      $array_get[$key] = $item;
    }
    return $array_get;
  }
  //  | << | 1 | 2 | 3 | >> |
  public function Pagination($page_num){
    $this->GetyourURL();
    $all_page_control = ceil($this->_count_row / (int)$this->_row_per_page);
    $active_con1 = ($page_num == 1) ? 'class="page-link active"':'class="page-link"';    
    $active_con3 = ($page_num == $all_page_control) ? 'class="page-link active"':'class="page-link"';
    echo '<div>
        <nav aria-label="Standard pagination">
            <ul class="pagination justify-content-center">
                <li class="page-item">
                    <a '.$active_con1.' href="'.$this->_your_URL.'?page=1" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>';
              for($i=1;$i<=$all_page_control;$i++){
                $active_con2 = ($page_num === $i) ? 'class="page-link active"':'class="page-link"';
                echo '<li class="page-item"><a '.$active_con2.' href="'.$this->_your_URL.'?page='.$i.'">'.$i.'</a></li>';
              }
                echo '<li class="page-item">
                    <a '.$active_con3.' href="'.$this->_your_URL.'?page='.$all_page_control.'" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
      </div>';
  }
   //   | 1 | 2 | 3 |
   public function PaginationSmall($page_num){
    $this->GetyourURL();
    $all_page_control = ceil($this->_count_row / (int)$this->_row_per_page);  
    echo '<div class="float-end">
        <nav aria-label="Pagination">
            <ul class="pagination pagination-sm justify-content-start">';
              for($i=1;$i<=$all_page_control;$i++){
                $active_con = ($page_num === $i) ? 'class="page-link active"':'class="page-link"';
                echo '<li class="page-item"><a '.$active_con.' href="'.$this->_your_URL.'?page='.$i.'">'.$i.'</a></li>';
              }
            echo '</ul>
        </nav>
      </div>';
  }
  function __destruct() {
    parent::__destruct();
  }
}


class FileSystem{
  protected $_system_directory;
  protected $_path_dir;
  public function __construct($path_directory){
      $this->_path_dir = $path_directory;
      date_default_timezone_set('Asia/Bangkok'); 
  }
  public function GetPathDirectory(){
      return $this->_path_dir;
  }
  public function GetSystemDirectory(){
      return $this->_system_directory;
  }
  public function ShowFiles(){
      $this->_system_directory = getcwd()."/".$this->_path_dir;
      $rd = scandir($this->_system_directory);
      if(sizeof($rd) > 2){
          return array_diff($rd,[".",".."]); 
      }
  }
  public function FilesExtensions($dir){
         return explode(".",$dir);   
  }
  public function FileSizes($dir){
      return $this->formatSizeUnits(filesize($dir));
  }

// Snippet from PHP Share: http://www.phpshare.org
public function FormatSizeUnits($bytes)
  {
      if ($bytes >= 1073741824)
      {
          $bytes = number_format($bytes / 1073741824, 2) . ' GB';
      }
      elseif ($bytes >= 1048576)
      {
          $bytes = number_format($bytes / 1048576, 2) . ' MB';
      }
      elseif ($bytes >= 1024)
      {
          $bytes = number_format($bytes / 1024, 2) . ' KB';
      }
      elseif ($bytes > 1)
      {
          $bytes = $bytes . ' bytes';
      }
      elseif ($bytes == 1)
      {
          $bytes = $bytes . ' byte';
      }
      else
      {
          $bytes = '0 bytes';
      }

      return $bytes;
  }
  function __destruct(){
      unset($this->directory);
  }
}
class JsonManages{
  private $_path_file;
  public $_dataset;
  function __construct($json_file){ 
    $this->_path_file = $json_file;
  } 
  public function GetLengthOfJSON(){
    return count($this->_dataset);
  }
  public function ReadFileJSON(){
    if(file_exists($this->_path_file)!= null){
      $rd = file_get_contents($this->_path_file); //อ่านข้อมูลไฟล์ JSON
      $this->_dataset = json_decode($rd);  //แปลงข้อมูล JSON -> Array                  
    }else{
        echo "<script>console.log('Found not json file');</script>";
    }
  }  
}
// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
?>
