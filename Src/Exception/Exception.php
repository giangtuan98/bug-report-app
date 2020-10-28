<?php

set_exception_handler([new \App\Exception\ExceptionHandler(), 'handle']);
set_error_handler([new \App\Exception\ExceptionHandler(), 'convertWarningsAndNoticesToException']);
