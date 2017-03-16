<?php
/**
 * Cutter Class
 * 
 * Taste Like Blade.
 * 
 * berawal dari, sebuah project menggunakan Laravel (pertama kali).
 * saya langsung tertarik dengan Blade (template engine laravel).
 * hingga pada suatu ketika, saya dapat project tim, (diharuskan) menggunakan CodeIgniter.
 * Nah, mau tidak mau, saya meng-iya-kan apa yang tim leader mau (namanya juga --senior--).
 * Pada akhirnya, library ini tidak digunakan. Hanya untuk pribadi.
 * Tobe Continue...,
 * 
 * Aturan.
 * 201609021553: agar tidak ambigu dalam penggunaannya,
 * semua file cutter-view, harus memiliki suffix *.cutter.php
 * 
 * @date		201608061555, 201608281400, 201608291945, 201609021729, 201610172024, 201611291546
 * @version		Version 1.2.1 (Tested with CI Version 3.1.0 - 3.1.3)
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Template View
 * @author		anovsiradj (Mayendra Costanov) <anov.siradj(22@(gmail|live).com|@gin.co.id)>
 * @link		https://github.com/anovsiradj/codeigniter-cutter/
 * @license		GPL-3.0 (Saya membuatnya bukan-atasnama perusahaan. jadi saya berhak menentukan legalitas kode-sumber ini).
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cutter
{
	protected $_current_layout = NULL;
	protected $_view_holder = array();
	protected $_data_holder = array();
	protected $_current_field = NULL;

	/**
	* Refference to CI sigleton
	* 
	* @return void
	*/
	function __construct()
	{
		$this->CI =& get_instance();
		$this->set_layout('layout');
	}

	/**
	* set_layout()
	* 
	* set current layout
	* 
	* @return $this
	*/
	public function set_layout($name)
	{
		$name .= '.cutter.php';
		$this->_current_layout = $name;
	}

	/**
	* Define view-field in layout and render field
	* 
	* @return void
	*/
	public function field($name)
	{
		// apa view data ada? lanjut
		if (isset($this->_view_holder[$name])) {
			// saya tidak suka pakai '' (string kosong), jadi saya pakai null
			// saya merasa tergan, kalau pakai ''.
			echo implode(NULL, $this->_view_holder[$name]);
		}
	}

	/**
	* view()
	* 
	* @return void | $this
	*/
	public function view($name, $data = array(), $render = TRUE)
	{
		// suffix. untuk identitas, kalau ini adalah view yg digunakan untuk cutter.
		// karena di CI ada filter jika '.' (titik) dianggap ekstensi
		// maka harus nulis nama view dengan lengkap
		$name .= '.cutter.php';

		// agar, data juga tersedia di layout
		$this->data($data);

		// hanya load view.
		$this->CI->load->view($name, $this->_data_holder, TRUE);

		// barang-kali, tidak ingin langsung tampilkan layout
		if ($render) $this->render();
	}

	/**
	* render()
	* 
	* hanya sekali eksekusi. tidak bisa berkali-kali
	* 
	* @return void
	*/
	public function render($data = array())
	{
		$this->data($data);
		$this->CI->load->view($this->_current_layout, $this->_data_holder);
	}


	/**
	* Start view-field output buffer
	* 
	* @return void
	*/
	public function start($name)
	{
		// kalau belum di akhiri, ya sudah
		if ($this->_current_field !== NULL) {
			throw new Exception('Error: Cutter::start() without closing current field.');
			return;
		}

		if (!isset($this->_view_holder[$name])) $this->_view_holder[$name] = array();

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
		// kalau tidak dimulai, ya sudah
		if ($this->_current_field === NULL) {
			throw new Exception('Error: Cutter::end() without start an field.');
			return;
		}

		$this->_view_holder[$this->_current_field][] = ob_get_clean();

		$this->_current_field = NULL;
	}

	/**
	* Data Manager
	* 
	* @return $this
	*/
	public function data()
	{
		$param = func_get_args();
		if (isset($param[0])) {
			if (is_array($param[0])) {
				foreach ($param[0] as $k => $v) {
					$this->_data_holder[$k] = $v;
				}

			} else {
				if (isset($param[1])) {
					$this->_data_holder[$param[0]] = $param[1];

				} else {
					if (isset($this->_data_holder[$param[0]])) {
						return $this->_data_holder[$param[0]];
					} else {
						return NULL;
					}
				}
			}

		} else {
			return $this->_data_holder;
		}
	}

}

/*

	CodeIgniter::Cutter Facade

*/

function cutter_field($name) {
	get_instance()->cutter->field($name);
}

function cutter_start($name) {
	get_instance()->cutter->start($name);
}

function cutter_end()
{
	get_instance()->cutter->end();
}
