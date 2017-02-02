<?php

namespace Bludata\Doctrine\Common\Interfaces;

interface BaseEntityInterface
{
    public function getId();

    public function save($flush = false);

    public function remove();

    public function flush($all = true);

    public function getRepository();

    public function forcePersist();

    public function prePersist();

    public function postPersist();

    public function preUpdate();

    public function postUpdate();

    public function preFlush();

    /**
     * Retona um array com o nome das propriedade que o cliente pode setar para realizar o store
     * É usado principalmente em $this->setPropertiesEntity e nos Controllers.
     * Este método não evita que uma propriedade seja alterada caso tenha seu método set().
     *
     * @return array
     */
    public function getOnlyStore();

    /**
     * Retona um array com o nome das propriedade que o cliente pode setar para realizar o update.
     * Por padrão retorna os mesmos valores de $this->getOnlyStore().
     * Este método pode ser sobrescrito nas classes filhas.
     * É usado principalmente em $this->setPropertiesEntity e nos Controllers.
     * Este método não evita que uma propriedade seja alterada caso tenha seu método set().
     *
     * @return array
     */
    public function getOnlyUpdate();

    /**
     * Set all attributes of the Entity with the values in $data.
     *
     * @param array $data Key value pair to set attributes
     */
    public function setPropertiesEntity(array $data);

    /**
     * Convert the Entity to array.
     *
     * @param array $options [description]
     *
     * @return [type] [description]
     */
    public function toArray(array $options = null);
}
