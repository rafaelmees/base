<?php

namespace Bludata\Doctrine\Common\Interfaces;

interface BaseEntityInterface
{
    public function getId();

    public function save();

    public function remove($abort = true);

    /**
     * @return null|\Bludata\Doctrine\ORM\Entities\BaseEntity
     */
    public function restoreRemoved();

    public function flush($all = true);

    public function getRepository();

    public function forcePersist();

    /**
     * @return void
     */
    public function prePersist();

    /**
     * @return void
     */
    public function postPersist();

    /**
     * @return void
     */
    public function preUpdate();

    /**
     * @return void
     */
    public function postUpdate();

    /**
     * @return void
     */
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
