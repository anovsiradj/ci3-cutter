<?php

/* CodeIgniter3:Cutter Facade */

function cutter_block($name)
{
	get_instance()->cutter->field($name);
}

function cutter_field($name) {
	get_instance()->cutter->field($name);
}

function cutter_start($name) {
	get_instance()->cutter->start($name);
}

function cutter_begin($name) {
	get_instance()->cutter->start($name);
}

function cutter_end()
{
	get_instance()->cutter->end();
}

function cutter_stop()
{
	get_instance()->cutter->end();
}
