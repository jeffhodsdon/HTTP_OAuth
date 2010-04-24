<?php

$base = dirname(__FILE__);
set_include_path("{$base}:{$base}/tests:" .  get_include_path());

require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

require_once 'PHPUnit/TextUI/Command.php';

$command = new PHPUnit_TextUI_Command;
$command->run($argv);

?>
