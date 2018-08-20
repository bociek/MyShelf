<?php

namespace Model;

class Users
{
    /**
     *
     * var array Users
     *
     */
    protected $users = array(
        'id'        => '1',
        'firstname' => 'Jan',
        'lastname'  => 'Kowalski',
        'username'  => 'jan01',
    );

    /**
     *
     * Find all users
     *
     */
    public function findAll()
    {
        return $this->users;
    }

    /**
     *
     * Find one user by id
     *
     */
    public function findOneById($id)
    {
        $user = [];

        if (isset($this->users[$id]) && count($this->users[$id])) {
            $user = $this->users[$id];
        }

        return $user;
    }

}