<?php
	include_once "head.php";
	if(GET_url_decode("deleting") > 1 && $__group_id == "0"){
		$db->addtable("users");			$db->where("id",GET_url_decode("deleting"));
		$db->addfield("hidden");		$db->addvalue("1");
		$update = $db->update();
		$_SESSION["alert_success"] = "Data deleted successfully!";
		?>
			<script type="text/JavaScript">setTimeout("location.href = 'user_list.php';",1500);</script>
		<?php
	}
	?>

	<!--Filter-->
	<div class="modal fade" id="filter_box" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header text-center">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				
				<div class="modal-body">
					<div class="login mx-auto mw-100">
						<h5 class="text-center">Filter Box</h5>
						<?=$f->start("filter","GET");?>
							<div class="form-group ">
								<p class="mb-2">Name</p>
								<?=$f->input("name",@$_GET["name"],"","form-control");?>
							</div>
							<div class="form-group">
								<p class="mb-2">Email</p>
								<?=$f->input("email",@$_GET["email"],"","form-control");?>
							</div>
							<div class="form-group">
								<p class="mb-2">Group</p>
								<?=$f->input("group",@$_GET["group"],"","form-control");?>
							</div>
							<div class="form-group">
								<p class="mb-2">Job Title</p>
								<?=$f->input("job_title",@$_GET["job_title"],"","form-control");?>
							</div>
							<div class="form-group">
								<p class="mb-2">Sort By</p>
								<?=$f->select("sort",[""=>"","name"=>"Name","email"=>"Email","group_id"=>"Group","job_title"=>"Job Title",],@$_GET["sort"],"","select_filter");?>
								<?php if(@$_GET["desc"]) {$checked = "checked";} ?>
								<?=$f->input("desc","","type='checkbox' style='background-color:red;' ".$checked);?> <font style="font-style:italic">Descending</font>
							</div>
							<?=$f->input("page","1","type='hidden'");?>
							<?=$f->input("do_filter","Load","type='submit' style='width:150px;'", "btn btn-primary");?>
							<?=$f->input("reset","Reset","type='button' style='width:150px;' onclick=\"window.location='?';\"", "btn btn-warning");?>
						<?=$f->end();?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--//Filter-->
	
	<!--Table-->
	<section class="typography py-lg-4 py-md-3 py-sm-3 py-3">
		<div class="table_list">
			<div class="sub-head mb-3 ">
			<?php include_once "a_notification.php"; ?>
				<h4>User List</h4>
					<?=$f->input("add","Add","type='button' onclick=\"window.location='user_add.php';\"", "btn btn-primary");?>
					<?=$f->input("filter","Filter","type='button' data-toggle='modal' data-target='#filter_box'", "btn btn-success");?>
			</div>
			
			<?php
				$whereclause = "";
				if(@$__group_id > 0)	$whereclause .= "hidden = '0' AND ";
				if(@$_GET["name"]!="") $whereclause .= "name LIKE '"."%".str_replace(" ","%",$_GET["name"])."%"."' AND ";
				if(@$_GET["email"]!="") $whereclause .= "email LIKE '"."%".str_replace(" ","%",$_GET["email"])."%"."' AND ";
				if(@$_GET["group"]!="") $whereclause .= "group_id IN (SELECT groups.id FROM groups WHERE name LIKE '%".$_GET["group"]."%') AND ";
				if(@$_GET["job_title"]!="") $whereclause .= "job_title LIKE '"."%".str_replace(" ","%",$_GET["job_title"])."%"."' AND ";

				$db->addtable("users");
				if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
				$maxrow = count($db->fetch_data(true));

				$db->addtable("users");
				if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
				if(@$_GET["sort"] != "") {
					if(@$_GET["desc"]) {$desc = "DESC";} else {$desc = "";}
					$db->order($_GET["sort"]." ".$desc);
				} else {$db->order("created_at DESC");}
				$db->limit($_limit);
				$users = $db->fetch_data(true);
				
				$count_data =count($db->fetch_all_data("users",["id"],substr($whereclause,0,-4)));
			?>
			
			<?php include "a_pagination.php";?>
			<div class="bd-example mb-4">
				<div style="overflow-x:auto;">
					<?=$t->start("","","data_content");?>
						<?=$t->header(["No","Action","Name","Email","Group","Job Title"]);?>
						<?php
							foreach($users as $data_user){
								if($data_user["id"] > 1) $password = "[".base64_decode($data_user["password"])."]";
								$actions = 	"<a href=\"user_edit.php?".url_encode("id")."=".url_encode($data_user["id"])."\"><img src='images/edit.png' style='width:30px; height:30px;' title='Edit'></a>";
								$actions .= "<img src='images/vertical.png' style='width:30px; height:30px;'>";
								$actions .= "<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?".url_encode("deleting")."=".url_encode($data_user["id"])."';}\"><img src='images/cancel.png' style='width:30px; height:30px;' title='Deactive'></a>";
								$inactive = "";
								if($__group_id == 0 && $data_user["hidden"] == 1) $inactive = "background-color:#ff4d4d;";
								echo $t->row([
								$start++,
								$actions,
								$data_user["name"],
								$data_user["email"].$password,
								$db->fetch_single_data("groups","name",["id" => $data_user["group_id"]]),
								$data_user["job_title"]
								],["style='width:3%' align=right nowrap","style='width:7%;".$inactive."'","","","",""]);
							}
						?>
					<?=$t->end();?>
				</div>
			</div>
			<?php include "a_pagination.php";?>
		</div>
	</section>
	<!--//Table-->

<?php
	include_once "footer.php";
	include_once "a_pop_up_js.php";
?>
</body>
</html>