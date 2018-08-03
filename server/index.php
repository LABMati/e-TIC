<?php
    session_start();

    if (isset($_SESSION['idinscrito'])) {
		$idinscrito   = $_SESSION['idinscrito'];
		$nome         = $_SESSION['nome'];
        $email        = $_SESSION['email'];        
    } else {
        unset($_SESSION['id'], $_SESSION['nome'], $_SESSION['email']);
        header("Location:http://www.etic.ifc-camboriu.edu.br/evento/login.php");
    }


 


 include("mpdf60/mpdf.php");



setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
$date=date_default_timezone_set('America/Sao_Paulo');
//$tempo= strftime('%A, %d de %B de %Y', strtotime('today'));
$tempo=(strftime('%A, %d de %B de %Y', strtotime('today'))) ;

require_once '../evento/aux/funcoes.php';
require_once '../evento/aux/conexao.php';


// $sql ="select idevento, (hora_fim-hora_inicio)/10000 as horas from inscrito as i
//         inner join registro_entrada re       on re.user = i.idinscrito          
//         inner join evento e on e.idevento = event   where i.idinscrito = ".$idinscrito;


$sql ="select idevento, timestampdiff(minute,hora_inicio,hora_fim) as horas from inscrito as i
        inner join registro_entrada re       on re.user = i.idinscrito          
        inner join evento e on e.idevento = event   where i.idinscrito = ".$idinscrito;

$eventos = consulta('', '', '',$sql);
$sql2 ="select certificado from inscrito where idinscrito = $idinscrito";
$busca = consulta('', '', '',$sql2);

foreach ($busca as $key) {
	$certificado=$key["certificado"];
}


if($certificado>=8603 and $certificado<=8703){
	$folha="170A";
	
}
if($certificado>=8704 and $certificado<=8804){
	$folha="170B";
}
if($certificado>=8805 and $certificado<=8905){
	$folha="171A";
}
if($certificado>=8906 and $certificado<=9006){
	$folha="171B";
}
if($certificado>=9007 and $certificado<=9107){
	$folha="172A";
}
if($certificado>=9108 and $certificado<=9208){
	$folha="172B";
}
if($certificado>=9209 and $certificado<=9309){
	$folha="173A";
}
if($certificado>=9310 and $certificado<=9410){
	$folha="173B";
}
if($certificado>=9411 and $certificado<9511){
	$folha="174A";
}
if($certificado>=9512 and $certificado<9612){
	$folha="174B";
}
if($certificado>=9613 and $certificado<9713){
	$folha="175A";
}
if($certificado>=9714 and $certificado<9802){
	$folha="175B";
}






$soma = 0;

$vetor = array();

foreach($eventos as $evento)
{
	if (in_array($evento, $vetor)){
    	
	}else{
		array_push($vetor, $evento);
		$soma+=$evento[horas]; 	
	}

    echo $evento[idevento]." - ".$evento[horas]."<br>";
   
}

$horasint = ($soma / 60);
$horasint = intval($horasint);
$minutos = ($soma % 60);

if($minutos > 0){
	$horasint++;
}


$html="

	<page size='A4'>
	<head>
	 <meta charset='UTF-8'>
	</head>
	<body style='background-image: url(images/fundo.png);'>
	<div>
		<img src='images/p1.png' width='30%' >
		<img src='images/logo.png' width='30%' >
		<img src='images/p2.png' width='30%'>
	</div><br>
	<div  align='center'>
		<img src='images/logo2.png' width='35%'	>
	</div><br>
	<div  align='center'>
		<img src='images/u172-4.png' width='30%'>
	</div>
	<div>
	Certificamos que <i style='text-transform: capitalize;font-weight:bold;'>$nome</i> participou do VIII Encontro de Tecnologia e Informação do Instituto Federal Catarinense – Campus Camboriú (VIII e-TIC), realizado no período de 19 a 22 de setembro de 2017, totalizando $horasint horas. 

<br><br><br><br>
<div style='text-align: right;margin-right: 10px;'>
  CAMBORIÚ, $tempo

 </div>
 <div style='text-align: left;margin-top: 5%;backgroun-color:tranparent'>
  <img src='images/assinatura_spider.png' width='35%'><br>
 Registro $certificado/2018 - fl $folha - Coordenação de Estágio e Extensão – Instituto Federal Catarinense – Campus  Camboriú
 </div>
 
 </body>
</page>
";



$sql ="select e.descricao from inscrito i
                inner join registro_entrada re
                         on re.user = i.idinscrito
                inner join evento e
                        on e.idevento = event
                         where i.idinscrito = $idinscrito
                group by event
";

$eventos = consulta('', '', '',$sql);


foreach($eventos as $evento)
        $txt .=   strip_tags($evento[data]." ".$evento[descricao])."<br><hr>";


$html2="
<page>

	<body  style='text-align:center;font-weight:normal;font-size:20px;'>
		
			<h1>COMPROVANTE DE PARTICIPAÇÃO</h1>
			<br>
			 $txt
        		
                                      
		
	</body>
</page>";

$mpdf=new mPDF('UTF-8','A4-L'); 
$mpdf->showImageErrors = true;


 $mpdf->SetDisplayMode('fullpage');
 //$css = file_get_contents("css/estilo.css");
 // $mpdf->WriteHTML($css,1);
 $mpdf->WriteHTML($html);
 $mpdf->AddPage($html2);
  $mpdf->WriteHTML($html2);

//  $mpdf->WriteHTML($ak); ak vai oq vc quer na outra pagina

 $mpdf->Output();
 $mpdf->exit();
 exit;

  // <div>id=$idinscrito Certifico que <b>$nome</b> participou do evento de extensão VIII E-TIC - ENCONTRO DE TECNOLOGIA E INFORMAÇÃO DO INSTITUTO FEDERAL CATARINENSE - CÂMPUS CAMBORIÚ, com carga horária de 24 hora(s), coordenado pelo Professor ALEXANDRE DE AGUIAR AMARAL, com $soma  hora(s) de atividades desenvolvidas. A atividade foi realizada no período de 19 a 22 de Setembro de 2017.</div>