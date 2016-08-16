<?php

require "/header.php"

?>

		<h3>Clients</h3>
		<hr>
			<div style="whitespace: nowrap;">
				<!-- <div style="display: inline-block; width: 50px">№</div> -->
				<div style="display: inline-block; width: 300px">FIO</div>
				<div style="display: inline-block; width: 200px">Phone</div>
				<div style="display: inline-block; width: 200px">Status</div>
				<div style="display: inline-block; width: 200px">Date registered</div>
				<div style="display: inline-block; width: 200px">Actions</div>
			</div>
		<hr>
		<?php
			foreach($clients as $client) {
				?>
				<div style="whitespace: nowrap;">
					<!-- <div style="display: inline-block; width: 50px"><?php echo $client["cID"]; ?></div> -->
					<div style="display: inline-block; width: 300px"><?php echo $client["cFIO"]; ?></div>
					<div style="display: inline-block; width: 200px"><?php echo $client["cPhone"]; ?></div>
					<div style="display: inline-block; width: 200px"><?php print_r ($statuses[$client["cStatus"]]); ?></div>
					<div style="display: inline-block; width: 200px"><?php echo $client["cRegistered"]; ?></div>
					<div style="display: inline-block; width: 200px">
						<a href="index.php?action=edit&id=<?php echo $client["cID"]; ?>">Edit</a>
						<a href="index.php?action=delete&id=<?php echo $client["cID"]; ?>">Delete</a>
					</div>
				</div>
				<?php
			}
		?>
		<br>
		<div style="whitespace: nowrap;">
			<div style="display: inline-block; width: 200px">
				<?php if(isset($_SESSION['pagenum']) && $_SESSION['pagenum'] > 1) { ?>
					<a href="?page=1&pagenum=<?php echo($_SESSION['pagenum']-1); ?>">Previous</a>
				<?php } ?>
			</div>
			<div style="display: inline-block; width: 200px">
				<?php if($_SESSION['pagenum'] < $this->pagenumMax) { ?>
					<a href="?page=1&pagenum=<?php echo($_SESSION['pagenum']+1); ?>">Next</a>
				<?php } ?>
			</div>
		</div>
		<br>
		<div style=""><a href="index.php?action=register">New client</a></div>

<?php

require "/footer.php"

?>

