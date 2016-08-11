<?php

/**
 * Cria uma nova instância do Faker
 *
 * @return Faker\Factory Factory para geração de dados randômicos
 */
function faker()
{
    return Faker\Factory::create('pt_Br');
}
