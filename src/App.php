<?php

namespace Plainmotif\Mizzenlite;

use Plainmotif\Mizzenlite\Container;
use Plainmotif\Mizzenlite\Request;
use Plainmotif\Mizzenlite\View\View;
use Plainmotif\Mizzenlite\EventManager;
use Plainmotif\Mizzenlite\Navigation\Navigation;
use Plainmotif\Mizzenlite\Helpers\ArrayHelper;
use Plainmotif\Mizzenlite\Page\PageRepositoryProvider;
use Plainmotif\Mizzenlite\Page\PageRepository;
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
         * @todo  create a default bag and only override if something special needed
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
            } else {
                $config      = new \StdClass;
                $config->url = isset($_SERVER['SERVER_NAME']) ?
                    'http://'.$_SERVER['SERVER_NAME'].'/' : 'http://mizzencms.net/';
                $config->siteName = 'MizzenLite';
            }

            return $config;
        });
        $bag->add('url', function () {
            $url = Url::createFromServer($_SERVER);
            /**
             * Remove inedex.php if exists
             */
            $url->getPath()->remove('index.php');
            /**
             * home needs to be converted to index
             */
            if ((string) $url->getPath() === '') {
                $url->setPath('index');
            }

            return $url;
        });
        $bag->add('pageRepository', function () {
            $prp = new PageRepositoryProvider(new PageRepository(), new Finder());
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
         * register all observers before we start so that modules can add their own functionality etc.
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

    public function registerObservers()
    {
        $em = $this->getBag()->getShared('eventManager');

        if (is_dir($this->getBag()->get('basePath')->path.'/module/Observers')) {
            $dir = opendir($this->getBag()->get('basePath')->path.'/module/Observers');

            while ($file = readdir($dir)) {
                if (!in_array($file, array('.', '..'))) {
                    $class  = 'mizzenlite\module\Observers\\'.str_replace('.php', '', $file);
                    $object = new $class();
                    $em->attach($object);
                }
            }

            closedir($dir);
        }
    }
}