<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 08.03.2014 3:32:18 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Controller;

use App\Cms\Ui\ActionMenu\Callback;
use App\Cms\Ui\ActionMenu\Url;
use App\Cms\Ui\DataModel\DataGrid\DataGrid;
use App\Cms\Ui\SectionManager;
use App\Cms\View\DataGridView;
use Capsule\Component\DataStruct\ReturnValue;
use Capsule\Component\Path\ComponentTemplatePath;
use Capsule\Component\Superglobals\Superglobals;
use Capsule\I18n\I18n;
use Capsule\Core\Fn;
use Capsule\DataModel\DataModel;
use Capsule\Common\Filter;
use Capsule\Tools\Tools;

/**
 * NestedItem.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class NestedItem extends ReferenceController
{
    protected $moduleClass = 'Capsule\\Unit\\Nested\\Item';

    /**
     * Значение фильтра по контейнеру
     *
     * @var string
     */
    protected $filterByContainer;

    /**
     * Some variants
     *
     * @var string
     */
    const ALL = 'all', BOUND = 'bound', WITHOUT_BINDING = 'without_binding';

    /**
     * Ключ суперглобального массива фильтра по контейнеру
     *
     * @var string
     */
    const FILTER_BY_CONTAINER = 'containerId';

    /**
     * Варианты значений фильтра
     *
     * @var array
     */
    protected $filterVariants = array(
        self::ALL => array(
            'value' => self::ALL,
            'text' => 'All',
            'selected' => false
        ),
        self::BOUND => array(
            'value' => self::BOUND,
            'text' => 'Bound',
            'selected' => false
        ),
        self::WITHOUT_BINDING => array(
            'value' => self::WITHOUT_BINDING,
            'text' => 'Without binding',
            'selected' => false
        )
    );

    protected function listItems(array $param = [])
    {
        $filter = $this->app->urlFilter;
        $module_class = $this->moduleClass;
        $module_config = $module_class::config();
        $container_class = Fn::cc($module_config->container, Fn::ns($module_class));
        foreach ($this->filterVariants as &$variant) {
            $variant['text'] = '<strong>' . I18n::_($variant['text']) . '</strong>';
        }
        $variants = array_replace($this->filterVariants, $container_class::optionsDataList());
        $this->filterByContainer();
        $this->filterByContainer = $this->env->get($this->filterByContainerKey());

        $path = new ComponentTemplatePath($this, 'filter');
        SectionManager::getInstance()->body->append(include $path);
        $toolbar = $this->app->registry->actionMenu;
        $toolbar->newMenuItem('New', new Url($filter($this->mod, 'add')));
        $toolbar->newMenuItem(
            'Delete selected',
            new Callback('CapsuleCmsDataGrid.getInstance().del()')
        );
        $toolbar->newMenuItem(
            'Filter: ' . I18n::_($variants[$this->filterByContainer]['text']),
            new Callback('CapsuleCmsDialog.getInstance(\'filter-by-container-window\').show()')
        );
        $c = $this->moduleClass;
        $config = $c::config();
        $title = $config->get('title') ?: untitled;
        $this->ui->title->prepend($title . '::List items');

        $p = $this->pagination();
        if (self::ALL === $this->filterByContainer) {
            $items = $c::page($p->currentPage, $p->itemsPerPage);
        } elseif (self::BOUND === $this->filterByContainer) {
            $items = $c::pageWithContainer($p->currentPage, $p->itemsPerPage);
        } elseif (self::WITHOUT_BINDING === $this->filterByContainer) {
            $items = $c::pageWithoutContainer($p->currentPage, $p->itemsPerPage);
        } else {
            $items = $c::pageByContainer($this->filterByContainer, $p->currentPage, $p->itemsPerPage);
        }
        $data_grid = new DataGrid(
            'capsule-ui-datagrid',
            ($this->moduleClass)::config(),
            new \ArrayIterator($items)
        );
        $data_grid->itemsPerPageVariants = $p->itemsPerPageVariants;
        $data_grid->pagesNumber = $p->pagesNumber;
        $data_grid->currentPage = $p->currentPage;
        $data_grid->itemsPerPage = $p->itemsPerPage;
        $data_grid->baseUrl = ($this->app->urlFilter)($this->mod);
        SectionManager::getInstance()->content->append(new DataGridView($data_grid));
    }

    /**
     * Инициализация переменной окружения - выбор текущего контейнера
     *
     * @param void
     * @return void
     */
    protected function filterByContainer()
    {
        $module_class = $this->moduleClass;
        $module_config = $module_class::config();
        $container_class = Fn::cc($module_config->container, Fn::ns($module_class));
        $variants = array_replace($this->filterVariants, $container_class::optionsDataList());
        $post = (new Superglobals())->post;
        $key = $this->filterByContainerKey();
        $default = key($variants);
        if (isset($post->{self::FILTER_BY_CONTAINER})) {
            $filter = $post->{self::FILTER_BY_CONTAINER};
            if (array_key_exists($filter, $variants)) {
                $this->env->set($key, $filter);
            }
        }
        if (!array_key_exists($this->env->get($key), $variants)) {
            $this->env->set($key, $default);
        }
    }

    /**
     * Возвращает ключ для хранения текущего контейнера в Env - переменные окружения пользователя
     *
     * @param void
     * @return string
     */
    protected function filterByContainerKey()
    {
        return Fn::concat_ws('::', $this->moduleClass, self::FILTER_BY_CONTAINER);
    }

    /**
     * (non-PHPdoc)
     * @see \App\Cms\Controller\ReferenceController::pages()
     */
    protected function pages($current_items_per_page)
    {
        $class = $this->moduleClass;
        if (self::ALL === $this->filterByContainer) {
            return $class::pages($current_items_per_page);
        }
        if (self::BOUND === $this->filterByContainer) {
            return $class::pagesWithContainer($current_items_per_page);
        }
        if (self::WITHOUT_BINDING === $this->filterByContainer) {
            return $class::pagesWithoutContainer($current_items_per_page);
        }
        return $class::pagesByContainer($this->filterByContainer, $current_items_per_page);
    }

    /**
     * @param string $class
     * @return ReturnValue
     */
    protected function createElement($class)
    {
        $item = ($class instanceof DataModel) ? $class : new $class;
        $this->filterByContainer = $this->env->get($this->filterByContainerKey());
        if (Filter::digit($this->filterByContainer)) $item->containerId = $this->filterByContainer;
        $config = $class::config();
        $properties = $config->properties;
        $post = (new Superglobals())->post;
        $ret = new ReturnValue;
        $ret->item = $item;
        foreach ($properties as $name => $property) {
            if ($class::isKey($name)) {
                continue;
            }
            if (!isset($property->formElement)) {
                continue;
            }
            if (!isset($post->$name)) {
                $ret->status = 1;
                return $ret;
            }
            try {
                $item->$name = $post->$name;
            } catch (\Exception $e) {
                $ret->status = 1;
                $this->ui->alert->append(I18n::_($e->getMessage()));
            }
        }
        if ($ret->status) {
            return $ret;
        }
        try {
            $item->store();
        } catch (\Exception $e) {
            $this->ui->alert->append(I18n::_($e->getMessage()));
            $ret->status = 1;
            return $ret;
        }
        $ret->status = 0;
        return $ret;
    }
}