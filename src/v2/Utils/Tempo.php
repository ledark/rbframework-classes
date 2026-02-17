<?php

namespace RBFrameworks\Core\Utils;

class Tempo
{

    //Funções Tempo Atualizadas e Repassadas em 07/04/2015
    //Atualização 2.0

    /**
     * htconvert_timestr2timefrac Converte tempo no formato HH:MM:SS para horas técnicas fracionadas
     * @param string no formato HH:MM:SS
     * @return string no formato HT.MT (hora tecnica com minutos) 
     */
    public static function htconvert_timestr2timefrac($timehuman = "00:00:00"):string
    {
        $arr = explode(":", $timehuman);
        $h = intval($arr[0]);
        $m = intval($arr[1]);
        $s = intval($arr[2]);
        if ($s > 30) $m++;

        //Fracionamento
        if ($m >= 0  and $m < 15) {
            $m = 15;
        } else  
        if ($m >= 15 and $m < 30) {
                $m = 30;
        } else
        if ($m >= 30 and $m < 45) {
                $m = 45;
        } else
        if ($m >= 45 and $m < 60) {
            $m = 0;
            $h++;
        }
        if ($m > 0) {
            $m = 60 / $m;
            $m = round(100 / $m);
        }
        $sh = $h;
        $sm = str_pad($m, 2, "0", STR_PAD_LEFT);
        return "$sh.$sm";
    }

    /**
     * htconvert_timestr2timehuman Converte tempo no formato HH:MM:SS para horas e minutos 
     * @param string no formato HH:MM:SS
     * @return string no formato HH:MM (hora e minuto) 
     */
    public static function htconvert_timestr2timehuman($timehuman = "00:00:00")
    {
        $arr = explode(":", $timehuman);
        $h = intval($arr[0]);
        $m = intval($arr[1]);
        $s = intval($arr[2]);
        if ($s > 30) $m++;
        if ($m < 15) $m = 15;
        $sm = str_pad($m, 2, "0", STR_PAD_LEFT);
        return "$h:$sm";
    }

    /**
     * date_decode Descobre o tipo de data passada retornando: br|en|unix
     * @param string no formato HH:MM:SS
     * @return string no formato HH:MM (hora e minuto) 
     */    
    public static function date_decode($datastr)
    {
        $datastr = trim($datastr);
        if (strlen($datastr) == 10) {
            if (strpos($datastr, '/')) {
                return 'br';
            } else
		if (strpos($datastr, '-')) {
                return 'en';
            } else {
                return 'unix';
            }
        } else
	if (is_numeric($datastr)) {
            return 'unix';
        }
    }

    //Converte a data para o formato especificado
    public static function date_convert($datastr, $para = 'unix')
    {
        $datastr = trim($datastr);
        if (empty($datastr)) return false;
        switch (self::date_decode($datastr)) {
            case 'br':
                switch ($para) {
                    case 'en':
                        return self::date_convert_br2en($datastr);
                        break;
                    case 'br':
                        return self::date_convert_br2br($datastr);
                        break;
                    case 'unix':
                        return self::date_convert_br2unix($datastr);
                        break;
                }
                break;
            case 'en':
                switch ($para) {
                    case 'en':
                        return self::date_convert_en2en($datastr);
                        break;
                    case 'br':
                        return self::date_convert_en2br($datastr);
                        break;
                    case 'unix':
                        return self::date_convert_en2unix($datastr);
                        break;
                }
                break;
            case 'unix':
                switch ($para) {
                    case 'en':
                        return self::date_convert_unix2en($datastr);
                        break;
                    case 'br':
                        return self::date_convert_unix2br($datastr);
                        break;
                    case 'unix':
                        return self::date_convert_unix2unix($datastr);
                        break;
                }
                break;
        }
    }

    //Conversões BR
    public static function date_convert_br2en($databr)
    {
        return substr($databr, 6, 4) . '-' . substr($databr, 3, 2) . '-' . substr($databr, 0, 2);
    }
    public static function date_convert_br2br($databr)
    {
        return $databr;
    }
    public static function date_convert_br2unix($databr)
    {
        return strtotime(self::date_convert_br2en($databr));
    }

    //Conversões EN
    public static function date_convert_en2en($dataen)
    {
        return $dataen;
    }
    public static function date_convert_en2br($dataen)
    {
        return substr($dataen, 8, 2) . '/' . substr($dataen, 5, 2) . '/' . substr($dataen, 0, 4);
    }
    public static function date_convert_en2unix($dataen)
    {
        return strtotime($dataen);
    }

    //Conversões UNIX
    public static function date_convert_unix2en($dataunix)
    {
        return date('Y-m-d', $dataunix);
    }
    public static function date_convert_unix2br($dataunix)
    {
        return date('d/m/Y', $dataunix);
    }
    public static function date_convert_unix2unix($dataunix)
    {
        return $dataunix;
    }

    //Determinar se a data passada pode ser tratada como Unix em valores positivos
    public static function date_unixpossible($datastr)
    {
        $ano = self::date_formatar($datastr, 'ano');
        if ($ano > 1969) {
            return true;
        } else {
            return false;
        }
    }

    public static function date_formatar($datastr, $return = 'd/m/Y')
    {
        switch ($return) {

                //Retornar o ANO de uma data
            case 'Y':
                $ano = self::date_convert($datastr, 'en');
                $ano = substr($ano, 0, 4);
                return $ano;
                break;

                //Retornar o MES de uma data
            case 'm':
                $mes = self::date_convert($datastr, 'en');
                $mes = substr($mes, 5, 2);
                return $mes;
                break;

                //Retornar o DIA de uma data
            case 'd':
                $dia = self::date_convert($datastr, 'en');
                $dia = substr($dia, 8, 2);
                return $dia;
                break;

                //Retornar o DIA DA SEMANA LONGO de uma data
            case 'semana':
                $dataunix = self::date_convert($datastr, 'unix');
                $w = date('w', $dataunix);
            case 0:
                return 'Domingo';
                break;
            case 1:
                return 'Segunda-feira';
                break;
            case 2:
                return 'Terça-feira';
                break;
            case 3:
                return 'Quarta-feira';
                break;
            case 4:
                return 'Quinta-feira';
                break;
            case 5:
                return 'Sexta-feira';
                break;
            case 6:
                return 'Sábado';
                break;
                break;

                //Retornar o MÊS VERBAL LONGO de uma data
            case 'mes':
                $mes = self::date_formatar($datastr, 'm');
                switch ($mes) {
                    case '01':
                        return 'Janeiro';
                        break;
                    case '02':
                        return 'Fevereiro';
                        break;
                    case '03':
                        return 'Março';
                        break;
                    case '04':
                        return 'Abril';
                        break;
                    case '05':
                        return 'Maio';
                        break;
                    case '06':
                        return 'Junho';
                        break;
                    case '07':
                        return 'Julho';
                        break;
                    case '08':
                        return 'Agosto';
                        break;
                    case '09':
                        return 'Setembro';
                        break;
                    case '10':
                        return 'Outubro';
                        break;
                    case '11':
                        return 'Novembro';
                        break;
                    case '12':
                        return 'Dezembro';
                        break;
                }
                break;

                //Exibição de Data Dinâmico
            case 'smart':
                $dataunix = self::date_convert($datastr, 'unix');
                return self::formatar_tempo2($dataunix);
                break;

                //Tempo Retroativo (Ex. 10 dias atrás)
            case 'passado':
                $dataunix = self::date_convert($datastr, 'unix');
                return self::formatar_tempo($dataunix);
                break;
        }
    }

    /*
 * Função formatar_tempo()
 * 
 * Está função retorna o tempo em que determinada ação ocorreu.
 * Exemplo:
 *  - Postagem em um blog:
 *    3 minutos atrás
 *    7 dias atrás
 *    2 meses atrás
 * 
 * e assim por diante.
 * 
 * COMO USAR
 * 
 * Insira no banco de dados o tempo em segundos usando a função time()
 * Quando quiser exibir o tempo passado é só chamar a função formatar_tempo()
 * e passar como parametro o valor que foi inserido no banco de dados.
 * 
 * Script feito por: Túlio Spuri <tulios@comp.ufla.br>
 * 
 * Qualquer dúvida é só entrar em contato <tulios@comp.ufla.br>
 * 
 */

    public static function formatar_tempo2($timeBD)
    {
        $timeNow = time();
        $timeRes = $timeNow - $timeBD;
        if ($timeRes < 86400) return date('H:i', $timeBD);
        if ($timeRes < 31104000) return date('d/m', $timeBD);
        else return date('m/Y', $timeBD);
    }


    public static function formatar_tempo($timeBD)
    {

        $timeNow = time();
        $timeRes = $timeNow - $timeBD;
        $nar = 0;

        // variável de retorno
        $r = "";

        // Agora
        if ($timeRes == 0) {
            $r = "agora";
        } else
            // Segundos
            if ($timeRes > 0 and $timeRes < 60) {
                $r = $timeRes . " segundos atr&aacute;s";
            } else
                // Minutos
                if (($timeRes > 59) and ($timeRes < 3599)) {
                    $timeRes = $timeRes / 60;
                    if (round($timeRes, $nar) >= 1 and round($timeRes, $nar) < 2) {
                        $r = round($timeRes, $nar) . " minuto atr&aacute;s";
                    } else {
                        $r = round($timeRes, $nar) . " minutos atr&aacute;s";
                    }
                } else
                    // Horas
                    // Usar expressao regular para fazer hora e MEIA
                    if ($timeRes > 3559 and $timeRes < 85399) {
                        $timeRes = $timeRes / 3600;

                        if (round($timeRes, $nar) >= 1 and round($timeRes, $nar) < 2) {
                            $r = round($timeRes, $nar) . " hora atr&aacute;s";
                        } else {
                            $r = round($timeRes, $nar) . " horas atr&aacute;s";
                        }
                    } else
                        // Dias
                        // Usar expressao regular para fazer dia e MEIO
                        if ($timeRes > 86400 and $timeRes < 2591999) {

                            $timeRes = $timeRes / 86400;
                            if (round($timeRes, $nar) >= 1 and round($timeRes, $nar) < 2) {
                                $r = round($timeRes, $nar) . " dia atr&aacute;s";
                            } else {

                                preg_match('/(\d*)\.(\d)/', $timeRes, $matches);

                                if ($matches[2] >= 5) {
                                    $ext = round($timeRes, $nar) - 1;

                                    // Imprime o dia
                                    $r = $ext;

                                    // Formata o dia, singular ou plural
                                    if ($ext >= 1 and $ext < 2) {
                                        $r .= " dia ";
                                    } else {
                                        $r .= " dias ";
                                    }

                                    // Imprime o final da data
                                    $r .= "&frac12; atr&aacute;s";
                                } else {
                                    $r = round($timeRes, 0) . " dias atr&aacute;s";
                                }
                            }
                        } else
                            // Meses
                            if ($timeRes > 2592000 and $timeRes < 31103999) {

                                $timeRes = $timeRes / 2592000;
                                if (round($timeRes, $nar) >= 1 and round($timeRes, $nar) < 2) {
                                    $r = round($timeRes, $nar) . " mes atr&aacute;s";
                                } else {

                                    preg_match('/(\d*)\.(\d)/', $timeRes, $matches);

                                    if ($matches[2] >= 5) {
                                        $ext = round($timeRes, $nar) - 1;

                                        // Imprime o mes
                                        $r .= $ext;

                                        // Formata o mes, singular ou plural
                                        if ($ext >= 1 and $ext < 2) {
                                            $r .= " mes ";
                                        } else {
                                            $r .= " meses ";
                                        }

                                        // Imprime o final da data
                                        $r .= "&frac12; atr&aacute;s";
                                    } else {
                                        $r = round($timeRes, 0) . " meses atr&aacute;s";
                                    }
                                }
                            } else
                                // Anos
                                if ($timeRes > 31104000 and $timeRes < 155519999) {

                                    $timeRes /= 31104000;
                                    if (round($timeRes, $nar) >= 1 and round($timeRes, $nar) < 2) {
                                        $r = round($timeRes, $nar) . " ano atr&aacute;s";
                                    } else {
                                        $r = round($timeRes, $nar) . " anos atr&aacute;s";
                                    }
                                } else
                                    // 5 anos, mostra data
                                    if ($timeRes > 155520000) {

                                        $localTimeRes = localtime($timeRes);
                                        $localTimeNow = localtime(time());

                                        $timeRes /= 31104000;
                                        $gmt = array();
                                        $gmt['mes'] = $localTimeRes[4];
                                        $gmt['ano'] = round($localTimeNow[5] + 1900 - $timeRes, 0);

                                        $mon = array("Jan ", "Fev ", "Mar ", "Abr ", "Mai ", "Jun ", "Jul ", "Ago ", "Set ", "Out ", "Nov ", "Dez ");

                                        $r = $mon[$gmt['mes']] . $gmt['ano'];
                                    }

        return $r;
    }

    //Para Efeitos de Compatibilidade
    public static function Tempo($time)
    {
        /*
	O tempo é passado em segundos.
	O ideal é converter esse tempo:
	*/
        //Menos de 60 Segundos: Retorne Segundos
        if ($time < 60) $return = "$time segundos";

        //Entre 1 minuto e 1 hora: Retone Minutos
        if ($time >= 60 and $time < 3600) {
            $return = intval($time / 60) . ' minuto(s)';
        }

        //Entre 1 hora e 1 dia 
        if ($time >= 3600 and $time < 86400) {
            $return = intval($time / 3600) . ' hora(s)';
        }

        //Entre 1 dia e 1 mês
        if ($time >= 86400 and $time < 2592000) {
            $return = intval($time / 86400) . ' dia(s)';
        }

        //Entre 1 mês e um ano
        if ($time >= 2592000 and $time < 31104000) {
            $return = intval($time / 2592000) . ' mes(es)';
        }

        //Mais de 1 ano
        if ($time >= 31104000) {
            $return = intval($time / 31104000) . ' ano(s)';
        }

        return $return;
    }
}
