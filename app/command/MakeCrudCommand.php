<?php
/**
 * Custom Make CRUD Command
 *
 * Extends webman's original make:crud to add a list() method
 * for paginated data retrieval in the generated controller.
 */

namespace app\command;

use Symfony\Component\Console\Attribute\AsCommand;
use Webman\Console\OrmType;

#[AsCommand('make:crud', 'Make CRUD (Model, Controller, Validator) with list method')]
class MakeCrudCommand extends \Webman\Console\Commands\MakeCrudCommand
{
    protected function createCrudController(
        string $name,
        string $namespace,
        string $file,
        string $ormType,
        ?string $validatorNamespace = null,
        ?string $modelNamespace = null,
        ?string $modelClass = null
    ): void
    {
        // Let parent generate the original controller file first
        parent::createCrudController(...func_get_args());

        // Read the generated file
        $content = file_get_contents($file);

        // Determine model short name
        $modelName = $modelClass ?: str_replace('Controller', '', $name);
        $isThinkOrm = $ormType === OrmType::THINKORM;

        // Build the list() method based on ORM type
        if ($isThinkOrm) {
            $listMethod = <<<PHP

    /**
     * List
     * @param Request \$request
     * @return Response
     */
    public function list(Request \$request): Response
    {
        \$page = (int)\$request->input('page', 1);
        \$pageSize = (int)\$request->input('page_size', 15);
        \$total = {$modelName}::count();
        \$items = {$modelName}::order('id', 'desc')
            ->offset((\$page - 1) * \$pageSize)
            ->limit(\$pageSize)
            ->select();
        return json(['code' => 0, 'msg' => 'ok', 'data' => [
            'items' => \$items,
            'total' => \$total,
            'page' => \$page,
            'page_size' => \$pageSize,
        ]]);
    }
}

PHP;
        } else {
            $listMethod = <<<PHP

    /**
     * List
     * @param Request \$request
     * @return Response
     */
    public function list(Request \$request): Response
    {
        \$page = (int)\$request->input('page', 1);
        \$pageSize = (int)\$request->input('page_size', 15);
        \$total = {$modelName}::count();
        \$items = {$modelName}::orderBy('id', 'desc')
            ->offset((\$page - 1) * \$pageSize)
            ->limit(\$pageSize)
            ->get();
        return json(['code' => 0, 'msg' => 'ok', 'data' => [
            'items' => \$items,
            'total' => \$total,
            'page' => \$page,
            'page_size' => \$pageSize,
        ]]);
    }
}

PHP;
        }

        // Replace the class closing brace with list method + class close
        // The generated file ends with "}\n" (class closing brace at column 0)
        $lastPos = strrpos($content, "}\n");
        if ($lastPos !== false) {
            $content = substr($content, 0, $lastPos) . $listMethod;
            file_put_contents($file, $content);
        }
    }
}
