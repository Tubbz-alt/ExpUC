<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Subject extends Model
{
    protected $guarded = [];

    protected static $rules = [
        'weekday' => 'required|integer|between:0,6'
    ];

    public function getTelegramGroups(){
        if($this->telegram_groups === null || $this->telegram_groups === "")
            return array();
        return json_decode($this->telegram_groups);
    }

    public function appendNewTelegramGroup(string $groupURL){
        $groupsArray = $this->getTelegramGroups();
        array_push($groupsArray, $groupURL);
        $this->telegram_groups = json_encode($groupsArray);
        $this->save();
    }

    public function removeTelegramGroup(int $index){
        $groupsArray = $this->getTelegramGroups();
        unset($groupsArray[$index]);
        $this->telegram_groups = json_encode(array_values($groupsArray));
        $this->save();
    }

    public function teachers(){
        return $this->belongsToMany(Teacher::class, 'subject_teacher');
    }

    public function users(){
        return $this->belongsToMany(User::class, 'subject_user');
    }

    public function save(array $options = [])
    {
        $validation = Validator::make(
            $this->getArrayableAttributes(),
            static::$rules
        );

        if($validation->fails()){
            throw new ValidationException($validation);
        }

        return parent::save($options);
    }

}
