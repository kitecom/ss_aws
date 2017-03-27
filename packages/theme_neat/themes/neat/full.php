<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
<div class="col-sm-12">

	

</div>
<div class="col-sm-12">
	<?php        
		$a = new Area('Main');
		$a->setAreaGridMaximumColumns(12);
		$a->display($c);
	?>
</div>

<?php   $this->inc('elements/footer.php'); ?>
