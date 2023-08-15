<?php echo $this->render_table_name($mode); ?>
<div class="xcrud-top-actions">
	
	<!--<button class="xcrud-button xcrud-blue" onclick="Xcrud.editMove('prev');"><< Prev</button>
	<button class="xcrud-button xcrud-blue" onclick="Xcrud.editMove('next');">Next >></button>-->
 <?php 
 if($this->is_next_previous){
	 echo $this->render_previous('Previous','edit','','xcrud-button xcrud-green','','edit');
	 echo $this->details_counter();    
	 echo $this->render_next('Next','edit','','xcrud-button xcrud-green','','edit');
     echo "<br><br>";  
 }
 ?>
  
    <?php echo $this->render_button('save_new','save','create','xcrud-button xcrud-blue','','create,edit') ?>
    <?php echo $this->render_button('save_edit','save','edit','xcrud-button xcrud-green','','create,edit') ?>
    <?php echo $this->render_button('save_return','save','list','xcrud-button xcrud-purple','','create,edit') ?>
    <?php echo $this->render_button('return','list','','xcrud-button xcrud-orange') ?>
    
    <!--<a class="xcrud-action xcrud-button xcrud-orange" title="Edit" href="javascript:;" data-primary="2" data-task="edit" data-next="true">Next</a>-->
</div>

<form data-parsley-validate='' class="parsley-form">
<div class="xcrud-view" >	
<?php echo $this->render_fields_list($mode); ?>
<br>
</div>
</form>

<div class="xcrud-nav">
    <?php echo $this->render_benchmark(); ?>
</div>

<?php
if($this->parsley_active){
	include("xcrud_parsley.php");
}
?>

<script>
	
</script>
