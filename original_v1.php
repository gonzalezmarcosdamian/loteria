<?php
    //-----------------------------------------------------------------------------------
    //    PROGRAMA LOTERIA
    //-----------------------------------------------------------------------------------
try{
    require('fpdf.php');
    require('functions.php');

    class pdf extends FPDF
    {
            public function Header()
            {   
            // Select Arial bold 15
            $this->SetFont('Arial','B',12);
            $this->SetFont('');
            
            $Organiza = $_POST['Organiza'];
            $Lugar = $_POST['Lugar'];
            $Fecha = $_POST['Fecha'];
            $Hora = $_POST['Hora'];
            $costo=$_POST['costocarton'];
            //Varibles a ingresar
            $org=$Organiza;
            $lug=$Lugar;
            $fe=$Fecha;
            $ho=$Hora;
            // Move to the right
            //$this->Cell(80);
            // Framed title
            $this->Cell(11,11,'Organiza: '.$org,0,0,'L');
            $this->Ln(5);
            $this->Cell(10,10,'Fecha: '.$fe.'   Hora: '.$ho.' hs',0,0,'L');
            $this->Ln(5);
            $this->Cell(10,10,'Lugar: '.$lug,0,0,'L');
            $this->Ln(5);
            $this->Cell(10,10,'Costo del carton: $ '.$costo,0,0,'L');
            // Line break
            $this->Ln(10);
            }
            
            public function Footer()
            {
            // Go to 1.5 cm from bottom
            $this->SetY(-85);
            // Select Arial italic 8
            $this->SetFont('Arial','B',10);
            $this->SetFont('');
            // Print centered page number
            //$this->Cell(80);
            $liniauno = $_POST['liniauno'];
            $llenouno = $_POST['llenouno'];
            
            $this->Cell(5,5,'PREMIOS: RONDA 1',0,0,'L');
            $this->Ln();
            $this->Cell(5,5,'QUINTINA: '.$liniauno,0,0,'L');
            $this->Ln();
            $this->Cell(5,5,'CARTON LLENO: '.$llenouno,0,0,'L');  
            $this->Ln(7);  

            $liniados = $_POST['liniados'];
            $llenodos = $_POST['llenodos'];

            $this->Cell(5,5,'PREMIOS: RONDA 2',0,0,'L');
            $this->Ln();
            $this->Cell(5,5,'QUINTINA: '.$liniados,0,0,'L');
            $this->Ln();
            $this->Cell(5,5,'CARTON LLENO: '.$llenodos,0,0,'L');  
            $this->Ln(7);  

            $liniatres = $_POST['liniatres'];
            $llenotres = $_POST['llenotres'];

            $this->Cell(5,5,'PREMIOS: RONDA 3',0,0,'L');
            $this->Ln();
            $this->Cell(5,5,'QUINTINA: '.$liniatres,0,0,'L');
            $this->Ln();
            $this->Cell(5,5,'CARTON LLENO: '.$llenotres,0,0,'L');  
            $this->Ln(7);

            $liniacuatro = $_POST['liniacuatro'];
            $llenocuatro = $_POST['llenocuatro'];  

            $this->Cell(5,5,'PREMIOS: RONDA 4',0,0,'L');
            $this->Ln();
            $this->Cell(5,5,'QUINTINA: '.$liniacuatro,0,0,'L');
            $this->Ln();
            $this->Cell(5,5,'CARTON LLENO: '.$llenocuatro,0,0,'L');  
            $this->Ln(6);  

            $this->SetFont('Arial','B',11);    

            $this->Cell(5,5,'NOTA: Se entrega el premio no su valor',0,0,'L');
            $this->Ln(3);
            $this->Cell(5,5,'no somos responsables por perdida de carton.',0,0,'L');
            $this->Ln(5);

            //$this->Cell(45,7,'CUOTA 1 / CUOTA 2 / CUOTA 3',1,1,'L'); 

            //$this->Cell(15,5,'CUOTA 2',1,1,'L');

           // $this->Cell(15,5,'CUOTA 3',1,1,'L');
            
            //.$this->PageNo()
            }
            //Dejamos de ejemplo como concatenar el nro de pagina
       
        }
            
        //inicializo el pdf  
        $f=new pdf('P','mm','A4');
        $f->AddPage('PORTRAIT','A4');
        $f->SetFont('Arial','B',10);
        
        $cantidad_de_cartones = $_POST['cantidaddecartones'];  
        
        $ronda=4; //RONDAS        

        $lista_originales=generar_lista($cantidad_de_cartones);

        imprimir_lista_txt($lista_originales);

        imprimir_lista($cantidad_de_cartones,$ronda,$lista_originales, $f);
        

    //$f->output();
    }
    //fin try
    catch (Exception $e) {
        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }
 ?>