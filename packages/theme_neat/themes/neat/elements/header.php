<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html>
<html lang="<?php   echo Localization::activeLanguage()?>" <?php   if ( $c->isEditMode() ) { echo 'class="editing"'; } ?>>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="<?php   echo $view->getThemePath()?>/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php   echo $view->getThemePath()?>/css/main.css?v=0.9">
	<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
	<?php   Loader::element('header_required')?>
	<?php
		$th = Loader::helper('text');
		$class = $th->sanitizeFileSystem($c->getCollectionName(), $leaveSlashes=false);
	?>
	<script>
        $(document).ready(function() {
            $('.tooltip').tooltipster();
        });
    </script>
</head>
<body>
<div class="c5wrapper <?php   echo $c->getPageWrapperClass()?> <?php print $class ?>">

<header class="col-sm-12 site-header">
	<div class="primary-nav">
	      	<div class="row top-header">
	      		<div class="col-sm-6 site-name">

					<?php 
					$a = new GlobalArea('Site Name');
					if (($a->getTotalBlocksInArea($c) > 0) || ($c->isEditMode())) {
				        $a->display($c);
				    } else { ?>
						<h1 class="site-title text-uppercase"><a href="<?php   echo DIR_REL?>/"><?php   echo h(Config::get('concrete.site')); ?></a></h1>
					<?php  } ?>

        		</div>
        		<div class="col-sm-6 top-nav-right">

					<?php 
					$a = new GlobalArea('Top Nav Right');
			        $a->display($c);
					?>

		    	</div>
			</div>
			<div class="row nav-header">	
      			<div class="col-sm-12 nav-wrapper">
     				 <nav class="navbar" id="main-nav">

				      	<div class="navbar-header">
					        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
					          <span class="sr-only">Toggle navigation</span>
					          <span class="icon-bar"></span>
					          <span class="icon-bar"></span>
					          <span class="icon-bar"></span>
					        </button>
				        
				     	</div>

      					<div class="collapse navbar-collapse">
				            <?php          
							$a = new GlobalArea('Header Nav');
							if (($a->getTotalBlocksInArea($c) > 0) || ($c->isEditMode())) {
						        //$a->setBlockLimit(1);
								$a->display($c);
						    } else {
						    	$bt_nav = BlockType::getByHandle('autonav');
								$bt_nav->controller->displayPages = 'top';
								$bt_nav->controller->orderBy = 'display_asc';                    
								$bt_nav->controller->displaySubPages = 'all';
								$bt_nav->controller->displaySubPageLevels = 'custom';
								$bt_nav->controller->displaySubPageLevelsNum = 1; 
								$bt_nav->render('templates/site_nav_right/view');
							} ?>
		      			</div>
					</nav>
    			</div><!-- /.col-12 -->
    		</div><!-- /.row -->
	</div>
</header>
	<div id="content" class="site-content">
			<div class="bg_stretch">
			<?php        
			$a = new Area('Background Image');
			$a->display($c);
			?>
			</div>
	<div class="container">

		
		
