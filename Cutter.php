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
 * Tobe Continue...,
 * 
 * Aturan.
 * 201609021553: agar tidak ambigu dalam penggunaannya,
 * semua file cutter-view, harus memiliki suffix *.cutter.php
 * 
 * @date		201608061555, 201608281400, 201608291945, 201609021729, 201610172024
 * @version		Version 1.1 (CI Version 3.1.0)
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Template View
 * @author		anovsiradj (Mayendra Costanov) <anov.siradj(22@(gmail|live).com|@gin.co.id)>
 * @link		https://github/anovsiradj/codeigniter-cutter/
 * @license		MIT License (Aku membuatnya bukan atas nama perusahaan. jadi saya berhak menentukan legalitas kode-sumber ini).
 */

defined('BASEPATH') OR exit('No direct script access allowed');

// cutter facecade
function cutter($field_name)
{
	static $ci_cutter;
	if(!isset($ci_cutter)) $ci_cutter =& get_instance()->cutter;
	$ci_cutter->field($field_name);
}
function cutter_start($field_name)
{
	static $ci_cutter;
	if(!isset($ci_cutter)) $ci_cutter =& get_instance()->cutter;
	$ci_cutter->start($field_name);
}
function cutter_end()
{
	static $ci_cutter;
	if(!isset($ci_cutter)) $ci_cutter =& get_instance()->cutter;
	$ci_cutter->end();
}

class Cutter
{
	protected $_default_layout = 'layout';
	protected $_view_holder = [];
	protected $_data_holder = [];
	protected $_current_field = NULL;
	protected $_current_stack = NULL;

	/**
	* Refference to CI sigleton
	* 
	* @return void
	*/
	function __construct()
	{
		$this->CI =& get_instance();
		$this->set_layout($this->_default_layout);
	}

	/**
	* set_layout()
	* 
	* set current layout
	* 
	* @return $this
	*/
	public function set_layout($cutter_view)
	{
		$this->_default_layout = $cutter_view . '.cutter.php';
		return $this;
	}

	/**
	* Define view-field in layout and render field
	* 
	* @return void
	*/
	public function field($field_name)
	{
		// gak ada data? ya sudah
		if ( ! isset($this->_view_holder[$field_name])) return;

		// saya tidak suka pakai '' (string kosong), jadi saya pakai null
		// saya merasa tergan, kalau pakai ''.
		echo implode(NULL, $this->_view_holder[$field_name]);
	}

	/**
	* view()
	* 
	* @return void | $this
	*/
	public function view($cutter_view, $data = [], $render = TRUE)
	{
		// suffix. untuk identitas, kalau ini adalah view yg digunakan untuk cutter.
		// karena di CI ada filter jika '.' (titik) dianggap ekstensi
		// maka harus nulis nama view dengan lengkap
		$cutter_view .= '.cutter.php';

		// agar, data juga tersedia di layout
		$this->_data_holder = array_merge($this->_data_holder, $data);

		// hanya load view.
		$this->CI->load->view($cutter_view, $this->_data_holder, TRUE);

		// barang-kali, tidak ingin langsung tampilkan layout
		if ($render) {
			$this->render();
		} else {
			return $this;
		}
	}

	/**
	* setview()
	* 
	* manual tambah view biasa, ke field pada cutter
	* 
	* @return $this
	*/
	public function set_view($field_name, $cutter_view, $data = [])
	{
		$cutter_view .= '.cutter.php';
		$this->_data_holder = array_merge($this->_data_holder, $data);
		$this->_view_holder[$field_name][] =& $this->CI->load->view($cutter_view, $this->_data_holder, TRUE);

		return $this;
	}

	/**
	* setecho()
	* 
	* tambah content biasa ke field
	* 
	* @return $this
	*/
	public function set_echo($field_name, $data_echo = NULL) {
		// barangkali bukan string
		if (is_string($data_echo)) {
			$this->_view_holder[$field_name][] = $data_echo;
		}
		return $this;
	}

	/**
	* render()
	* 
	* hanya sekali eksekusi. tidak bisa berkali-kali
	* 
	* @return void
	*/
	public function render()
	{
		// render, hanya bisa sekali
		static $is_already_rendered = false;
		if ($is_already_rendered) return;
		$is_already_rendered = true;

		$this->CI->load->view($this->_default_layout, $this->_data_holder);
	}

	/**
	* Begin view-field output buffer
	* 
	* @return void
	*/
	public function start($field_name, $stack = 'next')
	{
		// kalau belum di akhiri, ya sudah
		if ($this->_current_field !== NULL) return;

		$this->_current_field = $field_name;
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
	* Set data all cutter view
	* 
	* @return $this
	*/
	public function set_data($key, $value)
	{
		$this->_data_holder[$key] = $value;
		return $this;
	}

}
