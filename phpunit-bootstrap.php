<?php

// Set the current directory ahead in the include path for testing

$base = dirname(__FILE__);
set_include_path("{$base}:{$base}/tests:" . get_include_path());

?>
