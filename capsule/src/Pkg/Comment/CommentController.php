<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 02.04.2017
 * Time: 16:01
 */

namespace Pkg\Comment;


use App\Cms\Controller\ReferenceController;

class CommentController extends ReferenceController
{
    protected $moduleClass = 'Pkg.Comment.Comment';
}