<?php

class Recipe{
   public $name;
   public $ingredients;

   function __construct($name, $ingredients) {
      $this->name = $name;
      $this->ingredients = $ingredients;
   }
}

