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
$nocache = '?nocache=' . md5(microtime());
//$nocache = '';
?>
<script src="/capsule/components/jquery/jquery-3.1.1.min.js"></script>
<script src="/capsule/components/jquery-ui/jquery-ui.min.js"></script>
<script src="/capsule/assets/modules/AplAccordionMenu/AplAccordionMenu.js<?=$nocache?>"></script>
<script src="/capsule/assets/cms/js/js.js<?=$nocache?>"></script>
<?php foreach ($this->js as $item) echo $item ?>