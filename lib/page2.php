<?php

require "/header.php";

$step = 1;
if(isset($_POST['step']) && (int)$_POST['step'] > 0  && (int)$_POST['step'] < 366) {
	$step = (int)$_POST['step'];
}

?>
		<script type="text/javascript">
			var backgroundColor = [];
			var pbackgroundColor = [];
			var borderColor = [];
			var pborderColor = [];
			var labels = [];
			var data = [];
			var pdata = [];
		</script>

		<h3>Conversion&nbsp;&nbsp;&nbsp;<input type="checkbox" id="pred_on"><span>show predicted conversion</span></h3>
		<div>
			<form action="" method="post">
				<span>Set step</span>
				<input type="number" min="1" max="365" name="step" value="<?php echo $step; ?>">
				<input type="submit" value="Show">
			</form>
		</div>
		<hr>
		<div style="whitespace: nowrap;">
			<div class="column2">Period number</div>
			<div class="column2">Period start</div>
			<div class="column2">Period end</div>
			<div class="column2">Conversion</div>
			<div class="column2 conv_pred conv_pred_t">Predicted conversion</div>
		</div>
		<hr>
		<?php
		$res=$this->db->link->query("CALL getConvertion(".$step.")");
		echo $this->db->link->error;
		while($tmp=$res->fetch_assoc()) { ?>

			<div style="whitespace: nowrap;">
				<div class="column2"><?=$tmp["number"]?></div>
				<div class="column2"><?=$tmp["dfrom"]?></div>
				<div class="column2"><?=$tmp["dto"]?></div>
				<div class="column2"><?=$tmp["conv"]?></div>
				<div class="column2 conv_pred conv_pred_t"><?=$tmp["predconv"]?></div>
			</div>

			<script type="text/javascript">
				backgroundColor.push('rgba(255, 99, 132, 0.2)');
				pbackgroundColor.push('rgba(54, 162, 235, 0.3)');
				borderColor.push('rgba(255,99,132,1)');
				pborderColor.push('rgba(54, 162, 235, 0.3)');
				labels.push("<?=$tmp["dto"]?>");
				data.push(<?=$tmp["conv"]?>);
				pdata.push(<?=$tmp["predconv"]?>);
			</script>

		<?php } ?>

		<canvas id="convChart" width="100" height="20"></canvas>
		<div class="conv_pred conv_pred_t">
			<canvas id="pconvChart" width="100" height="20"></canvas>
		</div>

		<script type="text/javascript">
			$('document').ready(function(){
				$('#pred_on').change(function(){
					$('.conv_pred_t').toggleClass('conv_pred');
				});
			});

			var ctx = $("#convChart");
			var pctx = $("#pconvChart");

			var data = {
				labels: labels,
					datasets: [
						{
							label: "Conversion",
							backgroundColor: backgroundColor,
							borderColor: borderColor,
							borderWidth: 1,
							data: data,
						}
					]
				};

			var myBarChart = new Chart(ctx, {
				type: 'bar',
				data: data,
				options: {
		        	scales: {
						xAxes: [{
							stacked: true
						}],
						yAxes: [{
							stacked: true
						}]
					}
				}
			});

			var pdata = {
				labels: labels,
					datasets: [
						{
							label: "Predicted conversion",
							backgroundColor: pbackgroundColor,
							borderColor: pborderColor,
							borderWidth: 1,
							data: pdata,
						}
					]
				};

			var myBarChart = new Chart(pctx, {
				type: 'bar',
				data: pdata,
				options: {
		        	scales: {
						xAxes: [{
							stacked: true
						}],
						yAxes: [{
							stacked: true
						}]
					}
				}
			});
		</script>

<?php

require "/footer.php"

?>

