<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
<div class="row">	
	<div class="col-sm-6">
		<div class="col-content">
			<?php        
			$a = new Area('Col1');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="col-content">
			<?php        
			$a = new Area('Col2');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
		</div>
	</div>
</div>
<?php   $this->inc('elements/footer.php'); ?>
