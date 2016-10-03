<?php

// this an escape function to sanitize data
// can do this going in or coming out
// teacher recommends both

function escape($string) {
	return htmlentities($string, ENT_QUOTES, 'UTF-8');
}//escape passes string, lots more ways to do this