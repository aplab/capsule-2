<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 19.02.2017
 * Time: 17:46
 */

namespace Pkg\Post;


use App\Cms\Component\Seo\NamedActive;
use Pkg\DataModelConfigLocationNear;

class Post extends NamedActive
{
    use DataModelConfigLocationNear;
}