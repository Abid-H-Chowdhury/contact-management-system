<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(LIB_PATH.DS.'database.php');

class Customer extends DatabaseObject {
	
	protected static $table_name="customer";
	protected static $db_fields = array('id', 'cid', 'name', 'cname', 'email', 'cell', 'mobile', 'address', 'note', 
    'supplier', 'status',);
	
	public $id;
	public $cid;
    public $name;
	public $cname;
	public $email;
	public $cell;
	public $mobile;
	public $address;
	public $note;
    public $supplier;
	public $status;

	// Common Database Methods
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
  }
  
  public static function find_by_id($id=0) {
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE id={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
  }
  
  public static function find_by_sql($sql="") {
    global $database;
    $result_set = $database->query($sql);
    $object_array = array();
    while ($row = $database->fetch_array($result_set)) {
      $object_array[] = self::instantiate($row);
    }
    return $object_array;
  }

	public static function count_all() {
	  global $database;
	  $sql = "SELECT COUNT(*) FROM ".self::$table_name;
      $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
    return array_shift($row);
	}

	private static function instantiate($record) {
		// Could check that $record exists and is an array
        $object = new self;
		
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
	  // We don't care about the value, we just want to know if the key exists
	  // Will return true or false
	  return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
		// return an array of attribute names and their values
	  $attributes = array();
	  foreach(self::$db_fields as $field) {
	    if(property_exists($this, $field)) {
	      $attributes[$field] = $this->$field;
	    }
	  }
	  return $attributes;
	}
	
	protected function sanitized_attributes() {
	  global $database;
	  $clean_attributes = array();
	  // sanitize the values before submitting
	  // Note: does not alter the actual value of each attribute
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = $database->escape_value($value);
	  }
	  return $clean_attributes;
	}
	
	public function save() {
	  // A new record won't have an id yet.
	  return isset($this->id) ? $this->update() : $this->create();
	}
	
	public function create() {
		global $database;
		$attributes = $this->sanitized_attributes();
	  $sql = "INSERT INTO ".self::$table_name." (";
		$sql .= join(", ", array_keys($attributes));
	  $sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
	  if($database->query($sql)) {
	    $this->id = $database->insert_id();
	    return true;
	  } else {
	    return false;
	  }
	}

	public function update() {
	  global $database;
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value) {
		  $attribute_pairs[] = "{$key}='{$value}'";
		}
		$sql = "UPDATE ".self::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id=". $database->escape_value($this->id);
	  $database->query($sql);
	  return ($database->affected_rows() == 1) ? true : false;
	}

	public function delete() {
		global $database;
	  $sql = "DELETE FROM ".self::$table_name;
	  $sql .= " WHERE id=". $database->escape_value($this->id);
	  $sql .= " LIMIT 1";
	  $database->query($sql);
	  return ($database->affected_rows() == 1) ? true : false;
	}
/**
* @param customer ID (customer.id)
* @return customer info
*/     
    public static function return_customer($customerID,$column) {
        global $db;
        return $db->result_one("SELECT * FROM customer WHERE id='$customerID'")->$column;
    }
    
    public static function return_customer_uid($CID) {
        global $db;
        return $db->result_one("SELECT * FROM customer WHERE cid='$CID'")->id;
    }
    
    public static function supplier_list() {
       global $db;
       return $db->result_all("SELECT * FROM customer WHERE `supplier`='1' ORDER BY name ASC");
   }
/**
* @param customer ID
* @return true if customer is supplier false if not
*/    
    public static function is_supplier($customerID) {
        
        $data = self::customer_info($customerID,"supplier");
        if($data==1){
            return true;
        }else { return false; }
    }
       
/**
* @param customer ID
* @return true if customer false if user
*/    
    public static function is_customer($customerID) {
        global $db;
        $data = $db->number_rows("SELECT id FROM user  WHERE eid='$customerID'");
        if($data<1){
            return true;
        }else { return false; }
    }
/**
* @param customer CID/ID
* @return true if customer false if user
*/         
    public static function customer_info($customerID,$column=NULL) {
        global $db;
        
        if(is_numeric($customerID)){
            $data = $db->result_one("SELECT * FROM customer WHERE id='$customerID'");
        }else{
            $data = $db->result_one("SELECT * FROM customer WHERE cid='$customerID'");
        }
        
        if(!empty($column)){
            
            if(!empty($data)){
                return $data->$column;
            }else{
                return "";
            }
        }else{
            return $data;
        }        
    } 
    /**
     * @param int type id 1=customer/2=vendor
     * List of contact
     */
    public static function contact_list($status=NULL) {
       global $db;
        $whereArr = array();
        //if($type != "") $whereArr[] = " (type = '{$type}' OR type ='3') ";  
        if($status != "") $whereArr[] = "status = '{$status}' ";
        $whereStr = implode(" AND ", $whereArr);
        $sql = ("SELECT * FROM `customer` WHERE {$whereStr}  ORDER BY name ASC");
        return $db->result_all($sql);
    }
    
     public static function doctor_list($status=NULL,$ours=NULL,$dgroup=NULL){
        global $db;
        $sql = ""; 
        if(!empty($status)){
            $sql = " WHERE t2.status='{$status}'";
        }else{
            $sql = " WHERE t1.id<>0 ";
        }
        if(!empty($ours)){
            $sql.= " AND t1.ours='{$ours}'";
        }
        if(!empty($dgroup)){
            $sql.=" AND FIND_IN_SET('{$dgroup}',dgroup)";
        }
     

        return $db->result_all("SELECT * FROM doctors t1 INNER JOIN `customer` t2 ON t1.did=t2.id {$sql}");
   }
   
   public static function doctor_info($dID) {
        global $db;
        return $db->result_one("SELECT * FROM doctors t1 INNER JOIN `customer` t2 ON t1.did=t2.id WHERE t2.id='{$dID}'");
    } 
    
   public static function doctor_total_visit($uhid,$doctorID){
        global $db;
        return $db->result_one("SELECT COUNT(id) AS num FROM `appointment` WHERE uhid = '{$uhid}' AND  dID='{$doctorID}'")->num;
   }
   
   public static function doctor_last_visit($uhid,$doctorID) {
        global $db;
        $data = $db->result_one("SELECT adate FROM `appointment` WHERE uhid = '{$uhid}' AND dID='{$doctorID}' ORDER BY id DESC LIMIT 1");
        if(!empty($data)){
            return return_date($data->adate);
        }else{
            return "";
        }
   }
   
   public static function return_pcr_section($sectionID) {
        global $db;
        return $db->result_one("SELECT * FROM pcr_section WHERE sid='$sectionID'");
        if(!empty($data)){
            return $data;
        }else{return "";}
    } 
    
  public static function agent_list($status=NULL){
    global $db;
    $sql = " ";
    if(!empty($status)){
     $sql = " WHERE t2.status='$status' ";   
    }
    return $db->result_all("SELECT t1.id,t1.cid,t2.name,t2.cell,t2.union_id FROM agents t1 INNER JOIN customer t2 ON t1.cid=t2.cid {$sql} ORDER BY t2.name ASC ");
  } 
  
  public static function agent_info($agentID) {
        global $db;
        return $db->result_one("SELECT * FROM agents t1 INNER JOIN `customer` t2 ON t1.cid=t2.cid WHERE t1.id='{$agentID}'");
    }
    
/**
* @return  customer/party/shareholder group/designation list
*/      
     public static function group_list(){
        global $db;
        return $db->result_all("SELECT * FROM cus_group ORDER BY name ASC");
     }


     /**
* @return  Discount By
*/      
     public static function discountBy_list(){
        global $db;
        return $db->result_all("SELECT * FROM discountby ORDER BY cid ASC");
     }

     public static function find_discountBy($discountBy){
        global $db;
       $result =$db->result_one("SELECT * FROM discountby WHERE id = '$discountBy'");
       if(!empty($result)){
        return $result->name;
       }

     }
       
}

class Doctor extends DatabaseObject {
    
    
    public static function doctor_info($dID) {
        global $db;
        return $db->result_one("SELECT * FROM doctors t1 INNER JOIN `customer` t2 ON t1.did=t2.id WHERE t2.id='{$dID}'");
    } 
    
   public static function doctor_total_visit($uhid,$doctorID){
        global $db;
        return $db->result_one("SELECT COUNT(id) AS num FROM `appointment` WHERE uhid = '{$uhid}' AND  dID='{$doctorID}'")->num;
   }
   
   public static function doctor_last_visit($uhid,$doctorID) {
        global $db;
        $data = $db->result_one("SELECT adate FROM `appointment` WHERE uhid = '{$uhid}' AND dID='{$doctorID}' ORDER BY id DESC LIMIT 1");
        if(!empty($data)){
            return return_date($data->adate);
        }else{
            return "";
        }
   }
/**
* @param uhid, did, date
* get data of appointment 
*/   
   public static function appointment_check($uhid,$dID,$date) {
        global $db;
        return $db->result_one("SELECT * FROM appointment WHERE uhID='{$uhid}' AND dID='{$dID}' AND adate='{$date}'");
    }


    //function create By Mohiuddin
     public static function doctorGroupList(){
        global $db;
        return $db->result_all("SELECT * FROM doctor_group ORDER BY typeName ASC");
     }
    
    public static function appointment_booking($uhid,$dID,$date) {
      global $db;
      return $db->result_one("SELECT * FROM appointment WHERE uhID='{$uhid}' AND dID='{$dID}' AND adate='{$date}'");
    }  
      
} 

class Marketing extends DatabaseObject {
    
/**
* @param status active/inactive
* get all membership list 
*/  
  public static function membership_list($status=NULL) {  
    global $db;
    $sql = !empty($status) ? " WHERE status='$status' " : "";
    return $db->result_all("SELECT * FROM mk_memberships {$sql} ORDER BY mname ASC");
    
  }  

    public static function membership_info($membershipID) {
        global $db;
        return $db->result_one("SELECT * FROM mk_memberships t1 WHERE t1.id='{$membershipID}'");
        
    }
    
	
	
public static function getMshipDiscountPercent($itemID, $memId){
    global $db;
   /*first of get data from ac item then checking, item, subhead, head;*/ 

    $ac_item = $db->result_one("SELECT * FROM ac_item WHERE id='$itemID' ");
    if(!empty($ac_item)){
    $ckItemDiscount = $db->result_one("SELECT * FROM mk_mship_discount WHERE memId='$memId' AND itemId='$itemID' ");

     if(!empty($ckItemDiscount)){
      return $ckItemDiscount->percent;
     }elseif(empty($ckItemDiscount)){
     $shead= $ac_item->shead;
      $ckItemDiscount = $db->result_one("SELECT * FROM mk_mship_discount WHERE memId='$memId' AND subHead='$shead' ");
      if(!empty($ckItemDiscount)){
        return $ckItemDiscount->percent;
      }else{
        $head= $ac_item->head;
        $ckItemDiscount = $db->result_one("SELECT * FROM mk_mship_discount WHERE memId='$memId' AND head='$head' ");
        if(!empty($ckItemDiscount)){
        return $ckItemDiscount->percent;
      }
      }
     }
   }

}


public static function find_membership($uhid){ 

  global $db;

  $hcard = $db->result_one("SELECT * FROM hcard WHERE uhid='$uhid'");
  
    if(!empty($hcard)){

        if(!empty($hcard->membershipID)){
           return $hcard->membershipID;
          }else{
            return is_setting_session("HCARD_DEFAULT_MEMBERSHIPID");
          }

      }elseif(empty($hcard)) {

            $hcardMember = $db->result_one("SELECT hcard.membershipID, hcard.id FROM hcard INNER JOIN hcard_member ON hcard.cardno = hcard_member.cardno WHERE hcard_member.uhid='$uhid'");
              
              if(!empty($hcardMember)){
                  
                  if(!empty($hcardMember->membershipID)){
                    return $hcardMember->membershipID ;
                   }else{
                     return is_setting_session("HCARD_DEFAULT_MEMBERSHIPID");
                   }

               }elseif(empty($hcardMember)) {
                  
                  $agentmembership = $db->result_one("SELECT agents.membership FROM patients_info LEFT JOIN agents ON patients_info.agentID=agents.id WHERE patients_info.uhid='$uhid'");
                  
                  if(!empty($agentmembership)){
                   return $agentmembership->membership;
                }
            }
      }

}


public static function check_Membership_or_corporate_validity($uhid, $membershipID){ 
   global $db;
   $date=date("Y-m-d");
   if(!empty($membershipID)){
   $membershipInfo = $db->result_one("SELECT * FROM mk_memberships WHERE id='$membershipID' AND status=1");
   //firstly check memberships status
   if(!empty($membershipInfo)){ 
    //if membership expired date is not null then check validity 
   if(!empty($membershipInfo->expiredDate)){ 

        if($membershipInfo->expiredDate < $date){
             return "Membership offer is expired on ".return_date($membershipInfo->expiredDate);

        }else{

           $hcard = $db->result_one("SELECT * FROM hcard WHERE uhid='$uhid' AND membershipID='$membershipID'"); 
           
           if(!empty($hcard)){

            if(!empty($hcard->edate)){

                if($hcard->edate<$date){
                  
                   $agentmembership = $db->result_one("SELECT patients_info.uhid, agents.membership, agents.memExpiredDate FROM patients_info LEFT JOIN agents ON patients_info.agentID=agents.id WHERE patients_info.uhid='$uhid' AND agents.membership='$membershipID'");
                
                  if(!empty($agentmembership)){
                  if(!empty($agentmembership->memExpiredDate)){

                    if($agentmembership->memExpiredDate <$date){
                         return "Health Card and Corporate Membership is Expired on ".return_date($agentmembership->memExpiredDate);
                    }
                    }

                  }else{
                     return "Health Card Membership is Expired on ".return_date($hcard->edate);
                  }
                }
            }
        }else{
             
             $hcardMember = $db->result_one("SELECT hcard.membershipID, hcard.id, hcard.edate FROM hcard LEFT JOIN hcard_member ON hcard.id=hcard_member.cardno WHERE hcard_member.uhid='$uhid' AND hcard.membershipID='$membershipID'");
           
             if(!empty($hcardMember)){

                if(!empty($hcardMember->edate)){

                if($hcardMember->edate < $date){
                    
                   $agentmembership = $db->result_one("SELECT patients_info.uhid, agents.membership, agents.memExpiredDate FROM patients_info LEFT JOIN agents ON patients_info.agentID=agents.id WHERE patients_info.uhid='$uhid' AND agents.membership='$membershipID'");
                 
                  if(!empty($agentmembership)){
                     if(!empty($agentmembership->memExpiredDate)){
                    if($agentmembership->memExpiredDate <$date){
                         return "Health Card and Corporate Membership is Expired on ".return_date($agentmembership->memExpiredDate);
                    }
                    }
                  }else{
                     return "Health Card Membership is Expired on ".return_date($hcardMember->edate);
                  }
                }
            }
            

            }else{

                  $agentmembership = $db->result_one("SELECT patients_info.uhid, agents.membership, agents.memExpiredDate FROM patients_info LEFT JOIN agents ON patients_info.agentID=agents.id WHERE patients_info.uhid='$uhid' AND agents.membership='$membershipID'");
                  if(!empty($agentmembership)){
                    if(!empty($agentmembership->memExpiredDate)){
                    if($agentmembership->memExpiredDate < $date){
                         return "Corporate Membership is Expired on ".return_date($agentmembership->memExpiredDate);
                    }
                    }
                  }
            }
        }
    }

    }else{
        $hcard = $db->result_one("SELECT * FROM hcard WHERE uhid='$uhid' AND membershipID='$membershipID'"); 
           if(!empty($hcard)){
            if(!empty($hcard->edate)){
              
                if($hcard->edate<$date){
                   $agentmembership = $db->result_one("SELECT patients_info.uhid, agents.membership, agents.memExpiredDate FROM patients_info LEFT JOIN agents ON patients_info.agentID=agents.id WHERE patients_info.uhid='$uhid' AND agents.membership='$membershipID'");
                  
                  if(!empty($agentmembership)){

                    if(!empty($agentmembership->memExpiredDate)){

                        if($agentmembership->memExpiredDate <$date){
                             return "Health Card and Corporate Membership is Expired on ".return_date($agentmembership->memExpiredDate);
                        }
                    }

                  }else{
                     return "Health Card Membership is Expired on ".return_date($hcard->edate);
                  }
                }
            }
        }else{
             
             $hcardMember = $db->result_one("SELECT hcard.membershipID, hcard.id, hcard.edate FROM hcard LEFT JOIN hcard_member ON hcard.id=hcard_member.cardno WHERE hcard_member.uhid='$uhid' AND hcard.membershipID='$membershipID'");
           
             if(!empty($hcardMember)){
                if($hcardMember->edate < $date){
                    
                   $agentmembership = $db->result_one("SELECT patients_info.uhid, agents.membership, agents.memExpiredDate FROM patients_info LEFT JOIN agents ON patients_info.agentID=agents.id WHERE patients_info.uhid='$uhid' AND agents.membership='$membershipID'");
                 
                  if(!empty($agentmembership)){
                       
                        if(!empty($agentmembership->memExpiredDate)){
                                if($agentmembership->memExpiredDate <$date){
                                     return "Health Card and Corporate Membership is Expired on ".return_date($agentmembership->memExpiredDate);
                                }
                        }

                  }else{
                     return "Health Card Membership is Expired on ".return_date($hcardMember->edate);
                  }
                }
            

            }else{

                $agentmembership = $db->result_one("SELECT patients_info.uhid, agents.membership, agents.memExpiredDate FROM patients_info LEFT JOIN agents ON patients_info.agentID=agents.id WHERE patients_info.uhid='$uhid' AND agents.membership='$membershipID'");
                      if(!empty($agentmembership)){
                            if(!empty($agentmembership->memExpiredDate)){
                                if($agentmembership->memExpiredDate < $date){
                                     return "Corporate Membership is Expired on ".return_date($agentmembership->memExpiredDate);
                                 }
                            }
                      }
            }
        }
    } 
        
   }else{
    return "Membership is not active";
   }
}else{
   return "Membership not found"; 
}

}



   public static function NextCardNo($type) {
       global $db;
       $transNo = get_setting($type);
       $firstYear = substr($transNo,0,2);
       if($firstYear==substr(date("Y"),2)){
        $NEW_SN = substr($transNo,2)+1;
        $NEW_SN = substr(date("Y"),2).$NEW_SN;
        update_setting($NEW_SN,$type);
        return $transNo;
       }else{
        $NEW_SN = substr(date("Y"),2)."2";
        update_setting($NEW_SN,$type);
        return substr(date("Y"),2)."1";;
       }
   }
   
   public static function case_lists(){
      global $db;
    return $db->result_all("SELECT * FROM `case_items` WHERE status='1' ORDER BY orderby ASC");
   }



   /**
* @param status active/inactive
* get all pc commission list 
*/ 
    public static function pc_commission_list($status=NULL) {  
    global $db;
    $sql = !empty($status) ? " WHERE status='$status' " : "";
    return $db->result_all("SELECT * FROM mk_commission_list {$sql} ORDER BY mname ASC");
    
  }  

    public static function pc_commission_info($pcCommissionID) {
        global $db;
        return $db->result_one("SELECT * FROM mk_commission_list t1 WHERE t1.id='{$pcCommissionID}'");
        
    }

     public static function getPcCommissionSetPercent($itemID, $memId){
          global $db;
   /*first of get data from ac item then checking, item, subhead, head;*/ 

    $ac_item = $db->result_one("SELECT * FROM ac_item WHERE id='$itemID' ");
   
    if(!empty($ac_item)){

        $ckItemDiscount = $db->result_one("SELECT * FROM mk_commission_setup WHERE memId='$memId' AND itemId='$itemID' ");

         if(!empty($ckItemDiscount)){

            return $ckItemDiscount->percent;

            }elseif(empty($ckItemDiscount)){
          
                $shead= $ac_item->shead;
                $ckItemDiscount = $db->result_one("SELECT * FROM mk_commission_setup WHERE memId='$memId' AND subHead='$shead' ");
           
              if(!empty($ckItemDiscount)){
                return $ckItemDiscount->percent;

                }else{

                    $head= $ac_item->head;
                    $ckItemDiscount = $db->result_one("SELECT * FROM mk_commission_setup WHERE memId='$memId' AND head='$head' ");

                    if(!empty($ckItemDiscount)){
                    return $ckItemDiscount->percent;
                  }
              }
         }
   }

}

public static function getMaterialInformations($accessoriesItems,$i,$mateIDS, $itemID){
          global $db;
          $accessoriesInfo = $db->result_all("SELECT t1.id AS id, t1.iname AS iname, t2.price AS price,t1.head,t2.refP,t2.refF,t2.dislimit,t2.mat_items 
                                        FROM ac_item t1 INNER JOIN ac_itemservice t2 ON t1.id = t2.itemID 
                                        WHERE t1.id IN ($accessoriesItems) AND t2.status='1' ORDER BY t1.iname ASC");
 
                     $qty=1;      
            
                     $accessories_text =   ""; 
                     if($mateIDS!=0){
                       $accesoriesID=$mateIDS;
                      }else{
                        $accesoriesID="";
                      }
                      
                    $chekMateIDs=explode(",", $mateIDS);
                    $is_shortPayment=is_setting_session("PAYMENT_SYSTEM_SHORT");
                    $ITEM_ID_FOR_MULTIPLE_ACCESSORIES=is_setting_session("ITEM_ID_FOR_MULTIPLE_ACCESSORIES");

                       if(!empty($ITEM_ID_FOR_MULTIPLE_ACCESSORIES)){
                           
                            $itemIdArray =   explode(",",$ITEM_ID_FOR_MULTIPLE_ACCESSORIES);        
                            if(in_array($itemID,$itemIdArray)){
                                $qty=2;
                             
                            }
                        }

                 

                    if($is_shortPayment==0){
                    foreach ($accessoriesInfo as  $materialInfo) {  
                    if(!in_array($materialInfo->id,$chekMateIDs)){
                       
                        $accesoriesID .= $materialInfo->id.",";                 
                        $accessories_text.= '<tr>';
                        $accessories_text.= '<input type="hidden" name="itemID['.$i.'][itemid]" id="itemNo_' .$i. '" value="'.$materialInfo->id.'" class="form-control accessoriesID" autocomplete="off"/>';
                        $accessories_text.= '<td class="text-left"><input type="text" id="itemName_' .$i. '" value="'.$materialInfo->iname.'" class="form-control" readonly=""  autocomplete="off"></td>';
                        $accessories_text.= '<td><input type="text" name="itemID['.$i.'][qty]" id="qty_' .$i. '" value="'.$qty.'" class="form-control qty" value="1"  onkeypress="return IsNumeric(event);" tabindex="-1"></td>';
                        $accessories_text.= '<td><input type="text" name="itemID['.$i.'][amount]" id="price_' .$i. '" value="'.$materialInfo->price * $qty.'" class="form-control Price" readonly="" tabindex="-1"></td>';                                       
                        $accessories_text.= '<input type="hidden" name="itemID['.$i.'][discount]" id="discount_'.$i.'" class="form-control FixDiscount discnt" value="0"  readonly="" tabindex="-1">';
                        $accessories_text.= '<input type="hidden" name="itemID['.$i.'][fix_discount]" id="discount2_'.$i.'" class="form-control FixDiscount discnt2" value="0"  readonly="" tabindex="-1">';
                        $accessories_text.= '<input type="hidden" name="itemID['.$i.'][price]" id="net_price_'.$i.'" class="form-control" value="'.$materialInfo->price.'"  readonly="" tabindex="-1">';
                        $accessories_text.= '<input type="hidden" name="itemID['.$i.'][paid]" id="paid_'.$i.'" class="form-control TotalPayable paid" value="'.$materialInfo->price * $qty.'"  readonly="" tabindex="-1">';
                        $accessories_text.= '<input type="hidden" name="itemID['.$i.'][note]" class="form-control changesNo" value="" tabindex="-1"/>';
                        $accessories_text.= '<input type="hidden" name="itemID['.$i.'][referral]" class="form-control changesNo" value="0" tabindex="-1"/>';
                        $accessories_text.= '<td class="text-left"><i class="far fa-trash-alt delete"></i></td>';
                        $accessories_text.= '</tr>';
                        $i++;

                     }   
                 }
             }else{

                 foreach ($accessoriesInfo as  $materialInfo) {  
                 if(!in_array($materialInfo->id,$chekMateIDs)){
                      $accesoriesID .= $materialInfo->id.",";  
                 
                    $accessories_text.= '<tr>';
                    $accessories_text.= '<input type="hidden" name="itemID['.$i.'][itemid]" id="itemNo_' .$i. '" value="'.$materialInfo->id.'" class="form-control accessoriesID" autocomplete="off"/>';
                    $accessories_text.= '<td class="text-left"><input type="text" id="itemName_' .$i. '" value="'.$materialInfo->iname.'" class="form-control" readonly=""  autocomplete="off"></td>';
                    $accessories_text.= '<td><input type="text" name="itemID['.$i.'][qty]" id="qty_' .$i. '" value="'.$qty.'" class="form-control qty" value="1"  onkeypress="return IsNumeric(event);" tabindex="-1"></td>';
                    $accessories_text.= '<input type="hidden" name="itemID['.$i.'][amount]" id="price_' .$i. '" value="'.$materialInfo->price * $qty.'" class="form-control " readonly="" tabindex="-1">';                                       
                    $accessories_text.= '<input type="hidden" name="itemID['.$i.'][discount]" id="discount_'.$i.'" class="form-control FixDiscount discnt" value="0"  readonly="" tabindex="-1">';
                    $accessories_text.= '<input type="hidden" name="itemID['.$i.'][fix_discount]" id="discount2_'.$i.'" class="form-control FixDiscount discnt2" value="0"  readonly="" tabindex="-1">';
                    $accessories_text.= '<input type="hidden" name="itemID['.$i.'][price]" id="net_price_'.$i.'" class="form-control Price" value="'.$materialInfo->price.'"  readonly="" tabindex="-1">';
                    $accessories_text.= '<td><input type="text" name="itemID['.$i.'][paid]" id="paid_'.$i.'" class="form-control TotalPayable paid" value="'.$materialInfo->price * $qty.'"  readonly="" tabindex="-1"></td>';
                    $accessories_text.= '<input type="hidden" name="itemID['.$i.'][note]" class="form-control changesNo" value="" tabindex="-1"/>';
                    $accessories_text.= '<input type="hidden" name="itemID['.$i.'][referral]" class="form-control changesNo" value="0" tabindex="-1"/>';
                    $accessories_text.= '<td class="text-left"><i class="far fa-trash-alt delete"></i></td>';
                    $accessories_text.= '</tr>';
                     $i++;

                     }
                 }

             }

                     $data=array($accessories_text,$accesoriesID);
                    
           /*     return $accessories_text;*/
                 return $data;            

          }





}  



?>