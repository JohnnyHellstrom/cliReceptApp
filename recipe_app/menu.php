<?php
require("recipe.php");

$file = "recept.json";

function runMenu(){
   $choice = printMenu();

   switch ($choice) {
      case 0:
         die();
         break;
      case 1:
         searchRecipes();
         break;
      case 2:
         addRecipe();
         break;
      case 3:
         removeRecipe();
         break;
      case 4:
         printRecipes();
         break;
      default:
         echo "Ogiltigt menyval". PHP_EOL;
  }

}
//Gets all recipes with all searchwords/ingredients
function searchRecipes(){
   global $file;


   echo "Skriv in ingredienser" . PHP_EOL;
   $searchedIngredients = cleanIngredients(readline());

   $allRecipes = json_decode(file_get_contents($file));
   $searchHits = [];

   foreach ($allRecipes as $recipe) {
      $hit = true;
      foreach ($searchedIngredients as $searchword) {
         if(!in_array($searchword, $recipe->ingredients)){
            $hit = false;
            break;
         }
      }
      if($hit == true){
         $searchHits[] = $recipe->name;
      }
   }

   if(empty($searchHits)){
      echo "Ingen träff tyvärr" . PHP_EOL;
   } else {
      echo "Recept som matchar ditt sök" . PHP_EOL;
      foreach ($searchHits as $hit) {
         echo $hit . PHP_EOL;
      }
   }

}

function removeRecipe(){
   global $file;
   $removed = false;

   echo "Vilket recept vill du ta bort? ";
   $input = cleanInput(readline());

   $allRecipes = json_decode(file_get_contents($file));
   foreach($allRecipes as $key => $recipe){
      if($recipe->name == $input){
         unset($allRecipes[$key]);
         $allRecipes = array_values($allRecipes);
         $removed = true;
         echo "Receptet " . $input . " är borttaget. Tryck enter" . PHP_EOL;
         readline();
      }
   }
   if($removed == true){
      file_put_contents($file, json_encode($allRecipes));
   } else {
      echo "Fann inget recept med namnet " . $input . PHP_EOL;
   }
   
}

function printRecipes(){
   global $file;
   $allRecipes = json_decode(file_get_contents($file));
   echo "Alla sparade recept:" . PHP_EOL;
   foreach ($allRecipes as $recipe) {
      echo ucfirst(htmlspecialchars($recipe->name . PHP_EOL));
   }
   echo "Tryck enter för att komma tillbaka till menyn";
   readline();
}

function addRecipe(){
   global $file;

   echo "Namn på receptet: ";
   $name = cleanInput(readline());
   if(!$name){
      echo "Ej giltigt namn" . PHP_EOL;
      return;
   }

   echo "Skriv in ingridienser: ";
   $ingredients = cleanIngredients(readline());

   $allRecipes = json_decode(file_get_contents($file));
   if(empty($allRecipes)){
      $allRecipes = [];
   }
   
   $allRecipes[] = new Recipe($name, $ingredients);
   file_put_contents($file, json_encode($allRecipes));

   echo "Receptet sparat. Tryck enter";
   readline();
}

function printMenu(){
   echo PHP_EOL . "Välj från menyn:" . PHP_EOL . "1 Sök recept" . PHP_EOL . "2 Lägg till recept" . PHP_EOL . "3 Ta bort recept" . PHP_EOL . "4 Lista alla recept" . PHP_EOL . "0 Avsluta" . PHP_EOL;
   return readline();
}

// to lowercase and remove trailing commas and whitespace
function cleanInput($input){
   return strtolower(rtrim($input, ", \t\n"));
}
// replaces ", " with "," after running cleanInput
function cleanIngredients($input){  
   return explode(',', preg_replace("/, /", ",", cleanInput($input)));
}