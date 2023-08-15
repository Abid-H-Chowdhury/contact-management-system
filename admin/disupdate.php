<div class="modal fade" id="editmodal<?php echo $test->id ?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h3 class="modal-title">Update District Information</h3>
        </div>
        <div class="modal-body">
          <div class="col-md-2"></div>
          <div class="col-md-8">
            <div class="form-group">
              <label>District Name</label>
              <input type="hidden" name="id" value="<?php echo $test->id; ?>" />
              <input type="text" name="tname" value="<?php echo $test->name; ?>" class="form-control"
                required="required" />
            </div>
            <div class="form-group">
              <label>Bangla Name</label>
              <input type="text" name="tbname" value="<?php echo $test->bname; ?>" class="form-control"
                required="required" />
            </div>
            <div class="form-group">
              <label>URL</label>
              <input type="url" name="url" value="<?php echo $test->url; ?>" class="form-control" required="required" />
            </div>
          </div>
        </div>
        <div style="clear:both;"></div>
        <div class="modal-footer">
          <button name="update" class="btn btn-warning">Update</button>
          <button class="btn btn-danger" type="button" data-dismiss="modal"> Close</button>
        </div>
    </div>
    </form>
  </div>
</div>
</div>
<?php
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $tname = $_POST['tname'];
    $tbname = $_POST['tbname'];
    $url = $_POST['url'];
    $data1 = array(
        'name' => $tname,
        'bname' => $tbname,
        'url' => $url
    );
    $update = QB::table('district')->where('id', $id)->update($data1);
}
?>