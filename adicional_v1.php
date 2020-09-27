<?php
    //-----------------------------------------------------------------------------------
    //    PROGRAMA LOTERIA ADICIONALES y SORPRESAS
    //-----------------------------------------------------------------------------------
try{
    require('fpdf.php');
    require('functions.php');

    $f= new FPDF();
    $f->AddPage('PORTRAIT','A4'); //orientacion y tamanio

    class pdf extends FPDF
	{
		    public function Header()
		{	
    		// Select Arial bold 15
    		$this->SetFont('Arial','B',12);

            //Varibles a ingresar
            //$cartonesadicionales='adicionales o sorpresa';
            
            //$this->Cell(10,10,'Tipo de carton: '.$cartonesadicionales,0,0,'L');
    		// Line break
    		$this->Ln();
		    }
		    public function Footer()
			{
    		// Go to 1.5 cm from bottom
    		$this->SetY(-15);
    		// Select Arial italic 8
    		$this->SetFont('Arial','B',10);
            $this->SetFont('');
    		// Print centered page number
            //$this->Cell(80);
            //$this->Cell(5,5,'One soluciones informaticas ',0,0,'L');
            $this->Ln();
            //.$this->PageNo()
			}
			//Dejamos de ejemplo como concatenar el nro de pagina
	   
        }
              

        //PDF INICIALIZACION
        
        $f=new pdf('P','mm','A4');
        $f->AddPage('PORTRAIT','A4');
        $f->SetFont('Arial','B',10);
        $f->SetFont(''); 

       // $lista_originales=leer_originales();

        //Aca pedire que elija entre 3 o 4 rondas, y cantidad total de cartones
        $cantidad = $_POST['cantidad'];
        $ronda=1; //RONDAS

        $lista_cartones_adicionales=generar_lista_adicionales($cantidad);
       

        imprimir_lista_adicionales($cantidad,$ronda,$lista_cartones_adicionales,$f);

	$f->output();
	}
	//fin try
	catch (Exception $e) {
        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }
 ?>
