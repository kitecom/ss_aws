<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

		<div class="col-sm-8">
		<?php        
		$a = new Area('Main');
		$a->setAreaGridMaximumColumns(12);
		$a->display($c);
		?>
		</div>

		<div class="col-sm-3 col-sm-offset-1">
		<?php        
		$a = new Area('Sidebar');
		$a->display($c);
		?>
		</div>

<?php   $this->inc('elements/footer.php'); ?>
