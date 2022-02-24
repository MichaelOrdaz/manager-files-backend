<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FechasTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_fecha_eventos()
    {
        $fechas = [
            ['start' => '2022-02-01', 'end' => '2022-02-01', 'verdadero' => false],
            ['start' => '2022-02-01', 'end' => '2022-02-28', 'verdadero' => true],
            ['start' => '2022-02-01', 'end' => '2022-02-13', 'verdadero' => true],
            ['start' => '2022-02-12', 'end' => '2022-02-14', 'verdadero' => true],
            ['start' => '2022-02-15', 'end' => '2022-02-16', 'verdadero' => true],
            ['start' => '2022-02-18', 'end' => '2022-02-20', 'verdadero' => true],
            ['start' => '2022-02-22', 'end' => '2022-02-24', 'verdadero' => false],
        ];
        $rangoInicial = [
            'start' => '2022-02-13', 'end' => '2022-02-19'
        ];
        foreach ($fechas as $item) {
            $result = (
                //saber si el el rango esta entre las fecha de la bd
                (
                    (
                        strtotime($rangoInicial['start']) >= strtotime($item['start'])
                        &&
                        strtotime($rangoInicial['start']) <= strtotime($item['end'])
                    )
                    ||
                    (
                        strtotime($rangoInicial['end']) >= strtotime($item['start'])
                        &&
                        strtotime($rangoInicial['end']) <= strtotime($item['end'])
                    )
                )
                || 
                // saber si la fecha de la bd esta entre el rango
                (
                    (
                        strtotime($item['start']) >= strtotime($rangoInicial['start'])
                        &&
                        strtotime($item['start']) <= strtotime($rangoInicial['end'])
                    )
                    ||
                    (
                        strtotime($item['end']) >= strtotime($rangoInicial['start'])
                        &&
                        strtotime($item['end']) <= strtotime($rangoInicial['end'])
                    )
                )
            );
            $this->assertEquals($item['verdadero'], $result);
        }
    }

    public function test_fechas_segundo_metodo()
    {
        $fechas = [
            ['start' => '2022-02-01', 'end' => '2022-02-01', 'verdadero' => false],
            ['start' => '2022-02-01', 'end' => '2022-02-28', 'verdadero' => true],
            ['start' => '2022-02-01', 'end' => '2022-02-13', 'verdadero' => true],
            ['start' => '2022-02-12', 'end' => '2022-02-14', 'verdadero' => true],
            ['start' => '2022-02-15', 'end' => '2022-02-16', 'verdadero' => true],
            ['start' => '2022-02-18', 'end' => '2022-02-20', 'verdadero' => true],
            ['start' => '2022-02-22', 'end' => '2022-02-24', 'verdadero' => false],
        ];
        $rangoInicial = [
            'start' => '2022-02-13', 'end' => '2022-02-19'
        ];
        foreach ($fechas as $item) {
            $result = (
                strtotime($rangoInicial['start']) <= strtotime($item['end'])
                &&
                strtotime($rangoInicial['end']) >= strtotime($item['start'])
            );
            $this->assertEquals($item['verdadero'], $result);
        }
    }
}
