<?php
/**
 * Cutter Class
 *
 * @created		201608061555
 * @version		Version 1.0 (CI Version 3.1.0)
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Template View
 * @author		Mayendra Costanov (anovsiradj) <anov.siradj22@(gmail|live).com|anov.siradj@gin.co.id>
 * @link		https://github/anovsiradj/codeigniter-cutter/
 * @license		Internet License.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cutter
{
	protected $layout = 'layout';
	protected $_view_holder = [];
	protected $_data_holder = [];
	protected $_current_field = NULL;

	/**
	* Refference to CI sigleton
	* 
	* @return void
	*/
	function __construct()
	{
		$this->CI =& get_instance();
		$this->set_layout($this->layout);
	}

	/**
	* set_layout()
	* 
	* @return void
	*/
	public function set_layout($name)
	{
		$this->layout = $name . '.cutter.php';
	}

	/**
	* Define view-field in layout and render field
	* 
	* @return void
	*/
	public function field($name)
	{
		if (!isset($this->_view_holder[$name])) return;

		// saya tidak suka pakai '' (string kosong), jadi saya pakai null
		echo implode(NULL, $this->_view_holder[$name]);
	}

	/**
	* view()
	* 
	* @return void
	*/
	public function view($name, $data = [], $render = TRUE)
	{
		// suffix. untuk identitas.
		// karena di CI ada filter jika '.' (titik) dianggap ekstensi
		// maka harus nulis nama view dengan lengkap
		$name .= '.cutter.php';

		// jangan tampilkan view.
		$this->CI->load->view($name, $data, TRUE);

		// barang-kali, tidak ingin langsung tampilkan layout
		if ($render) $this->render();
	}

	/**
	* render()
	* 
	* @return void
	*/
	protected function render()
	{
		$this->CI->load->view($this->layout, $this->_data_holder);
	}

	/**
	* Begin view-field output buffer
	* 
	* @return void
	*/
	public function start($name)
	{
		// kalau tidak diawali, ya sudah
		if ($this->_current_field !== NULL) return;

		$this->_current_field = $name;
		ob_start();
	}

	/**
	* Stop view-field output buffer
	* then assign to holder
	* 
	* @return void
	*/
	public function end()
	{
		// kalau tidak dimulai, ya sudah
		if ($this->_current_field === NULL) return;

		$this->_view_holder[$this->_current_field][] = ob_get_clean();

		$this->_current_field = NULL;
	}

	/**
	* Set data to layout.
	* 
	* @return void
	*/
	public function data($key, $value)
	{
		$this->_data_holder[$key] = $value;
	}

}
