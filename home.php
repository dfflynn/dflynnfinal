<?php
if(isset($_GET['tag']))
{
    $tag= $_GET['tag'];
    include './api/spoonacularAPI.php';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Recipe Finder</title>
        <link href = "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel = "stylesheet">
        <style>@import url("./css/styles.css");</style>
        <link href="https://fonts.googleapis.com/css?family=EB+Garamond" rel="stylesheet">
    </head>
    <body>
        <br><br><br>
        <header>
            <h1>Recipe Search</h1>
        </header>
        <main>
        <form>
            <input type="text" name="tag" placeholder = "Enter Ingredients" value="<?=$_GET['tag']?>"/>
            </br>
            <input type="Submit" value="Search"/><button id="trivia">test</button>
        </form>
        
        <?php
        if(empty($_GET)) // form was not submitted
        { 
            echo "";
        } 
        else // form was submitted
        { 
            if(!empty($tag))
            {
                echo "<h1 style= 'margin: 0'> You searched for: ". $_GET['tag']. "</h1>";
                $recipes = ingredientSearch($_GET['tag'], 5);
                //print_r($recipes);
                echo "<br>";
                for($i = 0; $i < 5; $i++)
                {
                    echo $recipes[$i]['title'];
                    echo "<br>";
                    echo "<img src= " . $recipes[$i]['image'] .">";
                    echo "<br></br>";
                }
            }
        }
        ?>
        </main>
        
    </body>    
</html>