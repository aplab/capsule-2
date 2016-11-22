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
 * include js files in the head section
 */
use \Capsule\Tools\Assets\Assets as a;
use \Capsule\Tools\Assets\Js as j;
$assets = new a;
$assets
    ->add(new j('/capsule/components/jquery/jquery-3.1.1.min.js'))
    ->add(new j('/capsule/components/jquery.mousewheel/jquery.mousewheel.min.js'))
    ->add(new j('/capsule/components/bootstrap/js/bootstrap.min.js'))
    ->add(new j('/capsule/components/jquery-ui/jquery-ui.min.js'))
    ->add(new j('/capsule/components/js-cookie/js.cookie-2.1.3.min.js'))
    ->add(new j('/capsule/assets/modules/AplAccordionMenu/AplAccordionMenu.js', true))
    ->add(new j('/capsule/assets/modules/Scrollable/CapsuleUiScrollable.js', true))
    ->add(new j('/capsule/assets/cms/modules/CapsuleCmsActionMenu/CapsuleCmsActionMenu.js', true))
    ->add(new j('/capsule/assets/cms/modules/CapsuleCmsDataGrid/CapsuleCmsDataGrid.js', true))
    ->add(new j('/capsule/components/viewport-units-buggyfill/viewport-units-buggyfill.js'))
    ->add(new j('/capsule/assets/cms/js/js.js', true));
$assets->putJs();
foreach ($this->js as $item) echo $item;