<?php
/**
 * @author Sharif Ahmed
 * @Email winsharif@gmail.com
 * @http://esteemsoftbd.com
 * @copyright 2016
 *
 */
include_once("headlink.php");
require_once(LIB_PATH.DS.'image-resize.php');
Rbac::isAthorized(234); 
$time = date("Y-m-d");
$image_resize = new image_resizer();

$subheadLists = $db->result_all("SELECT * FROM ac_headsub WHERE head=3 OR head=4");

// Search
if(isset($_GET["unitID"])){

    $id = escape($_GET["unitID"]);
    $name = QB::table('inv_unit')->find($id)->uname;
}
if(isset($_GET["sourceID"])){

    $id = escape($_GET["sourceID"]);
    $name = QB::table('inv_source')->find($id)->sname;
}

if(isset($_GET["commonID"])){
    $id = escape($_GET["commonID"]);
   $name = QB::table('inv_values')->find($id)->vname;
}

if(isset($_GET["groupID"])){

    $id = escape($_GET["groupID"]);
    $info = QB::table('inv_group')->find($id);
    $name = $info->gname;
    $gorder = $info->gorder;
}

if(isset($_GET["signID"])){

    $id = escape($_GET["signID"]);
    $signatureInfo = QB::table('signatures')->find($id);
}


if(isset($_GET["unitID"]) && isset($_POST["item_name"])) {

    $id = escape($_GET["unitID"]);
    $item_name = escape($_POST["item_name"]);
    if ($db->query("Update `inv_unit` SET uname='$item_name' WHERE id='$id'")) {
        $flash->success("Item Name Updated Successfully.","setting-details.php?unitID={$id}");
    }
    else {
        $flash->error("Fail!!!! Please try again correctly.","setting-details.php");
    }
}

if(isset($_GET["sourceID"]) && isset($_POST["item_name"])) {

    $id = escape($_GET["sourceID"]);
    $item_name = escape($_POST["item_name"]);
    if ($db->query("Update `inv_source` SET sname='$item_name' WHERE id='$id'")) {
        $flash->success("Item Name Updated Successfully.","setting-details.php?sourceID={$id}");
    }
    else {
        $flash->error("Fail!!!! Please try again correctly.","setting-details.php");
    }
}

if(isset($_GET["commonID"]) && isset($_POST["item_name"])) {

    $id = escape($_GET["commonID"]);
    $item_name = escape($_POST["item_name"]);
    if ($db->query("Update `inv_values` SET vname='$item_name' WHERE id='$id'")) {
        $flash->success("Item Name Updated Successfully.","setting-details.php?commonID={$id}");
    }
    else {
        $flash->error("Fail!!!! Please try again correctly.","setting-details.php");
    }
}

if(isset($_GET["groupID"]) && isset($_POST["item_name"])) {

    $id = escape($_GET["groupID"]);
    $item_name = escape($_POST["item_name"]);
    $gorder = escape($_POST["gorder"]);
    if ($db->query("Update `inv_group` SET gname='$item_name',gorder='$gorder' WHERE id='$id'")) {
        $flash->success("Item Name Updated Successfully.","setting-details.php?groupID={$id}");
    }
    else {
        $flash->error("Fail!!!! Please try again correctly.","setting-details.php");
    }
}

// Operation for signatures
if(isset($_GET["signID"]) && isset($_POST["item_name"])) {

     $removeOldImage="";

     if(!empty($_FILES['files']['name'][0])) {

        $image_stack=array();
        $valid_formats = array("jpg", "png", "gif", "svg", "bmp");
        $max_file_size = 1024*4200; //4000 kb / 4MB
        $path = SITE_ROOT.DS."uploads".DS.SUBDOMAIN.DS."signature_img".DS; // Upload directory
        $removeOldImage= SITE_ROOT.DS."uploads".DS.SUBDOMAIN.DS."signature_img".DS.$signatureInfo->sign;

        // Loop $_FILES to execute all files
        foreach ($_FILES['files']['name'] as $f => $name){
            $file_tmp =$_FILES['files']['tmp_name'][$f];
            $file_name = $_FILES['files']['name'][$f];
            $file_type = $_FILES['files']['type'][$f];
            $file_size = $_FILES['files']['size'][$f];

            $ext = pathinfo($file_name, PATHINFO_EXTENSION); // get the file extension name like png jpg
            if ($_FILES['files']['error'][$f] == 4) {
                continue; // Skip file if any error found
            }
            if ($_FILES['files']['error'][$f] == 0) {
                if ($_FILES['files']['size'][$f] > $max_file_size) {
                    $flash->error("$name is too large!. Each file size must be less than 4 MB");
                    continue; // Skip large files
                }
                elseif( ! in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats) ){
                    $flash->error("$name is not a valid format");
                    continue; // Skip invalid file formats
                }
                else{ // No error found! Move uploaded files
                    if(move_uploaded_file($file_tmp,$path.$file_name))
                        $new_dir= uniqid().rand(1000, 9999).".".$ext;
                        $image_resize->load($path.$file_name);  // resize image
                        $image_resize->resizeToWidth(200);
                        $image_resize->save($path.$file_name);
                        $new_name = rename($path.$file_name,$path.$new_dir) ; // rename file name
                        array_push($image_stack,$new_dir); // file name store in array
                }
            }
        }
        $image_name_in_arry = implode(",", $image_stack);

    }else{

        $image_name_in_arry=$signatureInfo->sign;

    }


    $id = escape($_GET["signID"]);
    $name = escape($_POST["item_name"]);
    $type = escape($_POST["type"]);
    $value = escape($_POST["value"]);
    $side = escape($_POST["side"]);

    $status =isset($_POST["status"]) ? $_POST["status"] : 0;
    $master =isset($_POST["master"]) ? $_POST["master"] : 0;

    if($master=='1'){
        $db->query("UPDATE signatures SET master=0 WHERE side='{$side}' AND type >=0 ");
        $db->query("UPDATE signatures SET master=1 WHERE id='{$id}' ");
    }else{
        $db->query("UPDATE signatures SET master=0 WHERE id='{$id}' ");
    }
    if($status=='1'){
        $db->query("UPDATE signatures SET status=0 WHERE side='{$side}' AND type='{$type}'");
        $db->query("UPDATE signatures SET status=1 WHERE id='{$id}' ");
    }else{
        $db->query("UPDATE signatures SET status=0 WHERE id='{$id}' ");
    }

    if($db->query("UPDATE signatures SET name='{$name}',  value='{$value}', side='{$side}', type='{$type}', sign='{$image_name_in_arry}' WHERE id='{$id}' ")) {


       if(!empty($signatureInfo->sign) && file_exists($removeOldImage)){
                unlink($removeOldImage);
            }

      $flash->success("Signatures Update Successfully!!!","setting-details.php?signID={$id}");
    }
}

?>
<div class="print_div" id='print-div1'>
<div id="page-wrapper" style="margin: 0 0 0 0;">
    <div class="row print_div" id='print-div1'>
        <div class="col-lg-12">
        <center><?php $flash->display(); ?></center>
            <div class="panel panel-info">
                <div class="panel-heading">
                    Update Name
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="row avoid">
                        <form action="" method="POST" enctype="multipart/form-data">
                       <?php if(!isset($_GET["signID"])){ ?>
							<div class="col-md-4">
								<div class="form-group">
									<label>Name</label>
									<input type="text" required="" name="item_name" value="<?php echo $name;?>" class="form-control"/>
								</div>
							</div>
                            <?php if(isset($_GET["groupID"])){ ?>
                                <div class="col-md-4">
    								<div class="form-group">
    									<label>Order To Display</label>
    									<input type="number" required="" name="gorder" value="<?php echo $gorder;?>" class="form-control"/>
    								</div>
    							</div>
                            <?php } ?>
                            <?php }else { ?>
                                <div class="clearfix">
                                    <div class="col-lg-12">
                                        <div class="settingsTitle">
                                            <h4>Add New/Update Signature</h4>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group settingsItem">
                                                <h5 class="settingsItemTitle">Name</h5> 
                                                <input type="text" name="item_name" class="form-control" value="<?php echo $signatureInfo->name;?>" placeholder="Enter Person Full Name" />
                                            </div>
                                            <div class="form-group settingsItem">
                                                <h5 class="settingsItemTitle">Designation</h5>  
                                                <textarea name="value" class="form-control" rows="3"  placeholder="Enter Designation/Department/Education etc"><?php echo $signatureInfo->value;?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="image-wrapper">
                                                 <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <?php if(!empty($signatureInfo->sign)){ ?>
                                                    <div class="fileinput-new img-thumbnail">
                                                        <img src="<?php echo UPLOADS_URL;?>signature_img/<?php echo !empty($signatureInfo->sign) ? $signatureInfo->sign : "default.png" ; ?>" alt="<?php echo $signatureInfo->sign; ?>">
                                                    </div>
                                                    <div class="fileinput-preview fileinput-exists img-thumbnail"></div>
                                                  <?php } ?>
                                                    <div>
                                                        <span class="btn btn-default btn-file"><span class="fileinput-new">Change Signature Image</span>
                                                        <span class="fileinput-exists">Change</span>
                                                            <input type="file" id="file" class="form-control" name="files[]" value="" accept="image/*">
                                                        </span>
                                                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <div class="col-lg-12">                                                                                 
                                        <div class="col-lg-3 col-md-3">
                                            <div class="form-group settingsItem">
                                                <h5 class="settingsItemTitle">Signature Position</h5>
                                                <div class="radio-group" style="margin-bottom: 10px;">
                                                    <input type="radio" id="option1" name="side" value="1" <?php if($signatureInfo->side==1) echo "checked"; ?> /><label for="option1"> Left</label>
                                                    <input type="radio" id="option2" name="side" value="3" <?php if($signatureInfo->side==3) echo "checked"; ?>/><label for="option2"> Center</label>
                                                    <input type="radio" id="option3" name="side" value="2" <?php if($signatureInfo->side==2) echo "checked"; ?>/><label for="option3"> Right</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3">
                                            <div class="form-group settingsItem">
                                                <h5 class="settingsItemTitle">Default Signature</h5>                        
                                                <div class="form-group">
                                                    <label class="switch-wrapper">
                                                        <input type="checkbox" id="togBtn" name="status" value="1" <?php if(isset($signatureInfo->status) && $signatureInfo->status=="1"){ echo "checked"; }elseif(isset($signatureInfo->status) && $signatureInfo->status=="0"){ echo ""; }?> />
                                                        <div class="slider round">
                                                            <!--ADDED HTML -->
                                                            <span class="on">Yes</span><span class="off">No</span>
                                                            <!--END-->
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3">
                                            <div class="form-group settingsItem">
                                                <h5 class="settingsItemTitle">Master Signature</h5>                        
                                                <div class="form-group">
                                                    <label class="switch-wrapper">
                                                        <input type="checkbox" id="togBtn" name="master" value="1" <?php if(isset($signatureInfo->master) && $signatureInfo->master=="1"){ echo "checked"; }elseif(isset($signatureInfo->master) && $signatureInfo->master=="0"){ echo ""; } ?> />
                                                        <div class="slider round">
                                                            <!--ADDED HTML -->
                                                            <span class="on">Yes</span><span class="off">No</span>
                                                            <!--END-->
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3">
                                            <div class="form-group settingsItem">
                                                <h5 class="settingsItemTitle">Signature Type</h5>
                                                <select name="type" class="form-control">
                                                    <option value="">Select Type</option>
                                                    <?php foreach($subheadLists AS $shead) { ?>
                                                    <option value="<?php echo $shead->id;?>" <?php if($signatureInfo->type==$shead->id) echo "SELECTED"; ?> ><?php echo $shead->sname;?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                         <?php } ?>
                                <div class="col-md-3 pull-right text-right" style="clear: both;">
                                    <br />
								    <input type="submit" class="btn-save" onclick="if (! confirm('Do you want to Update?')) { return false; }" value="Save" />
                                    <a href="#" class="btn-reset" onclick="close_window();return false;">Cancel</a>
								</div>
				        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<style type="text/css">
/*Patient Photo*/
/*Photo Wrapper*/
.image-wrapper .fileinput-new.img-thumbnail{
    width: 160px;
    height: 40px;
    line-height: unset!important;
}
.image-wrapper .fileinput-preview.fileinput-exists.img-thumbnail{
    width: 160px;
    height: 40px;
    line-height: unset!important;
}
/*Photo Wrapper*/
/*Photo*/
.image-wrapper .fileinput-preview.fileinput-exists.img-thumbnail>img{
    width: 150px;
    height: 30px;
}
.image-wrapper .fileinput-new.img-thumbnail>img{
    width: 150px;
    height: 30px;
}
/*Photo*/
.image-wrapper .fileinput-new span.fileinput-new{
    padding: 5px 7px;
}
/*Patient Photo*/
</style>
<!-- Photo Add/Remove -->
<?php
$footer_link ='
<link rel="stylesheet" type="text/css" href="'.SITE_URL.'includes/jasny-bootstrap/css/jasny-bootstrap.min.css">
<script type="text/javascript" language="javascript" src="'.SITE_URL.'includes/jasny-bootstrap/js/jasny-bootstrap.min.js"></script>
</script>';
?>
<?php include("../footer.php"); ?>
<script>
    function close_window() {
        window.opener.location.reload(false);
            close();
    }
</script>