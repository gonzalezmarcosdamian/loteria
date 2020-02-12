<?php
    //-----------------------------------------------------------------------------------
    //    PROGRAMA LOTERIA
    //-----------------------------------------------------------------------------------
try{
    require('fpdf.php');
    $f= new FPDF();
    $f->AddPage('PORTRAIT','A4'); //orientacion y tamanio

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
            $this->Cell(10,10,'Costo del carton: '.$costo,0,0,'L');
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
            $this->Ln(5);  

            $this->SetFont('Arial','B',8);
            $this->SetFont('');
            $this->Cell(5,5,'NOTA: Se entrega el premio no su valor',0,0,'L');
            $this->Ln(3);
            $this->Cell(5,5,'no somos responsables por perdida de carton.',0,0,'L');
            $this->Ln(5);

            $this->Cell(45,7,'CUOTA 1 / CUOTA 2 / CUOTA 3',1,1,'L');

            //$this->Cell(15,5,'CUOTA 2',1,1,'L');

           // $this->Cell(15,5,'CUOTA 3',1,1,'L');
            
            //.$this->PageNo()
            }
            //Dejamos de ejemplo como concatenar el nro de pagina
       
        }

        function crear_carton(){
            //inicializacion
            $arreglo=array();
            $arreglo[0]=0;
            $aleatorio=0;
    
            $carton[0][0]=0;
    
            for($k=0;$k<9;$k++){
                $aleatorio=random_int(($k*10)+1,($k+1)*10);
                $arreglo[$k]=$aleatorio;
            }
            
            while($k<15){
                $aleatorio=random_int(1,90);
                if(esta($arreglo,$aleatorio)){
                    $aleatorio=random_int(1,90);
                }
                else{
                    $arreglo[$k]=$aleatorio;
                    $k++;
                }   
            } 
    
            sort($arreglo);
    
            //print_r($arreglo);
            //lleno el carton
            
            $o=0;

                for($i=0;$i<3;$i++){                
                    for($j=0;$j<5;$j++){
                        
                        $carton[$i][$j]=$arreglo[$o];
                        $o++;
                        }                      
                    }   
          
            //retorna el carton
            return $carton;
            }
    
            function esta($arreglo,$aleatorio){
                for($i=0;$i<count($arreglo);$i++){
                    if($arreglo[$i]==$aleatorio){
                        return true;
                    }
                }
            return false;
            }
            function imprimirxveces($rondas,$cartonimprimir,$f){ 
            //imprime el mismo carton segun las rondas
            
                for($k=1;$k<$rondas+1;$k++){
                    //$f->Cell(20); 
                    $f->SetFont('Arial','B',10);  
                    $f->SetFont('');
                    $f->Cell(9,9,'Ronda '.$k,0,0,'L');
                    $f->ln();
                    for($i=0;$i<3;$i++){                
                        for($j=0;$j<5;$j++){
                            $aux1=$cartonimprimir[$i][$j];
                            $f->SetFont('Arial','B',12);
                            $f->SetFont('');
                            $f->Cell(11,11,$aux1.' ',1,0,'C');
                            } 
                        $f->ln();                   
                        }  
                    //$f->ln();   
                }
            $f->ln();
            $f->AddPage('PORTRAIT','A4');
            }
            
            function imprimir_lista($cantidad,$rondas1,$lista,$f){ //imprime la lista, llamando a imprimirxveces, cada elemento de la lista es un carton
            $f->SetFont('Arial','B',12);
            $f->SetFont('');
            //$f->AddPage('PORTRAIT','A4');
            for($k=0;$k<$cantidad;$k++){ 
               //$f->Cell(9); 
               $f->Cell(5,5,'CARTON NRO: '.($k+1));
               $f->ln(2);
               imprimirxveces($rondas1,$lista[$k],$f);
            }
            //$f->AddPage('PORTRAIT','A4');
            $f->output();
            }

            function generar_lista($cantidad_cartones){ //obtengo un lista, y cada elemento de la lista es un carton
                $lista=array();
                $lista[0]=0;
                $carton_a_ingresar=array();
                $k=0;
    
                        while($k<$cantidad_cartones)
                        {
                            $carton_a_ingresar=crear_carton();
    
                            if(comparar_carton($lista,$carton_a_ingresar)){
                                $carton_a_ingresar=crear_carton();
                            }
                            else{
                                $lista[$k]=$carton_a_ingresar;
                                $k=$k+1;
                            }
                            
                        }
                    
                            
            return $lista;
            }

            function comparar_fila($carton_lista,$carton_a_ingresar,$fila){

                for($j=0;$j<5;$j++){
                    if($carton_lista[$fila][$j]<>$carton_a_ingresar[$fila][$j]){
                      return false;
                    } 
                }
            return true;
            }
            
            function comparar_carton($lista,$carton_a_ingresar){
            
                for($i=0;$i<count($lista);$i++){            
                    $carton_lista=$lista[$i];
                        
                        for($j=0;$j<3;$j++){
    
                            if(comparar_fila($carton_lista,$carton_a_ingresar,$j)){
                                return true;
                            } 
    
                        }      
                }
    
            return false;
            }
    
        
        $f=new pdf('P','mm','A4');
        $f->AddPage('PORTRAIT','A4');
        $f->SetFont('Arial','B',10);

        
        $cantidaddecartones = $_POST['cantidaddecartones'];  
        //Aca pedire que elija entre 3 o 4 rondas, y cantidad total de cartones
        $ronda=4; //RONDAS
        $cant=$cantidaddecartones;

        $listaget=generar_lista($cant);
        imprimir_lista($cant,$ronda,$listaget,$f);
        

    //$f->output();
    }
    //fin try
    catch (Exception $e) {
        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }
 ?>