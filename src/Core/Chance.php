<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Utils\Encoding;
use RBFrameworks\Core\Types\File;

//v3 - 06/01/2020
//v4 - 01/04/2024

class Chance {
    
    /**
     * Retorna nome completo de uma pessoa
     * @param string $sexo = [random, masc ou fem]
     * @param string $prefix = Uma string prefixando o nome, ou use "gender" para retornar [M] ou [F] de acordo
     * @return string
     */
    public static function nome(string $sexo = 'random', string $prefix = '') {
        if(empty($prefix)) {
            $prefix = self::porcent(5) ? self::pickOne(['Dr. ', 'Dra. ', 'Sr. ', 'Sra. ', 'Prof. ', 'Profa. ', 'Eng. ']) : '';
        }

        //Define $gender [names_masc, names_fem]
        switch($sexo) {
            case 'masc':
                $gender = 'names_masc';
                $sexo = '[M]';
            break;
            case 'fem':
                $gender = 'names_fem';
                $sexo = '[F]';
            break;
            default:
                $gender = self::prob('names_masc', 50, 'names_fem');
                $sexo = ($gender == 'names_masc') ? '[M]' : '[F]';
            break;
        }

        //Randomize $gender
        $gender = self::file($gender);
        $middle = (self::porcent(50)) ? self::file('names_middle') : '';
        $lastname = (self::porcent(5)) ? '' : self::file('last_names');
        $prefix = ($prefix == 'gender') ? $sexo : $prefix;

        //TrimAll
        $prefix = trim($prefix);
        $gender = trim($gender);
        $middle = trim($middle);
        $middle = (empty($middle)) ? ' ' : ' '.$middle.' ';
        $lastname = trim($lastname);
        $return = trim("{$prefix}{$gender}{$middle}{$lastname}");
        return $return;
    }
    
    public static function user() {
        $nome = self::nome('random', 'gender');
        return array(
            'nome' => substr($nome, 3),
            'sexo' => substr($nome, 1, 1),
            'idade' => self::age(17, 23, 62),
            'nacionalidade' => self::file('Paises'),
            'endereco' => (new Http('http://midiacriativa.com/cep/busca-json.php?cep=generate'))->getJsonResponse(),
        );
    }
    
    /**
     * 
     * @param int $min Idade mínima dos users
     * @param int $med Média da idade ou idade do publico alvo
     * @param int $max Idade máxima dos users
     * @param int $seed Variação média de idade do publico
     * @return int
     */
    public static function age(int $min = 0, int $med = 18, int $max = 99, int $seed = 10) {
        $age = rand($med-$seed, $med+$seed);
        if($age < $min) $age = $min;
        if($age > $max) $age = $max;
        if(self::porcent(50)) {
            $spectrum = $age - $min;
            $age = (self::porcent(35)) ? $age - rand(0, $spectrum) : $age;
        } else {
            $spectrum = $max - $age;
            $age = (self::porcent(35)) ? $age + rand(0, $spectrum) : $age;
        }
        return (int) $age;
    }
      
    //Chance::porcent(50) retornará true em 50% dos casos
    //Chance::porcent(12) retornará true em 12% dos casos
    public static function porcent($porcent):bool {
        return (rand(0,100) < $porcent) ? true : false;
    }
    
    /**
     * file function retorna um valor aleatório de um arquivo
     * @param string $file que pode ser um dos arquivos da pasta Chance, ou fornecer o caminho completo
     * @return string
     */
    public static function file($name) {
        $filepath = __DIR__.'/Chance/'.$name;
        if(!file_exists($filepath)) {
            $filepath = new File($name);
            if(!$filepath->hasFile()) throw new \Exception("Chance::file($name) - Arquivo não encontrado");
            $filepath = $filepath->getFilePath();
        }
        $file = file($filepath);
        $result = trim($file[array_rand($file)]);
        return Encoding::encode('utf-8', $result);
    }

    /**
     * cpf function retorna um CPF válido
     *
     * @param boolean $formated true para retorna formatado, false para retornar apenas os números. Padrão é true (formatado)
     * @example Chance::cpf(); //Retorna um CPF formatado (XXX.XXX.XXX-XX) or Chance::cpf(false);
     * @return void
     */
    public static function cpf(bool $formated = true) {
        $n = 9;
        $n1 = rand(0, $n);
        $n2 = rand(0, $n);
        $n3 = rand(0, $n);
        $n4 = rand(0, $n);
        $n5 = rand(0, $n);
        $n6 = rand(0, $n);
        $n7 = rand(0, $n);
        $n8 = rand(0, $n);
        $n9 = rand(0, $n);
        $d1 = $n9*2+$n8*3+$n7*4+$n6*5+$n5*6+$n4*7+$n3*8+$n2*9+$n1*10;
        $d1 = 11 - ($d1 % 11);
        if($d1 >= 10) $d1 = 0;
        $d2 = $d1*2+$n9*3+$n8*4+$n7*5+$n6*6+$n5*7+$n4*8+$n3*9+$n2*10+$n1*11;
        $d2 = 11 - ($d2 % 11);
        if($d2 >= 10) $d2 = 0;
        $strFormated = $n1.$n2.$n3.'.'.$n4.$n5.$n6.'.'.$n7.$n8.$n9.'-'.$d1.$d2;
        if($formated) {
            return $strFormated;
        } else {
            return $n1.$n2.$n3.$n4.$n5.$n6.$n7.$n8.$n9.$d1.$d2;
        }
    }

    /**
     * cnpj function retorna um CNPJ válido
     *
     * @param boolean $formated true para retornar formatado, false para retornar apenas os números. Padrão é true (formatado)
     * @example Chance::cnpj(); //Retorna um CNPJ formatado (XX.XXX.XXX/0001-XX) or Chance::cnpj(false);
     * @return string|integer
     */
    public static function cnpj(bool $formated = true) {
        $n = 9;
        $n1 = rand(0, $n);
        $n2 = rand(0, $n);
        $n3 = rand(0, $n);
        $n4 = rand(0, $n);
        $n5 = rand(0, $n);
        $n6 = rand(0, $n);
        $n7 = rand(0, $n);
        $n8 = rand(0, $n);
        $n9 = 0;
        $n10 = 0;
        $n11 = 0;
        $n12 = 1;
        $d1 = $n12*2+$n11*3+$n10*4+$n9*5+$n8*6+$n7*7+$n6*8+$n5*9+$n4*2+$n3*3+$n2*4+$n1*5;
        $d1 = 11 - ($d1 % 11);
        if($d1 >= 10) $d1 = 0;
        $d2 = $d1*2+$n12*3+$n11*4+$n10*5+$n9*6+$n8*7+$n7*8+$n6*9+$n5*2+$n4*3+$n3*4+$n2*5+$n1*6;
        $d2 = 11 - ($d2 % 11);
        if($d2 >= 10) $d2 = 0;
        $strFormated = $n1.$n2.'.'.$n3.$n4.$n5.'.'.$n6.$n7.$n8.'/0001-'.$n9.$n10.$n11.$n12.$d1.$d2;
        if($formated) {
            return $strFormated;
        } else {
            return $n1.$n2.$n3.$n4.$n5.$n6.$n7.$n8.'0001'.$n9.$n10.$n11.$n12.$d1.$d2;
        }
    }

    public static function pickOne($array) {
        return $array[array_rand($array)];
    }
	
    public static function discurso() {
        return self::file('discurso_a').' '.self::file('discurso_b').' '.self::file('discurso_c').' '.self::file('discurso_d');
    }

	/**
	 *	Method: "prop" add in: 22/01/2019
	 *	Chance::prob("resultado when true", 40, "resultado quando false); //Retorna a string1 em 40% dos casos, e a segunda string em 60%
	 *	Chance::prob("Infectado!", 30); //Retorna "Infectado!" em 30% dos casos, ou false em 70%;
	 *	Chance::prob(25); //Equivale a porcent(25)
	 *	Chance::prob("texto"); //Equivale a prob("texto", 50);
	*/
	public static function prob($param1, $param2 = null, $param3 = null) {
		if(is_numeric($param1) and is_null($param2) and is_null($param3)) {
			return self::porcent($param1);
		} else
		if(is_string($param1) and is_null($param2) and is_null($param3)) {
			return self::prob($param1, 50);
		} else
		if(is_string($param1) and is_numeric($param2) and is_null($param3)) {
			return (rand(0,100) < $param2) ? $param1 : false;
		} else
		if(is_string($param1) and is_numeric($param2) and is_string($param3)) {
			return (rand(0,100) < $param2) ? $param1 : $param3;
        } else
        if(is_callable($param1) and is_numeric($param3) and is_callable($param3)) {
            return (rand(0,100) < $param2) ? $param1(): $param3();
		} else {
			throw new \Exception("::prob fail");
		}
	}
	
	/**
		Method: increment add in: 22/01/2019
		Chance::increment("idade"); //Cada vez que a função é chamada, ela continua com o último valor gerado.
		Chance::increment("Paises"); //Caso a string seja um file válido, então continua da lista daquele arquivo.
	*/
	public static function increment($seed, $increment = 1) {
		
		//Definir Arquivos
		$seed_file = "log/class.Chance.seed.".$seed;
		$seed_internal = __DIR__."/class.Chance/".$seed;
		
		//Garantir que o arquivo de controle existe
		if(!file_exists($seed_file)) {
			file_put_contents($seed_file, "0");
		}
		$seed_last = file_get_contents($seed_file);
		$seed_next = floatval($seed_last) + $increment;
		file_put_contents($seed_file, $seed_next);
		
		if(file_exists($seed_internal)) {
			$list = file($seed_internal);
			if($seed_last > count($list)) {
				rename($seed_file, $seed_file.'~loop'.date('Y-m-d-H-i-s'));
				file_put_contents($seed_file, "0");
				return $list[0];
			} else {
				return $list[$seed_last];
			}
		} else {
			return $seed_next;
		}
		
	}

    /**
     * Funções lastname e pais para fins de retrocompatibilidade com a primeira versão
     * @return string
     */
    public static function lastname() {
        return self::file('last_names');
    }
    public static function pais() {
        return self::file('Paises');
    }
    
}