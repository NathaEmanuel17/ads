<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Subscription extends Entity
{
    protected $dates   = ['created_at', 'updated_at',];
    protected $casts   = [
        'is_paid'       => 'boolean'
    ];

    public function setHistory(array $history) 
    {

        $this->attributes['history'] = serialize($history);

        return $this;
    }

    public function getHistory() 
    {
        if(is_string($this->attributes['history'])) {

            $this->attributes['history'] = unserialize($this->attributes['history']);
        }

        return $this->attributes['history'];
    }

    
    public function setFeatures(object $features) 
    {

        $this->attributes['features'] = serialize($features);

        return $this;
    }

    public function getFeatures() 
    {
        if(is_string($this->attributes['features'])) {

            $this->attributes['features'] = (object) unserialize($this->attributes['features']);
        }

        return $this->attributes['features'];
    }

    public function isValid():bool
    {
        // a assinatura está paga? ou a inda não expirou a data de nova consulta na gerencianet?
        if(!$this->attributes['is_paid'] || $this->attributes['valid_until'] < date('Y-m-d H:i:s')) {

            return false;
        }

        // Sim... está valida
        return true;
    }
}
