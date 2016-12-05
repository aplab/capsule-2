<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 24.11.2016
 * Time: 1:55
 */

namespace App\Cms\View;

use App\Cms\Ui\DataModel\ObjectEditor\ObjectEditor;
use App\Cms\Ui\SectionManager;
use Capsule\Capsule;
use Capsule\Component\Path\ComponentTemplatePath;
use Capsule\Component\SectionManager\ToStringExceptionizer;

class ObjectEditorView
{
    protected $instance;

    public function __construct(ObjectEditor $instance)
    {
        $this->instance = $instance;
        SectionManager::getInstance()->onload->append(<<<JS
new CapsuleCmsObjectEditor($('#capsule-cms-object-editor')); 
JS
        );
    }

    public function __toString()
    {
        try {
            ob_start();
            include new ComponentTemplatePath($this, 'template');
            return ob_get_clean();
        } catch (\Exception $e) {
            set_error_handler(['\Capsule\Component\SectionManager\ToStringExceptionizer', 'errorHandler']);
            return ToStringExceptionizer::throwException($e);
        }
    }
}