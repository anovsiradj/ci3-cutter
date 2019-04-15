<?php
/**
 * CodeIgniter3 Cutter Template Library
 * 
 * Taste Like Blade.
 * 
 * @date 201608061555, 201608281400, 201608291945, 201609021729, 201610172024, 201611291546, 20190414
 * @version Version 2.0.0 (tested with CI version 3.1.10)
 *
 * @package     CodeIgniter
 * @subpackage	Libraries
 * @category    Template View
 * 
 * @author Mayendra Costanov (anovsiradj) <anov.siradj22@gmail.com>
 * @link https://github.com/anovsiradj/codeigniter-cutter/
 * @license WTFPL
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cutter
{
	protected $_current_layout = NULL;
	protected $_view_holder = array();
	protected $_data_holder = array();
	protected $_current_field = NULL;
	protected $_suffix = '.cutter.php';

	protected static $_facade = TRUE;

	/**
	* @return $this
	*/
	function __construct()
	{
		$this->CI =& get_instance();
		$this->set_layout('layout');
	}

	public function facade()
	{
		if (static::$_facade) {
			require __DIR__ . '/facade.php';
			static::$_facade = FALSE;
		}
	}

	/**
	* set_layout()
	* 
	* set current layout
	* 
	* @param string
	* 
	* @return $this
	*/
	public function set_layout($name)
	{
		$this->_current_layout = $name . $this->_suffix;
		return $this;
	}

	/**
	* @alias set_layout()
	*/
	public function layout($name)
	{
		return $this->set_layout($name);
	}

	/**
	* Define view-field in layout and render field
	* 
	* @return bool
	*/
	public function field($name)
	{
		if (isset($this->_view_holder[$name])) {
			echo implode(PHP_EOL, $this->_view_holder[$name]);
			return true;
		}
		return false;
	}

	/**
	* @alias field()
	*/
	public function block($name)
	{
		return $this->field($name);
	}

	/**
	* view()
	* 
	* @return $this
	*/
	public function view($name, $data = array(), $render = TRUE)
	{
		$this->data($data);

		$this->CI->load->view($name . $this->_suffix, $this->_data_holder, TRUE);

		if ($render) $this->render();

		return $this;
	}

	/**
	* render()
	* 
	* should have only called once.
	* 
	* @return void
	*/
	public function render($data = array())
	{
		$this->data($data);
		$this->CI->load->view($this->_current_layout, $this->_data_holder, FALSE);
	}

	/**
	* Begin view-field output buffer
	* 
	* @return void
	*/
	public function start($name)
	{
		if ($this->_current_field !== NULL) {
			throw new Exception('cannot opening Cutter without closing field.');
			return;
		}

		if (isset($this->_view_holder[$name]) === FALSE) $this->_view_holder[$name] = array();

		$this->_current_field = $name;

		ob_start();
	}

	/**
	* End view-field output buffer
	* then assign to holder
	* 
	* @return void
	*/
	public function end()
	{
		if ($this->_current_field === NULL) {
			throw new Exception('cannot closing Cutter without opening field.');
			return;
		}

		$this->_view_holder[$this->_current_field][] = ob_get_clean();
		$this->_current_field = NULL;
	}

	/**
	* @alias end()
	*/
	public function stop()
	{
		return $this->end();
	}

	/**
	* Data Manager
	* 
	* @param array
	* @param string
	* @param string|mixed
	* 
	* @return void|$this
	*/
	public function data()
	{
		$param = func_get_args();
		if (isset($param[0])) {
			if (is_array($param[0])) {
				foreach ($param[0] as $k) {
					$this->_data_holder[$k] =& $param[0][$v];
				}

			} else {
				if (isset($param[1])) {
					$this->_data_holder[$param[0]] =& $param[1];

				} else {
					if (isset($this->_data_holder[$param[0]])) return $this->_data_holder[$param[0]];
					else return NULL;
				}
			}

		} else {
			return $this->_data_holder;
		}
	}
}
