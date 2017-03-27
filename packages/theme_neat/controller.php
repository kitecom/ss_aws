<?php  

namespace Concrete\Package\ThemeNeat;

defined('C5_EXECUTE') or die(_("Access Denied."));

use Package;
use PageTheme;
use PageTemplate;

class Controller extends Package {

	protected $pkgHandle = 'theme_neat';
	protected $appVersionRequired = '5.7.4';
	protected $pkgVersion = '0.9.2';
	protected $pkgAllowsFullContentSwap = false;
	
	public function getPackageName() {
		return t('Neat');
	}
	
	public function getPackageDescription() {
		return t('Installs Neat: a clean, barebones theme for Concrete5');
	}
	
	public function install() {
		$pkg = parent::install();

		//install theme
		PageTheme::add('neat', $pkg);

		//install page types
		$this->installPageTemplates($pkg);

	}
	
	public function upgrade() {
		$pkg = Package::getByHandle($this->pkgHandle);
		$this->installPageTemplates($pkg);
		parent::upgrade();
	}
	
	private function installPageTemplates(&$pkg) {

		//Install page templates
		if (!PageTemplate::getByHandle('full')) {
            PageTemplate::add('full', t('Full'), 'full.png', $pkg);
        }
        if (!PageTemplate::getByHandle('left_sidebar')) {
            PageTemplate::add('left_sidebar', t('Left Sidebar'), 'left_sidebar.png', $pkg);
        }
        if (!PageTemplate::getByHandle('right_sidebar')) {
            PageTemplate::add('right_sidebar', t('Right Sidebar'), 'right_sidebar.png', $pkg);
        }

	}

}
