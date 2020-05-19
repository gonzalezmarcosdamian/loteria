<?php

    //Archivo de funciones

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

            //variable auxiliar para recorrer 
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

        //funcion dentro de la funcion crear carton
        function esta($arreglo,$aleatorio){
            for($i=0;$i<count($arreglo);$i++){
                if($arreglo[$i]==$aleatorio){
                    return true;
                }
            }
        return false;
        }
        //COMPARACIONES 

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

        
        //genero la lista completa        

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

        function generar_lista_adicionales($cantidad_cartones){ //obtengo un lista, y cada elemento de la lista es un carton
            $lista=array();
            $lista[0]=0;
            $carton_a_ingresar=array();
            $k=0;

            $originales=leer_originales();
                   
                while($k<$cantidad_cartones)
                    {
                        $carton_a_ingresar=crear_carton();

                        if((comparar_carton($lista,$carton_a_ingresar)) && (comparar_carton($originales,$carton_a_ingresar))){
                            $carton_a_ingresar=crear_carton();
                        }
                        else{
                            $lista[$k]=$carton_a_ingresar;
                            $k=$k+1;
                        }                        
                    }          
                        
        return $lista;
        }
        

        //IMPRESIONES

        function imprimir_x_veces($rondas,$cartonimprimir,$f){ 
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

                imprimir_x_veces($rondas1,$lista[$k],$f);
            }
            //$f->AddPage('PORTRAIT','A4');
            $f->output();
            }

        //impresion adicionales
        function imprimir_x_veces_adicionales($rondas,$cartonimprimir,$f){ 
            //imprime el mismo carton segun las rondas
            for($k=1;$k<$rondas+1;$k++){
                //$f->Cell(20); 
                
                $f->SetFont('Arial','B',10); 
                 $f->SetFont(''); 
                //$f->Cell(9,9,'Carton '.$tipo.' Nro: '.$k,0,0,'L');
                $f->ln();
                $cartonimprimir=crear_carton();
                for($i=0;$i<3;$i++){                
                    for($j=0;$j<5;$j++){
                        $aux1=$cartonimprimir[$i][$j];
                        $f->SetFont('Arial','B',12);
                        $f->SetFont(''); 
                        $f->Cell(10,10,$aux1.' ',1,0,'C');
                        } 
                    $f->ln();                   
                    }  
                $f->ln();   
            }
            $f->ln(0.1);
            //$f->AddPage('PORTRAIT','A4');
            }
            
            function imprimir_lista_adicionales($cantidad,$rondas1,$lista,$f){ //imprime la lista, llamando a imprimirxveces, cada elemento de la lista es un carton
            $f->SetFont('Arial','B',12);
            $f->SetFont(''); 
            for($k=0;$k<$cantidad;$k++){

               $fecha=$_POST['Fecha'];
               $hora=$_POST['Hora']; 
               $f->Cell(9,9,'Fecha: '.$fecha.' - Hora: '.$hora.' hs',0,0,'L');
               $f->ln(4);
               $tipo = $_POST['tipo'];
               $costo = $_POST['costo'];
               $f->Cell(9,9,'Carton '.$tipo.' Nro: '.($k+1).' - Costo: '.$costo,0,0,'L');
               //$f->Cell(5,5,'CARTONES ADICIONAL NRO: '.($k+1));
               //$f->ln(2);
               imprimir_x_veces_adicionales($rondas1,$lista[$k],$f);
               if(($k+1)%5==0){
                    $f->AddPage('PORTRAIT','A4');
               }
            }
            //$f->AddPage('PORTRAIT','A4');
            $f->output();
            }

        //imprimir en txt
        function imprimir_lista_txt($lista_cartones){

            $existe = file_exists("txt/originales.txt");
            if ($existe)    
              {  
                  unlink("txt/originales.txt");
              }
                                    
            $file = fopen("txt/originales.txt", "w");
           
            for($h=0;$h<count($lista_cartones);$h++){
                fwrite($file, "Carton". ($h+1));
                $carton=$lista_cartones[$h];
                for($i=0;$i<3;$i++){       
                    
                        for($j=0;$j<5;$j++){
                            fwrite($file, " ".$carton[$i][$j]);
                        } 
                       
                }
                fwrite($file,  PHP_EOL);
            }    
        fclose($file);
        }

              
       function leer_originales(){

          $lista_originales=array();
          $k=0;
          if (($handle = fopen("txt/originales.txt", "r")) !== FALSE) {
            
            while (($data = fgetcsv($handle, 0," ")) !== FALSE) {
                
                $carton_original[0][0]=0;
                $aux=1;
                
                for($i=0;$i<3;$i++) {
                    for($j=0;$j<5;$j++) {  
                        $carton_original[$i][$j] = $data[$aux];               
                        $aux++;
                        }
                    }
                    $lista_originales[$k]=$carton_original;  
                    $k++;
                
              }
            fclose($handle);
            } 
          return $lista_originales; 
          } 
        
        


?>