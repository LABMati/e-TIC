<?php
function param_filter($_dado, $tipo)
{
	if( isset($_dado) && !empty($_dado)){
		switch ($tipo) {
			case 'param':
				return $_dado;
				break;
			case 'str':
				$dado = filter_var($_dado, FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
				break;
			case 'int':
				$dado = filter_var( $_dado, FILTER_SANITIZE_NUMBER_INT);
				break;
			default:
	       		throw new Exception(" Bad Request;ERRO, parametros inválidos", 400);
				break;
		}
	}
	else throw new Exception(" Not Found;ERRO, PARAMETROS NÃO EXISTENTES", 404);
 
	return trim( strip_tags( $dado ));
}