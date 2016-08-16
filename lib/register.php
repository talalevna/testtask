<?php

require "/header.php"

?>

		<h3>Clients</h3>
		<hr>
			<div style="whitespace: nowrap;">
				<div style="display: inline-block; width: 300px">FIO</div>
				<div style="display: inline-block; width: 200px">Phone</div>
				<div style="display: inline-block; width: 200px">Status</div>
				<div style="display: inline-block; width: 200px">Actions</div>
			</div>
		<hr>
		<form action="" method="post">
			<div style="whitespace: nowrap;">
				<div style="display: inline-block; width: 300px">
					<input style="width: 250px;" type="text" id="cFIO" name="cFIO" />
				</div>
				<div style="display: inline-block; width: 200px">
					<input type="text" id="cPhone" name="cPhone" />
				</div>
				<div style="display: inline-block; width: 200px">
					<select id="cStatus" name="cStatus">
						<?php 
							foreach ($statuses as $id => $status) {
						?>
							<option value="<?php echo $id; ?>" ><?php echo $status; ?></option>
						<?php
							}
						?>
					</select>
				</div>
				<div style="display: inline-block; width: 200px">
					<!--a href="?action=edit&id=<?php echo $clients["cID"]; ?>">Save</a-->
					<input type="submit" value="save">
				</div>
			</div>
		</form>

		<a href="index.php?page=1&pagenum=<?php echo $_SESSION['pagenum'] ?>">Back</a>
<?php

require "/footer.php"

?>

