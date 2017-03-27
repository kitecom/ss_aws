<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
	<div class="row slider">	
		<div class="col-sm-8">
			<div class="col-content">
			<?php        
			$a = new Area('Slideshow');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="col-content">
			<?php        
			$a = new Area('Slideshow Right');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
			</div>
		</div>
	</div>
	<div class="row home-boxes one">
		
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

	</div>
	
	<div class="row home-boxes two">
		<div class="col-sm-3">
			<div class="col-content">
			<?php        
			$a = new Area('Col1-2');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="col-content">
				<?php        
				$a = new Area('Col2-2');
				$a->setAreaGridMaximumColumns(12);
				$a->display($c);
				?>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="col-content">
			<?php        
			$a = new Area('Col3-2');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="col-content">
			<?php        
			$a = new Area('Col4-2');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
			</div>
		</div>
	</div>
	<div class="row home-boxes three">
		<div class="col-sm-3">
			<div class="col-content">
			<?php        
			$a = new Area('Col1-3');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="col-content">
				<?php        
				$a = new Area('Col2-3');
				$a->setAreaGridMaximumColumns(12);
				$a->display($c);
				?>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="col-content">
			<?php        
			$a = new Area('Col3-3');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="col-content">
			<?php        
			$a = new Area('Col4-3');
			$a->setAreaGridMaximumColumns(12);
			$a->display($c);
			?>
			</div>
		</div>
	</div>
<?php   $this->inc('elements/footer.php'); ?>
