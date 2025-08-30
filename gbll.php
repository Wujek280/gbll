#! /usr/bin/env php
<?php
//$_all = "git for-each-ref --sort=committerdate refs/heads/ --format='%(color: cyan)%(refname:short)'";
$_all = "git for-each-ref --no-merged=master --sort=committerdate refs/heads/ --format='%(color: cyan)%(refname:short)'";

$arr = array_filter(explode(PHP_EOL, shell_exec("$_all")));
// $arr_nomerged = array_filter(explode(PHP_EOL, shell_exec("$_nomerged")));

/** Search */
$search = $argv[1] ?? null;
//
if($search){
    $arr = array_filter($arr, function($branch) use($search) {
         return preg_match("/{$search}/", $branch);
    });
    $arr = array_values($arr);

}

/** @var array HR + Inverted array of keys */
$keys = array_reverse(array_keys($arr));
$arr = array_combine( $keys, $arr);

// /** @var mixed Current - determine current branch - prevent switching to current branch */
// $current = null;

if(!$arr) return 0;

/** PRINT OPTIONS */
foreach ($arr as $i => $name) {
    $name = trim($name);
    $i+=1;

    // if(preg_match("/\*/", $name)) $current = $i;

    echo "\033[1;34m [$i] - $name \033[0m" . PHP_EOL;
}

/** GET USER INPUT */
$handle = fopen("php://stdin", "r");
$select = (int)fgets($handle);
fclose($handle);

/** Prevent switching to current branch */
// if($current === $select){
//     echo "Arleady on {$arr[$select]}!" . PHP_EOL;
//     return 1;
// }

$select -= 1;

echo "Switching to {$arr[$select]}...  " . PHP_EOL;

shell_exec("git checkout {$arr[$select]}");
