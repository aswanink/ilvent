<?php

    /***************DATABASE OPERATION CLASS STARTS ********************/
      
    class idealink{

        private $db                   = DB_DATABASE;
        private $host                 = DB_HOST;
        private $user                 = DB_USER;
        private $pass                 = DB_PASS;
		public string $tableName; // Declare the property
		public string $today;
		private $resource;
		public array $fields; // Explicitly declare the property
		public int|null $insert_id = null; 
        /*********************************************************
                     COMMON AREA OF CLASS - START
        **********************************************************/
		//PHP Deprecated:  Creation of dynamic property idealink::$tableName is deprecated
        function __construct($tableName){
            $this->today = date("Y-m-d");
            $this->tableName=$tableName;
            $this->resource = $this->connectDb();
			$this->fields = $this->optimiseFields();
        }
  		
		function __destruct(){
		   mysqli_close($this->resource);
		}
        /* METHOD : CONNECT DATABASE START */
      
		private function connectDb() {
			$connection = @mysqli_connect($this->host, $this->user, $this->pass,$this->db) or die("<span style='font-family:Arial, Helvetica, sans-serif; color:#FF0000; font-size:12px; font-weight:bold;'>An error occured for DATABASE ,Contact System Administrator : </span>" . mysqli_connect_error());
			//  @mysql_select_db($this->db)or die("<span style='font-family:Arial, Helvetica, sans-serif; color:#FF0000; font-size:12px; font-weight:bold;'>An error occured for DATABASE ,Contact System Administrator : </span>" . mysqli_errno());
			return $connection;
		}
      
        /* METHOD : CONNECT DATABASE END */      
		public function getDbConnection() {
			return $this->resource;
		}

        /* METHOD : mysqli_query START */
       public function query($sql) {
        // echo $sql."<br>"; //exit;
        $res = mysqli_query($this->resource,$sql);
        if (!$res) { echo $sql."<br>"; //exit;
		//Mail to support 
                  $email_support_id   = 'anoopta@gmail.com';
                  $email_support_subb = 'Error reported ';
                  $error_sup_loca= ($_SERVER['QUERY_STRING']<>'')?$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']:$_SERVER['PHP_SELF'];
                  $email_support_errorcontent = 'There is a <b style="color:red">Data base error '.mysqli_errno($this->resource).'</b> in <b style="color:red">'.$error_sup_loca.'.</b></br>';   
				//  $email_support_errorcontent.='Query is - '.$sql.'</br>';
                //  $email_support_errorcontent.='<br>IP Address : '.$_SERVER['REMOTE_ADDR'];
				//  $email_support_errorcontent.='<br>Date & Time : '.IDEA_TIMESTAMP;
				//  SendSimpleMailMessage($email_support_subb,$email_support_errorcontent,$email_support_id); 
                //
            die("<span style='color:red;font-size:12px;font-weight:bold;'>Database error : ".mysqli_errno($this->resource)." -Please Contact your Software Support</span>");
        } else {
            return $res;
        }
    }
  
        /* METHOD : mysqli_query END */

        /* METHOD :VALIDATES USER INPUTS ARRAY,$_GET,$_POST,$_REQUEST etc START*/
		public function validateUserInput($array)     
		{
			if (!is_array($array)) {
				return []; // Return an empty array if input is invalid
			}
			$db = $this->getDbConnection();
			foreach ($array as $key => $val) {
				if (is_string($val)) {
					$array[$key] = mysqli_real_escape_string($db, trim((string)$val));
				} elseif (is_array($val)) {
					$array[$key] = $this->validateUserInput($val); // Recursively sanitize nested arrays
				} else {
					$array[$key] = $val; // Leave other types (e.g., numbers) unchanged
				}
			}

			return $array; 
		}
        public function validateUserInputtest($array)     
        {
         	if($array ){
				foreach($array as $key=> $val){
					 $array[$key]    = addslashes(trim($val));	
				}
			}
           return $array; 
        }

        /* METHOD :VALIDATES USER INPUTS ARRAY,$_GET,$_POST,$_REQUEST etc START*/

        /* METHOD :VALIDATES USER STRING START*/

        public function validateUserString($string){
 
           return $string;
   
        }

        /* METHOD :VALIDATES USER STRING END*/

		/* METHOD GENERATE A RANDOM STRING STARTS */
		function getToken($length =6){
			return substr(md5(rand().rand()), 0, $length);
		}
		/* METHOD GENERATE A RANDOM STRING ENDS */
		
		function stringPad($data,$pad_length,$pad_element,$prefix){
			$token = str_pad($data,$pad_length,$pad_element,STR_PAD_LEFT);
			$token = $prefix.$token;
			return $token;
		}
		/*METHOD TO FIND NEXT OR PREVIOUS DATE*/
		function finddate($currentdate,$interval,$addorminus,$IntervalType){
		//Current date => Date from which it should be calculated
		//Interval => Tell the intervals by number to be added or substracted
		//AddorMinus => To Add date or to Substract Date
		//IntervalType => To give interval type like Day, Month or Year.
		
			$date = $currentdate;
			if(strtoupper($IntervalType)=="DAY"){
				$IntervalType="day";
			}
			if(strtoupper($IntervalType)=="MONTH"){
				$IntervalType="month";
			}
			if(strtoupper($IntervalType)=="YEAR"){
				$IntervalType="year";
			}
			if(strtoupper($addorminus)=="ADD"){
				$newdate = strtotime ( "+".$interval." ".$IntervalType  , strtotime ( $date ) ) ;
			}
			if(strtoupper($addorminus)=="MINUS"){
				$newdate = strtotime ( "-".$interval." ".$IntervalType  , strtotime ( $date ) ) ;
			}
			$newdate = date ( 'Y-m-j' , $newdate );
 
			return $newdate;
		}
		
		function beginTrans(){
				$this->query("SET autocommit=0");
				$this->query("START TRANSACTION");
		}
		
		function commitTrans(){
			$this->query("COMMIT");
		}

        /*METHOD : OPTIMISE FIELDS STARTS */
        //Get Table fields as an array

        private function optimiseFields(){
			//echo "SHOW COLUMNS FROM ".$this->tableName;
            $result = $this->query("SHOW COLUMNS FROM ".$this->tableName);
            if (!$result){
                   die("<span style='color:red;font-size:12px;font-weight:bold;'>Database error : ".mysqli_errno($this->resource)." -Please Contact your Software Support</span>");
                 
            }
            if (mysqli_num_rows($result) > 0){
                $structure = array();
                while ($row = mysqli_fetch_assoc($result))
                {
                    $structure[$row['Field']] = '';
                }
            }
            mysqli_free_result($result);
            return $structure;
        }

        /* METHOD OPTIMISE FIELDS ENDS */
   
        /* METHOD CLEAR ARRAY STARTS */
        //Will Clear the repeated Elements from array

        private function clearArray($array) 
        {
            if (!is_array($array) || count($array)==0){
                      die("<span style='color:red;font-size:12px;font-weight:bold;'>Database Field error :  -Please Contact your Software Support</span>");
            }
        //    $this->fields        = $this->optimiseFields();
            foreach ($array as $key => $val){
                if (!array_key_exists($key, $this->fields)){
                    unset($array[$key]);
                }
                else{
                  //  $val = mysqli_real_escape_string($this->resource,$val);
					$val = mysqli_real_escape_string($this->resource, $val ?? '');
                }
            }
            return $array;
        }

        /* METHOD CLEAR ARRAY ENDS */

        /* METHOD GET PRIMARY KEY STARTS */
        //getPrimarykey of the table

        private function getPrimaryKey()
        {
            $result = $this->query("SHOW COLUMNS FROM ".$this->tableName);
            if (!$result){
                  die("<span style='color:red;font-size:12px;font-weight:bold;'>Database error : ".mysqli_errno($this->resource)." -Please Contact your Software Support</span>");
            }
            else
            {
                if (mysqli_num_rows($result) > 0)
                {
                    while ($row = mysqli_fetch_assoc($result))
                    {
                        if ($row['Key'] == 'PRI')
                        {
                            $primaryKey = $row['Field'];
                        }
                    }
                    mysqli_free_result($result);
                    return $primaryKey;
                }
            }
        }

    function isExist($condition = NULL)
    {
        $res          = $this->getCount($condition);
        if ($res >= 1)
            return true;
        return false;
    }
     

        /*******************************************************
                         COMMON AREA OF CLASS - END
        ********************************************************/ 

        /*******************************************************
                     FETCH DATA FROM DATEBASE - STARTS
        ********************************************************/

  
         /* METHOD : CREATE AN SELECT SQL QUERY START */  
            //Condition and other parameters must be validated for malicious script attacks by developer
            //Return an SQLString
        private function createSelectQuery($fields=NULL,$condition=NULL,$order=NULL,$group=NULL,$limit=NULL){

            $fields         =    $fields             ? $fields                 : "*";
            $condition      =    $condition         ? " WHERE ".$condition    : "";
            $order          =    $order             ? " ORDER BY ".$order     : "";
            $group          =    $group             ? " GROUP BY ".$group     : "";
            $limit          =    $limit             ? " LIMIT ".$limit           : "";  
            $sql            =    "SELECT ".$fields." FROM ".$this->tableName.$condition.$order.$group.$limit;
            return $sql;
        }

         /* METHOD : CREATE AN SQL QUERY END */

         /* METHOD : GET A ROW OF DATA START */  
            //Return an Associative array
      
        public function getRowData($fields=NULL,$condition=NULL,$order=NULL,$group=NULL,$limit=NULL){
                $sql            =   $this->createSelectQuery($fields,$condition,$order,$group,$limit);
		//echo $sql; //exit;
                $res            =    $this->query($sql);
                $returnArray    =    mysqli_fetch_array($res,MYSQLI_ASSOC);
                mysqli_free_result($res);
                return $returnArray;
        }  

        /* METHOD : GET A ROW OF DATA END */
		
         /* METHOD : GET COUNT START */  
            //Return an SINGLE INTEGER VALUE
      
        public function getCount($condition){
		
        	 $condition      =    $condition         ? " WHERE ".$condition    : "";
			$this->primaryKey = $this->getPrimaryKey();
			$sql 	= "SELECT count({$this->primaryKey}) FROM {$this->tableName} $condition"; 
			//echo $sql; exit;
			$result = $this->query($sql);
			$res = mysqli_fetch_row($result);
			mysqli_free_result($result); 
			return $res[0];
        }  

        /* METHOD : GET A ROW OF DATA END */


        public function getSingleField($fields=NULL,$condition=NULL){
                $sql            =   $this->createSelectQuery($fields,$condition);
				//echo $sql;
                $res            =    $this->query($sql);
                $returnArray    =    mysqli_fetch_row($res);
                mysqli_free_result($res);
                return $returnArray[0];
        }  
		
		
        /* METHOD : GET DATA FROM DATABASE AS AN ARRAY START */         

        public function getData($fields=NULL,$condition=NULL,$order=NULL,$group=NULL,$limit=NULL){
                $sql            =   $this->createSelectQuery($fields,$condition,$order,$group,$limit);
				//echo $sql; //exit;
                $res            =    $this->query($sql);
                while($array    =    mysqli_fetch_array($res,MYSQLI_ASSOC)){
                    $returnArray[] = $array;
                }
                mysqli_free_result($res);
                return $returnArray;
        }
      
        public function joinQueryRow($sql){
		//echo $sql; //exit;
                $result    = $this->query($sql);
                $array = mysqli_fetch_array($result,MYSQLI_ASSOC);
                mysqli_free_result($result);
                return $array;
        }
      
       public function joinQuery($sql){
    // echo $sql; //exit;
    $res = $this->query($sql);
    $returnArray = [];  // Initialize the returnArray to avoid the undefined variable warning

    while($array = mysqli_fetch_array($res, MYSQLI_ASSOC)){
        $returnArray[] = $array;
    }

    mysqli_free_result($res);
    return $returnArray;
}

        /* METHOD : CREATE A LIST BOX STARTS */

        public function createListBox($strKey, $strVal, $selected = NULL, $where = NULL, $order = NULL)
        {
            $strOptions = "";
            $where  = ($where) ? " WHERE $where "       : "";
            $order  = ($order) ? " ORDER BY $order "    : "";
            $sql    = "SELECT * FROM {$this->tableName} $where $order;"; //echo $sql; exit;
            $res  = $this->query($sql);
            while($array    =   mysqli_fetch_array($res ,MYSQLI_ASSOC)){
                    $result[]  = $array;
            }
            mysqli_free_result($res); 
            if($result)
            foreach ($result as $key => $val){
                $strOptions .= "<option value='{$val[$strKey]}' ".(($val[$strKey] == $selected) ? "selected" : "").">".ucfirst($val[$strVal])."</option>\r\n";
            }
            return $strOptions;
        }

		//URL ENCODE
	public function encodeUrlId($id){
	//	$urlid=base64_encode($id);
	//	return $urlid;
		return $id;
	}
		
		//URL DECODE
	 public function decodeUrlId($id){
     //   $urlid=base64_decode($id);
	//	return $urlid;
		return $id;
     }

	

	function getPageLink($pgNo, $totPage, $url, $count = "5")
	{	
		$intPre 	= $pgNo - 1;
		$intNex 	= $pgNo + 1;
		$intFirst 	= $pgNo - 5; 
		$intLast 	= $pgNo + 5;
		$strReturn  ="";

		if ($intFirst <= 0){
			$intFirst	= 1;
		}
		
		if ($intLast >= $totPage){
			$intLast	= $totPage;
		}
		
		if ($intPre <= 0){
			$intPre		= 1;
		} 
		else {
			$strReturn	= str_replace("{pgNo}", "$intPre", $url);
			$strReturn	= str_replace("{pgTxt}", "Prev", $strReturn); 
		}
		
		for ($i = $intFirst; $i <= $intLast; $i++){
			if ($i != $pgNo) {
				$strTemp	= str_replace("{pgNo}", "$i", $url);
				$strReturn	.= str_replace("{pgTxt}", "$i", $strTemp);
			} else {
				$strReturn	.= "<li class='active'><a href=''>$i</a></li>";
			}
		} 
		if ($intNex > $totPage){
			$intNex		= $totPage;
		} 
		else {
			$strTemp	= str_replace("{pgNo}", "$intNex", $url);
			$strReturn .= str_replace("{pgTxt}", "Next", $strTemp); 
		}
		$strReturn= '<div class="pagination"><ul>'.$strReturn.'</ul></div>';
		return $strReturn;
	}
	function getPaginationString($page = 1, $totalitems=1, $limit = 10, $adjacents = 1, $targetpage=''){		
        //defaults
        if(!$adjacents) $adjacents = 1;
        if(!$limit) $limit = 10;
        if(!$page) $page = 1;
        $url = $targetpage;
        
        //other vars
        $prev = $page - 1;									//previous page is page - 1
        $next = $page + 1;									//next page is page + 1
        $lastpage = ceil($totalitems / $limit);				//lastpage is = total items / items per page, rounded up.
        $lpm1 = $lastpage - 1;								//last page minus 1
        
        /* 
            Now we apply our rules and draw the pagination object. 
            We're actually saving the code to a variable in case we want to draw it more than once.
        */
        $pagination = "";
        if($lastpage > 1)
        {	
           // $pagination .= "<div class=\"pagination\">";
            //previous button
            if ($page > 1){
                $strTemp	= str_replace("{pgNo}", "$prev", $url);
                $pagination	.= str_replace("{pgTxt}", '<i class="fa-solid fa-angles-left"></i>', $strTemp);
                //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=$prev\"><<</a>";
            }else{
                $pagination.= "<li class='page-item disabled'>
                                    <a class='page-link' href='javascript:void(0)' tabindex='-1'>
                                        <i class='fa-solid fa-angles-left'></i>
                                    </a>
                                </li>";	
            }
            //pages	
            if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
            {	
                for ($counter = 1; $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page){
                        $pagination.= "<li class=\"page-item active\"><a class='page-link' href='javascript:void(0)'>$counter</a></li>";
                    }else{
                        $strTemp	= str_replace("{pgNo}", "$counter", $url);
                        $pagination	.= str_replace("{pgTxt}", "$counter", $strTemp);
                        //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=$counter\">$counter</a>";
                    }					
                }
            }
            elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
            {
                //close to beginning; only hide later pages
                if($page < 1 + ($adjacents * 2))		
                {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                    {
                        if ($counter == $page){
                            $pagination.= "<li class=\"page-item active\"><a class='page-link' href='javascript:void(0)'>$counter</a></li>";
                        }else{
                            $strTemp	= str_replace("{pgNo}", "$counter", $url);
                            $pagination	.= str_replace("{pgTxt}", "$counter", $strTemp);
                            //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=$counter\">$counter</a>";	
                        }				
                    }
                    $pagination.= "...";
                    $strTemp	= str_replace("{pgNo}", "$lpm1", $url);
                    $pagination	.= str_replace("{pgTxt}", "$lpm1", $strTemp);
                    $strTemp	= str_replace("{pgNo}", "$lastpage", $url);
                    $pagination	.= str_replace("{pgTxt}", "$lastpage", $strTemp);
                    //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=$lpm1\">$lpm1</a>";
                    //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=$lastpage\">$lastpage</a>";		
                }
                //in middle; hide some front and some back
                elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                {  // This is the location
                    $strTemp	= str_replace("{pgNo}", "1", $url);
                    $pagination	.= str_replace("{pgTxt}", "1", $strTemp);
                    //$strTemp	= str_replace("{pgNo}", "2", $url);
                    //$pagination	.= str_replace("{pgTxt}", "2", $strTemp);
                    //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=1\">1</a>";
                    //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=2\">2</a>";
                    $pagination.= "...";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                    {
                        if ($counter == $page){
                            $pagination.= "<li class=\"page-item active\"><a class='page-link' href='javascript:void(0)'>$counter</a></li>";
                        }else{
                            $strTemp	= str_replace("{pgNo}", "$counter", $url);
                            $pagination	.= str_replace("{pgTxt}", "$counter", $strTemp);
                            //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=$counter\">$counter</a>";	
                        }				
                    }
                    $pagination.= "...";
                    $strTemp	= str_replace("{pgNo}", "$lpm1", $url);
                    $pagination	.= str_replace("{pgTxt}", "$lpm1", $strTemp);
                    $strTemp	= str_replace("{pgNo}", "$lastpage", $url);
                    $pagination	.= str_replace("{pgTxt}", "$lastpage", $strTemp);
                    //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=$lpm1\">$lpm1</a>";
                    //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=$lastpage\">$lastpage</a>";		
                }
                //close to end; only hide early pages
                else
                {
                    $strTemp	= str_replace("{pgNo}", "1", $url);
                    $pagination	.= str_replace("{pgTxt}", "1", $strTemp);
                    $strTemp	= str_replace("{pgNo}", "2", $url);
                    $pagination	.= str_replace("{pgTxt}", "2", $strTemp);
                    //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=1\">1</a>";
                    //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=2\">2</a>";
                    $pagination.= "...";
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page){
                            $pagination.= "<li class=\"page-item active\"><a class='page-link' href='javascript:void(0)'>$counter</a></li>";
                        }else{
                            $strTemp	= str_replace("{pgNo}", "$counter", $url);
                            $pagination	.= str_replace("{pgTxt}", "$counter", $strTemp);
                            //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=$counter\">$counter</a>";
                        }					
                    }
                }
            }
            
            //next button
            if ($page < $counter - 1){
                $strTemp	= str_replace("{pgNo}", "$next", $url);
                $pagination	.= str_replace("{pgTxt}", '<i class="fa-solid fa-angles-right"></i>', $strTemp);
                //$pagination.= "<a href=\"advance_search.php?desig=&loc=&locid=&expr=&gender=A&jtype=1&pgNo=$next\">>></a>";
            }else{
                $pagination.= '<li class="page-item">
                                    <a class="page-link" href="javascript:void(0)">
                                        <i class="fa-solid fa-angles-right"></i>
                                    </a>
                                </li>';
            }
           // $pagination.= "</div>\n";		
        }
        
            return $pagination;
    
        }
	function getBootsPageLink($pgNo, $totPage, $url, $count = "2")
	{	
		$intPre 	= $pgNo - 1;
		$intNex 	= $pgNo + 1;
		$intFirst 	= $pgNo - 5; 
		$intLast 	= $pgNo + 5;
		$strReturn  ="";

		if ($intFirst <= 0){
			$intFirst	= 1;
		}
		
		if ($intLast >= $totPage){
			$intLast	= $totPage;
		}
		
		if ($intPre <= 0){
			$intPre		= 1;
		} 
		else {
			$strReturn	= str_replace("{pgNo}", "$intPre", $url);
			$strReturn	= str_replace("{pgTxt}", '<span aria-hidden="true">&laquo;</span>', $strReturn); 
		}
		
		for ($i = $intFirst; $i <= $intLast; $i++){
			if ($i != $pgNo) {
				$strTemp	= str_replace("{pgNo}", "$i", $url);
				$strReturn	.= str_replace("{pgTxt}", "$i", $strTemp);
			} else {
				$strReturn	.= "<li class='page-item active'><a class='page-link' href=''>$i</a></li>";
			}
		} 
		if ($intNex > $totPage){
			$intNex		= $totPage;
		} 
		else {
			$strTemp	= str_replace("{pgNo}", "$intNex", $url);
			$strReturn .= str_replace("{pgTxt}", '<span aria-hidden="true">&raquo;</span>', $strTemp); 
		}
		// $strReturn= '<nav>
//						<ul class="pagination">
//							<li><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
//							<li><a href="#">1</a></li>
//							<li><a href="#">2</a></li>
//							<li><a href="#">3</a></li>
//							<li class="active"><a href="#">4</a></li>
//							<li><a href="#">5</a></li>
//							<li><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
//						</ul>
//					</nav>';
		$strReturn= '<nav>
						<ul class="pagination">'.$strReturn.'</ul></nav>';
		//$strReturn= '<div class="pagination"><ul>'.$strReturn.'</ul></div>';
		return $strReturn;
	}

        /**********************************************************
                        FETCH DATA FROM DATEBASE - ENDS
        ***********************************************************/

        /**********************************************************
                INSERT,UPDATE,DELETE DATA TO DATEBASE - STARTS
        ***********************************************************/

        /* METHOD INSERT TO TABLE :STARTS */

        function insert($InsertArray){
            if ((!is_array($InsertArray) || count($InsertArray)==0)){
               die("<span style='color:red;font-size:12px;font-weight:bold;'>Data Insert Error : -Please Contact your Software Support</span>");
            }
            if($InsertArray){
                //$values = $this->validateUserInput($InsertArray);
                $values = $this->clearArray($InsertArray);
            }

           $tempF  = implode(",", array_keys($values));
           $tempV  = implode("', '", array_values($values));                
           $sql    =  "INSERT INTO {$this->tableName} ($tempF) VALUES ('$tempV')";
		 // echo $sql;//exit;
            $res    = $this->query($sql);
            $this->insert_id = mysqli_insert_id($this->resource);
            return true;
        }

        /* METHOD INSERT TO TABLE  ENDS   */
		
      	/* METHOD TO GET INSERTID :STARTS */
		
		function insertId(){
			return $this->insert_id;
		}
		
		/* METHOD TO GET INSERTID :STARTS */

        /* METHOD UPDATE TO TABLE :STARTS */

        function update($values,$condition){
			$condition  = ($condition) ? " WHERE $condition "       : "";
            if ((!is_array($values) || count($values)==0)){
               die("<span style='color:red;font-size:12px;font-weight:bold;'>Data Update Error :  -Please Contact your Software Support</span>");
            }
			
            if($values){
                //$values = $this->validateUserInput($values);
                $values = $this->clearArray($values);
            }
            array_walk($values, "alter");
            $tempV   = implode(", ", array_values($values));
 			 $sql =   "UPDATE {$this->tableName} SET $tempV  $condition ";
		//	echo $sql;// exit; 
            $res    = $this->query($sql);
            return true;
        }

        /* METHOD UPDATE TO TABLE  ENDS   */

        /* METHOD DELETE FROM TABLE :STARTS */

        function delete($condition){
			$condition  = ($condition) ? " WHERE $condition "       : "";
            $sql = "DELETE FROM {$this->tableName} $condition ";
			//echo $sql; exit;
            $res  = $this->query($sql);
            return true;
        }

        /* METHOD UPDATE TO TABLE  ENDS   */

        /**********************************************************
                INSERT,UPDATE,DELETE DATA TO DATEBASE - ENDS
        ***********************************************************/
		
		/***********************************************************
				PAGE CUSTOM FUNCTIONS
		***********************************************************/
		
function url_slug($str, $options = array()) {
    // Make sure string is in UTF-8 and strip invalid UTF-8 characters
    $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
    
    $defaults = array(
        'delimiter' => '-',
        'limit' => null,
        'lowercase' => true,
        'replacements' => array(),
        'transliterate' => false,
    );
    
    // Merge options
    $options = array_merge($defaults, $options);
    
    $char_map = array(
        // Latin
        'À' => 'A', '?' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C', 
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', '?' => 'I', 'Î' => 'I', '?' => 'I', 
        '?' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', '?' => 'O', 
        'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'U' => 'U', '?' => 'Y', 'Þ' => 'TH', 
        'ß' => 'ss', 
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 
        'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'o' => 'o', 
        'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'u' => 'u', 'ý' => 'y', 'þ' => 'th', 
        'ÿ' => 'y',
        // Latin symbols
        '©' => '(c)',
        // Greek
        '?' => 'A', '?' => 'B', 'G' => 'G', '?' => 'D', '?' => 'E', '?' => 'Z', '?' => 'H', 'T' => '8',
        '?' => 'I', '?' => 'K', '?' => 'L', '?' => 'M', '?' => 'N', '?' => '3', '?' => 'O', '?' => 'P',
        '?' => 'R', 'S' => 'S', '?' => 'T', '?' => 'Y', 'F' => 'F', '?' => 'X', '?' => 'PS', 'O' => 'W',
        '?' => 'A', '?' => 'E', '?' => 'I', '?' => 'O', '?' => 'Y', '?' => 'H', '?' => 'W', '?' => 'I',
        '?' => 'Y',
        'a' => 'a', 'ß' => 'b', '?' => 'g', 'd' => 'd', 'e' => 'e', '?' => 'z', '?' => 'h', '?' => '8',
        '?' => 'i', '?' => 'k', '?' => 'l', 'µ' => 'm', '?' => 'n', '?' => '3', '?' => 'o', 'p' => 'p',
        '?' => 'r', 's' => 's', 't' => 't', '?' => 'y', 'f' => 'f', '?' => 'x', '?' => 'ps', '?' => 'w',
        '?' => 'a', '?' => 'e', '?' => 'i', '?' => 'o', '?' => 'y', '?' => 'h', '?' => 'w', '?' => 's',
        '?' => 'i', '?' => 'y', '?' => 'y', '?' => 'i',
        // Turkish
        'S' => 'S', 'I' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'G' => 'G',
        's' => 's', 'i' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'g' => 'g', 
        // Russian
        '?' => 'A', '?' => 'B', '?' => 'V', '?' => 'G', '?' => 'D', '?' => 'E', '?' => 'Yo', '?' => 'Zh',
        '?' => 'Z', '?' => 'I', '?' => 'J', '?' => 'K', '?' => 'L', '?' => 'M', '?' => 'N', '?' => 'O',
        '?' => 'P', '?' => 'R', '?' => 'S', '?' => 'T', '?' => 'U', '?' => 'F', '?' => 'H', '?' => 'C',
        '?' => 'Ch', '?' => 'Sh', '?' => 'Sh', '?' => '', '?' => 'Y', '?' => '', '?' => 'E', '?' => 'Yu',
        '?' => 'Ya',
        '?' => 'a', '?' => 'b', '?' => 'v', '?' => 'g', '?' => 'd', '?' => 'e', '?' => 'yo', '?' => 'zh',
        '?' => 'z', '?' => 'i', '?' => 'j', '?' => 'k', '?' => 'l', '?' => 'm', '?' => 'n', '?' => 'o',
        '?' => 'p', '?' => 'r', '?' => 's', '?' => 't', '?' => 'u', '?' => 'f', '?' => 'h', '?' => 'c',
        '?' => 'ch', '?' => 'sh', '?' => 'sh', '?' => '', '?' => 'y', '?' => '', '?' => 'e', '?' => 'yu',
        '?' => 'ya',
        // Ukrainian
        '?' => 'Ye', '?' => 'I', '?' => 'Yi', '?' => 'G',
        '?' => 'ye', '?' => 'i', '?' => 'yi', '?' => 'g',
        // Czech
        'C' => 'C', 'D' => 'D', 'E' => 'E', 'N' => 'N', 'R' => 'R', 'Š' => 'S', 'T' => 'T', 'U' => 'U', 
        'Ž' => 'Z', 
        '?' => 'c', '?' => 'd', 'e' => 'e', 'n' => 'n', 'r' => 'r', 'š' => 's', 't' => 't', 'u' => 'u',
        'ž' => 'z', 
        // Polish
        'A' => 'A', 'C' => 'C', 'E' => 'e', '?' => 'L', 'N' => 'N', 'Ó' => 'o', 'S' => 'S', 'Z' => 'Z', 
        'Z' => 'Z', 
        'a' => 'a', 'c' => 'c', 'e' => 'e', 'l' => 'l', 'n' => 'n', 'ó' => 'o', 's' => 's', 'z' => 'z',
        'z' => 'z',
        // Latvian
        'A' => 'A', 'C' => 'C', 'E' => 'E', 'G' => 'G', 'I' => 'i', 'K' => 'k', 'L' => 'L', 'N' => 'N', 
        'Š' => 'S', 'U' => 'u', 'Ž' => 'Z',
        '?' => 'a', '?' => 'c', 'e' => 'e', 'g' => 'g', 'i' => 'i', 'k' => 'k', 'l' => 'l', 'n' => 'n',
        'š' => 's', 'u' => 'u', 'ž' => 'z'
    );
    
    // Make custom replacements
    $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
    
    // Transliterate characters to ASCII
    if ($options['transliterate']) {
        $str = str_replace(array_keys($char_map), $char_map, $str);
    }
    
    // Replace non-alphanumeric characters with our delimiter
    $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
    
    // Remove duplicate delimiters
    $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
    
    // Truncate slug to max. characters
    $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
    
    // Remove delimiter from ends
    $str = trim($str, $options['delimiter']);
    
    return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}

function isWeekend($date) {
    return (date('N', strtotime($date)) > 6);
}

	function verticletext($string)
    {
       $tlen = strlen($string);
       for($i=0;$i<$tlen;$i++)
       {
            echo substr($string,$i,1)."<br />";   
       }
    }
}
    /***************DATABASE OPERATION CLASS ENDS ********************/

/**********************************************************
            GENERAL MISC FUNCTIONS _START
***********************************************************/

/*METHOD : FORMATING UPDATE SQL MUST FOR CLASS -START */

function alter(&$val, $key){
    $val = "$key = '$val'";
}
/*METHOD : FORMATING UPDATE SQL MUST FOR CLASS -END*/

/*METHOD : PRINT ARRAY WITH FORMATTING  -START */
function pr($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}
/*METHOD : PRINT ARRAY WITH FORMATTING  -END */

//Quotation Status display
function setQuoteStatus($status,$approved,$roletype){
	if($status == '0'){
         $quotestatus= 'New';   
	}
	if($status == '1' && $roletype == 'user'){
         $quotestatus= 'On Draft';   
	}
	if($status == '1' && $roletype == 'admin'){
         $quotestatus= '<span class="label label-warning">On Draft</span>';   
	}
	if($status == '2' && $roletype == 'user'){
         $quotestatus= 'Submitted';   
	}
	if($status == '2' && $roletype == 'admin'){
         $quotestatus= '<span class="label label-info">Submitted</span>';   
	}
	if($status == '3' && $roletype == 'user'){
         $quotestatus= 'Viewed';   
	}
	if($status == '3' && $roletype == 'admin'){
         $quotestatus= '<span class="label label-warning">Viewed</span>';   
	}
	if($status == '4' && $roletype == 'user'){
         $quotestatus= 'In Progress';   
	}
	if($status == '4' && $roletype == 'admin'){
         $quotestatus= '<span class="label label-primary">In Progress</span>';   
	}
	if($status == '5' && $roletype == 'user'){
         $quotestatus= 'ReCalled';   
         //$quotestatus= 'ReQuote';   
	}
	if($status == '5' && $roletype == 'admin'){
         $quotestatus= '<span class="label label-info">ReCalled</span>';   
	}
	if($status == '6' && $approved == '0' && $roletype == 'user'){
         $quotestatus= 'In Progress';   
	}
	if($status == '6' && $approved == '2' && $roletype == 'user'){
         $quotestatus= 'In Progress';   
	}
	if($status == '6' && $approved == '1' && $roletype == 'user'){
         $quotestatus= 'Ready';   
	}
	if($status == '7' && $approved == '1' && $roletype == 'user'){
         $quotestatus= 'Ready';   
	}
	if($status == '6' && $approved == '1' && $roletype == 'admin'){
         $quotestatus= '<span class="label label-primary">Quote Ready</span>';   
	}
	if($status == '6' && $approved == '0' && $roletype == 'admin'){
         $quotestatus= '<span class="label label-success">Costing Done</span>';   
	}
	if($status == '6' && $approved == '2' && $roletype == 'admin'){
       $quotestatus= '<span class="label label-warning" style="color:black;">Need Recosting</span>';   
	}
	if($status == '7' && $roletype == 'admin'){
         $quotestatus= '<span class="label label-info">Viewed by User</span>';   
	}
	if($status == '9' && $roletype == 'user'){
         $quotestatus= 'Expired';   
	}
	if($status == '9' && $roletype == 'admin'){
         $quotestatus= '<span class="label label-danger">Expired</span>';   
	}
	if($status == '10' && $roletype == 'user'){
         $quotestatus= 'Quote Confirmed';   
	}
	if($status == '10' && $roletype == 'admin'){
         $quotestatus= '<span class="label label-success">Quote Confirmed</span>';   
	}
	if($status == '11' && $roletype == 'user'){
         $quotestatus= '<span style="color:red;">Quote Declined</span>';   
	}
	if($status == '11' && $roletype == 'admin'){
         $quotestatus= '<span class="label label-danger">Quote Declined</span>';   
	}
	if($status == '12' && $roletype == 'user'){
         $quotestatus= 'Enquiry';   
	}
	if($status == '12' && $roletype == 'admin'){
         $quotestatus= '<span class="label label-primary">Enquiry</span>';   
	}
	if($status == '13'){
         $quotestatus= '<span class="label label-success">Purchase Order Accepted by BO</span>';   
	}
	if($status == '14'){
         $quotestatus= '<span class="label label-primary">Order Acknowledged</span>';   
	}
	if($status == '15'){
         $quotestatus= '<span class="label label-info">Proforma Invoice Sent</span>';   
	}
	return $quotestatus;
	}

    //CLASS FOR NUMBER FORMAT IN SALARY
  
    // Parent class
    class Employee {
        protected $salary;
    
        public function __construct($salary) {
            $this->salary = $salary;
        }
    }
    
    // Child class
    class EmployeeFormatted extends Employee {
        public function getFormattedSalary() {
            return number_format((float)$this->salary, 2);
        }
    }
   
    
	
?>