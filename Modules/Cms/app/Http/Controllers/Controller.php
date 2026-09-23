<?php

namespace Modules\Cms\Http\Controllers;

use App\Concerns\ControllerTrait;

abstract class Controller extends \App\Http\Controllers\Controller
{
    use ControllerTrait;

    protected function template($file = null, $folder = null, $core = false)
    {
        $action = 'table';

        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $frame) {
            if (isset($frame['function']) && preg_match('/^(get|post)/', $frame['function'])) {
                $action = strtolower(preg_replace('/^(get|post)/', '', $frame['function']));
                break;
            }
        }

        if (in_array($action, ['update', 'create'])) {
            $action = 'form';
        }

        if ($file) {
            $action = $file;
        }

        $module = strtolower(str_replace('Controller', '', class_basename(get_class($this))));

        if ($folder) {
            $module = $folder;
        }

        return 'cms::pages.'.$module.'.'.$action;
    }
}
