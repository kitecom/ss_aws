<?php   defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Validation\CSRF\Token;
?>


		<div class="row">
			<div class="col-sm-12">
				<div class="page-footer-container">
					<?php  
					$a = new Area('Page Footer');
					$a->setAreaGridMaximumColumns(12);
					$a->display($c);
					?>
				</div>
			</div>	
		</div>

	</div>
</div><!-- ./site-content -->

	<footer class="site-footer" role="contentinfo">
		<div class="container">
			<div class="row">	
				<div class="col-sm-6 footer-1">
					<?php          
					$a = new GlobalArea('Footer');
					$a->display();
					?>
				</div>
				<div class="col-sm-6 footer-2">
					<?php          
					$a = new GlobalArea('Footer Two');
					$a->display();
					?>
				</div>
				
			</div>
			<div class="row">	
				<div class="col-sm-6 site-colophon">
				</div>
				<div class="col-sm-6 site-colophon">
						<?php  
						// login link or logout form
			            if (!id(new User)->isLoggedIn()) {
			            ?>
			              <a href="<?php   echo URL::to('/login')?>"><?php   echo t('Log in') ?></a>
			            <?php  
			            } else {
			              $token = new Token();
			            ?>
			              <form class="logout" action="<?php   echo URL::to('/login', 'logout') ?>">
			                <?php   id(new Token())->output('logout'); ?>
			                  <a href="#" onclick="$(this).closest('form').submit();return false"><?php   echo t('Log out') ?></a>
			              </form>
			            <?php  
			            }
			            ?>
				</div>
			</div>
		</div>
	</footer><!--end footer-->

</div><!--end c5wrapper-->

<script src="<?php   echo $view->getThemePath()?>/js/bootstrap.min.js"></script>
<script src="<?php   echo $view->getThemePath()?>/js/neat_stuff.js"></script>
<script src="<?php   echo $view->getThemePath()?>/js/tooltipster.bundle.js"></script>
<script src="<?php   echo $view->getThemePath()?>/js/tooltipster.main.js"></script>

<?php   Loader::element('footer_required')?>

</body>
</html>