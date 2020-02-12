<?php

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

        //echo $k;

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

        function generar_lista($cantidad_cartones){ //obtengo un lista, y cada elemento de la lista es un carton
            $lista=array();
            
            $carton_referencia=crear_carton();
            $lista[0]=$carton_referencia;

            $k=0;
            $aux=0;
            //$t=0;
                    while($k<$cantidad_cartones)
                    {
                        $carton_a_ingresar=crear_carton();

                        if(comparar_carton($lista,$carton_a_ingresar)){
                           // print_r($lista[$k]);
                           // echo " -- ";
                            //print_r($carton_a_ingresar);
                            $carton_a_ingresar=crear_carton();
                           $aux++;
                        }
                        else{
                            $lista[]=$carton_a_ingresar;
                            $k=$k+1;
                        }

                      //$t=$t+1;
                      
                    }
        //echo "ciclos ".$t;         
        echo $aux." Cantidad de coincidencias en carton";               
        return $lista;
        }

        function comparar_fila($carton_lista,$carton_a_ingresar,$fila){
            for($j=0;$j<5;$j++){
                if(($carton_lista[$fila][$j])<>($carton_a_ingresar[$fila][$j])){
                    
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
                           // echo " primero";
                           // print_r($carton_lista);
                           // echo " segundo";
                            //print_r($carton_a_ingresar);
                            //echo " fila igual";
                            return true;
                        } 

                    }      
            }

        return false;
        }
//input


$cant=3000;
$lista=generar_lista($cant);

?>