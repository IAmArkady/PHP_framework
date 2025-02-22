<?php

namespace src;

class TemplateEngine{
    static private $path = PROJECT_ROOT .
                    DIRECTORY_SEPARATOR .
                    'templates' .
                    DIRECTORY_SEPARATOR;
    private static function replaceVariables($content, $data){
        foreach ($data as $key => $value) {
            $pattern = '/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/';
            $content = preg_replace($pattern, $value, $content);
        }
        return $content;
    }
    private static function parseArguments($argsString)
    {
        $args = [];
        foreach (explode(',', $argsString) as $arg)
            $args[] = trim($arg, " '\"");
        return $args;
    }

    private static function executeFunctions($content)
    {
        return preg_replace_callback('/\{\{\s*(.*?)\s*\}\}/', function ($matches) {
            $expression = $matches[1];
            if (preg_match('/\{\{\s*([a-zA-Z_][a-zA-Z0-9_\\\]*)::([a-zA-Z_][a-zA-Z0-9_]*)\((.*)\)\s*\}\}/', $expression, $methodMatch)) {
                $className = $methodMatch[1];
                $methodName = $methodMatch[2];
                $args = self::parseArguments($methodMatch[3]);

                if (class_exists($className) && method_exists($className, $methodName))
                    return call_user_func_array([$className, $methodName], $args);
                else
                    return 'Ошибка: Класс или метод не найдены';
            }

            try {
                return eval("return {$expression};");
            } catch (\Throwable $e) {
                return 'Ошибка в выражении: {$expression}';
            }
        }, $content);
    }
    static public function render($template, $data = []){
        $templatePath = self::$path . $template . '.html.php';
        if (!file_exists($templatePath)) {
            error_log("Error: Template not found {$template}");
            return '';
        }

        $content = file_get_contents($templatePath);
        $content = self::executeFunctions(self::replaceVariables($content, $data));
        ob_start();
        extract($data);
        eval('?>' . $content);
        return ob_get_clean();
    }

}