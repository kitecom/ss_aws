<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
<div class="row">	
	<div class="col-sm-6">
		<div class="col-content">
			<?php        
			$a = new Area('Main Pic');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="col-content">
			<?php        
			$a = new Area('Small Pic 1');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="col-content">
			<?php        
			$a = new Area('Small Pic 2');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
		</div>
	</div>
</div>
<div class="row">	
	<div class="col-sm-8">
		<div class="col-content">
			<?php        
			$a = new Area('Content');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="col-content">
			<?php        
			$a = new Area('Sidebar');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
		</div>
	</div>
</div>
<?php   $this->inc('elements/footer.php'); ?>
