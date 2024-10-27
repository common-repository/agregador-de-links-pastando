<?php

/*
Plugin Name: Agregador de Links Pastando 1.0
Plugin URI: http://wordpress.org/extend/plugins/agregador-de-links-pastando/
Description: O agregador de links funciona de maneira prática e rápida. Ao ativar o plugin, você vai poder enviar através da página de publicação de novos posts, seus links diretamente para o pastando.com.br. Que irá divulgar seus links aumentar suas visitas de maneira bem legais. Acesse http://www.pastando.com.br/plugin.php para saber mais
Version: 1.0
Author: Luiz Antonio Jr
Author URI: http://www.pastando.com.br
License: Pastando Rights GNU
*/

/*  Copyright Fev 2011 Luiz Antonio Jr  (email : jzin7@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//add_action('save_post', 'enviar_post');
add_action('publish_post', 'enviar_post');
add_action('admin_menu', 'my_post_options_box');
add_action('save_post', 'custom_add_save');

function enviar_post($post_ID){

update_custom_meta($post_ID, $_POST['pastando_postar'], 'pastando_postar');
update_custom_meta($post_ID, $_POST['pastando_categoria'], 'pastando_categoria');
update_custom_meta($post_ID, $_POST['pastando_titulo'], 'pastando_titulo');
update_custom_meta($post_ID, $_POST['pastando_miniatura'], 'pastando_miniatura');

$postar = get_post_meta($post_ID, "pastando_postar", true);
if ($postar == 'Sim') {		

$categoria = get_post_meta($post_ID, "pastando_categoria", true);
	switch (strtolower($categoria)) {
    case "curiosidades":
        $categoria_id = 1;
        break;
    case "tecnologia":
        $categoria_id = 3;
        break;
    case "automóveis":
        $categoria_id = 4;
        break;
    case "turismo":
        $categoria_id = 5;
        break;
    case "filmes":
        $categoria_id = 6;
        break;	
    case "jogos":
        $categoria_id = 7;
        break;	
    case "mulher":
        $categoria_id = 8;
        break;			
    case "nosso planeta":
        $categoria_id = 9;
        break;					
    case "adulto":
        $categoria_id = 10;
        break;
	default: 			
		$categoria_id = 0;
	}

query_posts( 'p='.$post_ID );
// the Loop
while (have_posts()) : the_post();
	$titulo_pastando = get_post_meta($post_ID, "pastando_titulo", true);
	$titulo = get_the_title();
	
	if (ltrim(rtrim($titulo_pastando)) == '') {
		$titulo_final = $titulo;
	} else
		$titulo_final = $titulo_pastando;
	$link = get_permalink();
	$thumbnail = get_post_meta($post_ID, "pastando_miniatura", true);

endwhile;

/*			$postdata = http_build_query(
				array(
					'titulo'	=> $titulo_final,
					'link'		=> $link,
					'miniatura'	=> $thumbnail,
					'categoria'	=> $categoria_id
				)
			);
			
			$opts = array('http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => $postdata
				)
			);

			$context  = stream_context_create($opts);
			file_get_contents('http://www.pastando.com.br/plugin_wordpress.php', false, $context);
*/

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://www.pastando.com.br/plugin_wordpress.php");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		
		$data = array(
				'titulo'	=> $titulo_final,
				'link'		=> $link,
				'miniatura'	=> $thumbnail,
				'categoria'	=> $categoria_id
		);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
			
	} // fim do postar = 'sim'
} //fim da função


function my_post_options_box() {
add_meta_box('post_info', 'Agregador de links Pastando.com.br', 'custom_post_info', 'post', 'side', 'high');
}


function custom_post_info() {
global $post;
?>
<style>

#mycustom-div{
background:url(http://pastando.com.br/pastando_plugin.jpg);
border:4px solid #030;
}
label
{
font-weight:bold;
font-size:14px;
}
</style>
<fieldset id="mycustom-div">
<div>
<p>


<script type="text/javascript"
 src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript">



$(document).ready(function() {
     
	//codigo para abrir ou não as opções
	var opcao = $('#pastando_postar').val();
	  if (opcao == 'Sim') { 
	$('#conteudo').show("slow");
	} else {
		$('#conteudo').hide("slow");
	}
	//abri ou nao
	
	//abre as opcoes ao mudar de sim para nao e vice versa se o usuario deseja postar.
$('#pastando_postar').change(function() {
	var opcao = $('#pastando_postar').val();
	if (opcao == 'Sim') { 
	$('#conteudo').show("slow");
	} else {
		$('#conteudo').hide("slow");
	}
});

  });
</script>
<?
$link = get_permalink($post->ID);
//checo se o link já existe no banco de dados e se ja enviou 3 no dia de hoje


/*			$postdata = http_build_query(
				array(
					'link_post'	=> $link,
				)
			);
			
			$opts = array('http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => $postdata
				)
			);

			$context  = stream_context_create($opts);
			$existe = file_get_contents('http://www.pastando.com.br/plugin_link_existe.php', false, $context);
*/
/*$file = fopen("http://www.pastando.com.br/plugin_link_existe.php?link_post=" . $link, "rb");
	$existe = fread($file, 8192);
	fclose($file);
*/

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.pastando.com.br/plugin_link_existe.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, true);

$data = array(
    'link_post' => $link
);

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$output = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
$existe= $output;

if ($existe == 1) {
echo 'Este link já existe no pastando.com.br';
} else
if	($existe == 2) {
echo 'Você já enviou 3 links hoje. Aguarde até amanha para postar novamente.';
} else {
?>

<label for="pastando_postar" >Enviar este link?</label><br />
<select name="pastando_postar" id="pastando_postar">
<option<?php selected( get_post_meta($post->ID, 'pastando_postar', true), 'Nao' ); ?>>Nao</option>
<option<?php selected( get_post_meta($post->ID, 'pastando_postar', true), 'Sim' ); ?>>Sim</option>
</select>

<br />
<br />
<div id="conteudo" style="display:none">
<label for="pastando_categoria" >Categoria:</label><br />
<select name="pastando_categoria" id="pastando_categoria">
<option<?php selected( get_post_meta($post->ID, 'pastando_categoria', true), 'Humor' ); ?>>Humor</option>
<option<?php selected( get_post_meta($post->ID, 'pastando_categoria', true), 'Curiosidades' ); ?>>Curiosidades</option>
<option<?php selected( get_post_meta($post->ID, 'pastando_categoria', true), 'Tecnologia' ); ?>>Tecnologia</option>
<option<?php selected( get_post_meta($post->ID, 'pastando_categoria', true), 'Automóveis' ); ?>>Automóveis</option>
<option<?php selected( get_post_meta($post->ID, 'pastando_categoria', true), 'Turismo' ); ?>>Turismo</option>
<option<?php selected( get_post_meta($post->ID, 'pastando_categoria', true), 'Filmes' ); ?>>Filmes</option>
<option<?php selected( get_post_meta($post->ID, 'pastando_categoria', true), 'Jogos' ); ?>>Jogos</option>
<option<?php selected( get_post_meta($post->ID, 'pastando_categoria', true), 'Mulher' ); ?>>Mulher</option>
<option<?php selected( get_post_meta($post->ID, 'pastando_categoria', true), 'Nosso Planeta' ); ?>>Nosso Planeta</option>
<option<?php selected( get_post_meta($post->ID, 'pastando_categoria', true), 'Adulto' ); ?>>Adulto</option>
</select>
<br />
<br />
<label for="pastando_miniatura">URL Miniatura (JPG ou PNG):</label><br />
<input type="text" name="pastando_miniatura" id="cpi_text_option" value="<?php echo get_post_meta($post->ID, 'pastando_miniatura', true); ?>">
<br />
<br />

<label for="pastando_titulo">Titulo:(Substitui o título do post)</label><br />
<input type="text" name="pastando_titulo" id="cpi_text_option" value="<?php echo get_post_meta($post->ID, 'pastando_titulo', true); ?>">
<br /><br />
</div>
</p>
</div>


<? } //fim do if para saber se o link ja foi postado. Se nao foi postado mostra o que está aki em cima ?>

</fieldset>

<?php
}

function custom_add_save($post_ID){
// called after a post or page is saved
if($parent_id = wp_is_post_revision($post_ID))
{
$post_ID = $parent_id;
}

if ($_POST['pastando_postar']) {
update_custom_meta($post_ID, $_POST['pastando_postar'], 'pastando_postar');
}

if ($_POST['pastando_categoria']) {
update_custom_meta($post_ID, $_POST['pastando_categoria'], 'pastando_categoria');
}

if ($_POST['pastando_titulo']) {
update_custom_meta($post_ID, $_POST['pastando_titulo'], 'pastando_titulo');
}

if ($_POST['pastando_miniatura']) {
update_custom_meta($post_ID, $_POST['pastando_miniatura'], 'pastando_miniatura');
}

}

function update_custom_meta($postID, $newvalue, $field_name) {
// To create new meta
if(!get_post_meta($postID, $field_name)){
add_post_meta($postID, $field_name, $newvalue);
}else{
// or to update existing meta
update_post_meta($postID, $field_name, $newvalue);
}
}
?>