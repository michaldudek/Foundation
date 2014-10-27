<?php
/**
 * @see https://github.com/michaldudek/Foundation/blob/master/src/MD/Foundation/Utils/FilesystemUtils.php
 */
if (!function_exists('globstar')) :
function globstar($pattern, $flags = 0) {
  // if not using ** then just use PHP's glob()
  if (stripos($pattern, '**') === false) {
    // turn off the custom flags
    $files = glob($pattern, $flags);

    sort($files);

    return $files;
  }

  $patterns = array();

  // if globstar is inside braces
  if ($flags & GLOB_BRACE) {
    $regexp = '/\{(.+)?([\*]{2}[^,]?)(.?)\}/i';
    // check if this situation really occurs (otherwise we can end up with infinite nesting)
    if (preg_match($regexp, $pattern)) {
      // extract the globstar from inside the braces and add a new pattern to patterns list
      $patterns[] = preg_replace_callback('/(.+)?\{(.+)?([\*]{2}[^,]?)(.?)\}(.?)/i', function($matches) {
        $brace = '{'. $matches[2] . $matches[4] .'}';
        if ($brace === '{,}' || $brace === '{}') {
          $brace = '';
        }

        $pattern = $matches[1] . $brace . $matches[5];
        return str_replace('//', '/', $pattern);
      }, $pattern);

      // and now change the braces in the main pattern to globstar
      $pattern = preg_replace_callback($regexp, function($matches) {
        return $matches[2];
      }, $pattern);
    }
  }

  $pos = stripos($pattern, '**');

  $rootPattern = substr($pattern, 0, $pos) .'*';
  $restPattern = substr($pattern, $pos + 2);

  $patterns[] = $restPattern;

  while($dirs = glob($rootPattern, GLOB_ONLYDIR)) {
    $rootPattern = $rootPattern .'/*';

    foreach($dirs as $dir) {
      $patterns[] = $dir . $restPattern;
    }
  }

  $files = array();

  foreach($patterns as $pat) {
    $files = array_merge($files, globstar($pat, $flags));
  }

  $files = array_unique($files);

  sort($files);

  return $files;
}
endif;
