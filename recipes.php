<?php
session_start();

include 'database.php';
include_once './api/spoonacularAPI.php';

if(isset($_SESSION['user']))
{
    $loggedIn = true;
    $name = $_SESSION['user'];
    
}
else
    header("Location: index.php");

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Recipe Finder</title>
        <!--
            !!!!!! IMPORTANT !!!!!
            PLEASE DON'T CHANGE THE ORDER OF THESE STYLE AND SCRIPT TAGS
        -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <link rel="icon" href="img/chefhat.png">
        <style>@import url("./css/styles.css");</style>
        <link href="https://fonts.googleapis.com/css?family=EB+Garamond" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
	    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>   
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    </head>
    
    <body>
        
        <main>
            <?php include_once "inc/navigation.php";?>

            <!--EDIT RECIPE MODAL-->
            <div class="modal fade" id="editRecipeModal" tabindex="-1" role="dialog" aria-labelledby="editRecipeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editRecipeModalLabel"></h5> <!-- RECIPE NAME TO EDIT GOES IN HERE -->
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div style="inline-block" id="editRecipeImgDiv"></div> <!-- RECIPE IMAGE TO EDIT GOES HERE -->
                            <div style="inline-block" id="editRecipeInfoDiv" ></div> <!-- RECIPE INFO TO EDIT GOES HERE -->
                        </div>
                        <div class="modal-footer">
                            <!--<button type="button" class="btn btn-danger" data-dismiss="modal" id="deleteRecipeButton">Delete Recipe</button>-->
                            <button type="button" class="btn btn-danger" id="deleteRecipeButton">Delete Recipe</button>
                            <button type="button" class="btn btn-primary" id="saveChangesButton">Save Changes</button>
                            <button type="button" class="btn btn-success" id="changesSavedButton">Changes saved!</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeButton">Close</button>
                        </div>
                    </div>
                </div>
            </div> 
            
            <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel">Are you sure?</h5> 
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="noButton">No, keep!</button>
                            <button type="button" class="btn btn-success" data-dismiss="modal" id="yesButton">Yes, delete!</button>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="deleteRecipeButton">Delete Recipe</button>
                            <button type="button" class="btn btn-primary" id="saveChangesButton">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeButton">Close</button>
                        </div>
                    </div>
                </div>
            </div> 
            
            
        <?php
            echo "<input type='hidden' id='hiddenUserID' value='" . getUserID() . "'>";
            echo "<input type='hidden' id='hiddenRecipeID' value=''>";
            // echo "<h1>" . getUserID() . "</h1>";
        ?>
            
        </main>
        
        <script src="inc/js/functions.js"></script>
        <script src="inc/js/modal.js"></script>
        <!--<h1><?php echo $name ?>'s Recipes</h1>-->
        <br/>
        <h1>Your Recipes</h1>
        <h2>Click on any image to be able to edit it.<br/>
        Once the window opens, you can click on the title and/or the information and change it.</h2>

        <?php
            function getUserID(){
                $dbConn = getDatabaseConnection();
                $name = $_SESSION['user'];
                $temp = '"'. $name . '"';
                $sql = "SELECT userid FROM `final_users` WHERE username=" . $temp;
                $statement = $dbConn->prepare($sql);
                $statement->execute();
                $records = $statement->fetchAll();
                return $records[0]['userid'];
            }
            
            function getUserData(){
                $userID = getUserID();
                $dbConn = getDatabaseConnection();
                // $sql = "SELECT final_user_recipes.userid, final_user_recipes.name, final_user_recipes.imageURL, final_user_recipes.description
                $sql = "SELECT * 
                        FROM final_user_recipes
                        INNER JOIN final_users
                        ON final_user_recipes.userid=final_users.userid
                        WHERE final_user_recipes.userid=" . $userID;
                $statement = $dbConn->prepare($sql);
                $statement->execute();
                $records = $statement->fetchAll();
                foreach($records as $recipe){
                    //print_r($recipe);
                    // $temp = 0;
                    // echo "<table>";
                    //     echo "<tr>";
                    //     while($temp < 3) {
                            
                    //         echo "<td>";
                    //             echo $recipe['name'];
                    //             echo "</br>";
                    //             echo "<img src='" . $recipe['imageURL'] . "'/>";
                    //         echo "</td>";
                    //         $temp++;
                    //     }
                        
                    //     echo "</tr>";
                    // echo "</table>";
                    // echo "<table>";
                    //     echo "<tr>";
                    //         echo "<td>";
                    //             echo $recipe['name'];
                    //             echo "</br>";
                    //             echo "<img src='" . $recipe['imageURL'] . "'/>";
                    //         echo "</td>";
                    //     echo "</tr>";
                    // echo "</table>";
                    //$temp = $recipe['name'];
                    // echo '<div class="recipeResult" id="' . $recipe['name'] . '" onclick="createEditRecipeModal(this.id)">';
                    echo '<div class="recipeResult" id="' . $recipe['recipeid'] . '" onclick="createEditRecipeModal(this.id)">';
                            echo "<p style='color:white;margin-bottom: 20px'>" . $recipe['name'] . "</p>";
                            echo "<img style='width:500px;height:344px' src='" . $recipe['imageURL'] ."'>";
                            echo '</div>';
                            echo '<br/><br/>';
                    
                }
            }
            
            getUserData();
        
        ?>
    </body>    
</html>
