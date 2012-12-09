<?php
class Country_Form_Field_PoolSelect extends Kwf_Form_Field_Select
{
    public function setPool($pool)
    {
        $table = new CountryPool();
        $this->setValues($table->fetchAll());
        return $this;
    }
}
