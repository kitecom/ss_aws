<?php
namespace Concrete\Core\Application;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Cache\Page\PageCacheRecord;
use Concrete\Core\Cache\OpCache;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\EntityManager\Driver\DriverInterface;
use Concrete\Core\Database\EntityManager\Provider\PackageProvider;
use Concrete\Core\Database\EntityManager\Provider\PackageProviderFactory;
use Concrete\Core\Database\EntityManagerConfigUpdater;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Foundation\ClassLoader;
use Concrete\Core\Foundation\EnvironmentDetector;
use Concrete\Core\Foundation\Runtime\DefaultRuntime;
use Concrete\Core\Foundation\Runtime\RuntimeInterface;
use Concrete\Core\Http\DispatcherInterface;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Logging\Query\Logger;
use Concrete\Core\Routing\DispatcherRouteCallback;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\Updater\Update;
use Concrete\Core\Url\Url;
use Concrete\Core\Url\UrlImmutable;
use Config;
use Core;
use Database;
use Environment;
use Illuminate\Container\Container;
use Job;
use JobSet;
use Log;
use Concrete\Core\Support\Facade\Package;
use Page;
use Redirect;
use Concrete\Core\Http\Request;
use Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use User;
use View;

class Application extends Container
{
    protected $installed = null;
    protected $environment = null;
    protected $packages = array();

    /**
     * Turns off the lights.
     *
     * @param array $options Array of options for disabling certain things during shutdown
     *      Add `'jobs' => true` to disable scheduled jobs
     *      Add `'log_queries' => true` to disable query logging
     */
    public function shutdown($options = array())
    {
        \Events::dispatch('on_shutdown');

        $config = $this['config'];

        if ($this->isInstalled()) {
            if (!isset($options['jobs']) || $options['jobs'] == false) {
                $this->handleScheduledJobs();
            }

            $logger = new Logger();
            $r = Request::getInstance();

            if ($config->get('concrete.log.queries.log') &&
                (!isset($options['log_queries']) || $options['log_queries'] == false)) {
                $connection = Database::getActiveConnection();
                if ($logger->shouldLogQueries($r)) {
                    $loggers = array();
                    $configuration = $connection->getConfiguration();
                    $loggers[] = $configuration->getSQLLogger();
                    $configuration->setSQLLogger(null);
                    if ($config->get('concrete.log.queries.clear_on_reload')) {
                        $logger->clearQueryLog();
                    }

                    $logger->write($loggers);
                }
            }

            foreach (\Database::getConnections() as $connection) {
                $connection->close();
            }
        }
        if ($config->get('concrete.cache.overrides')) {
            Environment::saveCachedEnvironmentObject();
        } else {
            $env = Environment::get();
            $env->clearOverrideCache();
        }
        exit;
    }

    /**
     * @param \Concrete\Core\Http\Request $request
     * @deprecated Use the dispatcher object to dispatch
     */
    public function dispatch(Request $request)
    {
        /** @var DispatcherInterface $dispatcher */
        $dispatcher = $this->make(DispatcherInterface::class);

        return $dispatcher->dispatch($request);
    }

    /**
     * Utility method for clearing all application caches.
     */
    public function clearCaches()
    {
        \Events::dispatch('on_cache_flush');

        $this['cache']->flush();
        $this['cache/expensive']->flush();

        $config = $this['config'];

        // Delete and re-create the cache directory
        $cacheDir = $config->get('concrete.cache.directory');
        if (is_dir($cacheDir)) {
            $fh = Core::make('helper/file');
            $fh->removeAll($cacheDir, true);
        }
        $this->setupFilesystem();

        $pageCache = PageCache::getLibrary();
        if (is_object($pageCache)) {
            $pageCache->flush();
        }

        // Clear the file thumbnail path cache
        $connection = $this['database'];
        $sql = $connection->getDatabasePlatform()->getTruncateTableSQL('FileImageThumbnailPaths');
        try {
            $connection->executeUpdate($sql);
        } catch(\Exception $e) {}

        // clear the environment overrides cache
        $env = \Environment::get();
        $env->clearOverrideCache();

        // Clear localization cache
        Localization::clearCache();

        // clear block type cache
        BlockType::clearCache();

        // Clear precompiled script bytecode caches
        OpCache::clear();

        \Events::dispatch('on_cache_flush_end');
    }

    /**
     * If we have job scheduling running through the site, we check to see if it's time to go for it.
     */
    protected function handleScheduledJobs()
    {
        $config = $this['config'];

        if ($config->get('concrete.jobs.enable_scheduling')) {
            $c = Page::getCurrentPage();
            if ($c instanceof Page && !$c->isAdminArea()) {
                // check for non dashboard page
                $jobs = Job::getList(true);
                $auth = Job::generateAuth();
                $url = "";
                // jobs
                if (count($jobs)) {
                    foreach ($jobs as $j) {
                        if ($j->isScheduledForNow()) {
                            $url = View::url(
                                                  '/ccm/system/jobs/run_single?auth=' . $auth . '&jID=' . $j->getJobID(
                                                  )
                                );
                            break;
                        }
                    }
                }

                // job sets
                if (!strlen($url)) {
                    $jSets = JobSet::getList(true);
                    if (is_array($jSets) && count($jSets)) {
                        foreach ($jSets as $set) {
                            if ($set->isScheduledForNow()) {
                                $url = View::url(
                                                      '/ccm/system/jobs?auth=' . $auth . '&jsID=' . $set->getJobSetID(
                                                      )
                                    );
                                break;
                            }
                        }
                    }
                }

                if (strlen($url)) {
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $config->get('app.curl.verifyPeer'));
                    $res = curl_exec($ch);
                }
            }
        }
    }

    /**
     * Returns true if concrete5 is installed, false if it has not yet been.
     */
    public function isInstalled()
    {
        if ($this->installed === null) {
            if (!$this->isShared('config')) {
                throw new \Exception('Attempting to check install status before application initialization.');
            }

            $this->installed = $this->make('config')->get('concrete.installed');
        }

        return $this->installed;
    }

    /**
     * Checks to see whether we should deliver a concrete5 response from the page cache.
     */
    public function checkPageCache(\Concrete\Core\Http\Request $request)
    {
        $library = PageCache::getLibrary();
        if ($library->shouldCheckCache($request)) {
            $record = $library->getRecord($request);
            if ($record instanceof PageCacheRecord) {
                if ($record->validate()) {
                    return $library->deliver($record);
                }
            }
        }

        return false;
    }

    public function handleAutomaticUpdates()
    {
        $config = $this['config'];
        $installed = $config->get('concrete.version_db_installed');
        $core = $config->get('concrete.version_db');
        if ($installed < $core) {
            Update::updateToCurrentVersion();
        }
    }

    /**
     * Register package autoloaders. Has to come BEFORE session calls.
     */
    public function setupPackageAutoloaders()
    {
        $pla = \Concrete\Core\Package\PackageList::get();
        $pl = $pla->getPackages();
        $cl = ClassLoader::getInstance();
        /** @var \Package[] $pl */
        foreach ($pl as $p) {
            \Config::package($p);
            if ($p->isPackageInstalled()) {
                $pkg = $this->make('Concrete\Core\Package\PackageService')->getClass($p->getPackageHandle());
                if (is_object($pkg) && (!$pkg instanceof \Concrete\Core\Package\BrokenPackage)) {
                    $cl->registerPackage($pkg);
                    $this->packages[] = $pkg;
                }
            }
        }
    }

    /**
     * Run startup and localization events on any installed packages.
     */
    public function setupPackages()
    {
        $checkAfterStart = false;

        $config = $this['config'];

        $loc = Localization::getInstance();
        $entityManager = $this['Doctrine\ORM\EntityManager'];
        $configUpdater = new EntityManagerConfigUpdater($entityManager);

        foreach ($this->packages as $pkg) {
            if ($config->get('concrete.updates.enable_auto_update_packages')) {
                $dbPkg = \Package::getByHandle($pkg->getPackageHandle());
                $pkgInstalledVersion = $dbPkg->getPackageVersion();
                $pkgFileVersion = $pkg->getPackageVersion();
                if (version_compare($pkgFileVersion, $pkgInstalledVersion, '>')) {
                    $loc->pushActiveContext('system');
                    $dbPkg->upgradeCoreData();
                    $dbPkg->upgrade();
                    $loc->popActiveContext();
                }
            }

            $service = $this->make('Concrete\Core\Package\PackageService');
            $service->setupLocalization($pkg);

            if (method_exists($pkg, 'on_start')) {
                $pkg->on_start();
            }

            $service->bootPackageEntityManager($pkg);

            if (method_exists($pkg, 'on_after_packages_start')) {
                $checkAfterStart = true;
            }
        }

        $config->set('app.bootstrap.packages_loaded', true);

        // After package initialization, the translations adapters need to be
        // reinitialized when accessed the next time because new translations
        // are now available.
        $loc->removeLoadedTranslatorAdapters();

        if ($checkAfterStart) {
            foreach ($this->packages as $pkg) {
                if (method_exists($pkg, 'on_after_packages_start')) {
                    $pkg->on_after_packages_start();
                }
            }
        }
    }

    /**
     * Ensure we have a cache directory.
     */
    public function setupFilesystem()
    {
        $config = $this['config'];

        if (!is_dir($config->get('concrete.cache.directory'))) {
            @mkdir($config->get('concrete.cache.directory'), $config->get('concrete.filesystem.permissions.directory'));
            @touch($config->get('concrete.cache.directory') . '/index.html', $config->get('concrete.filesystem.permissions.file'));
        }
    }

    /**
     * Returns true if the app is run through the command line.
     */
    public static function isRunThroughCommandLineInterface()
    {
        return defined('C5_ENVIRONMENT_ONLY') && C5_ENVIRONMENT_ONLY || PHP_SAPI == 'cli';
    }

    /**
     * Using the configuration value, determines whether we need to redirect to a URL with
     * a trailing slash or not.
     *
     * @return \Concrete\Core\Routing\RedirectResponse
     */
    public function handleURLSlashes(SymfonyRequest $request, Site $site)
    {
        $siteConfig = $site->getConfigRepository();
        $trailing_slashes = $siteConfig->get('seo.trailing_slash');
        $path = $request->getPathInfo();

        // If this isn't the homepage
        if ($path && $path != '/') {

            // If the trailing slash doesn't match the config, return a redirect response
            if (($trailing_slashes && substr($path, -1) != '/') ||
                (!$trailing_slashes && substr($path, -1) == '/')) {

                $parsed_url = Url::createFromUrl($request->getUri(),
                $trailing_slashes ? Url::TRAILING_SLASHES_ENABLED : Url::TRAILING_SLASHES_DISABLED);

                $response = new RedirectResponse($parsed_url, 301);
                $response->setRequest($request);

                return $response;
            }
        }
    }

    /**
     * If we have redirect to canonical host enabled, we need to honor it here.
     *
     * @return \Concrete\Core\Routing\RedirectResponse
     */
    public function handleCanonicalURLRedirection(SymfonyRequest $r, Site $site)
    {
        $globalConfig = $this['config'];
        $siteConfig = $site->getConfigRepository();

        if ($globalConfig->get('concrete.seo.redirect_to_canonical_url') && $siteConfig->get('seo.canonical_url')) {
            $requestUri = $r->getUri();

            $path = parse_url($requestUri, PHP_URL_PATH);
            $trailingSlash = substr($path, -1) === '/';

            $url = UrlImmutable::createFromUrl($requestUri, $trailingSlash);

            $canonical = UrlImmutable::createFromUrl($siteConfig->get('seo.canonical_url'),
                (bool) $siteConfig->get('seo.trailing_slash')
            );

            // Set the parts of the current URL that are specified in the canonical URL, including host,
            // port, scheme. Set scheme first so that our port can use the magic "set if necessary" method.
            $new = $url->setScheme($canonical->getScheme()->get());
            $new = $new->setHost($canonical->getHost()->get());
            $new = $new->setPort($canonical->getPort()->get());

            // Now we have our current url, swapped out with the important parts of the canonical URL.
            // If it matches, we're good.
            if ($new == $url) {
                return null;
            }

            // Uh oh, it didn't match. before we redirect to the canonical URL, let's check to see if we have an SSL
            // URL
            if ($siteConfig->get('seo.canonical_ssl_url')) {
                $ssl = UrlImmutable::createFromUrl($siteConfig->get('seo.canonical_ssl_url'));

                $new = $url->setScheme($ssl->getScheme()->get());
                $new = $new->setHost($ssl->getHost()->get());
                $new = $new->setPort($ssl->getPort()->get());

                // Now we have our current url, swapped out with the important parts of the canonical URL.
                // If it matches, we're good.
                if ($new == $url) {
                    return null;
                }
            }

            $response = new RedirectResponse($new, '301');

            return $response;
        }
    }

    /**
     * Get or check the current application environment.
     *
     * @param  mixed
     *
     * @return string|bool
     */
    public function environment()
    {
        if (count(func_get_args()) > 0) {
            return in_array($this->environment, func_get_args());
        } else {
            return $this->environment;
        }
    }

    /**
     * Detect the application's current environment.
     *
     * @param  array|string|Callable  $environments
     *
     * @return string
     */
    public function detectEnvironment($environments)
    {
        $r = Request::getInstance();
        $pos = stripos($r->server->get('SCRIPT_NAME'), DISPATCHER_FILENAME);
        if ($pos > 0) {
            //we do this because in CLI circumstances (and some random ones) we would end up with index.ph instead of index.php
            $pos = $pos - 1;
        }
        $home = substr($r->server->get('SCRIPT_NAME'), 0, $pos);
        $this['app_relative_path'] = rtrim($home, '/');

        $args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;

        $detector = new EnvironmentDetector();

        return $this->environment = $detector->detect($environments, $args);
    }

    /**
     * Instantiate a concrete instance of the given type.
     *
     * @param  string $concrete
     * @param  array $parameters
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    public function build($concrete, array $parameters = array())
    {
        $object = parent::build($concrete, $parameters);
        if (is_object($object) && $object instanceof ApplicationAwareInterface) {
            $object->setApplication($this);
        }

        return $object;
    }

    /**
     * @return RuntimeInterface
     */
    public function getRuntime()
    {
        // Set the runtime to a singleton
        $runtime_class = 'Concrete\Core\Foundation\Runtime\DefaultRuntime';
        if (!$this->isShared($runtime_class)) {
            $this->singleton($runtime_class);
        }

        /** @var DefaultRuntime $runtime */
        $runtime = $this->make($runtime_class);

        // If we're in CLI, lets set the runner to the CLI runner
        if ($this->isRunThroughCommandLineInterface()) {
            $runtime->setRunClass('Concrete\Core\Foundation\Runtime\Run\CLIRunner');
        }

        return $runtime;
    }

    /**
     * @deprecated Use the singleton method
     *
     * @param $abstract
     * @param $concrete
     */
    public function bindShared($abstract, $concrete)
    {
        return $this->singleton($abstract, $concrete);
    }
}
