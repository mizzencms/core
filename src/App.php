<?php

namespace Mizzencms\Core;

use Mizzencms\Core\Container;
use Mizzencms\Core\Request;
use Mizzencms\Core\View\View;
use Mizzencms\Core\EventManager;
use Mizzencms\Core\Navigation\Navigation;
use Mizzencms\Core\Helpers\ArrayHelper;
use Mizzencms\Core\Page\PageRepositoryGenerator;
use Mizzencms\Core\Page\PageRepository;
use Mizzencms\Core\MetaParser\MetaParser;
use Symfony\Component\Finder\Finder;
use League\Url\Url;

class App extends Base
{
    /**
     * @todo redo
     * @param  Directory $basePath
     */
    public function init(\Directory $basePath)
    {
        /**
         * @todo  create a default bag and only override if something 
         * special needed
         */
        $bag = Container::getInstance();

        $bag->add('basePath', function () use ($basePath) {
            return $basePath;
        });
        /**
         * @todo  move to config class and create a set up
         */
        $bag->add('config', function () use ($basePath) {
            $configPath = $basePath->path.'/config/config.json';

            if (is_readable($configPath)) {
                $config = ArrayHelper::configArrayToObject(
                    json_decode(file_get_contents($configPath), true)
                );

                if (!isset($config->baseDir)) {
                    $config->baseDir = '';
                }
            } else {
                $config      = new \StdClass;
                $config->url = isset($_SERVER['SERVER_NAME']) ?
                    'http://'.$_SERVER['SERVER_NAME'].'/' : 
                    'http://mizzencms.net/';
                $config->siteName = 'MizzenLite';
                $config->baseDir = '';
            }

            return $config;
        });
        $bag->add('url', function () use ($bag) {
            $url = Url::createFromServer($_SERVER);
            /**
             * Remove index.php if exists
             */
            $url->getPath()->remove('index.php');
            /**
             * remove base dir (for when we are installed in a dir)
             */
            if (!empty($bag->get('config')->baseDir)) {
                $url->getPath()->remove($bag->get('config')->baseDir);
            }
            /**
             * home needs to be converted to index
             */
            if ((string) $url->getPath() === '') {
                $url->setPath('index');
            }

            return $url;
        });
        $bag->add('metaParser', function () {
            return new MetaParser();
        });
        $bag->add('pageRepository', function () {
            $prp = new PageRepositoryGenerator(
                new PageRepository(),
                new Finder()
            );
            $prp->populateRepository();

            return $prp->getPageRepository();
        });
        $bag->add('navigation', function () {
            return (new Navigation())->createPageMenu();
        });
        $bag->add('eventManager', function () {
            return new EventManager();
        });
        $bag->add('view', function () {
            return new View();
        });
    }

    public function run()
    {
        /**
         * register all observers before we start so that modules can add their 
         * own functionality etc.
         */
        $this->registerObservers();
        $this->triggerEvent('appRun', array('app' => $this));

        $url = $this->getBag()->get('url');

        $pageRepository = $this->getBag()->getShared('pageRepository');
        $page           = $pageRepository->findByPath((string) $url->getPath());
        $view           = $this->getBag()->get('view');

        $view->setPage($page);
        $view->config = $this->getBag()->getShared('config');

        $this->triggerEvent('appRunAfter', array('app' => $this));

        return $view->render();
    }
    /**
     * @todo redo
     */
    public function registerObservers()
    {
        $em              = $this->getBag()->getShared('eventManager');
        $pathModule      = $this->getBag()->get('basePath')->path.'/module/';
        $disabledModules = [];

        if (isset($this->getBag()->get('config')->module) 
            && isset($this->getBag()->get('config')->module->disabled)) {

            $disabledModules = (array) $this->getBag()
                ->get('config')
                ->module
                ->disabled
            ;
        }
        if (is_dir($pathModule)) {
            $dir = opendir($pathModule);

            while ($vendor = readdir($dir)) {
                if (!in_array($vendor, array('.', '..'))) {
                    $pathVendor = $pathModule.$vendor.'/';
                    $vendorDir  = opendir($pathVendor);

                    while ($module = readdir($vendorDir)) {
                        if (!in_array($module, array('.', '..'))
                            && !in_array($vendor.'/'.$module, $disabledModules)) {
                            $pathObserver = $pathVendor.$module.'/Module.php';

                            if (file_exists($pathObserver)) {
                                $class  = 'Module\\'.$vendor.'\\'.$module.'\\Module';
                                $object = new $class();
                                $em->attach($object);
                            }
                        }
                    }
                    closedir($vendorDir);
                }
            }
            closedir($dir);
        }
    }
}