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
 * Time: 17:47
 */

namespace Pkg\Post;


use App\Cms\Controller\ReferenceController;

class PostController extends ReferenceController
{
    protected $moduleClass = 'Pkg.Post.Post';
}