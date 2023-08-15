</div>
<!-- /#wrapper -->
<!----- Footer Starts ----->
<footer id="footer">
  <!--Footer-->
  <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <p class=""> Copyright © <?php echo date("Y"); ?> - Developed By <a target="_blank"
            href="http://esteemsoftbd.com">Esteem Soft Limited.</a></p>
      </div>
    </div>
  </div>
</footer>
<!----- Footer Ens ----->



<?php if (isset($_GET["page"])) {
	$helpContent = 0;  //get_helpContentByPage($_GET["page"]);
}
if (!empty($helpContent)) { ?>
<!-- Modal Starts -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php if (isset($helpContent)) {
																											echo $helpContent->title;
																										} else {
																											echo "Help Content Not Found";
																										} ?></h4>
      </div>
      <div class="modal-body">
        <div class="content-wrapper">
          <p><?php if (isset($helpContent)) {
									echo $helpContent->content;
								} ?></p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>
<!-- Modal Ends -->
<!----- Sidenav Open & Close ----->
<script>
var o = document.getElementById("sidenavOne");
var to = document.getElementById("sidenavTwo");
to.style.display = 'none';

function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
  document.getElementById("page-wrapper").style.marginLeft = "250px";
  o.style.display = '';
  to.style.display = 'none';
}

function closeNav() {

  document.getElementById("mySidenav").style.width = "0";
  document.getElementById("page-wrapper").style.marginLeft = "0";
  o.style.display = 'none';
  to.style.display = '';

}
</script>
<!----- Sidenav Open & Close ----->
<!-- jQuery Version 1.11.0 -->
<script src="<?php echo SITE_URL; ?>js/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="<?php echo SITE_URL; ?>js/bootstrap.min.js"></script>
<!-- Metis Menu Plugin JavaScript -->
<script src="<?php echo SITE_URL; ?>js/plugins/metisMenu/metisMenu.min.js"></script>
<!-- Custom Theme JavaScript -->
<script src="<?php echo SITE_URL; ?>js/plugins/datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="<?php echo SITE_URL; ?>js/jQuery.print.js"></script>
<?php if (isset($footer_link)) {
	echo $footer_link;
} ?>
<script>
$(document).ready(function() {
  $('[data-toggle="popover"]').popover();
});

if (document.getElementById("table")) {
  $(document).ready(function() {
    $("#table").dataTable({
      "oLanguage": {
        "sSearch": ""
      },
      "iDisplayLength": <?php echo isset($_SESSION['PAGINATION_MAIN']) ? $_SESSION['PAGINATION_MAIN'] : 500; ?>
    });
  });
}
</script>
<script src="<?php echo SITE_URL; ?>js/template.js"></script>
</body>

</html>