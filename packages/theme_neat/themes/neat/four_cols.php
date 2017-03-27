<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

		<div class="col-sm-3">
			<div class="col-content">
			<?php        
			$a = new Area('Col1');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="col-content">
			<?php        
			$a = new Area('Col2');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="col-content">
			<?php        
			$a = new Area('Col3');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="col-content">
			<?php        
			$a = new Area('Col4');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
			</div>
		</div>

<?php   $this->inc('elements/footer.php'); ?>
