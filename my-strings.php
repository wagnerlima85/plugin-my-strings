<?php 
/*
	Plugin Name: MyStrings
	Plugin URI: https://github.com/wagnerlima85/plugin-my-strings/
	Description: Plugin para Wordpress com ênfase no input de um campo de texto para avaliação.
	Author: Wagner Lima
	Author URI: https://br.linkedin.com/in/wagnerlima
	Version: 0.1
	Text Domain: my-strings
	License: GPL v2 ou superior
*/

$return_message = '';
$strings_for_my_strings = array(
	'message'  => '<textarea name="ms_message" id="ms_message" class="form-control" rows="3" placeholder="Informe sua mensagem"></textarea>'
);

//Configurando o plugin
function my_strings_active() {
	global $wpdb;

	$charset_collate = $wpdb-> get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS wp_mystrings (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		message text NOT NULL,
		created_by varchar(255) NOT NULL,
		created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once(ABSPATH. 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
}

register_activation_hook( __FILE__, 'my_strings_active' );

//Adicionando o plugin ao menu
function add_my_strings_menu(){
	add_menu_page( 'Incluindo mensagem em MyString', 'MyString', 'manage_options', 'my-strings', 'show_form','dashicons-edit', '5');
}

//Criando formulário inincialmente apenas com o campo de mensagem
function show_form(){

	global $strings_for_my_strings, $return_message;

	$message = isset($_POST['ms_message']) ? stripslashes(trim($_POST['ms_message'])) : '';
	
	if(isset($_POST['submit'])){
		if(empty($message)){
			$return_message ='<div class="alert alert-danger" role="alert">O campo de mensagem precisa ser preenchido com pelo menos 10 caracteres.</div>';
		}else{
			insert_my_strings($message);
			$return_message ='<div class="alert alert-success" role="alert">Mensagem adicionada com sucesso.</div>';
		}
	} 
	
	$url_styles = '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">';
	$my_strings_form = '
	<div class="container">
		<div class="py-5">
			<h4>Bem-vindo ao MyString</h4>
			<p class="lead">Aqui você enviará a mensagem que será exibida exclusivamente um endpont desenvolvido para este teste.</p>
		</div>
		<div class="row">
			<div class="col-md-12">
				<form action="" method="POST">
					<div class="form-group">
						<label for="ms_message">Messagem</label>
						'. $strings_for_my_strings['message'] .'
						'. $return_message .'
					</div>
					<button name="submit" type="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
		</div>
	</div>
		'. $url_styles;
	
	echo $my_strings_form;
}

add_action('admin_menu', 'add_my_strings_menu');

//Adicionando mensagem e rastreando
function insert_my_strings($msg) {
	global $wpdb;

	$curr_user = wp_get_current_user();
	$email= $curr_user->user_email;
	$cur_date = date("Y-m-d H:i:s");
	$wpdb-> query("INSERT INTO wp_mystrings 
			(message, created_by, created_at) 
			VALUES 
			('$msg', '$email', '$cur_date')");
}