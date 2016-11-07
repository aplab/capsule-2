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
?>
<link rel="stylesheet" href="/capsule/components/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" href="/capsule/components/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="/capsule/assets/modules/AplAccordionMenu/AplAccordionMenu.css">
<link rel="stylesheet" href="/capsule/assets/cms/css/style.css">
<?php foreach ($this->css as $item) echo $item ?>