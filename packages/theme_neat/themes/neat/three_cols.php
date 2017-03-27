<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

		<div class="col-sm-4">
		<?php        
		$a = new Area('Col1');
		$a->setAreaGridMaximumColumns(12);
		$a->display($c);
		?>
		</div>

		<div class="col-sm-4">
		<?php        
		$a = new Area('Col2');
		$a->setAreaGridMaximumColumns(12);
		$a->display($c);
		?>
		</div>

		<div class="col-sm-4">
		<?php        
		$a = new Area('Col3');
		$a->setAreaGridMaximumColumns(12);
		$a->display($c);
		?>
		</div>

<?php   $this->inc('elements/footer.php'); ?>
