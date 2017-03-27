<?php  

namespace Concrete\Package\ThemeNeat\Theme\Neat;

use Concrete\Core\Page\Theme\Theme;

class PageTheme extends Theme {

	protected $pThemeGridFrameworkHandle = 'bootstrap3';

	public function registerAssets()
	{
	    $this->providesAsset('javascript', 'bootstrap/*');
	    $this->providesAsset('css', 'bootstrap/*');
	    $this->providesAsset('css', 'blocks/form');
	    $this->providesAsset('css', 'core/frontend/*');

	    $this->requireAsset('css', 'font-awesome');
	    $this->requireAsset('javascript', 'jquery');
	    $this->requireAsset('javascript-conditional', 'html5-shiv');
        $this->requireAsset('javascript-conditional', 'respond');
	}

	public function getThemeDefaultBlockTemplates()
    {
        return array(
            'autonav' => 'site_nav_right'
        );
    }

    public function getThemeEditorClasses()
    {
        return array(
            array('title' => t('Button'), 'menuClass' => '', 'spanClass' => 'btn'),
        );
    }

    public function getThemeAreaClasses()
    {
        return array(
            'Page Header' => array('no-side-margins', 'area-content-accent'),
            'Page Footer' => array('no-side-margins', 'area-content-accent'),
        );
    }

    public function getThemeBlockClasses()
    {
        return array(
            'image_slider' => array('boxed-captions')
        );
    }

}
