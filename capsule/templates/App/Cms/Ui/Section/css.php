<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 31.10.2016
 * Time: 0:31
 *
 * include css files in the head section
 */
use \Capsule\Tools\Assets\Assets as a;
use \Capsule\Tools\Assets\Css as c;
$assets = new a;
$assets
    ->add(new c('/capsule/components/jquery-ui/jquery-ui.min.css'))
    ->add(new c('/capsule/components/bootstrap/css/bootstrap.min.css'))
    ->add(new c('/capsule/components/font-awesome/css/font-awesome.min.css'))
    ->add(new c('/capsule/assets/modules/AplAccordionMenu/AplAccordionMenu.css', true))
    ->add(new c('/capsule/assets/cms/modules/CapsuleCmsActionMenu/CapsuleCmsActionMenu.css', true))
    ->add(new c('/capsule/assets/modules/Scrollable/CapsuleUiScrollable.css', true))
    ->add(new c('/capsule/assets/cms/modules/CapsuleCmsDataGrid/CapsuleCmsDataGrid.css', true))
    //->add(new c('/capsule/assets/cms/modules/CapsuleCmsDataGrid/CapsuleCmsDataGridRU.css', true))
    ->add(new c('/capsule/assets/cms/css/style.css', true));
$assets->putCss();
foreach ($this->css as $item) echo $item;